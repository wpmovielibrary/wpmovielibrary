
wpml = wpml || {};

var wpml_queue;

	wpml.queue = wpml_queue = {

		current_post_id: undefined,
		current_movie: undefined,
		current_queue: undefined,

		queued_list: '#wpml_import_queue',

		select: '#wpml-queued-list input[type=checkbox]',
		select_all: '#wpml-queued-list-header #post_all',
		
		progress: '#queue_progress',
		progress_value: '#queue_progress_value',
		progress_queued: '#_queued_imported',
	}

		wpml.queue.movies = wpml_movies_queue = {};

			/**
			* Handle Bulk actions
			* 
			* @since    1.0.0
			*/
			wpml.queue.movies.do = function() {

				var $action = $('select[name=queue-action]'),
				    movies = [];

				if ( 'delete' == $action.val() ) {
					$action.nextAll( '.spinner' ).css( { display: 'inline-block' } );
					$( wpml_queue.select + ':checked' ).each( function() {
						movies.push( this.id.replace( 'post_', '' ) );
					});
					wpml_import_movies.delete( movies );
				}
				else if ( 'dequeue' == $action.val() ) {
					$action.nextAll( '.spinner' ).css( { display: 'inline-block' } );
					$( wpml_queue.select + ':checked' ).each( function() {
						movies.push( this.id.replace( 'post_', '' ) );
					});
					wpml_movies_queue.remove( movies );
				}
				else {
					return false;
				}
			};

			/**
			 * Add Movies to the Queue
			 * 
			 * @since    1.0.0
			 * 
			 * @param    array|int    One or more movies to enqueue
			 */
			wpml.queue.movies.add = function( movies ) {

				var queue = [];

				if ( ! $.isArray( movies ) )
					var movies = [ movies ];

				for ( var i = 0; i < movies.length; i++ ) {

					var post_id = movies[ i ];
					wpml_queue.current_post_id = post_id;

					if ( post_id && wpml_queue_utils.get_val( 'tmdb_id' ) )
						queue.push( wpml_queue_utils.prepare_queue() );

					$( '#enqueue_' + post_id + ' .dashicons' ).after( '<span class="spinner"></span>' );
					$( '#enqueue_' + post_id).addClass( 'loading' );
					
				}
				
				wpml._post({
					data: {
						action: 'wpml_enqueue_movies',
						nonce: wpml.get_nonce( 'enqueue-movies' ),
						movies: queue
					},
					error: function( response ) {
						wpml_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpml_state.set( this, 'error' );
						});
					},
					success: function( response ) {
						var message = ( response.data.length > 1 ? wpml_ajax.lang.enqueued_movies : wpml_ajax.lang.enqueued_movie );
						wpml_state.clear();
						wpml_state.set( message.replace( '%s', response.data.length ), 'updated' );
						wpml_import_view.reload( {} );
						wpml_import_view.reload( {}, 'queued' );
						$( '.spinner' ).hide();
						$( '#p_' + post_id + '_tmdb_data' ).remove();
					},
					complete: function( r ) {
						wpml.update_nonce( 'enqueue-movies', r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Remove Movies from the Queue
			 * 
			 * @since    1.0.0
			 * 
			 * @param    array|int    One or more movies to dequeue
			 */
			wpml.queue.movies.remove = function( movies ) {

				var post_id = 0,
				    queue = [];

				if ( ! $.isArray( movies ) )
					var movies = [ movies ];

				for ( var i = 0; i <= movies.length - 1; i++ ) {
					post_id = movies[ i ];
					if ( post_id )
						queue.push( post_id );
				}

				wpml._post({
					data: {	action: 'wpml_dequeue_movies',
						nonce: wpml.get_nonce( 'dequeue-movies' ),
						movies: queue
					},
					error: function( response ) {
						wpml_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpml_state.set( this, 'error' );
						});
					},
					success: function( response ) {

						$( response.data ).each(function() {
							$( '#post_' + this ).parents( 'tr, li' ).fadeToggle().remove();
						});

						var message = ( response.data.length > 1 ? wpml_ajax.lang.dequeued_movies : wpml_ajax.lang.dequeued_movie );
						wpml_state.clear();
						wpml_state.set( message.replace( '%s', response.data.length ), 'updated' );
						wpml_import_view.reload( {} );
						wpml_import_view.reload( {}, 'queued' );
						$( '.spinner' ).hide();
					},
					complete: function( r ) {
						wpml.update_nonce( 'dequeue-movies', r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Import Queued Movies
			 * 
			 * @since    1.0.0
			 */
			wpml.queue.movies.import = function() {

				timer = undefined;

				wpml_queue.current_queue = $( wpml_queue.select + ':checked' );
				$( wpml_queue.current_queue ).each( function( i, movie ) {

					var index = i + 1,
					    post_id = $( this ).val(),
					    $li = $( 'li#p_' + post_id ),
					    $status = $li.find( '.column-status .movie_status' );

					wpml_queue.current_post_id = post_id;

					$.ajaxQueue({
						data: {
							action: 'wpml_import_queued_movie',
							nonce: wpml.get_nonce( 'import-queued-movies' ),
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
							$status.text( '' );
						},
						success: function( response ) {
							$( wpml_queue.progress_value ).val( parseInt( $( wpml_queue.progress_value ).val() ) + 1 );
							$( wpml_queue.progress_queued ).text( index );
							$( wpml_queue.progress ).width( Math.ceil( ( index / wpml_queue.current_queue.length ) * 100 ) + '%' );
						},
						complete: function() {
							$status.text( wpml_ajax.lang.imported );
							wpml.update_nonce( 'import-queued-movies', r.responseJSON.nonce );
						},
					}).done( function() {
						window.clearTimeout( timer );
						timer = window.setTimeout( function() {
							wpml_import_view.reload( {}, 'queued' );
						}, 2000 );
					});
				});
			};

		/**
		 * Utils for Queue
		 */
		wpml.queue.utils = wpml_queue_utils = {};

			/**
			 * Prepare Metadata object
			 * 
			 * @since    1.0.0
			 */
			wpml.queue.utils.prepare_queue = function() {
				return metadata = {
					post_id: wpml_queue.utils.get_val('post_id'),
					tmdb_id: wpml_queue.utils.get_val('tmdb_id'),
					poster: wpml_queue.utils.get_val('poster'),
					meta: {
						title: wpml_queue.utils.get_val('title'),
						original_title: wpml_queue.utils.get_val('original_title'),
						overview: wpml_queue.utils.get_val('overview'),
						production_companies: wpml_queue.utils.get_val('production_companies'),
						production_countries: wpml_queue.utils.get_val('production_countries'),
						spoken_languages: wpml_queue.utils.get_val('spoken_languages'),
						runtime: wpml_queue.utils.get_val('runtime'),
						genres: wpml_queue.utils.get_val('genres'),
						release_date: wpml_queue.utils.get_val('release_date')
					},
					crew: {
						director: wpml_queue.utils.get_val('director'),
						producer: wpml_queue.utils.get_val('producer'),
						photography: wpml_queue.utils.get_val('photography'),
						composer: wpml_queue.utils.get_val('composer'),
						author: wpml_queue.utils.get_val('author'),
						writer: wpml_queue.utils.get_val('writer'),
						cast: wpml_queue.utils.get_val('cast')
					}
				};
			};

			/**
			 * Get spectific field value
			 * 
			 * @since    1.0.0
			 */
			wpml.queue.utils.get_val = function( slug ) {
				return $('input#p_' + wpml_queue.current_post_id + '_tmdb_data_' + slug, '#tmdb_data_form').val() || '';
			};

			wpml.queue.utils.toggle_button = function() {

				if ( $( wpml_queue.select + ':checked' ).length != $( wpml_queue.select ).length )
					$( wpml_queue.select_all ).prop( 'checked', false );
				else
					$( wpml_queue.select_all ).prop( 'checked', true );
			};

			wpml.queue.utils.toggle_inputs = function() {

				if ( ! $( wpml_queue.select_all ).prop( 'checked' ) )
					$( wpml_queue.select ).prop( 'checked', false );
				else
					$( wpml_queue.select ).prop( 'checked', true );
			};
