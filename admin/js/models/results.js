
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	Result: Backbone.Model.extend({}),

	Results: Backbone.Collection.extend({

		/**
		 * Initialize the Collection.
		 * 
		 * @since    3.0
		 * 
		 * @param    array     models
		 * @param    object    options
		 * 
		 * @return   void
		 */
		initialize: function( models, options ) {

			this.page        = options.page        || 1;
			this.total_pages = options.total_pages || 1;
		},

		/**
		 * Override the reset() method to trigger a 'remove' on each
		 * model of the Collection before actually resetting.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		reset: function() {

			this.each( function( model ) {
				this.trigger( 'remove', model, this, {} );
			}, this );

			return Backbone.Collection.prototype.reset.apply( this, arguments );
		}
	})

} );
