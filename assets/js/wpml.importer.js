
wpml = wpml || {};

var wpml_importer;

	wpml.importer = wpml_importer = {

		action: 'input#doaction, input#doaction2',
		select_all: '.movies > thead input[type=checkbox], .movies > tfoot input[type=checkbox]',
		select: '.movies > tbody input[type=checkbox]',
		fetch: '.fetch_movie',
		delete: '.delete_movie',

		// List import
		import_list: '#wpml_import',
		// List import textarea
		list: '#wpml_import_list',
		// List import button
		importer: '#wpml_importer',

		// Imported movies
		imported_list: '#wpml_imported',

		timer: undefined,
		delay: 500,

		init: function() {},
		import: function() {},
		doaction: function() {},
		fetch_movie: function() {},
		delete_movie: function() {},
		get_movies: function() {},
		reload: function() {},
		navigate: function() {},
		paginate: function() {}
	};

		/**
		 * Init WPML Importer
		 */
		wpml.importer.init = function() {

			$(wpml_importer.select_all).unbind('click').on( 'click', function( e ) {
				wpml.reinit_checkboxes_all( e, $(this) );
			});

			$(wpml_importer.select).unbind('click').on( 'click', function( e ) {
				wpml.reinit_checkboxes( e, $(this) );
			});

			$(wpml_importer.action, wpml_importer.imported_list).unbind('click').on( 'click', function( e ) {
				e.preventDefault();
				wpml_importer.doaction( this.id );
			});

			// Nav and sort links click
			$('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a', wpml_importer.imported_list).unbind('click').on( 'click', function( e ) {
				e.preventDefault();
				wpml_importer.navigate( this );
			});

			// Update when manually changing page number in input field
			$('input[name=paged]', wpml_importer.imported_list).unbind('keyup').on( 'keyup', function( e ) {
				if ( 13 == e.which )
					e.preventDefault();
				wpml_importer.paginate();
			});

			// Import a list of titles
			$(wpml_importer.importer, wpml_importer.import_list).unbind('click').on( 'click', function( e ) {
				e.preventDefault();
				wpml_importer.import();
			});
		};

		/**
		 * Handle Bulk actions
		 */
		wpml.importer.doaction = function( action ) {

			var $action = $('select[name=' + action.replace('do','') + ']'),
			    movies = [];

			if ( 'tmdb_data' == $action.val() ) {
				wpml_importer.get_movies();
			}
			else if ( 'delete' == $action.val() ) {
				$(wpml_importer.select + ':checked').each(function() {
					movies.push( this.id.replace('post_','') );
				});
				$action.nextAll('.spinner').css({display: 'inline-block'});
				wpml_importer.delete_movie( movies );
			}
			else if ( 'enqueue' == $action.val() ) {
				$(wpml_importer.select + ':checked').each(function() {
					movies.push( this.id.replace('post_','') );
				});
				$action.nextAll('.spinner').css({display: 'inline-block'});
				wpml_queue._enqueue( movies );
			}
			else {
				return false;
			}
		};

		/**
		 * Import a list of movies by title.
		 * 
		 * Call WPML_Import::import_movies_callback() to create movie draft
		 * for each title submitted and update the table
		 */
		wpml.importer.import = function() {

			if ( undefined == $(wpml_importer.list) || '' == $(wpml_importer.list).val() )
				return false;

			wpml._post({
				data: {
					action: 'wpml_import_movies',
					wpml_import_list: $(wpml_importer.list).val(),
					wpml_ajax_movie_import: $('#wpml_ajax_movie_import').val()
				},
				error: function( response ) {
					wpml_state.clear();
					$.each( response.responseJSON.errors, function() {
						wpml_state.set( this, 'error' );
					});
				},
				success: function( response ) {

					$('.updated').remove();
					$(wpml_importer.list).val('');
					wpml_state.set( response, 'updated');
					$('#_wpml_imported').trigger('click');
					wpml_importer.reload({});
				}
			});
		};

		// Fetch movie data 
		wpml.importer.fetch_movie = function( post_id, title ) {

			var post_id = post_id || 0,
			    $tr = $('#fetch_' + post_id).parents('tr'),
			    title = title || $tr.find('.movietitle span.movie_title').text();

			if ( ! post_id )
				return false;

			if ( '0' == $('#p_' + post_id + '_tmdb_data_tmdb_id').val() ) {
				$tr.prop('id', 'p_'+post_id);
				$tr.find('.poster').addClass('loading');
				wpml_import.search_movie( post_id, title );
			}
		};

		/**
		 * Delete a movie import draft
		 */
		wpml.importer.delete_movie = function( movies ) {
			wpml._get({
				data: {	action: 'wpml_delete_movies',
					wpml_check: wpml_ajax.utils.wpml_check,
					movies: movies
				},
				error: function( response ) {
					wpml_state.clear();
					$.each( response.responseJSON.errors, function() {
						wpml_state.set( this, 'error' );
					});
				},
				success: function( response ) {

					$(response.data).each(function() {
						$('#post_'+this).parents('tr, li').fadeToggle().remove();
					});

					if ( ! $(wpml_importer.select + ':checked').length ) {
						wpml_importer.reload({}, 'queued');
						wpml_importer.reload({});
					}
					$('.spinner').hide();
				}
			});
		};

		/**
		 * Get all selected movies and apply bulk fetch data.
		 * 
		 * Call wpml.import.search_movie() on each selected movie to
		 * fetch the related data.
		 */
		wpml.importer.get_movies = function() {

			$(wpml_importer.select + ':checked').each(function( i ) {
				// Set post_id for search_movie
				var post_id = this.value,
				    tr = $(this).parents('tr'),
				    title = tr.find('.movietitle span.movie_title').text();
				tr.prop('id', 'p_'+post_id);

				if ( ! post_id.length )
					return false;

				if ( '0' == $('#p_' + post_id + '_tmdb_data_tmdb_id').val() ) {
					tr.find('.poster').addClass('loading');
					wpml_import.search_movie( post_id, title );
				}
			});
		};

		/**
		 * Reload the movie table. Used when new movies are imported or
		 * when browsing through the table.
		 */
		wpml.importer.reload = function( data, list ) {

			if ( 'queued' == list ) {
				var _selector = '#wpml_import_queue',
				    _rows = '.wp-list-table',
				    _data = {
					action: 'wpml_fetch_queued_movies',
					wpml_fetch_queued_movies_nonce: $('#wpml_fetch_queued_movies_nonce').val(),
				};
			}
			else {
				var _selector = '#wpml_imported',
				    _rows = '.wp-list-table tbody',
				    _data = {
					action: 'wpml_fetch_imported_movies',
					wpml_fetch_imported_movies_nonce: $('#wpml_fetch_imported_movies_nonce').val(),
				};
			}

			var data = $.extend( _data, data );

			wpml._get({
				data: data,
				error: function( response ) {
					wpml_state.clear();
					$.each( response.responseJSON.errors, function() {
						wpml_state.set( this, 'error' );
					});
				},
				success: function( response ) {

					if ( response.data.rows.length )
						$(_rows, _selector).html( response.data.rows );
					if ( response.data.column_headers.length )
						$('thead tr, tfoot tr', _selector).html( response.data.column_headers );
					if ( response.data.pagination.bottom.length )
						$('.tablenav.top .tablenav-pages', _selector).html( $(response.data.pagination.top).html() );
					if ( response.data.pagination.top.length )
						$('.tablenav.bottom .tablenav-pages', _selector).html( $(response.data.pagination.bottom).html() );

					if ( 'queued' == list )
						wpml_importer.update_count( 'import_queue', response.data.total_items, response.i18n.total_items_i18n );
					else
						wpml_importer.update_count( 'imported', response.data.total_items, response.i18n.total_items_i18n );
				},
				complete: function() {
					wpml_import.init();
					wpml_importer.init();
				}
			});
		};

		/**
		 * Navigate through the table pages using navigation arrow links
		 * 
		 * @param    object    Link HTML Object
		 */
		wpml.importer.navigate = function( link ) {
			var query = link.search.substring( 1 );
			var data = {
				paged: wpml.http_query_var( query, 'paged' ) || '1',
				order: wpml.http_query_var( query, 'order' ) || 'asc',
				orderby: wpml.http_query_var( query, 'orderby' ) || 'title'
			};
			wpml_importer.reload( data );
		};

		/**
		 * Navigate through the table pages using pagination input
		 */
		wpml.importer.paginate = function() {
			var data = {
				paged: parseInt( $('input[name=paged]').val() ) || '1',
				order: $('input[name=order]').val() || 'asc',
				orderby: $('input[name=orderby]').val() || 'title'
			};

			window.clearTimeout( wpml_importer.timer );
			wpml_importer.timer = window.setTimeout(function() {
				wpml_importer.reload( data );
			}, wpml_importer.delay);
		};

		/**
		 * Update the menu badges containing movies counts.
		 * 
		 * @param    string    Which menu, queued or imported?
		 * @param    int       Increment or decrement?
		 */
		wpml.importer.update_count = function( wot, i, i_i18n ) {
			
			var wot = ( 'import_queue' == wot ? wot : 'imported' ),
			    i = ( i >= 0 ? i : '0' ),
			    $span = $('#_wpml_' + wot + ' span');

			$span.text( '' + i );

			if ( 'import_queue' == wot ) {
				$('.displaying-num', wpml_queue.queued_list).text( i_i18n );
				$('#_queued_left', wpml_queue.queued_list).text( i );
			}
		}

	wpml.importer.init();