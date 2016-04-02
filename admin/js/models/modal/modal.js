
wpmoly = window.wpmoly || {};

var Modal = wpmoly.model.Modal = {};

Modal.ImagesCache = new Backbone.Collection;

_.extend( Modal, {

	Image: Backbone.Model.extend({}),
} );

_.extend( Modal, {

	Images: Backbone.Collection.extend({

		model: wpmoly.model.Modal.Image,

		/**
		 * Initialize the Collection.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    collection
		 * @param    object    options
		 * 
		 * @return   void
		 */
		initialize: function( collection, options ) {

			var options = options || {};
			this.controller = options.controller;

			this.cache = wpmoly.model.Modal.ImagesCache;
		},

		/**
		 * Backbone.sync override.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    method
		 * @param    object    collection
		 * @param    object    options
		 * 
		 * @return   void
		 */
		sync: function( method, collection, options ) {

			if ( 'read' == method ) {

				var reload = options.reload || false;
				if ( reload ) {
					this.cache.reset();
				}

				if ( ! reload && ! this.cache.isEmpty() ) {
					this.add( this.cache.models );
					return;
				}

				wpmoly.trigger( 'modal:images:loading' );

				return wp.ajax.send( 'wpmoly_api_fetch_images', {
					context : this,
					data    : {
						_nonce  : '',
						post_id : this.controller.post_id,
						tmdb_id : this.controller.tmdb_id
					}
				} ).done( function( response ) {

					if ( reload || this.cache.isEmpty() ) {
						this.cache.add( response );
					}

					wpmoly.trigger( 'modal:images:loaded' );

					this.add( response );
				} ).fail( function( response ) {

					wpmoly.trigger( 'modal:images:failed', response );
				} );

			} else {
				return Backbone.sync.apply( this, arguments );
			}
		}

	})

} );
