
wpml = wpml || {};

var wpml_importer;

	wpml.importer = wpml_importer = {

		action: '#wpml-import input#doaction, #wpml-import input#doaction2',
		fetch: '#wpml-import .fetch_movie',
		delete: '#wpml-import .delete_movie',
		list: '#wpml_import_list',

		timer: undefined,
		delay: 500,

		init: undefined,
		add: undefined,
		doaction: undefined,
		fetch_movie: undefined,
		delete_movie: undefined,
		get_movies: undefined,
		reload: undefined,
		navigate: undefined,
		paginate: undefined
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
				wpml_importer.add();
			});
		};

		/**
		 * Import a list of movies by title.
		 * 
		 * Call WPML_Import::import_movies_callback() to create movie draft
		 * for each title submitted and update the table
		 */
		wpml.importer.add = function() {

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

		/**
		 * Handle Bulk actions
		 */
		wpml.importer.doaction = function( action ) {

			var action = $('select[name=' + action.replace('do','') + ']');
			$(this).addClass('loader');

			if ( 'tmdb_data' == action.val() ) {
				wpml_importer.get_movies();
			}
			else if ( 'delete' == action.val() ) {
				$('.movies > tbody input[type=checkbox]:checked').each(function() {
					var id = this.id.replace('post_','');
					wpml_importer.delete_movie( id );
				});
			}
			else {
				return false;
			}
		};

		// Fetch movie data 
		wpml.importer.fetch_movie = function( link ) {

			var $link = $(link),
			    post_id = $link.attr('data-post-id'),
			    tr = $link.parents('tr'),
			    title = tr.find('.movietitle span.movie_title').text();

			if ( ! post_id.length )
				return false;

			tr.prop('id', 'p_'+post_id);
			tr.find('.poster').addClass('loading');

			wpml.import.search_movie( post_id, title, tr.find('.poster') );
		};

		/**
		 * Delete a movie import draft
		 */
		wpml.importer.delete_movie = function( id ) {
			wpml.get({
					action: 'wpml_delete_movie',
					wpml_check: ajax_object.wpml_check,
					post_id: id
				},
				function( response ) {
					$('#post_'+id).parents('tr').remove();
					wpml_importer.reload({
						paged: parseInt( $('input[name=paged]').val() ) || '1',
						order: $('input[name=order]').val() || 'asc',
						orderby: $('input[name=orderby]').val() || 'title'
					});
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
			$('.movies > tbody input[type=checkbox]:checked').each(function( i ) {
				// Set post_id for search_movie
				post_id = this.value;
				tr      = $(this).parents('tr');
				tr.prop('id', 'p_'+post_id);

				title = tr.find('.movietitle span.movie_title').text();

				if ( ! post_id.length )
					return false;

				tr.find('.poster').addClass('loading');
				wpml.import.search_movie( post_id, title );
			});
		};

		/**
		 * Reload the movie table. Used when new movies are imported or
		 * when browsing through the table.
		 */
		wpml.importer.reload = function( data ) {

			var data = $.extend({
					action: 'wpml_fetch_imported_movies',
					wpml_fetch_imported_movies_nonce: $('#wpml_fetch_imported_movies_nonce').val(),
				},
				data
			);

			wpml.get(
				data,
				function( response ) {

					var response = $.parseJSON( response );
					if ( response.rows.length )
						$('#the-list').html( response.rows );
					if ( response.column_headers.length )
						$('thead tr, tfoot tr').html( response.column_headers );
					if ( response.pagination.bottom.length )
						$('.tablenav.top .tablenav-pages').html( $(response.pagination.top).html() );
					if ( response.pagination.top.length )
						$('.tablenav.bottom .tablenav-pages').html( $(response.pagination.bottom).html() );

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