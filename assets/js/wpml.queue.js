
wpml = wpml || {};

var wpml_queue;

	wpml.queue = wpml_queue = {

		action: '#do-queue-action',
		import_queued: '#wpml_import_queued',
		select: '#wpml-queued-list input[type=checkbox]',
		select_all: '#wpml-queued-list-header #post_all',
		enqueue: '#wpml-import .enqueue_movie',
		dequeue: '#wpml-queued-list .dequeue_movie',

		// Queued movies
		queued_list: '#wpml_import_queue',
		// Imported movies
		imported_list: '#wpml_imported',

		init: function() {},
		doaction: function() {},
		_enqueue: function() {},
		_dequeue: function() {}
	}

		wpml.queue.init = function() {

			$(wpml_queue.import_queued, wpml_queue.queued_list).prop('disabled', true);

			$(wpml_queue.select, wpml_queue.queued_list).on( 'click', function() {
				wpml_queue.toggle_button();
			});

			$(wpml_queue.select_all, wpml_queue.queued_list).on( 'click', function() {
				wpml_queue.toggle_inputs();
			});

			$(wpml_queue.action, wpml_queue.queued_list).on( 'click', function( e ) {
				e.preventDefault();
				wpml_queue.doaction();
			});

			$(wpml_queue.import_queued, wpml_queue.queued_list).on( 'click', function( e ) {
				e.preventDefault();
				wpml_queue.import( this );
			});
			
		};

		/**
		 * Handle Bulk actions
		 */
		wpml.queue.doaction = function() {

			var action = $('select[name=queue-action]'),
			    movies = [];

			if ( 'delete' == action.val() ) {
				$(wpml_queue.select + ':checked').each(function() {
					movies.push( this.id.replace('post_','') );
				});
				wpml_importer.delete_movie( movies );
			}
			else if ( 'dequeue' == action.val() ) {
				$(wpml_queue.select + ':checked').each(function() {
					movies.push( this.id.replace('post_','') );
				});
				wpml_queue._dequeue( movies );
			}
			else {
				return false;
			}
		};

		wpml.queue._enqueue = function( link ) {

			var $link = $(link),
			    tr = $link.parents('tr'),
			    title = tr.find('.movietitle span.movie_title').text();
			post_id = $link.attr('data-post-id');

			if ( ! post_id.length || ! _get_val('tmdb_id') )
				return false;

			var metadata = {
				post_id: _get_val('post_id'),
				tmdb_id: _get_val('tmdb_id'),
				poster: _get_val('poster'),
				meta: {
					title: _get_val('title'),
					original_title: _get_val('original_title'),
					overview: _get_val('overview'),
					production_companies: _get_val('production_companies'),
					production_countries: _get_val('production_countries'),
					spoken_languages: _get_val('spoken_languages'),
					runtime: _get_val('runtime'),
					genres: _get_val('genres'),
					release_date: _get_val('release_date')
				},
				crew: {
					director: _get_val('director'),
					producer: _get_val('producer'),
					photography: _get_val('photography'),
					composer: _get_val('composer'),
					author: _get_val('author'),
					writer: _get_val('writer'),
					cast: _get_val('cast')
				}
			};

			function _get_val( wot ) {
				return $('input#p_' + post_id + '_tmdb_data_' + wot, '#tmdb_data_form').val() || false;
			}

			wpml._post({
					action: 'wpml_enqueue_movies',
					wpml_ajax_movie_enqueue: $('#wpml_ajax_movie_enqueue').val(),
					post_id: metadata.post_id,
					title: metadata.meta.title,
					metadata: metadata
				},
				function( response ) {
					
					wpml_importer.update_count( 'imported', -1 );
					wpml_importer.update_count( 'import_queue', 1 );

					tr.remove();
					wpml_importer.reload({
						paged: parseInt( $('input[name=paged]').val() ) || '1',
						order: $('input[name=order]').val() || 'asc',
						orderby: $('input[name=orderby]').val() || 'title'
					});
					wpml_importer.reload( {} );
					wpml_importer.reload( {}, 'queued' );
				}
			);
		};

		wpml.queue._dequeue = function( movies ) {

			

			/*wpml._post({
					action: 'wpml_dequeue_movies',
					wpml_ajax_movie_dequeue: $('#wpml_ajax_movie_dequeue').val(),
					post_id: movies
				},
				function( response ) {

					$(movies).each(function() {
						$('#post_'+this).parents('tr, li').fadeToggle().remove();
					});

					wpml_importer.reload({});
					wpml_importer.reload({}, 'queued');
				}
			);*/
		};

		wpml.queue.import = function() {

			
		};

		wpml.queue.toggle_button = function() {
			if ( $('input[type=checkbox]:checked', '#wpml_import_queue').length )
				$(wpml_queue.import_queued).prop('disabled', false);
			else
				$(wpml_queue.import_queued).prop('disabled', true);

			if ( $(wpml_queue.select + ':checked').length != $(wpml_queue.select).length )
				$(wpml_queue.select_all).prop( 'checked', false );
			else
				$(wpml_queue.select_all).prop( 'checked', true );
		};

		wpml.queue.toggle_inputs = function() {

			if ( ! $(wpml_queue.select_all).prop('checked') )
				$(wpml_queue.select).prop( 'checked', false );
			else
				$(wpml_queue.select).prop( 'checked', true );
		};

	wpml.queue.init();