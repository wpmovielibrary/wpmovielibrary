
wpmoly = window.wpmoly || {};

var Modal = wpmoly.view.Modal || {};

_.extend( Modal, {

	Image: wp.media.View.extend({

		tagName: 'li',

		className: 'attachment save-ready',

		template: wp.template( 'wpmoly-modal-image' ),

		events: {
			'click .js--select-attachment': 'toggleSelectionHandler',
			'click .check':                 'checkClickHandler',
			'keydown':                      'toggleSelectionHandler'
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @param    object    options
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			var options = options || {};
			this.controller = options.controller;
			this.collection = this.controller.collection;
			this.selection  = this.controller.selection;

			this.listenTo( this.model, 'add',    this.select );
			this.listenTo( this.model, 'remove', this.deselect );

			this.listenTo( this.selection, 'reset', this.updateSelect );

			this.listenTo( this.model, 'change:existing', this.updateExisting );

			this.listenTo( this.model, 'selection:single selection:unsingle', this.details );

		},

		/**
		 * Render the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		render: function() {

			this.$el.html( this.template( this.model.toJSON() ) );

			this.$el.attr( 'data-lang',    this.model.get( 'iso_639_1' ) );
			this.$el.attr( 'aria-label',   this.model.get( 'name' ) );
			this.$el.attr( 'aria-checked', 'false' );
			this.$el.prop( 'role',         'checkbox' );
			this.$el.prop( 'tabindex',     0 );

			// Check if the model is selected.
			this.updateSelect();

			return this;
		},

		/**
		 * Dispose the current view. Override wp.media.View.dispose() to
		 * unbind the selection before disposing.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		dispose: function() {

			if ( this.selection ) {
				this.selection.off( null, null, this );
			}

			// call 'dispose' directly on the parent class
			wp.media.View.prototype.dispose.apply( this, arguments );

			return this;
		},

		/**
		 * Wrapper for the selection handler. Make distinction between
		 * multiple selection, single selection, pressed keys...
		 *
		 * @since    3.0
		 *
		 * @param    object    JS event
		 *
		 * @return   void
		 */
		toggleSelectionHandler: function( event ) {

			var method,
			   keyCode = event.keyCode ? event.keyCode : event.charCode;

			// Don't do anything inside inputs and on the attachment check and remove buttons.
			if ( 'INPUT' === event.target.nodeName || 'BUTTON' === event.target.nodeName ) {
				return;
			}

			// Catch arrow events
			if ( 37 === keyCode || 38 === keyCode || 39 === keyCode || 40 === keyCode ) {
				wpmoly.trigger( 'modal:image:keydown:arrow', event );
				event.preventDefault();
				return;
			}

			// Catch enter and space events
			if ( 'keydown' === event.type && 13 !== keyCode && 32 !== keyCode ) {
				return;
			}

			event.preventDefault();

			wpmoly.trigger( 'modal:image:preview', this.model );

			method = 'toggle';
			if ( event.shiftKey ) {
				method = 'between';
			} else if ( event.ctrlKey || event.metaKey ) {
				method = 'toggle';
			}

			this.toggleSelection({
				method: method
			});

			this.controller.trigger( 'selection:toggle' );
		},

		/**
		 * Handle the image selection process.
		 *
		 * @since    3.0
		 *
		 * @param    object    options
		 *
		 * @return   void
		 */
		toggleSelection: function( options ) {

			var method = options && options.method,
			    single, models, singleIndex, modelIndex;

			if ( ! this.selection ) {
				return;
			}

			single = this.selection.single();
			method = _.isUndefined( method ) ? this.selection.multiple : method;

			// If the `method` is set to `between`, select all models that
			// exist between the current and the selected model.
			if ( 'between' === method && single && this.selection.multiple ) {
				// If the models are the same, short-circuit.
				if ( single === this.model ) {
					return;
				}

				singleIndex = this.collection.indexOf( single );
				modelIndex  = this.collection.indexOf( this.model );

				if ( singleIndex < modelIndex ) {
					models = this.collection.models.slice( singleIndex, modelIndex + 1 );
				} else {
					models = this.collection.models.slice( modelIndex, singleIndex + 1 );
				}

				this.selection.add( models );
				this.selection.single( this.model );

				return;

			// If the `method` is set to `toggle`, just flip the selection
			// status, regardless of whether the model is the single model.
			} else if ( 'toggle' === method ) {
				this.selection[ this.selected() ? 'remove' : 'add' ]( this.model );
				this.selection.single( this.model );
				return;
			} else if ( 'add' === method ) {
				this.selection.add( this.model );
				this.selection.single( this.model );
				return;
			}

			// Fixes bug that loses focus when selecting a featured image
			if ( ! method ) {
				method = 'add';
			}

			if ( method !== 'add' ) {
				method = 'reset';
			}

			if ( this.selected() ) {
				// If the model is the single model, remove it.
				// If it is not the same as the single model,
				// it now becomes the single model.
				this.selection[ single === this.model ? 'remove' : 'single' ]( this.model );
			} else {
				// If the model is not selected, run the `method` on the
				// selection. By default, we `reset` the selection, but the
				// `method` can be set to `add` the model to the selection.
				this.selection[ method ]( this.model );
				this.selection.single( this.model );
			}
		},

		/**
		 * Update selection status for the current image.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		updateSelect: function() {

			this[ this.selected() ? 'select' : 'deselect' ]();
		},

		/**
		 * Check whether the image is selected or not.
		 *
		 * @since    3.0
		 *
		 * @param    object    options
		 *
		 * @return   unresolved|Boolean
		 */
		selected: function() {

			if ( this.selection ) {
				return !! this.selection.get( this.model.cid );
			}
		},

		/**
		 * Select an image. Add 'selected' CSS class and check
		 * 'aria-checked' attribute.
		 *
		 * @since    3.0
		 *
		 * @param    object    model
		 * @param    object    collection
		 *
		 * @return   void
		 */
		select: function( model, collection ) {

			// Check if a selection exists and if it's the collection provided.
			// If they're not the same collection, bail; we're in another
			// selection's event loop.
			if ( ! this.selection || ( collection && collection !== this.selection ) ) {
				return;
			}

			// Bail if the model is already selected.
			if ( this.$el.hasClass( 'selected' ) ) {
				return;
			}

			// Add 'selected' class to model, set aria-checked to true.
			this.$el.addClass( 'selected' ).attr( 'aria-checked', true );
		},

		/**
		 * Deselect an image. Remove 'selected' CSS class and uncheck
		 * 'aria-checked' attribute.
		 *
		 * @since    3.0
		 *
		 * @param    object    model
		 * @param    object    collection
		 *
		 * @return   void
		 */
		deselect: function( model, collection ) {

			// Check if a selection exists and if it's the collection provided.
			// If they're not the same collection, bail; we're in another
			// selection's event loop.
			if ( ! this.selection || ( collection && collection !== this.selection ) ) {
				return;
			}

			this.$el.removeClass( 'selected' ).attr( 'aria-checked', false ).find( '.check' ).attr( 'tabindex', '-1' );
		},

		/**
		 * Show/hide the already-existing-notice icon.
		 *
		 * @since    3.0
		 *
		 * @param    object     model
		 * @param    boolean    value
		 * @param    object     options
		 *
		 * @return   void
		 */
		updateExisting: function( model, value, options ) {

			var value = true === value ? true : false;

			this.$( '.attachment-preview' ).toggleClass( 'existing', value );
		},

		/**
		 * Outline the selected image used in the details view.
		 *
		 * @since    3.0
		 *
		 * @param    object    model
		 * @param    object    collection
		 *
		 * @return   void
		 */
		details: function( model, collection ) {

			var selection = this.options.selection,
				details;

			if ( this.selection !== collection ) {
				return;
			}

			details = this.selection.single();
			this.$el.toggleClass( 'details', details === this.model );
		},

		/**
		 * Add the model if it isn't in the selection, if it is in the
		 * selection, remove it.
		 *
		 * @since    3.0
		 *
		 * @param    object    JS event
		 *
		 * @return   void
		 */
		checkClickHandler: function ( event ) {

			var selection = this.options.selection;
			if ( ! this.selection ) {
				return;
			}

			event.stopPropagation();
			if ( this.selection.where( { id: this.model.get( 'id' ) } ).length ) {
				this.selection.remove( this.model );
				// Move focus back to the attachment tile (from the check).
				this.$el.focus();
			} else {
				this.selection.add( this.model );
			}
		}

	}),

	Images: wp.media.View.extend({

		id: _.uniqueId( '__attachments-view-' ),

		tagName: 'ul',

		className: 'attachments ui-sortable ui-sortable-disabled',

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			var options = options || {};
			this.controller = options.controller;
			this.collection = new Backbone.Collection;

			this._viewsByCid = {};

			this.bindHandlers();
			this.mirrorCollection();

			this.on( 'ready', function() {
				var mode = this.controller.frame.content.mode(),
				  models = this.controller.collection.where({ type: mode });
				this.collection.reset();
				this.collection.add( models );
			}, this );

			wpmoly.on( 'modal:images:filter:size',     this.filterBySize,     this );
			wpmoly.on( 'modal:images:filter:language', this.filterByLanguage, this );
		},

		/**
		 * Bind view and subviews to the collection.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		bindHandlers: function() {

			this.listenTo( this.collection, 'add', this.createImage );

			this.listenTo( this.collection, 'remove', function( model, collection, options ) {
				var view = this._viewsByCid[ model.cid ];
				delete this._viewsByCid[ model.cid ];
				if ( view ) {
					view.remove();
				}
			} );

			this.listenTo( this.collection, 'reset', function( collection, options ) {
				_.each( this._viewsByCid, function( view ) {
					view.remove();
				}, this );
			} );

			return this;
		},

		/**
		 * Mirror the controller's collection to manipulate the internal
		 * collection.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		mirrorCollection: function() {

			this.listenTo( this.controller.collection, 'add', function( model, collection, options ) {
				if ( this.controller.frame.content.mode() === model.get( 'type' ) ) {
					this.collection.add( model, options );
				}
			}, this );

			this.listenTo( this.controller.collection, 'remove', function( model, collection, options ) {
				this.collection.remove( model, options );
			}, this );

			this.listenTo( this.controller.collection, 'reset', function( collection, options ) {
				this.collection.reset( options );
			}, this );

			return this;
		},

		/**
		 * Empty and repopulate the collection.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		resetCollection: function() {

			var models = this.controller.collection.where({
				type: this.controller.frame.content.mode()
			});

			this.collection.reset();
			this.collection.add( models );

			return this;
		},

		/**
		 * Create a new Image view and add it to the current view's
		 * subviews.
		 *
		 * @since    3.0
		 *
		 * @param    object    model
		 *
		 * @return   void
		 */
		createImage: function( model, collection, options ) {

			model.set( 'tabindex', this.views.all().length );
			var view = new wpmoly.view.Modal.Image({
				model      : model,
				controller : this.controller
			});

			this._viewsByCid[ model.cid ] = view;

			return this.views.add( view );
		},

		/**
		 * Filter the collection by image size.
		 *
		 * Empty the collection of all images and add the images matching
		 * the selected size.
		 *
		 * @since    3.0
		 *
		 * @param    int    min Minimal images width
		 * @param    int    max Maximal images width
		 *
		 * @return   Returns itself to allow chaining
		 */
		filterBySize: function( min, max ) {

			this.collection.reset();

			var min = parseInt( min ),
			    max = parseInt( max ),
			  where = {};

			var images = this.controller.collection.where({
				type: this.controller.frame.content.mode()
			});

			if ( ! min && ! max ) {
				this.collection.add( images );
			} else if ( min && max ) {
				this.collection.add(
					images.filter( function( image ) {
						var width = image.get( 'width' );
						return min <= width && max > width;
					} )
				);
			} else if ( min && ! max ) {
				this.collection.add(
					images.filter( function( image ) {
						return min <= image.get( 'width' );
					} )
				);
			}
		},

		/**
		 * Filter the collection by language.
		 *
		 * Empty the collection of all images and add the images matching
		 * the selected language.
		 *
		 * @since    3.0
		 *
		 * @param    string    language Language ISO639-1 code or 'all'
		 *
		 * @return   Returns itself to allow chaining
		 */
		filterByLanguage: function( language ) {

			this.collection.reset();

			var where = {};
			    where.type = this.controller.frame.content.mode();;
			if ( 'all' !== language ) {
				where.iso_639_1 = language;
			}

			this.collection.add( this.controller.collection.where( where ) );
		}
	})

} );
