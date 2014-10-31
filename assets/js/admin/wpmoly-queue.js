
wpmoly = wpmoly || {};

var wpmoly_queue;

	wpmoly.queue = wpmoly_queue = {

		current_post_id: undefined,
		current_movie: undefined,
		current_queue: undefined,

		queued_list: '#wpmoly_import_queue',

		select: '#wpmoly-queued-list input[type=checkbox]',
		select_all: '#wpmoly-queued-list-header #post_all',
		
		progress: '#queue_progress',
		progress_block: '#queue_progress_block',
		progress_value: '#queue_progress_value',
		progress_status: '#queue_status',
		progress_status_message: '#queue_status_message',
		progress_queued: '#_queued_imported',
		progress_left: '#_queued_left',
	}

		wpmoly.queue.movies = wpmoly_movies_queue = {};

			/**
			* Handle Bulk actions
			* 
			* @since    1.0
			*/
			wpmoly.queue.movies.do = function() {

				var $action = $('select[name=queue-action]'),
				    movies = [];

				if ( 'delete' == $action.val() ) {
					$action.nextAll( '.spinner' ).css( { display: 'inline-block' } );
					$( wpmoly_queue.select + ':checked' ).each( function() {
						movies.push( this.id.replace( 'post_', '' ) );
					});
					wpmoly_import_movies.delete( movies );
				}
				else if ( 'dequeue' == $action.val() ) {
					$action.nextAll( '.spinner' ).css( { display: 'inline-block' } );
					$( wpmoly_queue.select + ':checked' ).each( function() {
						movies.push( this.id.replace( 'post_', '' ) );
					});
					wpmoly_movies_queue.remove( movies );
				}
				else {
					return false;
				}
			};

			/**
			 * Add Movies to the Queue
			 * 
			 * @since    1.0
			 * 
			 * @param    array|int    One or more movies to enqueue
			 */
			wpmoly.queue.movies.add = function( movies ) {

				var queue = [];

				if ( ! $.isArray( movies ) )
					var movies = [ movies ];

				for ( var i = 0; i < movies.length; i++ ) {

					var post_id = movies[ i ];
					wpmoly_queue.current_post_id = post_id;

					if ( post_id && '' != wpmoly_queue_utils.get_val( 'tmdb_id' ) ) {
						queue.push( wpmoly_queue_utils.prepare_queue() );

						$( '#enqueue_' + post_id + ' .wpmolicon' ).after( '<span class="spinner"></span>' );
						$( '#enqueue_' + post_id).addClass( 'loading' );
					}
				}

				if ( ! queue.length ) {
					wpmoly_state.clear();
					wpmoly_state.set( wpmoly_lang.missing_meta, 'warning' );
					return false;
				}

				wpmoly._post({
					data: {
						action: 'wpmoly_enqueue_movies',
						nonce: wpmoly.get_nonce( 'enqueue-movies' ),
						movies: queue
					},
					error: function( response ) {
						wpmoly_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpmoly_state.set( this, 'error' );
						});
					},
					success: function( response ) {
						var message = ( response.data.length > 1 ? wpmoly_lang.enqueued_movies : wpmoly_lang.enqueued_movie );
						wpmoly_state.clear();
						wpmoly_state.set( message.replace( '%s', response.data.length ), 'updated' );
						wpmoly_import_view.reload( {} );
						wpmoly_import_view.reload( {}, 'queued' );
						$( '.spinner' ).hide();
						$( '#p_' + post_id + '_meta_data' ).remove();
					},
					complete: function( r ) {
						wpmoly.update_nonce( 'enqueue-movies', r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Remove Movies from the Queue
			 * 
			 * @since    1.0
			 * 
			 * @param    array|int    One or more movies to dequeue
			 */
			wpmoly.queue.movies.remove = function( movies ) {

				var post_id = 0,
				    queue = [];

				if ( ! $.isArray( movies ) )
					var movies = [ movies ];

				for ( var i = 0; i <= movies.length - 1; i++ ) {
					post_id = movies[ i ];
					if ( post_id )
						queue.push( post_id );
				}

				wpmoly._post({
					data: {	action: 'wpmoly_dequeue_movies',
						nonce: wpmoly.get_nonce( 'dequeue-movies' ),
						movies: queue
					},
					error: function( response ) {
						wpmoly_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpmoly_state.set( this, 'error' );
						});
					},
					success: function( response ) {

						$( response.data ).each(function() {
							$( '#post_' + this ).parents( 'tr, li' ).fadeToggle().remove();
						});

						var message = ( response.data.length > 1 ? wpmoly_lang.dequeued_movies : wpmoly_lang.dequeued_movie );
						wpmoly_state.clear();
						wpmoly_state.set( message.replace( '%s', response.data.length ), 'updated' );
						wpmoly_import_view.reload( {} );
						wpmoly_import_view.reload( {}, 'queued' );
						$( '.spinner' ).hide();
					},
					complete: function( r ) {
						wpmoly.update_nonce( 'dequeue-movies', r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Import Queued Movies
			 * 
			 * @since    1.0
			 */
			wpmoly.queue.movies.import = function() {

				timer = undefined;

				wpmoly_queue.current_queue = $( wpmoly_queue.select + ':checked' );
				$( wpmoly_queue.current_queue ).each( function( i, movie ) {

					var index = i + 1,
					    post_id = $( this ).val(),
					    $li = $( 'li#p_' + post_id ),
					    $status = $li.find( '.column-status .movie_status' );

					wpmoly_queue.current_post_id = post_id;

					$.ajaxQueue({
						data: {
							action: 'wpmoly_import_queued_movie',
							nonce: wpmoly.get_nonce( 'import-queued-movies' ),
							post_id: post_id
						},
						beforeSend: function() {
							$status.text( wpmoly_lang.in_progress );
							window.clearTimeout( timer );
							timer = window.setTimeout(function() {
								var t = $status.text();
								if ( '...' != t.substring( t.length, t.length - 3 ) )
									$status.text( t + '.' );
								else
									$status.text( wpmoly_lang.in_progress );
							}, 1000 );
						},
						error: function( response ) {
							wpmoly_state.clear();
							$.each( response.responseJSON.errors, function() {
								wpmoly_state.set( this, 'error' );
							});
							$status.text( '' );
						},
						success: function( response ) {
							var progress = Math.ceil( ( index / wpmoly_queue.current_queue.length ) * 100 );
							$( wpmoly_queue.progress_value ).val( parseInt( $( wpmoly_queue.progress_value ).val() ) + 1 );
							$( wpmoly_queue.progress_queued ).text( index );
							$( wpmoly_queue.progress ).animate( { width:progress  + '%' }, 250 );
							if ( 100 == progress ) {
								$( wpmoly_queue.progress_status_message ).css( { display: 'inline-block' } ).text( wpmoly_lang.done );
								$( wpmoly_queue.progress_status ).hide();
							}
						},
						complete: function() {
							$status.text( wpmoly_lang.imported );
							wpmoly.update_nonce( 'import-queued-movies', r.responseJSON.nonce );
						},
					}).done( function() {
						window.clearTimeout( timer );
						timer = window.setTimeout( function() {
							wpmoly_import_view.reload( {}, 'queued' );
						}, 2000 );
					});
				});
			};

		/**
		 * Utils for Queue
		 */
		wpmoly.queue.utils = wpmoly_queue_utils = {};

			/**
			 * Prepare Metadata object
			 * 
			 * @since    1.0
			 */
			wpmoly.queue.utils.prepare_queue = function() {
				return metadata = {
					post_id: wpmoly_queue.utils.get_val('post_id'),
					tmdb_id: wpmoly_queue.utils.get_val('tmdb_id'),
					poster: wpmoly_queue.utils.get_val('poster'),
					title: wpmoly_queue.utils.get_val('title'),
					original_title: wpmoly_queue.utils.get_val('original_title'),
					overview: wpmoly_queue.utils.get_val('overview'),
					production_companies: wpmoly_queue.utils.get_val('production_companies'),
					production_countries: wpmoly_queue.utils.get_val('production_countries'),
					spoken_languages: wpmoly_queue.utils.get_val('spoken_languages'),
					runtime: wpmoly_queue.utils.get_val('runtime'),
					genres: wpmoly_queue.utils.get_val('genres'),
					release_date: wpmoly_queue.utils.get_val('release_date'),
					director: wpmoly_queue.utils.get_val('director'),
					producer: wpmoly_queue.utils.get_val('producer'),
					photography: wpmoly_queue.utils.get_val('photography'),
					composer: wpmoly_queue.utils.get_val('composer'),
					author: wpmoly_queue.utils.get_val('author'),
					writer: wpmoly_queue.utils.get_val('writer'),
					cast: wpmoly_queue.utils.get_val('cast')
				};
			};

			/**
			 * Get spectific field value
			 * 
			 * @since    1.0
			 */
			wpmoly.queue.utils.get_val = function( slug ) {
				return $('input#p_' + wpmoly_queue.current_post_id + '_meta_data_' + slug, '#meta_data_form').val() || '';
			};

			wpmoly.queue.utils.toggle_button = function() {

				if ( $( wpmoly_queue.select + ':checked' ).length != $( wpmoly_queue.select ).length )
					$( wpmoly_queue.select_all ).prop( 'checked', false );
				else
					$( wpmoly_queue.select_all ).prop( 'checked', true );
			};

			wpmoly.queue.utils.toggle_inputs = function() {

				if ( ! $( wpmoly_queue.select_all ).prop( 'checked' ) )
					$( wpmoly_queue.select ).prop( 'checked', false );
				else
					$( wpmoly_queue.select ).prop( 'checked', true );
			};

			wpmoly.queue.utils.update_progress = function() {

				var checked = $( wpmoly_queue.select + ':checked' ).length;
				if ( checked ) {
					$( wpmoly_queue.progress_left ).text( checked );
					$( wpmoly_queue.progress_block ).addClass('visible');
				}
				else {
					$( wpmoly_queue.progress_left ).text( '0' );
					$( wpmoly_queue.progress_block ).removeClass('visible');
				}
			};

			wpmoly.queue.utils.init = function() {
				$( wpmoly_queue.select + ', ' + wpmoly_queue.select_all ).on( 'click', function() {
					wpmoly_queue_utils.update_progress();
				});
			};

		wpmoly_queue_utils.init();
