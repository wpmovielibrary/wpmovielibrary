
wpmoly = window.wpmoly || {};

_.extend( wpmoly.controller, {

	GridBuilder: Backbone.Model.extend({

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function( attributes, options ) {

			this.builder = new wpmoly.model.GridBuilder;
		},

		setType: function( value ) {

			this.builder.set({ type: value });

			//this.builder.save();
		},

		setMode: function( value ) {

			this.builder.set({ mode: value });

			//this.builder.save();
		},

		setTheme: function( value ) {

			this.builder.set({ theme: value });

			//this.builder.save();
		},
	})

});