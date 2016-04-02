
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view, {

	Frame: wp.Backbone.View.extend({

		/**
		 * Set a mode for the frame view.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    mode
		 * @param    object    options
		 * 
		 * @return   Returns the current mode.
		 */
		mode: function( mode, options ) {

			if ( ! mode ) {
				return this._mode;
			}

			// Bail if we're trying to change to the current mode.
			if ( mode === this._mode ) {
				return this;
			}

			var options = options || {}, view;

			if ( this._mode ) {
				this.trigger( 'deactivate:' + this._mode, options );
			} else {
				this.trigger( 'deactivate', options );
			}

			this._mode = mode;

			var view = this.modes[ mode ];
			    view = new view( _.extend( options, {
				controller: this.controller
			} ) );
			this.views.set( view );

			this.trigger( 'activate:' + this._mode, options );

			return this;
		}
	})

});