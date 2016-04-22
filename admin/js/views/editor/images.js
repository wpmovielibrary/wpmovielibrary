
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view, {

	ImageList: wp.Backbone.View.extend({

		/**
		 * Initialize the View.
		 * 
		 * @param    object    Options
		 * 
		 * @since    3.0
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
			this.collection = options.collection || {};
			this.columns    = options.columns    || 4;
			this.images = {};

			// Add new views for new movies
			this.listenTo( this.collection, 'add', function( model, collection, options ) {
				this.views.add( this.create_subview( model ), { at: 0 } );
			} );

			// Remove views when models are removed
			this.listenTo( this.collection, 'remove', function( model, collection, options ) {
				var view = this.images[ model.cid ];
				delete this.images[ model.cid ];

				if ( view ) {
					view.remove();
				}
			} );

			// Re-render the view when collection is emptied
			this.listenTo( this.collection, 'reset', this.render );

			// Event handlers
			_.bindAll( this, 'set_grid' );

			// Reset columns on resize
			this.$window = wpmoly.$( window );
			this.$window.off( 'resize.' + this.id ).on( 'resize.' + this.id, _.debounce( this.set_grid, 50 ) );

			
		},

		/**
		 * Prepare the View.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Model
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		prepare: function() {

			if ( this.collection.length ) {
				this.views.set( this.collection.map( this.create_subview, this ) );
			} else {
				this.views.unset();
				this.collection.fetch().done(
					_.debounce( this.set_grid, 50 )
				);
			}
		},

		/**
		 * Render the view.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function(  ) {

			wp.Backbone.View.prototype.render.apply( this, arguments );

			this.$el.attr( 'data-columns', this.columns );

			var more = new this.moreView({ controller: this.controller });
			this.views.add( more );

			//this.set_grid();
			_.defer( this.set_grid, 50 );

			return this;
		},

		/**
		 * Calcul the best number of columns to use and resize thumbnails
		 * to fit correctly.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		set_grid: function() {

			var prev = this.columns,
			   width = this.$el.actual( 'width' ) - 50;

			if ( width ) {
				this.columns = Math.min( Math.ceil( width / this.ideal_width ), 12 ) || 1;
				if ( ! prev || prev !== this.columns ) {
					this.$el.closest( '.wpmoly-imported-images' ).attr( 'data-columns', this.columns );
				}
			}

			this.set_columns( force = true );

			return this;
		},

		/**
		 * Fix thumbnails height to display properly in the grid.
		 *
		 * If the force parameter is set to true every movie in the
		 * grid will be resized; it set to false only movies not already
		 * resized will be considered.
		 * 
		 * @since    3.0
		 * 
		 * @param boolean force resize
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		set_columns: function( force ) {

			if ( ! this.collection.length ) {
				return;
			}

			if ( true === force ) {
				var $li = this.$( 'li' ),
				$items = $li.find( '.thumbnail' );
				$items.css({ width: '', height: '' });
				$li.css({ width: '' });
			} else {
				var $li = this.$( 'li' ).not( '.resized' ),
				$items = $li.find( '.thumbnail' );
			}

			var width = this.$( 'li:first' ).actual( 'width' ) - 10,
			   height = this.set_thumbnail_height( width );

			this.thumbnail_width  = width;
			this.thumbnail_height = height;

			$li.addClass( 'resized' ).css({
				width: this.thumbnail_width,
				height: this.thumbnail_height
			});
			$items.css({
				width: this.thumbnail_width,
				height: this.thumbnail_height
			});

			return this;
		},

		/**
		 * Calculate thumbnail height for the given width value.
		 * 
		 * This should be andled in child views to ensure all thumbnails
		 * have a common ratio. Default behaviour is to return the given
		 * width, resulting in square-shaped thumbnails.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    width
		 * 
		 * @return   int
		 */
		set_thumbnail_height: function( width ) {

			return width;
		},

		/**
		 * Create a backdrop subview.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Model
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		create_subview: function( model ) {

			var view = new this.imageView({ model: model, controller: this.controller, collection: this.collection, parent: this });

			return this.images[ model.cid ] = view;
		}
	}),

	ImageEditor: wp.Backbone.View.extend({

		template: wp.template( 'wpmoly-editor-image-editor' ),

		events: {
			'click a': 'preventDefault',
			'click [data-action="edit"]':  'edit',
			'click [data-action="close"]': 'close',
			'change [data-image-data], #image_description': 'update'
		},

		/**
		 * Initialize the View.
		 * 
		 * @param    object    Options
		 * 
		 * @since    3.0
		 */
		initialize: function( options ) {

			this.model      = options.model      || {};
			this.controller = options.controller || {};

			this.listenTo( this.model, 'change', this.change );

			this.listenTo( this.model, 'autosave:start', this.saving );
			this.listenTo( this.model, 'autosave:done',  this.saved );
			this.listenTo( this.model, 'autosave:fail',  this.notsaved );
		},

		/**
		 * Render the view.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			var data = _.extend( this.model.toJSON() || {}, {
				type:  this.type
			} );

			this.$el.html( this.template( data ) );

			return this;
		},

		/**
		 * Update the Model with new values.
		 * 
		 * @since    3.0
		 * 
		 * @param    Event     JS 'change' event.
		 */
		update: function( event ) {

			var $elem = this.$( event.currentTarget ),
			      key = $elem.attr( 'data-image-data' ),
			    value = $elem.val();

			this.model.set( key, value );
		},

		/**
		 * Update the View to match Model changes.
		 * 
		 * @since    3.0
		 * 
		 * @param    Model     Model changed
		 * @param    object    Changes options
		 */
		change: function( model, options ) {

			_.each( model.changed, function( value, key ) {
				var $field = this.$( '[data-image-data="' + key + '"]' );
				if ( $field.length ) {
					$field.val( value );
				}
			}, this );
		},

		/**
		 * Animate the field saving process.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    fields Changed fFields, usually one single field
		 * @param    string    status Saving or saved?
		 */
		saving: function( fields, status ) {

			_.each( fields, function( value, key ) {
				var $field = this.$( '[data-image-data="' + key + '"]' ),
				     $elem = $field.parent( '.editor-form-item-value' );
				if ( $elem.length ) {
					if ( 'saving' === status ) {
						$elem.addClass( 'saving' );
					} else if ( 'saved' === status ) {
						$elem.removeClass( 'saving' );
						$elem.append( '<div class="saved-notice"><span class="wpmolicon icon-yes"></span></div>' );
						window.setTimeout( function() {
							wpmoly.$( '.saved-notice' ).fadeOut( 750 );
						}, 750 );
					} else if ( 'failed' === status ) {
						$elem.removeClass( 'saving' );
						$elem.append( '<div class="saved-notice"><span class="wpmolicon icon-no"></span></div>' );
						window.setTimeout( function() {
							wpmoly.$( '.saved-notice' ).fadeOut( 1500 );
						}, 1500 );
					} 
				}
			}, this );
		},

		/**
		 * Animate the field saving process.
		 * 
		 * Shortcut function for saving( field, 'saved' ).
		 * 
		 * @since    3.0
		 * 
		 * @param    object    fields Changed fFields
		 */
		saved: function( fields ) {

			this.saving( fields, 'saved' );
		},

		/**
		 * Animate the field saving process.
		 * 
		 * Shortcut function for saving( field, 'failed' ).
		 * 
		 * @since    3.0
		 * 
		 * @param    object    fields Changed fFields
		 */
		notsaved: function( fields ) {

			this.saving( fields, 'failed' );
		},

		/**
		 * Open the real WordPress Image Editor.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		edit: function( event ) {

			wpmoly.info( 'Open image editor' );

			// Open the editor modal but hide its content while loading
			wp.media.editor.open();
			wp.media.frame.$el.hide();

			var  media = wp.media, frame = media.frame,
			     state = frame.states.get( 'edit-image' ),
			attachment = media.attachment( this.model.get( 'id' ) );

			if ( window.imageEdit && state ) {

				this.listenTo( frame, 'content:deactivate:edit-image', function() {
					frame.close();
				} );

				// Attachment hasn't been fetched yet
				if ( _.isUndefined( attachment.mime ) ) {
					attachment.fetch().done( function() {
						state.set( 'image', attachment );
						frame.setState( 'edit-image' );
						state.trigger( 'activate' );
						frame.$el.show();
					} );
				} else {
					state.set( 'image', attachment );
					frame.setState( 'edit-image' );
					state.trigger( 'activate' );
					frame.$el.show();
				}
			} else {
				frame.escape();
			}
		},

		/**
		 * Close the editor view.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		close: function( event ) {

			wpmoly.info( 'Close image editor' );

			// Close WP Image Editor
			imageEdit.close();

			wpmoly.trigger( 'editor:image:edit:close' );
		},

		/**
		 * Deactivate links.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		preventDefault: function( event ) {

			event.stopPropagation();
			event.preventDefault();
		}

	}),

	Images: wpmoly.view.Frame.extend({

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Options
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
			this.collection = options.collection || {};

			wpmoly.on( 'editor:image:edit:close', this.open_list,   this );
			wpmoly.on( 'editor:image:edit:open',  this.open_editor, this );

			this.on( 'ready', function() {
				this.mode( 'list', {
					controller: this.controller,
					collection: this.collection
				} );
			}, this );
		},

		/**
		 * Activate the image editor.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model
		 * @param    object    options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		open_editor: function( model, options ) {

			var options = options || {};
			_.extend( options, {
				model: model
			} );

			return this.mode( 'editor', options );
		},

		/**
		 * Activate the image editor.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		open_list: function( options ) {

			var options = options || {};
			_.extend( options, {
				collection: this.collection
			} );

			return this.mode( 'list', options );
		},
	})
} );

_.extend( wpmoly.view, {

	BackdropList: wpmoly.view.ImageList.extend({

		id: 'wpmoly-imported-backdrops',

		tagName: 'ul',

		className: 'wpmoly-imported-images wpmoly-imported-backdrops clearfix',

		imageView: wpmoly.view.Backdrop,
		
		moreView: wpmoly.view.BackdropMore,

		ideal_width: 220,

		/**
		 * Calculate thumbnail height for the given width value.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    width
		 * 
		 * @return   int
		 */
		set_thumbnail_height: function( width ) {

			return Math.round( width / 1.5 );
		}
	}),

	PosterList: wpmoly.view.ImageList.extend({

		id: 'wpmoly-imported-posters',

		tagName: 'ul',

		className: 'wpmoly-imported-images wpmoly-imported-posters clearfix',

		imageView: wpmoly.view.Poster,
		
		moreView: wpmoly.view.PosterMore,

		ideal_width: 160,

		/**
		 * Calculate thumbnail height for the given width value.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    width
		 * 
		 * @return   int
		 */
		set_thumbnail_height: function( width ) {

			return Math.round( width * 1.5 );
		},
	}),

	BackdropEditor: wpmoly.view.ImageEditor.extend({

		id: 'wpmoly-backdrops-editor',

		tagName: 'div',

		className: 'wpmoly-images-editor wpmoly-backdrops-editor clearfix',

		type: 'backdrop'

	}),

	PosterEditor: wpmoly.view.ImageEditor.extend({

		id: 'wpmoly-posters-editor',

		tagName: 'div',

		className: 'wpmoly-images-editor wpmoly-posters-editor clearfix',

		type: 'poster'

	}),

	Backdrops: wpmoly.view.Images.extend({

		id: 'wpmoly-backdrops-preview',

		tagName: 'div',

		className: 'wpmoly-images-preview wpmoly-backdrops-preview',

		default_mode: 'images',

		/**
		 * Initialize the View.
		 * 
		 * @param    object    Options
		 * 
		 * @since    3.0
		 */
		initialize: function( options ) {

			wpmoly.view.Images.prototype.initialize.apply( this, arguments );

			this.modes = {
				list:   wpmoly.view.BackdropList,
				editor: wpmoly.view.BackdropEditor
			};
		}

	}),

	Posters: wpmoly.view.Images.extend({

		id: 'wpmoly-posters-preview',

		tagName: 'div',

		className: 'wpmoly-images-preview wpmoly-posters-preview',

		default_mode: 'images',

		/**
		 * Initialize the View.
		 * 
		 * @param    object    Options
		 * 
		 * @since    3.0
		 */
		initialize: function( options ) {

			wpmoly.view.Images.prototype.initialize.apply( this, arguments );

			this.modes = {
				list:   wpmoly.view.PosterList,
				editor: wpmoly.view.PosterEditor
			};
		}

	})

} );
