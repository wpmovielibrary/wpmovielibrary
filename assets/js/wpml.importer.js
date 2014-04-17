
wpml = wpml || {};

var wpml_importer;

	wpml.importer = wpml_importer = {

		action: '#wpml-import input#doaction, #wpml-import input#doaction2',
		select: '.movies > tbody input[type=checkbox]',
		fetch: '#wpml-import .fetch_movie',
		delete: '#wpml-import .delete_movie',
		list: '#wpml_import_list',

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

			$(wpml_importer.action).on( 'click', function( e ) {
				e.preventDefault();
				wpml_importer.doaction( this.id );
			});

			$(wpml_importer.fetch).on( 'click', function( e ) {
				e.preventDefault();
				wpml_importer.fetch_movie( this );
			});

			$(wpml_importer.delete).on( 'click', function( e ) {
				e.preventDefault();
				wpml_importer.delete_movie( $(this).attr('data-post-id') );
			});

			// Nav and sort links click
			$('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').unbind('click').on( 'click', function( e ) {
				e.preventDefault();
				wpml_importer.navigate( this );
			});

			// Update when manually changing page number in input field
			$('input[name=paged]').unbind('keyup').on( 'keyup', function( e ) {
				if ( 13 == e.which )
					e.preventDefault();
				wpml_importer.paginate();
			});

			// Import a list of titles
			$('#wpml_importer').unbind('click').on( 'click', function( e ) {
				e.preventDefault();
				wpml_importer.import();
			});
		};

		/**
		 * Handle Bulk actions
		 */
		wpml.importer.doaction = function( action ) {

			var action = $('select[name=' + action.replace('do','') + ']'),
			    movies = [];

			if ( 'tmdb_data' == action.val() ) {
				wpml_importer.get_movies();
			}
			else if ( 'delete' == action.val() ) {
				$(wpml_importer.select + ':checked').each(function() {
					movies.push( this.id.replace('post_','') );
				});
				wpml_importer.delete_movie( movies );
			}
			else if ( 'enqueue' == action.val() ) {
				$(wpml_importer.select + ':checked').each(function() {
					movies.push( this.id.replace('post_','') );
				});
				wpml.queue.push( movies );
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

			wpml.post({
					action: 'wpml_import_movies',
					wpml_import_list: $(wpml_importer.list).val(),
					wpml_ajax_movie_import: $('#wpml_ajax_movie_import').val()
				},
				function( response ) {
					if ( 0 == response )
						return false;

					$('.updated').remove();
					$(wpml_importer.list).val('');
					$('#wpml-tabs').before('<div class="updated"><p>'+response+'</p></div>');
					$('#_wpml_imported').trigger('click');
					wpml_importer.reload({
						paged: 1,
						order: 'asc',
						orderby: 'title'
					});
				}
			);
		};

		// Fetch movie data 
		wpml.importer.fetch_movie = function( link ) {

			var $link = $(link),
			    post_id = $link.attr('data-post-id'),
			    tr = $link.parents('tr'),
			    title = tr.find('.movietitle span.movie_title').text();

			if ( ! post_id.length )
				return false;

			if ( '0' == $('#p_' + post_id + '_tmdb_data_tmdb_id').val() ) {
				tr.prop('id', 'p_'+post_id);
				tr.find('.poster').addClass('loading');
				wpml.import.search_movie( post_id, title );
			}
		};

		/**
		 * Delete a movie import draft
		 */
		wpml.importer.delete_movie = function( movies ) {
			wpml.get({
					action: 'wpml_delete_movie',
					wpml_check: wpml_ajax.utils.wpml_check,
					post_id: movies
				},
				function( response ) {

					$(movies).each(function() {
						$('#post_'+this).parents('tr, li').fadeToggle().remove();
					});

					wpml_importer.reload({});
				}
			);
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
					wpml.import.search_movie( post_id, title );
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
				    _data = {
					action: 'wpml_fetch_queued_movies',
					wpml_fetch_queued_movies_nonce: $('#wpml_fetch_queued_movies_nonce').val(),
				};
			}
			else {
				var _selector = '#wpml_imported',
				    _data = {
					action: 'wpml_fetch_imported_movies',
					wpml_fetch_imported_movies_nonce: $('#wpml_fetch_imported_movies_nonce').val(),
				};
			}

			var data = $.extend( _data, data );

			wpml.get(
				data,
				function( response ) {

					var response = $.parseJSON( response );

					if ( undefined == response.rows )
						return false;

					if ( response.rows.length )
						$('#the-list', _selector).html( response.rows );
					if ( response.column_headers.length )
						$('thead tr, tfoot tr', _selector).html( response.column_headers );
					if ( response.pagination.bottom.length )
						$('.tablenav.top .tablenav-pages', _selector).html( $(response.pagination.top).html() );
					if ( response.pagination.top.length )
						$('.tablenav.bottom .tablenav-pages', _selector).html( $(response.pagination.bottom).html() );

					if ( 'queued' == list )
						wpml_importer.update_count( 'import_queue', response.total_pages_i18n );
					else
						wpml_importer.update_count( 'imported', response.total_pages_i18n );

					wpml.import.init();
					wpml.importer.init();
				}
			);
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
		wpml.importer.update_count = function( wot, i ) {
			var wot = ( 'import_queue' == wot ? wot : 'imported' ),
			    i = ( 0 >= i ? i : 0 ),
			    $span = $('#_wpml_' + wot + ' span');

			$span.text( '' + i );
		}

	wpml.importer.init();