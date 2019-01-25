/**
 * Define some utility functions.
 *
 * @since 1.0.0
 *
 * @package WPSeriesLibrary
 */

(function( _, JSON ) {

	/**
	 * Calculate JSON size.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} json JSON.
	 *
	 * @return int
	 */
	JSON.size = JSON.size || function( json ) {

		var json = JSON.stringify( json ),
		    utf8 = encodeURIComponent( json ).match( /%[89ABab]/g );

  	return json.length + ( utf8 ? utf8.length : 0 );
	};

	/**
	 * Render JSON into collapsible, themeable HTML.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} json JSON.
	 *
	 * @return string
	 */
	JSON.render = JSON.render || function( json, options ) {

		if ( ! renderjson ) {
			return json;
		}

		var options = options || {};

		renderjson.set_show_to_level( options.level || 0 );

		return renderjson( json );
	};

	/**
	 * JSON syntax highlighting.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} json JSON.
	 *
	 * @return string
	 */
	JSON.highlight = JSON.highlight || function( json ) {

		if ( ! _.isString( json ) ) {
			json = JSON.stringify( json, null, "\t" );
		}

    json = json.replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );

    return json.replace( /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function ( match ) {
        var cls = 'number';
				if ( /^"/.test( match ) ) {
            if ( /:$/.test( match ) ) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if ( /true|false/.test( match ) ) {
            cls = 'boolean';
        } else if ( /null/.test( match ) ) {
            cls = 'null';
        }

        return '<span class="' + cls + '">' + match + '</span>';
    } );
	}

	/**
	 * Returns the sum of all values of a list.
	 *
	 * @since 1.0.0
	 *
	 * @param {mixed} list
	 *
	 * @return int
	 */
	_.sum = _.sum || function( list ) {

		if ( _.isObject( list ) ) {
			list = _.values( list );
		}

		if ( ! _.isArray( list ) ) {
			return NaN;
		}

		return list.reduce( function( a, b ) {
			return _.isNumber( a ) && _.isNumber( b ) ? a + b : 0;
		}, 0 );
	};

	/**
	 * Returns true is the value is true.
	 *
	 * Accepts litteral values.
	 *
	 * @since 1.0.0
	 *
	 * @param {mixed} value
	 *
	 * @return boolean
	 */
	_.isTrue = _.isTrue || function( value ) {

		if ( _.isString( value ) ) {
			value = value.toLowerCase();
		}

		return _.contains( [ 1, '1', true, 'true' ], value );
	};

	/**
	 * Returns false is the value is false.
	 *
	 * Accepts litteral values.
	 *
	 * @since 1.0.0
	 *
	 * @param {mixed} value
	 *
	 * @return boolean
	 */
	_.isFalse = _.isFalse || function( value ) {

		if ( _.isString( value ) ) {
			value = value.toLowerCase();
		}

		return _.contains( [ 0, '0', false, 'false' ], value );
	};

})( _, JSON );
