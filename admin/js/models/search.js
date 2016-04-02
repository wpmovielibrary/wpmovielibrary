
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	Search: Backbone.Model.extend({

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    attributes
		 * @param    object    options
		 */
		initialize: function( attributes, options ) {

			var options = options || {};
			this.controller = options.controller;

			this.post_id = options.post_id || '';
		},

		
	})

} );
