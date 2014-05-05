
wpml = wpml || {};

var wpml_queue;

	wpml.queue = wpml_queue = {

		current_post_id: undefined,
		current_movie: undefined,
		current_queue: undefined,

		action: '#do-queue-action',
		import_queued: '#wpml_import_queued',
		select: '#wpml-queued-list input[type=checkbox]',
		select_all: '#wpml-queued-list-header #post_all',
		enqueue: '#wpml-import .enqueue_movie',
		dequeue: '#wpml-queued-list .dequeue_movie',

		progress: '#queue_progress',
		progress_value: '#queue_progress_value',
		progress_queued: '#_queued_imported',

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

			$(wpml_queue.select, wpml_queue.queued_list).unbind('click').on( 'click', function() {
				wpml_queue.toggle_button();
			});

			$(wpml_queue.select_all, wpml_queue.queued_list).unbind('click').on( 'click', function() {
				wpml_queue.toggle_inputs();
			});

			$(wpml_queue.action, wpml_queue.queued_list).unbind('click').on( 'click', function( e ) {
				e.preventDefault();
				wpml_queue.doaction();
			});

			$(wpml_queue.import_queued, wpml_queue.queued_list).unbind('click').on( 'click', function( e ) {
				e.preventDefault();
				wpml_queue.import();
			});
			
		};

		/**
		 * Handle Bulk actions
		 */
		wpml.queue.doaction = function() {

			var $action = $('select[name=queue-action]'),
			    movies = [];

			if ( 'delete' == $action.val() ) {
				$action.nextAll('.spinner').css({display: 'inline-block'});
				$(wpml_queue.select + ':checked').each(function() {
					movies.push( this.id.replace('post_','') );
				});
				wpml_importer.delete_movie( movies );
			}
			else if ( 'dequeue' == $action.val() ) {
				$action.nextAll('.spinner').css({display: 'inline-block'});
				$(wpml_queue.select + ':checked').each(function() {
					movies.push( this.id.replace('post_','') );
				});
				wpml_queue._dequeue( movies );
			}
			else {
				return false;
			}
		};

		wpml.queue._enqueue = function( movies ) {

			var queue = [];

			for ( var i = 0; i < movies.length; i++ ) {

				var post_id = movies[ i ];
				wpml_queue.current_post_id = post_id;

				if ( post_id && wpml_queue._get_val('tmdb_id') )
					queue.push( wpml_queue._prepare_queue() );
			}
			
			wpml._post({
				data: {
					action: 'wpml_enqueue_movies',
					wpml_ajax_movie_enqueue: $('#wpml_ajax_movie_enqueue').val(),
					movies: queue
				},
				error: function( response ) {
					wpml_state.clear();
					$.each( response.responseJSON.errors, function() {
						wpml_state.set( this, 'error' );
					});
				},
				success: function( response ) {
					wpml_importer.reload( {} );
					wpml_importer.reload( {}, 'queued' );
					$('.spinner').hide();
					$('#p_' + post_id + '_tmdb_data').remove();
				}
			});
		};

		wpml.queue._prepare_queue = function() {
			return metadata = {
				post_id: wpml_queue._get_val('post_id'),
				tmdb_id: wpml_queue._get_val('tmdb_id'),
				poster: wpml_queue._get_val('poster'),
				meta: {
					title: wpml_queue._get_val('title'),
					original_title: wpml_queue._get_val('original_title'),
					overview: wpml_queue._get_val('overview'),
					production_companies: wpml_queue._get_val('production_companies'),
					production_countries: wpml_queue._get_val('production_countries'),
					spoken_languages: wpml_queue._get_val('spoken_languages'),
					runtime: wpml_queue._get_val('runtime'),
					genres: wpml_queue._get_val('genres'),
					release_date: wpml_queue._get_val('release_date')
				},
				crew: {
					director: wpml_queue._get_val('director'),
					producer: wpml_queue._get_val('producer'),
					photography: wpml_queue._get_val('photography'),
					composer: wpml_queue._get_val('composer'),
					author: wpml_queue._get_val('author'),
					writer: wpml_queue._get_val('writer'),
					cast: wpml_queue._get_val('cast')
				}
			};
		};

		wpml.queue._get_val = function( slug ) {
			return $('input#p_' + wpml_queue.current_post_id + '_tmdb_data_' + slug, '#tmdb_data_form').val() || '';
		};

		wpml.queue._dequeue = function( movies ) {

			var post_id = 0,
			      queue = [];

			for ( var i = 0; i <= movies.length - 1; i++ ) {
				post_id = movies[ i ];
				if ( post_id )
					queue.push( post_id );
			}

			wpml._post({
				data: {	action: 'wpml_dequeue_movies',
					wpml_ajax_movie_dequeue: $('#wpml_ajax_movie_dequeue').val(),
					movies: queue
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

					wpml_importer.reload({});
					wpml_importer.reload({}, 'queued');
					$('.spinner').hide();
				}
			});
		};

		wpml.queue.import = function() {

			timer = undefined;

			wpml_queue.current_queue = $(wpml_queue.select + ':checked');
			$(wpml_queue.current_queue).each( function( i, movie ) {

				var index = i + 1,
				    post_id = $(this).val(),
				    $li = $('li#p_' + post_id ),
				    $status = $li.find('.column-status .movie_status');

				wpml_queue.current_post_id = post_id;

				$.ajaxQueue({
					data: {
						action: 'wpml_import_queued_movie',
						wpml_movie_import_queue: $('#wpml_movie_import_queue').val(),
						post_id: post_id
					},
					beforeSend: function() {
						$status.text( wpml_ajax.lang.in_progress );
						window.clearTimeout( timer );
						timer = window.setTimeout(function() {
							var t = $status.text();
							if ( '...' != t.substring( t.length, t.length - 3 ) )
								$status.text( t + '.' );
							else
								$status.text( wpml_ajax.lang.in_progress );
						}, 1000 );
					},
					error: function( response ) {
						wpml_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpml_state.set( this, 'error' );
						});
						$status.text('');
					},
					success: function( response ) {
						$(wpml_queue.progress_value).val( parseInt( $(wpml_queue.progress_value).val() ) + 1 );
						$(wpml_queue.progress_queued).text( index );
						$(wpml_queue.progress).width( Math.ceil( ( index / wpml_queue.current_queue.length ) * 100 ) + '%' );
					},
					complete: function() {
						$status.text( wpml_ajax.lang.imported );
					},
				}).done(function() {
					window.clearTimeout( timer );
					timer = window.setTimeout(function() {
						wpml_importer.reload({}, 'queued');
					}, 2000 );
				});
			});
		};

		wpml.queue.toggle_button = function() {

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