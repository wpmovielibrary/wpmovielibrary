
wpmoly = wpmoly || {};

wpmoly.settings = wpmoly_settings = {}

	wpmoly.settings.init = function() {

		wpmoly.settings.panels.init();
		wpmoly.settings.sortable.init();
	};

	wpmoly.settings.panels = wpmoly_panels = {

		links: '#wpmoly-tabs .wpmoly-tabs-nav a',
		link: '#wpmoly-tabs .wpmoly-tabs-nav li',
		link_active: '#wpmoly-tabs .wpmoly-tabs-nav li.active',
		panels: '#wpmoly-tabs .wpmoly-tabs-panels > .form-table',
		active: 0,
	};

		/**
		 * Init Events
		 * 
		 * @since    1.0
		 */
		wpmoly.settings.panels.init = function() {

			$( '#wpmoly-sort-meta_used h3, #wpmoly-sort-details_used h3' ).text( wpmoly_ajax.lang.used );
			$( '#wpmoly-sort-meta_available h3, #wpmoly-sort-details_available h3' ).text( wpmoly_ajax.lang.available );

			if ( $( wpmoly_panels.link_active ).length )
				wpmoly_panels.active = $( wpmoly_panels.link ).index( $( wpmoly_panels.link_active ) );

			var panel = $( wpmoly_panels.panels )[ wpmoly_panels.active ];

			$( wpmoly_panels.panels ).hide();
			$( panel ).addClass( 'active' );

			$( wpmoly_panels.links ).on( 'click', function( e ) {
				e.preventDefault();
				wpmoly_panels.switch_panel( this );
			});

		};

		/**
		 * Switch between panels
		 * 
		 * @since    1.0
		 * 
		 * @param    object   Caller link DOM Element
		 */
		wpmoly.settings.panels.switch_panel = function( link ) {

			__link = link;
			var index = $(wpmoly_panels.links).index( link );

			if ( wpmoly_panels.panels.length >= index )
				var panel = $( wpmoly_panels.panels )[ index ];

			var tab = $( link ).attr( 'data-section' );
			var url = link.href.replace( link.hash, '' );
			if ( link.hash.length || '#' == url.substring( url.length, url.length - 1 ) )
				url = url.substring( 0, ( url.length - 1 ) );

			    var section = link.href.indexOf( '&wpmoly_section' );
			if ( section > 0 )
				url = url.substring( 0, section );

			$( '.wpmoly-tabs-panels .form-table, .wpmoly-tabs-nav' ).removeClass( 'active' );
			$( panel ).addClass( 'active' );
			$( link ).parent( 'li' ).addClass( 'active' );

			window.history.replaceState( {}, '' + url + '&' + tab, '' + url + '&' + tab );
			$( 'input[name="_wp_http_referer"]' ).val( document.location.pathname + document.location.search + '&' + tab );
		};

	wpmoly.settings.sortable = wpmoly_sortable = {

		draggable: '#draggable',
		droppable: '#droppable',
		default_selected: 'default_movie_meta_selected',
		default_droppable: 'default_movie_meta_droppable',
	};

		/**
		 * Init Events
		 * 
		 * @since    1.0
		 */
		wpmoly.settings.sortable.init = function() {

			$( wpmoly_sortable.draggable + ', ' + wpmoly_sortable.droppable ).sortable({
				connectWith: 'ul',
				placeholder: 'highlight',
				update: function( event, ui ) {
					wpmoly_sortable.update_item_style( ui );
				},
				stop: function( event, ui ) {
					wpmoly_sortable.update_item();
				}
			});

			$( wpmoly_sortable.draggable + ', ' + wpmoly_sortable.droppable ).disableSelection();
		};


		/**
		 * Change item style depending on status
		 * 
		 * @since    1.0
		 * 
		 * @param    object    UI Object
		 */
		wpmoly.settings.sortable.update_item_style = function( ui ) {

			if ( undefined == ui.sender || ! ui.sender.length )
				return false;

			var _id = ui.sender[0].id;
			var item = ui.item[0];

			if ( 'draggable' == ui.sender[0].id )
				$( item ).removeClass( wpmoly_sortable.default_selected ).addClass( wpmoly_sortable.default_droppable );
			else if ( 'droppable' == ui.sender[0].id )
				$( item ).removeClass( wpmoly_sortable.default_droppable ).addClass( wpmoly_sortable.default_selected );
		};

		/**
		 * Init Events
		 * 
		 * @since    1.0
		 */
		wpmoly.settings.sortable.update_item = function() {

			values = [];
			var items = $('.'+wpmoly_sortable.default_selected);
			$.each( items, function() {
				var value = $( this ).attr( 'data-movie-meta' );
				$( '#wpmoly_settings-wpmoly-default_movie_meta option[value="'+value+'"]' ).prop( 'selected', true );
				values.push( value );
			});
			$( '#default_movie_meta_sorted' ).val( values.join( ',' ) );
		};

	/**
	 * Settings utils
	 * 
	 * @since    1.0
	 */
	wpmoly.settings.utils = wpmoly_settings_utils = {

		_key_check: {
			element: '#APIKey_check',
			event: 'click'
		},
		_label_on: {
			element: '.label_onoff .label_on',
			event: 'click'
		},
		_label_off: {
			element: '.label_onoff .label_off',
			event: 'click'
		},
		_default_movie_detail: {
			element: '.default_movie_detail',
			event: 'click'
		},
	};

		/**
		 * Init Events
		 * 
		 * @since    1.0
		 */
		wpmoly.settings.utils.init = function() {

			$( wpmoly_settings_utils._key_check.element ).on( wpmoly_settings_utils._key_check.event, function( e ) {
				e.preventDefault();
				wpmoly_settings_utils.api_ckeck();
			});

			$( wpmoly_settings_utils._label_on.element ).on( wpmoly_settings_utils._label_on.event, function() {
				wpmoly_settings_utils.toggle_radio( this, true );
			});

			$( wpmoly_settings_utils._label_off.element ).on( wpmoly_settings_utils._label_off.event, function() {
				wpmoly_settings_utils.toggle_radio( this, false );
			});

			$( wpmoly_settings_utils._default_movie_detail.element ).on( wpmoly_settings_utils._default_movie_detail.event, function() {
				wpmoly_settings_utils.details_select( this );
			});
		};

		/**
		 * Movie Details styled select list
		 * 
		 * @since    1.0
		 * 
		 * @param    string    Detail type
		 */
		wpmoly.settings.utils.details_select = function( item ) {
			var $item = $( item ),
			    value = $item.attr( 'data-movie-detail' );

			if ( $item.hasClass( 'selected' ) ) {
				$item.removeClass( 'selected' );
				$( '#wpmoly_settings-wpmoly-default_movie_details option[value="'+value+'"]' ).prop( 'selected', false );
			}
			else {
				$item.addClass( 'selected' );
				$( '#wpmoly_settings-wpmoly-default_movie_details option[value="'+value+'"]' ).prop( 'selected', true );
			}
		}

		/**
		 * Check API Key validity
		 * 
		 * @since    1.0
		 */
		wpmoly.settings.utils.api_ckeck = function() {

			var $input = $( 'input#APIKey_check' ),
			    key = $( 'input#wpmoly_settings-tmdb-apikey' ).val();

			$( '#api_status' ).remove();

			if ( '' == key ) {
				$input.after( '<span id="api_status" class="invalid">' + wpmoly_ajax.lang.empty_key + '</span>' );
				return false;
			}
			else if ( 32 != key.length ) {
				$input.after( '<span id="api_status" class="invalid">' + wpmoly_ajax.lang.length_key + '</span>' );
				return false;
			}
			
			wpmoly._get({
				data: {
					action: 'wpmoly_check_api_key',
					nonce: wpmoly.get_nonce( 'check-api-key' ),
					key: key
				},
				beforeSend: function() {
					$input.next( '.spinner' ).css( { position: 'absolute', display: 'inline' } );
				},
				error: function( response ) {
					$input.after( '<span id="api_status" class="invalid">' + response.responseJSON.errors.invalid[ 0 ] + '</span>' );
				},
				success: function( response ) {
					$input.after( '<span id="api_status" class="valid">' + response.data.message + '</span>' );
				},
				complete: function( r ) {
					$input.nextAll( '.spinner' ).css( { position: '', display: '' } );
					wpmoly.update_nonce( 'check-api-key', r.responseJSON.nonce );
				}
			});
		};

		/**
		 * Styled input radio
		 * 
		 * @since    1.0
		 * 
		 * @param    object     DOM Element
		 * @param    boolean    Checked false or true
		 */
		wpmoly.settings.utils.toggle_radio = function( toggle, status ) {

			var $label_off = $( toggle ).parent( '.label_onoff' ).find( '.label_off' ),
			    $label_on = $( toggle ).parent( '.label_onoff' ).find( '.label_on' ),
			    $disable = $( toggle ).parent( '.label_onoff' ).next( '.label_onoff_radio' ).find( '.enable' ),
			    $enable = $( toggle ).parent( '.label_onoff' ).next( '.label_onoff_radio' ).find( '.disable' ),
			    disable = status || false;

			if ( ! disable ) {
				$enable.prop( 'checked', true );
				$label_on.removeClass( 'active' );
				$label_off.addClass( 'active' );
			}
			else {
				$disable.prop( 'checked', true );
				$label_off.removeClass( 'active' );
				$label_on.addClass( 'active' );
			}
		};

	wpmoly.settings.init();
	wpmoly.settings.utils.init();