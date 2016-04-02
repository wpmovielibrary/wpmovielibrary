
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	Image: Backbone.Model.extend({

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Model
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		initialize: function( model, options ) {

			var options = options || {};
			if ( true === options.parse ) {
				this.set( this.parse( model ), { silent: true } );
			}

			this.on( 'change', this.autosave, this );
		},

		/**
		 * Parse Ajax response to Model.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Model
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		parse: function( response, options ) {

			if ( response.data ) {
				_.each( response.data, function( value, key ) {
					response[ key ] = value;
				}, this );
				delete response.data;
			}

			return response;
		},

		/**
		 * Save the Meta.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model Current model
		 * 
		 * @return   deferred
		 */
		autosave: function( model ) {

			return this.sync( 'update', model, {} );
		},

		/**
		 * Override Backbone.sync()
		 * 
		 * @since    3.0
		 * 
		 * @param    string    Method
		 * @param    object    Model
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		sync: function( method, model, options ) {

			if ( 'update' == method ) {

				if ( ! _.size( model.changed ) ) {
					return;
				}

				_.extend( options || {}, {
					context: this,
					data: {
						action: 'save-attachment',
						id:      this.get( 'id' ),
						nonce:   this.get( 'nonces' ).update,
						post_id: this.post_id || 0,
						changes: this.changed
					},
					beforeSend: function() {
						this.trigger( 'autosave:start', model.changed, this );
					},
					success: function( response ) {
						this.trigger( 'autosave:done', model.changed, this );
					},
					error: function( response ) {
						this.trigger( 'autosave:fail', model.changed, this );
						wpmoly.error( response );
					},
					complete: function() {
						this.trigger( 'autosave:end', model.changed, this );
					},
				} );

				return wp.ajax.send( options );

			} else {
				return Backbone.sync.apply( this, arguments );
			}
		}
	})
} );

_.extend( wpmoly.model, {

	Backdrop: wpmoly.model.Image.extend({}),

	Poster: wpmoly.model.Image.extend({})
} );
