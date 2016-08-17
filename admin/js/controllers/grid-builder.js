
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

			this.builder.set({ type: type });
			this.builder.set({ mode: 'grid' });
			this.builder.set({ theme: 'default' });

			//this.builder.save();
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

			this.builder.set({ mode: mode });
			this.builder.set({ theme: 'default' });

			//this.builder.save();
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

			//this.builder.save();
		}
	})

});