
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

			this.builder = new wpmoly.model.GridBuilder( {}, { controller: this } );
		},

		/**
		 * Set grid type.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    Grid type.
		 * 
		 * @return   void
		 */
		setType: function( type ) {

			this.builder.set( this.builder.defaults(), { silent: true } );
			this.builder.set({ theme: 'default', mode: 'grid', type: type });
		},

		/**
		 * Set grid mode.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    Grid mode.
		 * 
		 * @return   void
		 */
		setMode: function( mode ) {

			this.builder.set({ theme: 'default', mode: mode });
		},

		/**
		 * Set grid theme.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    Grid theme.
		 * 
		 * @return   void
		 */
		setTheme: function( theme ) {

			this.builder.set({ theme: theme });

			this.setTitle();
		}
	})

});