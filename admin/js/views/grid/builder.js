
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid = {};

_.extend( Grid, {

	Builder: wp.Backbone.View.extend({

		//template: wp.template( 'wpmoly-grid-builder-type-metabox' ),

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};

			this.set_regions();
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
		}

	})

} );
