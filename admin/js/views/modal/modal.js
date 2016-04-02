
wpmoly = window.wpmoly || {};

var Modal = wpmoly.view.Modal = {};

_.extend( Modal, {

	Toolbar: wp.media.view.Toolbar.extend({

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function() {

			_.defaults( this.options, {
				event: 'import',
				close: false,
				items: {
					// See wp.media.view.Button
					import: {
						id       : 'movie-images-button',
						style    : 'primary',
						text     : wpmolyL10n.importImages,
						priority : 80,
						click    : function() {
							wpmoly.trigger( 'modal:images:import' );
						}
					}
				}
			});

			wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );

			this.set( 'pagination', new wp.media.view.Button({
				tagName  : 'button',
				classes  : 'mexp-pagination button button-secondary',
				id       : 'movie-images-reload',
				text     : wpmolyL10n.reload,
				priority : -20,
				click    : function() {
					wpmoly.trigger( 'modal:images:reload' );
				}
			}) );

			this.selection = this.options.selection;
			this.listenTo( this.selection, 'add remove reset', this.toggleButton );
		},

		/**
		 * Refresh the Toolbar View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		refresh: function() {

			var selection = this.controller.state().props.get( '_all' ).get( 'selection' );

			// @TODO i think this is redundant
			this.get( 'import' ).model.set( 'disabled', ! selection.length );

			wp.media.view.Toolbar.prototype.refresh.apply( this, arguments );
		},

		toggleButton: function() {

			var button = this.get( 'import' );
			    button.model.set({ disabled: ! this.selection.length });
		}

	}),

	ImagesSelection: wp.media.View.extend({

		className: 'media-toolbar-third',

		template: wp.template( 'wpmoly-modal-selection' ),

		events: {
			'click [data-action="selection-switch"]' : 'openSwitch',
			'click [data-action="set-as-backdrop"]'  : 'setAsBackdrop',
			'click [data-action="set-as-poster"]'    : 'setAsPoster'
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
			this.toolbar    = options.toolbar;
			this.selection  = options.selection;

			this.listenTo( this.selection, 'selection:single',   this.open );
			this.listenTo( this.selection, 'selection:unsingle', this.close );
		},

		/**
		 * Render the View.
		 * 
		 * Only show the View when needed to avoid overcrowding the
		 * Toolbar; the view's container is hidden until a media is
		 * selected.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		render: function() {

			this.$el.html( this.template() );
			this.$el.hide();

			return this;
		},

		/**
		 * Show the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		open: function() {

			if ( this.selection.length ) {
				this.$el.show();
			}
		},

		/**
		 * Hide the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		close: function() {

			if ( ! this.selection.length ) {
				this.$el.hide();
			}
		},

		/**
		 * Open the image switcher.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' event.
		 * 
		 * @return   void
		 */
		openSwitch: function( event ) {

			this.toolbar.secondary.$el.toggle();

			this.$( '.selection-switcher' ).toggleClass( 'open' );
			this.$( '.switch-icon' ).toggleClass( 'icon-arrow-right' ).toggleClass( 'icon-arrow-left' );
		},

		/**
		 * Set selected images as backdrops.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' event.
		 * 
		 * @return   void
		 */
		setAsBackdrop: function( event ) {

			event.preventDefault();

			return this.setAsImage( 'backdrop' );
		},

		/**
		 * Set selected images as posters.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' event.
		 * 
		 * @return   void
		 */
		setAsPoster: function( event ) {

			event.preventDefault();

			return this.setAsImage( 'poster' );
		},

		/**
		 * Set selected images as backdrops or posters.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' event.
		 * 
		 * @return   void
		 */
		setAsImage: function( type ) {

			if ( ! _.contains( [ 'backdrop', 'poster' ], type ) ) {
				return false;
			}

			var title = s.aquote( wpmoly.editor.controller.meta.get( 'title' ) ),
			   images = wpmoly.l10n._n( wpmolyL10n.selectedImages, this.selection.length ),
			imagetype = wpmoly.l10n._n( wpmolyL10n[ type ], this.selection.length ),
			  confirm = wpmoly.confirm( s.sprintf( wpmolyL10n.setImagesAs, images, s.decapitalize( imagetype ), '<em>' + title + '</em>' ) );

			confirm.on( 'confirm', function() {
				wpmoly.trigger( 'modal:images:set-as', type );
			}, this );

			confirm.open();
		}
	})
} );
