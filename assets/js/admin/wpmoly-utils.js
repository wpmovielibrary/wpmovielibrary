
$ = jQuery;

wpmoly = wpmoly || {};

	/**
	 * WPMOLY filter for AJAX Request
	 * 
	 * @since    1.0
	 * 
	 * @param    string      Request type: GET, POST
	 * @param    object      Data object to pass
	 * @param    function    Function to run on success
	 * @param    function    Function to run on complete
	 */
	wpmoly.__ajax = function( data ) {
		var data = $.extend({
				url: ajaxurl
			},
			data
		);
		$.ajax( data );
	};

	/**
	 * WPMOLY filter for AJAX GET Request
	 * 
	 * @since    1.0
	 * 
	 * @param    object      Data object to pass
	 */
	wpmoly._get = function( data ) {
		wpmoly.__ajax({
			type: 'GET',
			data: data.data || {},
			beforeSend: data.beforeSend || function() {},
			success: data.success || function() {},
			complete: data.complete || function() {},
			error: data.error || function() {}
		});
	};

	/**
	 * WPMOLY filter for AJAX POST Request
	 * 
	 * @since    1.0
	 * 
	 * @param    object      Data object to pass
	 */
	wpmoly._post = function( data ) {
		wpmoly.__ajax({
			type: 'POST',
			data: data.data || {},
			beforeSend: data.beforeSend || function() {},
			success: data.success || function() {},
			complete: data.complete || function() {},
			error: data.error || function() {}
		});
	};

	/**
	 * Determine which data package the submitted field name belongs to.
	 * 
	 * @since    1.0
	 * 
	 * @param    string    Field name
	 * 
	 * @return   string    Data package name
	 */
	wpmoly.switch_data = function( f_name ) {

		switch ( f_name ) {
			case "poster":
			case "title":
			case "original_title":
			case "overview":
			case "production_companies":
			case "production_countries":
			case "spoken_languages":
			case "runtime":
			case "genres":
			case "release_date":
				var _data = 'meta';
				break;
			case "director":
			case "producer":
			case "photography":
			case "composer":
			case "author":
			case "writer":
			case "cast":
				var _data = 'crew';
				break;
			default:
				var _data = 'data';
				break;
		}

		return _data;
	};

	/**
	 * Status indicator
	 */
	wpmoly.state = wpmoly_state = {

		container: '#wpmoly_status',

		init: function() {},
		set: function() {},
		clear: function() {}
	};

		/**
		 * Update status
		 * 
		 * @since    1.0
		 * 
		 * @param    string    Status Message
		 * @param    string    Status type: error, update, warning
		 */
		wpmoly.state.set = function( message, style ) {

			if ( 'error' == style )
				var bg = '#ff4e46';
			else if ( 'warning' == style )
				var bg = '#ffd259';
			else if ( 'success' == style )
				var bg = '#99ff5c';
			else
				var bg = '#fff';

			$( wpmoly_state.container ).empty().append( '<p>' + message + '</p>' ).removeClass().addClass( style ).show();

			if ( 'error' == style ) {
				$( '.spinner, .loading' ).removeClass( 'spinner loading' );
				$( window ).scrollTop( $( wpmoly_state.container ).offset().top - 42 );
			}

			$( wpmoly_state.container ).css( { backgroundColor: bg } );
			$( wpmoly_state.container ).animate( { backgroundColor: '#FFF' }, 1500 );
		};

		/**
		 * Clear status
		 */
		wpmoly.state.clear = function() {
			$( wpmoly_state.container ).on( 'click', function() {
				$( this ).empty().removeClass().hide();
			});
		};

		/**
		 * Init status
		 */
		wpmoly.state.init = function() {

			$( wpmoly_state.container ).empty().removeClass().hide();
		};

	/**
	 * Parse URL Query part to extract specific variables
	 * 
	 * @since    1.0
	 * 
	 * @param    string    URL Query part to parse
	 * @param    string    Wanted variable name
	 * 
	 * @return   string|boolean    Variable value if available, false else
	 */
	wpmoly.http_query_var = function( query, variable ) {

		var vars = query.split("&");
		for ( var i = 0; i <vars.length; i++ ) {
			var pair = vars[ i ].split("=");
			if ( pair[0] == variable )
				return pair[1];
		}
		return false;
	};

	/**
	 * Reinit WP_List_Table Checkboxes events. Events are messed up when
	 * using AJAX to reload tables' contents, so we need to override WordPress
	 * default jQuery handlers for Checkboxes click events.
	 * 
	 * @since    1.0
	 * 
	 * @param    object    Click Event Object
	 * 
	 * @return   boolean
	 */
	wpmoly.reinit_checkboxes_all = function( e, $input ) {

		var c = $input.prop('checked'),
			kbtoggle = 'undefined' == typeof toggleWithKeyboard ? false : toggleWithKeyboard,
			toggle = e.shiftKey || kbtoggle;

		$input.closest( 'table' ).children( 'tbody' ).filter(':visible')
		.children().children('.check-column').find(':checkbox')
		.prop('checked', function() {
			if ( $input.is(':hidden') )
				return false;
			if ( toggle )
				return $input.prop( 'checked' );
			else if (c)
				return true;
			return false;
		});

		$input.closest('table').children('thead,  tfoot').filter(':visible')
		.children().children('.check-column').find(':checkbox')
		.prop('checked', function() {
			if ( toggle )
				return false;
			else if (c)
				return true;
			return false;
		});

	};

	/**
	 * Reinit multiple checkboxes selection usin Shift+click.
	 * 
	 * Events are messed up when * using AJAX to reload tables' contents, so
	 * we need to override WordPress default jQuery handlers for Checkboxes
	 * click events.
	 * 
	 * @since    1.0
	 * 
	 * @param    object    Click Event Object
	 * 
	 * @return   boolean
	 */
	wpmoly.reinit_checkboxes = function( e, $input ) {

		if ( 'undefined' == e.shiftKey ) { return true; }
		if ( e.shiftKey ) {
			if ( !lastClicked ) { return true; }
			checks = $( lastClicked ).closest( 'form' ).find( ':checkbox' );
			first = checks.index( lastClicked );
			last = checks.index( $input );
			checked = $input.prop('checked');
			if ( 0 < first && 0 < last && first != last ) {
				sliced = ( last > first ) ? checks.slice( first, last ) : checks.slice( last, first );
				sliced.prop( 'checked', function() {
					if ( $input.closest('tr').is(':visible') )
						return checked;

					return false;
				});
			}
		}
		lastClicked = $input;

		// toggle "check all" checkboxes
		var unchecked = $input.closest('tbody').find(':checkbox').filter(':visible').not(':checked');
		$input.closest('table').children('thead, tfoot').find(':checkbox').prop('checked', function() {
			return ( 0 === unchecked.length );
		});

		return true;
	};

	/**
	 * Find current action's nonce value.
	 * 
	 * @since    1.0
	 * 
	 * @param    string    Action name
	 * 
	 * @return   boolean|string    Nonce value if available, false else.
	 */
	wpmoly.get_nonce = function( action ) {

		var nonce_name = '#_wpmolynonce_' + action.replace( /\-/g, '_' ),
		         nonce = null;

		if ( undefined != $( nonce_name ) )
			nonce = $( nonce_name ).val();

		return nonce;
	};

	/**
	 * Update current action's nonce value.
	 * 
	 * @since    1.0
	 * 
	 * @param    string    Action name
	 */
	wpmoly.update_nonce = function( action, nonce ) {

		var nonce_name = '#_wpmolynonce_' + action.replace( /\-/g, '_' );

		if ( undefined != $( nonce_name ) && undefined != nonce )
			$( nonce_name ).val( nonce );
	};

	/**
	 * Init script.
	 * 
	 * @since    2.1.4
	 */
	wpmoly.init = function() {

		var pagenow = window.pagenow   || false,
		  adminpage = window.adminpage || false;

		if ( ! pagenow || ! adminpage ) {
			return;
		}

		if ( ( 'edit-movie' == pagenow && 'edit-php' == adminpage ) || ( 'movie' == pagenow && 'post-new-php' == adminpage ) ) {
			$( '#toplevel_page_wpmovielibrary, #toplevel_page_wpmovielibrary > a' ).addClass( 'wp-has-current-submenu wp-open-submenu' );
		}
	};
