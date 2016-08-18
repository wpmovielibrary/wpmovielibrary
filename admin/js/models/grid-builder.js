
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	GridBuilder: Backbone.Model.extend({

		defaults: function() {
			var defaults = {};
			_.each( _wpmolyGridBuilderData.settings, function( value, key ) {
				defaults[ key ] = '';
			}, this );
			return defaults;
		},

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    attributes
		 * @param    object    options
		 * 
		 * @return   void
		 */
		initialize: function( attributes, options ) {

			var options = options || {};
			this.controller = options.controller;

			var data = _wpmolyGridBuilderData || {};

			this.set( data.settings );
			this.types  = data.types  || '';
			this.modes  = data.modes  || '';
			this.themes = data.themes || '';

			this.on( 'change', this.update, this );
		},

		/**
		 * Save settings.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model
		 * @param    object    options
		 * 
		 * @return   xhr
		 */
		update: function( model, options ) {

			if ( model.isEmpty() ) {
				return;
			}

			return wp.ajax.post( 'wpmoly_autosave_grid_setting', {
				data        : model.toJSON(),
				post_id     : this.controller.get( 'post_id' ),
				_ajax_nonce : this.controller.get( 'nonce' )
			} );
		}
	})

});