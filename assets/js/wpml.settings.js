
wpml = wpml || {};

wpml.settings = wpml_settings = {}

	wpml.settings.init = function() {

		wpml.settings.panels.init();
		wpml.settings.sortable.init();
	};

	wpml.settings.panels = wpml_panels = {

		links: '#wpml-tabs .wpml-tabs-nav a',
		link: '#wpml-tabs .wpml-tabs-nav li',
		link_active: '#wpml-tabs .wpml-tabs-nav li.active',
		panels: '#wpml-tabs .wpml-tabs-panels > .form-table',
		active: 0,
	};

		/**
		 * Init Events
		 * 
		 * @since    1.0.0
		 */
		wpml.settings.panels.init = function() {

			if ( $( wpml_panels.link_active ).length )
				wpml_panels.active = $( wpml_panels.link ).index( $( wpml_panels.link_active ) );

			var panel = $( wpml_panels.panels )[ wpml_panels.active ];

			$( wpml_panels.panels ).hide();
			$( panel ).addClass( 'active' );

			$( wpml_panels.links ).on( 'click', function( e ) {
				e.preventDefault();
				wpml_panels.switch_panel( this );
			});

		};

		/**
		 * Switch between panels
		 * 
		 * @since    1.0.0
		 * 
		 * @param    object   Caller link DOM Element
		 */
		wpml.settings.panels.switch_panel = function( link ) {

			__link = link;
			var index = $(wpml_panels.links).index( link );

			if ( wpml_panels.panels.length >= index )
				var panel = $( wpml_panels.panels )[ index ];

			var tab = $( link ).attr( 'data-section' );
			var url = link.href.replace( link.hash, '' );
			if ( link.hash.length || '#' == url.substring( url.length, url.length - 1 ) )
				url = url.substring( 0, ( url.length - 1 ) );

			    var section = link.href.indexOf( '&wpml_section' );
			if ( section > 0 )
				url = url.substring( 0, section );

			$( '.wpml-tabs-panels .form-table, .wpml-tabs-nav' ).removeClass( 'active' );
			$( panel ).addClass( 'active' );
			$( link ).parent( 'li' ).addClass( 'active' );

			window.history.replaceState( {}, '' + url + '&' + tab, '' + url + '&' + tab );
		};

	wpml.settings.sortable = wpml_sortable = {

		draggable: '#draggable',
		droppable: '#droppable',
		default_selected: 'default_movie_meta_selected',
		default_droppable: 'default_movie_meta_droppable',
	};

		/**
		 * Init Events
		 * 
		 * @since    1.0.0
		 */
		wpml.settings.sortable.init = function() {

			$( wpml_sortable.draggable + ', ' + wpml_sortable.droppable ).sortable({
				connectWith: 'ul',
				placeholder: 'highlight',
				update: function( event, ui ) {
					wpml_sortable.update_item_style( ui );
				},
				stop: function( event, ui ) {
					wpml_sortable.update_item();
				}
			});

			$( wpml_sortable.draggable + ', ' + wpml_sortable.droppable ).disableSelection();
		};


		/**
		 * Change item style depending on status
		 * 
		 * @since    1.0.0
		 * 
		 * @param    object    UI Object
		 */
		wpml.settings.sortable.update_item_style = function( ui ) {

			if ( undefined == ui.sender || ! ui.sender.length )
				return false;

			var _id = ui.sender[0].id;
			var item = ui.item[0];

			if ( 'draggable' == ui.sender[0].id )
				$( item ).removeClass( wpml_sortable.default_selected ).addClass( wpml_sortable.default_droppable );
			else if ( 'droppable' == ui.sender[0].id )
				$( item ).removeClass( wpml_sortable.default_droppable ).addClass( wpml_sortable.default_selected );
		};

		/**
		 * Init Events
		 * 
		 * @since    1.0.0
		 */
		wpml.settings.sortable.update_item = function() {

			values = [];
			var items = $('.'+wpml_sortable.default_selected);
			$.each( items, function() {
				var value = $( this ).attr( 'data-movie-meta' );
				$( '#wpml_settings-wpml-default_movie_meta option[value="'+value+'"]' ).prop( 'selected', true );
				values.push( value );
			});
			$( '#default_movie_meta_sorted' ).val( values.join( ',' ) );
		};

	/**
	 * Settings utils
	 * 
	 * @since    1.0.0
	 */
	wpml.settings.utils = wpml_settings_utils = {

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
		 * @since    1.0.0
		 */
		wpml.settings.utils.init = function() {

			$( wpml_settings_utils._key_check.element ).on( wpml_settings_utils._key_check.event, function( e ) {
				e.preventDefault();
				wpml_settings_utils.api_ckeck();
			});

			$( wpml_settings_utils._label_on.element ).on( wpml_settings_utils._label_on.event, function() {
				wpml_settings_utils.toggle_radio( this, true );
			});

			$( wpml_settings_utils._label_off.element ).on( wpml_settings_utils._label_off.event, function() {
				wpml_settings_utils.toggle_radio( this, false );
			});

			$( wpml_settings_utils._default_movie_detail.element ).on( wpml_settings_utils._default_movie_detail.event, function() {
				wpml_settings_utils.details_select( this );
			});
		};

		/**
		 * Movie Details styled select list
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    Detail type
		 */
		wpml.settings.utils.details_select = function( item ) {
			var $item = $( item ),
			    value = $item.attr( 'data-movie-detail' );

			if ( $item.hasClass( 'selected' ) ) {
				$item.removeClass( 'selected' );
				$( '#wpml_settings-wpml-default_movie_details option[value="'+value+'"]' ).prop( 'selected', false );
			}
			else {
				$item.addClass( 'selected' );
				$( '#wpml_settings-wpml-default_movie_details option[value="'+value+'"]' ).prop( 'selected', true );
			}
		}

		/**
		 * Check API Key validity
		 * 
		 * @since    1.0.0
		 */
		wpml.settings.utils.api_ckeck = function() {

			var $input = $( 'input#APIKey_check' ),
			    key = $( 'input#wpml_settings-tmdb-apikey' ).val();

			$( '#api_status' ).remove();

			if ( '' == key ) {
				$input.after( '<span id="api_status" class="invalid">' + wpml_ajax.lang.empty_key + '</span>' );
				return false;
			}
			else if ( 32 != key.length ) {
				$input.after( '<span id="api_status" class="invalid">' + wpml_ajax.lang.length_key + '</span>' );
				return false;
			}
			
			wpml._get({
				data: {
					action: 'wpml_check_api_key',
					nonce: wpml.get_nonce( 'check-api-key' ),
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
					wpml.update_nonce( 'check-api-key', r.responseJSON.nonce );
				}
			});
		};

		/**
		 * Styled input radio
		 * 
		 * @since    1.0.0
		 * 
		 * @param    object     DOM Element
		 * @param    boolean    Checked false or true
		 */
		wpml.settings.utils.toggle_radio = function( toggle, status ) {

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

	wpml.settings.init();
	wpml.settings.utils.init();