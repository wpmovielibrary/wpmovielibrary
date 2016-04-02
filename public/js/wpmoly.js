
wpmolyL10n = window.wpmolyL10n || {};

_.sprintf  = s.sprintf  = sprintf;
_.vsprintf = s.vsprintf = vsprintf;


// Create the plugin namespace.
wpmoly = window.wpmoly = {

	NAME: 'WPMovieLibrary',

	SLUG: 'wpmoly',

	VERSION: '3.0',

	debug: 'verbose',

	L10n: {},

	runners: [],

	$: jQuery,

	model: {},

	view: {},

	controller: {},

	post_id: jQuery( '#post_ID' ).val() || ''
};

(function( $, _, Backbone ) {

	/**
	 * Use Backbone.Events to extend wpmoly.
	 * 
	 * This way we're able to use internal events everywhere they're needed
	 * and we allow third-party code to bind and interact with the plugin.
	 */
	_.extend( wpmoly, Backbone.Events );

	_.extend( wpmoly, {

		/**
		 * Confirm Modal.
		 * 
		 * Returns a Backbone.View instance to bind 'confirm' and
		 * 'cancel' events.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    message
		 * @param    string    callback
		 * @param    object    options
		 * 
		 * @return   void
		 */
		confirm: function( message, callback, options ) {

			var options = options || {}, confirm;
			    options = {
				callback: options.callback || false,
				model: new Backbone.Model({
					text: message || '',
					icon: options.icon || ''
				})
			};

			return new wpmoly.view.Confirm( options );
		},

		/**
		 * Wrapper for console.error()
		 * 
		 * @since    3.0
		 * 
		 * @param    string    message
		 * @param    string    code
		 * @param    object    options
		 * 
		 * @return   void
		 */
		error: function( message, code, options ) {

			var options = options || {};
			    options.type = 'error';

			return wpmoly.log( message, code, options );
		},

		/**
		 * Wrapper for console.warn()
		 * 
		 * @since    3.0
		 * 
		 * @param    string    message
		 * @param    string    code
		 * @param    object    options
		 * 
		 * @return   void
		 */
		warn: function( message, code, options ) {

			var options = options || {};
			    options.type = 'warn';

			return wpmoly.log( message, code, options );
		},

		/**
		 * Wrapper for console.info()
		 * 
		 * @since    3.0
		 * 
		 * @param    string    message
		 * @param    string    code
		 * @param    object    options
		 * 
		 * @return   void
		 */
		info: function( message, code, options ) {

			var options = options || {};
			    options.type = 'info';

			return wpmoly.log( message, code, options );
		},
		
		/**
		 * Custom replacement for console.log()
		 * 
		 * Make sure console.error(), console.warn(), console.info() or
		 * console.log() functions are available and use them to output
		 * custom messages in the browser's JS console.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    message
		 * @param    string    code
		 * @param    object    options
		 * 
		 * @return   void
		 */
		log: function( message, code, options ) {

			var options = options || {}, callback, prefix;

			if ( _.isObject( message ) ) {
				return message.map( function( data ) {
					return wpmoly.log( data.message, data.code, options );
				} );
			}

			prefix = '[wpmovielibrary] ';
			if ( code ) {
				prefix += '(' + code + ') ';
			}

			if ( 'error' === options.type && _.isFunction( console.error ) ) {
				return console.error( prefix + message );
			} else if ( 'warn' === options.type && _.isFunction( console.warn ) ) {
				return console.warn( prefix + message );
			} else if ( 'info' === options.type && _.isFunction( console.info ) ) {
				return console.info( prefix + message );
			} else if ( _.isFunction( console.log ) ) {
				return console.log( prefix + message );
			}

			return;
		}
	} );

	/**
	 * We can go our own way! We can call it another lonely day…
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	wpmoly.run = function() {

		wpmoly.info( wpmolyL10n.run );

		if ( 'verbose' === wpmoly.debug ) {
			wpmoly.on( 'all', function( event ) { wpmoly.info( event ); }, this );
		}

		_.each( wpmoly.runners, function( runner ) {
			runner.run();
		} );

		return wpmoly;
	};

})( jQuery, _, Backbone );

jQuery( document ).ready( function() {
	wpmoly.run();
} );