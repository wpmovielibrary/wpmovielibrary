
$ = $ || jQuery;

wpmoly = wpmoly || {};

	wpmoly.importer = {};
	
		wpmoly.importer.view = wpmoly_import_view = {

			timer: undefined,
			delay: 500,

			select: '#wpmoly_imported #the-list input[type=checkbox]',
			select_all: '#cb-select-all-1, #cb-select-all-2',

			queue_select: '#wpmoly_import_queue #wpmoly-queued-list input[type=checkbox]'
		};

			/**
			 * Init Events
			 * 
			 * @since    1.0
			 */
			wpmoly.importer.view.init = function() {

				$( wpmoly_import_view.select_all ).unbind( 'click' ).on( 'click', function( e ) {
					wpmoly.reinit_checkboxes_all( e, $( this ) );
				});

				$( wpmoly_import_view.select ).unbind( 'click' ).on( 'click', function( e ) {
					wpmoly.reinit_checkboxes( e, $( this ) );
				});
			};

			/**
			 * Reload the movie table. Used when new movies are imported or
			 * when browsing through the table.
			 * 
			 * @since    1.0
			 */
			wpmoly.importer.view.reload = function( data, list ) {

				if ( 'queued' == list ) {
					var _selector = '#wpmoly_import_queue',
					    _rows = '.wp-list-table',
					    _data = {
						action: 'wpmoly_queued_movies',
						nonce_name: 'queued-movies',
						nonce: wpmoly.get_nonce( 'queued-movies' )
					};
				}
				else {
					var _selector = '#wpmoly_imported',
					    _rows = '.wp-list-table tbody',
					    _data = {
						action: 'wpmoly_imported_movies',
						nonce_name: 'imported-movies',
						nonce: wpmoly.get_nonce( 'imported-movies' )
					};
				}

				var data = $.extend( _data, data );

				wpmoly._get({
					data: data,
					error: function( response ) {
						wpmoly_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpmoly_state.set( this, 'error' );
						});
					},
					success: function( response ) {

						if ( undefined != response.data.rows )
							$(_rows, _selector).html( response.data.rows );
						if ( undefined != response.data.column_headers )
							$( 'thead tr', _selector ).html( response.data.column_headers );
						if ( undefined != response.data.column_footers )
							$( 'tfoot tr', _selector ).html( response.data.column_footers );
						if ( undefined != response.data.pagination.bottom )
							$( '.tablenav.top .tablenav-pages', _selector ).html( $(response.data.pagination.top).html() );
						if ( undefined != response.data.pagination.top )
							$( '.tablenav.bottom .tablenav-pages', _selector ).html( $(response.data.pagination.bottom).html() );

						if ( 'queued' == list )
							wpmoly_import_view.update_count( 'import_queue', response.data.total_items, response.i18n.total_items_i18n );
						else
							wpmoly_import_view.update_count( 'imported', response.data.total_items, response.i18n.total_items_i18n );

						wpmoly_queue_utils.init();
					},
					complete: function( r ) {
						wpmoly.update_nonce( data.nonce_name, r.responseJSON.nonce );
						wpmoly_import_view.init();
					}
				});
			};

			/**
			 * Navigate through the table pages using navigation arrow links
			 * 
			 * @since    1.0
			 * 
			 * @param    object    Link HTML Object
			 */
			wpmoly.importer.view.navigate = function( link ) {
				var query = link.search.substring( 1 );
				var data = {
					paged: wpmoly.http_query_var( query, 'paged' ) || '1',
					order: wpmoly.http_query_var( query, 'order' ) || 'asc',
					orderby: wpmoly.http_query_var( query, 'orderby' ) || 'title'
				};
				wpmoly_import_view.reload( data );
			};

			/**
			 * Navigate through the table pages using pagination input
			 * 
			 * @since    1.0
			 */
			wpmoly.importer.view.paginate = function() {
				var data = {
					paged: parseInt( $( 'input[name=paged]' ).val() ) || '1',
					order: $( 'input[name=order]' ).val() || 'asc',
					orderby: $( 'input[name=orderby]' ).val() || 'title'
				};

				window.clearTimeout( wpmoly_import_view.timer );
				wpmoly_import_view.timer = window.setTimeout( function() {
					wpmoly_import_view.reload( data );
				}, wpmoly_import_view.delay );
			};

			/**
			 * Update the menu badges containing movies counts.
			 * 
			 * @since    1.0
			 * 
			 * @param    string    Which menu, queued or imported?
			 * @param    int       Increment or decrement?
			 */
			wpmoly.importer.view.update_count = function( wot, i, i_i18n ) {
				
				var wot = ( 'import_queue' == wot ? wot : 'imported' ),
				    i = ( i >= 0 ? i : '0' ),
				    $span = $( '#_wpmoly_' + wot + ' span' );

				$span.text( '' + i );

				if ( 'import_queue' == wot ) {
					$( '.displaying-num', wpmoly_queue.queued_list ).text( i_i18n );
					$( wpmoly_queue.progress_block ).removeClass( 'visible' );
					$( wpmoly_queue.progress_left, wpmoly_queue.queued_list ).text( '0' );
					$( wpmoly_queue.progress_queued, wpmoly_queue.queued_list ).text( '0' );
					$( wpmoly_queue.progress_value ).val( 0 );
					$( wpmoly_queue.progress ).width( 0 );
					$( wpmoly_queue.progress_status ).css( { display: 'inline-block' } );
					$( wpmoly_queue.progress_status_message ).hide();
				}
			};

			/**
			 * Check the Select All checkboxes if all inputs are
			 * checked in Import List Table
			 * 
			 * This is needed because of the AJAX update of the table 
			 * breaking WordPress' JavaScript Events handlers.
			 * 
			 * @since    1.0
			 */
			wpmoly.importer.view.toggle_button = function() {

				if ( $( wpmoly_import_view.select + ':checked' ).length != $( wpmoly_import_view.select ).length )
					$( wpmoly_import_view.select_all ).prop( 'checked', false );
				else
					$( wpmoly_import_view.select_all ).prop( 'checked', true );
			};

			/**
			 * Check the all checkboxes in Import List Table when any
			 * of the Check All inputs is checked
			 * 
			 * This is needed because of the AJAX update of the table 
			 * breaking WordPress' JavaScript Events handlers.
			 * 
			 * @since    1.0
			 * 
			 * @param    int    Which selector.
			 */
			wpmoly.importer.view.toggle_inputs = function( select ) {

				if ( ! $( wpmoly_import_view.select_all ).prop('checked') )
					$( wpmoly_import_view.select ).prop( 'checked', false );
				else
					$( wpmoly_import_view.select ).prop( 'checked', true );
			};