/**
 * WPSeriesLibrary instance runner.
 *
 * @since 3.0.0
 *
 * @package WPSeriesLibrary
 */

(function() {

	'use strict';

	wpmoly = window.wpmoly = {

		$ : jQuery,

		runners : {},

		Backbone : {

			View : wp.Backbone.View.extend({

				/**
				 * Selectize select elements.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				selectize : function() {

					var options = {
						closeAfterSelect : true,
					};

					_.each( this.$( '[data-selectize="1"]' ), function( select ) {
						var $select = this.$( select ),
						    country = $select.attr( 'data-selectize-country' ) || false,
						    plugins = $select.attr( 'data-selectize-plugins' ) || '',
						     create = $select.attr( 'data-selectize-create' );

						if ( country ) {
							options.render = {
								item : function( item, escape ) {
									wpmoly.debug( item );
									return '<div><span class="flag flag-"></span></div>';
								},
								option : function( item, escape ) {
									wpmoly.debug( item );
									return '<div><span class="flag flag-"></span></div>';
								},
							};
						}

						$select.selectize( _.extend( options, {
							plugins : _.filter( plugins.split( ',' ) ),
							create  : true === !! create || false,
						}) );
					}, this );

					return this;
				},

				/**
				 * Render the View.
				 *
				 * Add 'render' and 'rendered' events triggered before and after rendering
				 * the View.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				render : function() {

					var options;

					if ( this.prepare ) {
						options = this.prepare();
					}

					this.trigger( 'render', options );

					this.views.detach();

					if ( this.template ) {
						options = options || {};
						this.trigger( 'prepare', options );
						this.$el.html( this.template( options ) );
					}

					this.views.render();

					this.trigger( 'rendered', options );

					return this;
				},

			}),

		},

		/**
		 * Observe a Backbone Model to log any event for debug purpose.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} model Model to observe.
		 */
		observe : function( model, options ) {

			if ( ! _.isFunction( model.on ) ) {
				return false;
			}

			model.on( 'all', function() {
				var event = _.first( arguments ),
				   params = _.rest( arguments );

				if ( options.name ) {
					event = options.name + ':' + event;
				}

				console.debug( event, params );
			} );
		},

		/**
		 * Translate singular or plural form based on the supplied number.
		 *
		 * @since 3.0.0
		 *
		 * @param {array} text   Array containing texts to be used if the number is singular or plural.
		 * @param {int}   number Number to compare against to use either the singular or plural form.
		 *
		 * @return {string}
		 */
		_n : function( text, number ) {

			var number = number || '',
			      text = text || '';

			return s.sprintf( ( 1 === number || 1 === parseInt( number ) ) ? text[0] : text[1], number );
		},

		/**
		* General plugin notifier.
		*
		* Use Toasts if available, browser console otherwise.
		*
		* If text is an xhr response, parse response and use data to build the
		* notification message.
		*
		* @since 3.0.0
		*
		* @param {string} text    Notification message.
		* @param {object} options Notification options.
		*
		* @return {mixed}
		*/
		notify : function( text, options ) {

			var response, message = '';

			// text is actually an XHR response.
			if ( _.isObject( text ) && ! _.isUndefined( text.responseText ) ) {

				// Parse JSON response.
				response = JSON.parse( text.responseText );

				// Retrieve response's message.
				if ( ! _.isUndefined( response.message ) ) {
					message = response.message;
				}

				// Show additional data is WordPress is in debug mode.
				if ( wpmolyApiSettings.verbose ) {
					if ( ! _.isUndefined( options.debug ) ) {
						message += '<code>&gt;&nbsp;' + options.debug + '</code>';
					}
					if ( ! _.isUndefined( response.code ) && ! _.isUndefined( response.data.status ) ) {
						message += '<code>&gt;&nbsp;Error ' + response.data.status + ': ' + response.code + '</code>';
					}
					if ( ! _.isUndefined( response.data.params ) ) {
						_.each( response.data.params, function( param ) {
							message += '<code>&gt;&nbsp;' + param + '</code>';
						} );
					}
				}

			} else if ( _.isString( text ) ) {
				message = text;
			} else {
				console.trace();
				console.warn( '[wpmovielibrary]: notification failed.', text, options );
				return false;
			}

			var type = options.type || '';
			if ( ! _.contains( [ 'success', 'info', 'warning', 'error' ], type ) ) {
				type = 'info';
			}

			if ( ! _.isUndefined( window.toasts ) ) {
				switch ( type ) {
					case 'debug':
						toasts.fry( message, options );
						break;
					case 'success':
						toasts.bake( message, options );
						break;
					case 'warning':
						toasts.fry( message, options );
						break;
					case 'error':
						toasts.burn( message, options );
						break;
					case 'info':
					default:
						toasts.cook( message, options );
						break;
				}
			} else {
				switch ( type ) {
					case 'debug':
						console.debug( message, options );
						break;
					case 'info':
						console.info( message, options );
						break;
					case 'warning':
						console.warn( message, options );
						break;
					case 'error':
						console.error( message, options );
						break;
					case 'success':
					default:
						console.log( message, options );
						break;
				}
			}
		},

		/**
		* Debug.
		*
		* @since 3.0.0
		*
		* @param {string} message Debug message.
		* @param {object} options Debug options.
		*
		* @return {mixed}
		*/
		debug : function( message, options ) {

			var options = _.extend( options || {}, { type : 'debug' } );

			return wpmoly.notify( message, options );
		},

		/**
		* Notify success.
		*
		* @since 3.0.0
		*
		* @param {string} message Notification message.
		* @param {object} options Notification options.
		*
		* @return {mixed}
		*/
		success : function( message, options ) {

			var options = _.extend( options || {}, { type : 'success' } );

			return wpmoly.notify( message, options );
		},

		/**
		* Notify info.
		*
		* @since 3.0.0
		*
		* @param {string} message Notification message.
		* @param {object} options Notification options.
		*
		* @return {mixed}
		*/
		info : function( message, options ) {

			var options = _.extend( options || {}, { type : 'info' } );

			return wpmoly.notify( message, options );
		},

		/**
		* Notify warnings.
		*
		* @since 3.0.0
		*
		* @param {string} message Notification message.
		* @param {object} options Notification options.
		*
		* @return {mixed}
		*/
		warning : function( message, options ) {

			var options = _.extend( options || {}, { type : 'warning' } );

			return wpmoly.notify( message, options );
		},

		/**
		* Notify errors.
		*
		* @since 3.0.0
		*
		* @param {string} message Notification message.
		* @param {object} options Notification options.
		*
		* @return {mixed}
		*/
		error : function( message, options ) {

			var options = _.extend( options || {}, { type : 'error' } );

			return wpmoly.notify( message, options );
		},
	};

	/**
	 * You can go our own way! You can call it another lonely day…
	 *
	 * @since 3.0.0
	 *
	 * @return Returns itself to allow chaining.
	 */
	wpmoly.run = function() {

		console.info( '[wpmovielibrary]: Run Forrest, run!' );

		_.each( wpmoly.runners, function( runner ) {
			runner.run();
		} );

		return _.omit( wpmoly, 'runners', 'run' );
	};

})();

jQuery( document ).ready( function() {
	wpmoly = wpmoly.run();
} );
