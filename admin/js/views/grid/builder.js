
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid = {};

_.extend( Grid, {

	Builder: wp.Backbone.View.extend({

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
			this.model = this.controller.builder;

			this.set_regions();
			this.bindEvents();
		},

		/**
		 * Set Regions (subviews).
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		set_regions: function() {

			this.type = new wpmoly.view.Grid.Type({ controller: this.controller });

			this.views.set( '#wpmoly-grid-builder-type-metabox', this.type );

			this.togglePostbox( this.model, this.model.get( 'type' ) );
		},

		/**
		 * Bind events.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		bindEvents: function() {

			this.listenTo( this.model, 'change:type',  this.togglePostbox );
			this.listenTo( this.model, 'change:mode',  this.togglePostbox );
			this.listenTo( this.model, 'change:theme', this.togglePostbox );
		},

		/**
		 * Show/Hide ButterBean Metaboxes depending on grid type.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		togglePostbox: function( model, value, options ) {

			var $postbox = this.$( '#butterbean-ui-' + value + '-grid-settings' ),
			  $postboxes = this.$( '.butterbean-ui.postbox' );
			if ( ! $postbox.length ) {
				return;
			}

			$postboxes.removeClass( 'active' );
			$postbox.addClass( 'active' );
		},
	})

} );
