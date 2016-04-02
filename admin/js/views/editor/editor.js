
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view, {

	Editor: wp.Backbone.View.extend({

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};

			this.prefill();
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

			this.meta      = new wpmoly.view.Meta({ controller: this.controller });
			this.details   = new wpmoly.view.Details({ controller: this.controller });
			this.backdrops = new wpmoly.view.Backdrops({ controller: this.controller, collection: this.controller.backdrops });
			this.posters   = new wpmoly.view.Posters({ controller: this.controller, collection: this.controller.posters });

			this.views.set( '#wpmoly-meta-meta-panel',      this.meta );
			this.views.set( '#wpmoly-meta-details-panel',   this.details );
			this.views.set( '#wpmoly-meta-backdrops-panel', this.backdrops );
			this.views.set( '#wpmoly-meta-posters-panel',   this.posters );
		},

		/**
		 * Silently prefill the Model with values from the form.
		 * 
		 * @since    3.0
		 */
		prefill: function() {

			var $fields = this.$( '[data-meta-type]' );
			if ( ! $fields.length ) {
				return;
			}

			_.map( $fields, function( field ) {
				var $field = this.$( field )
				       key = $field.attr( 'data-meta-key' ),
				      type = $field.attr( 'data-meta-type' ),
				     value = $field.val();

				if ( 'meta' === type ) {
					this.controller.meta.set( key, value, { silent: true } );
				} else if ( 'detail' === type ) {
					this.controller.details.set( key, value, { silent: true } );
				}
			}, this );
		}
	})
} );
