
wpmoly = window.wpmoly || {};

var Modal = wpmoly.view.Modal || {};

_.extend( Modal, {

	ImagesToolbar: wp.media.View.extend({

		className: 'media-toolbar-secondary',

		template: wp.template( 'wpmoly-modal-toolbar' ),

		events: {
			'change [data-action="filter-size"]'     : 'filterBySize',
			'change [data-action="filter-language"]' : 'filterByLanguage',
		},

		/**
		 * Initialize the View.
		 * 
		 * Handle the ImagesBrowser's Toolbar subview. Display the select
		 * fields and spinner.
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

			wpmoly.on( 'modal:images:loading', function() {
				this.$( '.spinner' ).addClass( 'is-active' );
			}, this );
			wpmoly.on( 'modal:images:loaded movie:images:failed', function() {
				this.$( '.spinner' ).removeClass( 'is-active' );
			}, this );
		},

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		render: function() {

			this.$el.html( this.template({
				mode: this.controller.frame.content.mode()
			}) );

			return this;
		},

		filterBySize: function( event ) {

			var $elem = this.$( event.currentTarget ).find( 'option:selected' ),
			      min = $elem.attr( 'data-min-value' ) || 0,
			      max = $elem.attr( 'data-max-value' ) || 0;

			wpmoly.trigger( 'modal:images:filter:size', min, max );
		},

		filterByLanguage: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.val();

			wpmoly.trigger( 'modal:images:filter:language', value );
		}

	}),

	ImagesSidebar: wp.media.View.extend({

		template: wp.template( 'wpmoly-modal-sidebar' ),

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		render: function() {

			var data = {};
			if ( this.model ) {
				var data = this.model.toJSON();
			}

			this.el = this.template( data );

			return this;
		}

	})

} );

_.extend( Modal, {

	ImagesBrowser: wp.media.View.extend({

		className: 'attachments-browser movie-images-browser',

		template: wp.template( 'wpmoly-modal-browser' ),

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			var options = options || {};
			this.frame      = options.frame;
			this.controller = options.controller;

			this.ideal_width = wpmoly.$( window ).width() < 640 ? 150 : 200

			wpmoly.on( 'modal:image:preview', this.previewImage, this );
			wpmoly.on( 'modal:image:keydown:arrow', this.browse, this );

			_.bindAll( this, 'setColumns' );

			this.on( 'ready', this.bindEvents );
			_.defer( this.setColumns, this );

		},

		/**
		 * Bind Events.
		 * 
		 * Mostly used to handle window resize and recalculte columns.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		bindEvents: function() {

			wpmoly.$( window ).off( 'resize.media-modal-columns' ).on( 'resize.media-modal-columns', _.debounce( this.setColumns, 50 ) );
		},

		/**
		 * Calculate the optimal number of columns for the grid view.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		setColumns: function() {

			var prev = this.columns,
			   width = this.$el.width();

			if ( width ) {
				this.columns = Math.min( Math.round( width / this.ideal_width ), 12 ) || 1;

				if ( ! prev || prev !== this.columns ) {
					this.$el.closest( '.media-frame-content' ).attr( 'data-columns', this.columns );
				}
			}
		},

		/**
		 * Display the image preview in the Sidebar.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    options
		 * 
		 * @return   void
		 */
		previewImage: function( model ) {

			if ( this.sidebar ) {
				this.sidebar.remove();
			}

			this.sidebar = new wpmoly.view.Modal.ImagesSidebar({
				model      : model,
				controller : this.controller
			});

			this.views.set( '.media-sidebar', this.sidebar );
		},

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		render: function() {

			this.$el.html( this.template() );

			this.toolbar = new wpmoly.view.Modal.ImagesToolbar({
				controller: this.controller
			});
			this.sidebar = new wpmoly.view.Modal.ImagesSidebar({
				controller: this.controller
			});
			this.browser = new wpmoly.view.Modal.Images({
				controller: this.controller
			});

			this.views.set( '.media-toolbar', this.toolbar );
			this.views.set( '.media-sidebar', this.sidebar );
			this.views.set( '.media-browser', this.browser );

			return this;

		},

		/**
		 * Navigate through images when directional arrows are hit.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS event
		 * 
		 * @return   void
		 */
		browse: function( event ) {

			var attachments = this.browser.$el.children( 'li.attachment' ),
				 perRow = this.columns,
				  index = attachments.filter( ':focus' ).index(),
				    row = ( index + 1 ) <= perRow ? 1 : Math.ceil( ( index + 1 ) / perRow );

			if ( index === -1 ) {
				return;
			}

			// Left arrow
			if ( 37 === event.keyCode ) {
				if ( 0 === index ) {
					return;
				}
				attachments.eq( index - 1 ).focus();
			}

			// Up arrow
			if ( 38 === event.keyCode ) {
				if ( 1 === row ) {
					return;
				}
				attachments.eq( index - perRow ).focus();
			}

			// Right arrow
			if ( 39 === event.keyCode ) {
				if ( attachments.length === index ) {
					return;
				}
				attachments.eq( index + 1 ).focus();
			}

			// Down arrow
			if ( 40 === event.keyCode ) {
				if ( Math.ceil( attachments.length / perRow ) === row ) {
					return;
				}
				attachments.eq( index + perRow ).focus();
			}
		}

	})
} );
