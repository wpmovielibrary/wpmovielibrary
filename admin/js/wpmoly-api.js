
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	// API namespace.
	wpmoly.api = {};

	/**
	 * The API object uses its own event system to make it usable
	 * independently from the plugin.
	 */
	_.extend( wpmoly.api, Backbone.Events );

	_.extend( wpmoly.api, {

		locked: false,

		/**
		 * Lock the API.
		 * 
		 * @since    3.0
		 */
		lock: function() {

			wpmoly.api.locked = true;
			wpmoly.api.trigger( 'locked' );
		},

		/**
		 * Unlock the API.
		 * 
		 * @since    3.0
		 */
		unlock: function() {

			wpmoly.api.locked = false;
			wpmoly.api.trigger( 'unlocked' );
		},

		/**
		 * Movie related API functions.
		 * 
		 * @since    3.0
		 */
		movie: {

			/**
			 * API search.
			 * 
			 * Search TheMovieDB for movies matching the query. To
			 * get a specific movie using its ID use wpmoly.api.fetch()
			 * 
			 * @since    3.0
			 * 
			 * @param    string    query
			 * @param    object    params
			 * 
			 * @return   xhr
			 */
			search: function( query, params ) {

				var data = _.extend( {
					query: query,
					nonce: ''
				}, params ), results;

				return wp.ajax.send( 'wpmoly_api_search_movie', {
					data    : data,
					beforeSend: function() {
						wpmoly.api.lock();
						wpmoly.api.trigger( 'movie:search:start', data );
					},
					success : function( response ) {
						wpmoly.api.trigger( 'movie:search:success', response );
					},
					error   : function( response ) {
						wpmoly.api.trigger( 'movie:search:error', response );
						wpmoly.error( response );
					},
					complete : function() {
						wpmoly.api.unlock();
						wpmoly.api.trigger( 'movie:search:end' );
					}
				} );
			},

			/**
			 * API fetch.
			 * 
			 * Fetch a specific movie from TheMovieDB by its ID.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    query
			 * @param    object    params
			 * 
			 * @return   xhr
			 */
			fetch: function( query, params ) {

				var data = _.extend( {
					query: query,
					nonce: ''
				}, params ), results;

				wp.ajax.send( 'wpmoly_api_fetch_movie', {
					data    : data,
					beforeSend: function() {
						wpmoly.api.lock();
						wpmoly.api.trigger( 'movie:fetch:start', data );
					},
					success : function( response ) {
						wpmoly.api.trigger( 'movie:fetch:success', response );
					},
					error   : function( response ) {
						wpmoly.api.trigger( 'movie:fetch:error', response );
						wpmoly.error( response );
					},
					complete : function() {
						wpmoly.api.unlock();
						wpmoly.api.trigger( 'movie:fetch:end' );
					}
				} );
			}

		},

		image: {

			getUrl: function( type, path, size, options ) {

				var size = size || 'original',
				 options = _.defaults( { secure: true }, options ),
				  config = wpmoly.api.configuration,
				   sizes = config.sizes[ type ] || {},
				    base = options.secure ? config.secure_base_url : config.base_url;

				return base + ( sizes[ size ] || size ) + path;
			},
		},

		backdrop: {

			getUrl: function( path, size, options ) {

				return wpmoly.api.image.getUrl( 'backdrop', path, size, options );
			},
		},

		poster: {

			getUrl: function( path, size, options ) {

				return wpmoly.api.image.getUrl( 'poster', path, size, options );
			},
		}

	} );

	/**
	 * TMDb basic configuration.
	 * 
	 * This should not change much and therefore can be hardcoded rather
	 * than cached and printed on each page load.
	 */
	wpmoly.api.configuration = {
		base_url        : "http://image.tmdb.org/t/p/",
		secure_base_url : "https://image.tmdb.org/t/p/",
		sizes           : {
			backdrop         : {
				medium   : "w300",
				large    : "w780",
				full     : "w1280",
				original : "original"
			},
			poster           : {
				xxsmall  : "w92",
				xsmall   : "w154",
				small    : "w185",
				medium   : "w342",
				large    : "w500",
				full     : "w780",
				original : "original"
			},
			profile          : {
				xxxsmall : "w45",
				small    : "w185",
				large    : "h632",
				original : "original"
			},
			still            : {
				xxsmall  : "w92",
				small    : "w185",
				medium   : "w300",
				original : "original"
			}
		}
	};

})( jQuery, _, Backbone );
