
wpml = wpml || {};

var wpml_queue;

	wpml.queue = wpml_queue = {

		import_queued: '#wpml_import_queued',
		select: '#wpml_import_queue input[type=checkbox]',
		enqueue: '#wpml-import .enqueue_movie',

		init: function() {},
		push: function() {}
	}

		wpml.queue.init = function() {

			$(wpml_queue.import_queued).prop('disabled', true);

			$(wpml_queue.select).on( 'click', function( e ) {
				wpml_queue.toggle_button();
			});

			$(wpml_queue.import_queued).on( 'click', function( e ) {
				e.preventDefault();
				wpml_queue.import( this );
			});

			$(wpml_queue.enqueue).on( 'click', function( e ) {
				e.preventDefault();
				wpml_queue.push( this );
			});
			
		};

		wpml.queue.push = function( link ) {

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

			wpml.post({
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
					wpml_importer.reload( {}, 'queued' );

					console.log( response );
				}
			);
		};

		wpml.queue.import = function() {

			
		};

		wpml.queue.toggle_button = function() {
			if ( $('input[type=checkbox]:checked', '#wpml_import_queue').length )
				$(wpml_queue.import_queued).prop('disabled', false);
			else
				$(wpml_queue.import_queued).prop('disabled', true);
		};

	wpml.queue.init();