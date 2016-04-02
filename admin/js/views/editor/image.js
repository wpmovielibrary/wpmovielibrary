
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view, {

	ImageMore: wp.Backbone.View.extend({

		template: wp.template( 'wpmoly-editor-image-more' ),

		events: {
			'click a': 'preventDefault',
			'click [data-action="import"]': 'import',
			'click [data-action="upload"]': 'upload',
		},

		/**
		 * Initialize the View.
		 * 
		 * @param    object    Options
		 * 
		 * @since    3.0
		 */
		initialize: function( options ) {

			var options = options || {};
			this.controller = options.controller || {};
		},

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			var data = {
				type: this.type
			};

			this.$el.html( this.template( data ) );

			return this;
		},

		/**
		 * Trigger Backdrop import.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		import: function( event ) {

			wpmoly.trigger( 'editor:' + this.type + ':import:open' );
		},

		/**
		 * Trigger Backdrop upload.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		upload: function( event ) {

			wpmoly.trigger( 'editor:' + this.type + ':upload:open' );
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

	Image: wp.Backbone.View.extend({

		template: wp.template( 'wpmoly-editor-image' ),

		events: {
			'click a': 'preventDefault',
			'click [data-action="toggle-menu"]': 'toggle',
			'click [data-action="edit"]':        'edit',
			'click [data-action="remove"]':      'unset',
			'click [data-action="featured"]':    'set_featured'
		},

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Options
		 * 
		 * @return   null
		 */
		initialize: function( options ) {

			this.model = options.model || {};
			this.controller = options.controller || {};
			this.collection = options.collection || {};
			this.parent     = options.parent     || {};

			this.menu = false;
			this.on( 'toggle:menu', this.render, this );
		},

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			var data = _.extend( this.model.toJSON() || {}, {
				menu   : this.menu,
				type   : this.type
			} );

			this.$el.html( this.template( data ) );

			return this;
		},

		/**
		 * 
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		toggle: function( event ) {

			this.menu = ! this.menu;
			this.trigger( 'toggle:menu' );

			if ( true === this.menu ) {
				var self = this;
				wpmoly.$( 'body' ).one( 'click', function() {
					self.menu = false;
					self.trigger( 'toggle:menu' );
				} );
			}
		},

		/**
		 * Open WP Image Editor
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		edit: function( event ) {

			wpmoly.trigger( 'editor:image:edit:open', this.model );

			return this;
		},

		/**
		 * Remove image from backdrops/posters.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		unset: function( event ) {

			wpmoly.trigger( 'editor:image:remove', this.model, this.type, this.collection );

			return this;
		},

		/**
		 * Set poster as featured image.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		set_featured: function( event ) {

			wpmoly.trigger( 'editor:image:featured', this.model );
			this.trigger( 'toggle:menu' );

			return this;
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

	})

} );

_.extend( wpmoly.view, {

	BackdropMore: wpmoly.view.ImageMore.extend({

		tagName: 'li',

		className: 'wpmoly-image wpmoly-backdrop',

		type: 'backdrop'
	}),

	PosterMore: wpmoly.view.ImageMore.extend({

		tagName: 'li',

		className: 'wpmoly-image wpmoly-poster',

		type: 'poster'
	}),

	Backdrop: wpmoly.view.Image.extend({

		tagName: 'li',

		className: 'wpmoly-imported-image wpmoly-imported-backdrop',

		type: 'backdrop'

	}),

	Poster: wpmoly.view.Image.extend({

		tagName: 'li',

		className: 'wpmoly-imported-image wpmoly-imported-poster',

		type: 'poster'

	})
} );