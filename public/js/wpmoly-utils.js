
wpmoly = window.wpmoly || {};

(function( $, _, s, Backbone ) {

	Date.prototype.toAPITimeString = function() {

		return _.map( [
			this.getHours(),
			this.getMinutes(),
			this.getSeconds()
		], function( d ) {
			return s.lpad( d, 2, '0' );
		} ).join( ':' );
	};

	Date.prototype.toAPIDateString = function() {

		return [
			this.getFullYear(),
			s.lpad( this.getMonth() + 1, 2, '0' ),
			s.lpad( this.getDate(), 2, '0' )
		].join( '-' );
	};

	/**
	 * Localized quote method extending underscore-string quote method.
	 * 
	 * Add french quotes instead of regular quotes.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    string
	 * @param    int       html
	 * 
	 * @return   string
	 */
	s.aquote = function( str, html ) {

		if ( 'fr_FR' != wpmolyL10n._locale ) {
			return s.quote( str );
		}

		return html ? [ '&laquo;', str, '&raquo;' ].join( '&nbsp;' ) : [ '«', str, '»' ].join( ' ' );
	};

	/**
	 * Internal function that returns an efficient (for current engines)
	 * version of the passed-in callback, to be repeatedly applied in other
	 * Underscore functions.
	 * 
	 * Borrowed from Underscore.js 1.8
	 * 
	 * @since    3.0
	 * 
	 * @param    function    func
	 * @param    object      context
	 * @param    int         argCount
	 * 
	 * @return   mixed
	 */
	wpmoly.optimizeCb = function( func, context, argCount ) {

		if ( context === void 0 ) {
			return func;
		}

		switch ( argCount == null ? 3 : argCount ) {
			case 1: return function( value ) {
				return func.call( context, value );
			};
			case 2: return function( value, other ) {
				return func.call( context, value, other );
			};
			case 3: return function( value, index, collection ) {
				return func.call( context, value, index, collection );
			};
			case 4: return function( accumulator, value, index, collection ) {
				return func.call( context, accumulator, value, index, collection );
			};
		}

		return function() {
			return func.apply( context, arguments );
		};
	};

	/**
	 * A mostly-internal function to generate callbacks that can be applied
	 * to each element in a collection, returning the desired result — either
	 * identity, an arbitrary callback, a property matcher, or a property
	 * accessor.
	 * 
	 * Borrowed from Underscore.js 1.8
	 * 
	 * @since    3.0
	 * 
	 * @param    function    func
	 * @param    object      context
	 * @param    int         argCount
	 * 
	 * @return   mixed
	 */
	wpmoly.cb = function( value, context, argCount ) {

		if ( value == null ) {
			return _.identity;
		}

		if ( _.isFunction( value ) ) {
			return wpmoly.optimizeCb( value, context, argCount );
		}

		if ( _.isObject( value ) ) {
			return _.matches( value );
		}

		return _.property( value );
	};

	/**
	 * Returns the results of applying the iteratee to each element of the
	 * object.
	 * 
	 * In contrast to _.map it returns an object
	 * 
	 * @since    3.0
	 * 
	 * @param    object      obj
	 * @param    function    iteratee
	 * @param    object      context
	 * 
	 * @return   object
	 */
	_.mapObject = _.mapObject || function( obj, iteratee, context ) {

		iteratee = wpmoly.cb( iteratee, context );

		var keys =  _.keys(obj),
		  length = keys.length,
		 results = {}, currentKey;

		for ( var index = 0; index < length; index++ ) {
			currentKey = keys[ index ];
			results[ currentKey ] = iteratee( obj[ currentKey ], currentKey, obj );
		}

		return results;
	};

	wpmoly.l10n = {

		/**
		 * Translation function to handle number-based strings.
		 * 
		 * String should be an array containing the possible translations
		 * [ '0 items', '1 item', '%d items'] or [ 'No item', '%d items']
		 * 
		 * @since    3.0
		 * 
		 * @param    string    string
		 * @param    int       n
		 * @param    string    param1, param
		 * 
		 * @return   string
		 */
		_n: function( string, n ) {

			if ( ! _.isArray( string ) ) {
				return '';
			}

			if ( 2 === string.length ) {
				string.unshift( '' );
			}

			var index = Math.max( 0, Math.min( n, 2 ) ),
			   string = string[ index ];

			return s.sprintf.apply( this, arguments );
		}
	};

	wpmoly.nonce = {

		/**
		 * Find current action's nonce value.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    Action name
		 * 
		 * @return   boolean|string    Nonce value if available, false else.
		 */
		get: function( action ) {

			var nonce_name = '#_wpmolynonce_' + action.replace( /\-/g, '_' ),
			         nonce = wpmoly.$( nonce_name ).val() || '';

			return nonce;
		},

		/**
		 * Update current action's nonce value.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    Action name
		 * 
		 * @return   void
		 */
		set: function( action, nonce ) {

			var nonce_name = '#_wpmolynonce_' + action.replace( /\-/g, '_' );

			wpmoly.$( nonce_name ).val( nonce );
		}
	};

	wpmoly.utils = {

		/**
		 * Find bracketed tags in a string.
		 * 
		 * Mainly used to prepare backdrops/posters titles and captions.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    string String to parse
		 * 
		 * @return   array
		 */
		matchTags: function( string ) {

			var reg = /{([^}]+),(\d)}|{([^}]+)}/gi,
			m, tags = [];

			while ( null !== ( m = reg.exec( string ) ) ) {
				if ( m.index === reg.lastIndex ) {
					reg.lastIndex++;
				}

				// Simple tag {tag}
				if ( m[3] && ! m[1] ) {
					tags.push({
						tag  : m[0],
						meta : m[3]
					});
				// Complex tag {tag,n}
				} else if ( m[1] && ! m[3] ) {
					tags.push({
						tag  : m[0],
						meta : m[1],
						n    : m[2]
					});
				}
			}

			return tags;
		},

		/**
		 * Generate a pagination menu.
		 * 
		 * @since    3.0
		 * 
		 * @param    int       current
		 * @param    int       total
		 * @param    object    options
		 * 
		 * @return   string
		 */
		paginate: function( current, total, options ) {

			var options = options || {},
			        tag = options.tag || 'li',
			      items = [];

			if( 1 >= total ) {
				return;
			}

			if ( 1 < current ) {
				items.push( '<' + tag + '><a data-pagination="prev" href="#"><span class="wpmolicon icon-arrow-left"></span></a></' + tag + '>' );
			}

			if ( 1 == current ) {
				items.push( '<' + tag + ' class="active"><a data-pagination="1" href="#">1</a></' + tag + '>' );
			} else {
				items.push( '<' + tag + '><a data-pagination="1" href="#">1</a></' + tag + '>' );
			}

			if ( current > 2 ) {
				items.push( '<' + tag + '>…</' + tag + '>' );
				if ( current === total && total > 3 ) {
					items.push( '<' + tag + '><a data-pagination="' + ( current - 2 ) + '" href="#">' + ( current - 2 ) + '</a></' + tag + '>' );
				}
				items.push( '<' + tag + '><a data-pagination="' + ( current - 1 ) + '" href="#">' + ( current - 1 ) + '</a></' + tag + '>');
			}

			if ( current != 1 && current != total ) {
				items.push( '<' + tag + ' class="active"><a data-pagination="' + current + '" href="#">' + current + '</a></' + tag + '>' );
			}

			if ( current < total - 1 ) {
				items.push( '<' + tag + '><a data-pagination="' + ( current + 1 ) + '" href="#">' + ( current + 1 ) + '</a></' + tag + '>' );
				if ( current == 1 && total > 3 ) {
					items.push( '<' + tag + '><a data-pagination="' + ( current + 2 ) + '" href="#">' + ( current + 2 ) + '</a></' + tag + '>' );
				}
				items.push( '<' + tag + '>…</' + tag + '>' );
			}

			if ( total === current ) {
				items.push( '<' + tag + ' class="active"><a data-pagination="' + total + '" href="#">' + total + '</a></' + tag + '>' );
			} else {
				items.push( '<' + tag + '><a data-pagination="' + total + '" href="#">' + total + '</a></' + tag + '>' );
			}

			if ( current < total ) {
				items.push( '<' + tag + '><a data-pagination=="next" href="#"><span class="wpmolicon icon-arrow-right"></span></a></' + tag + '>' );
			}

			return items;
		}
	};

})( jQuery, _, s, Backbone );