
$ = jQuery;

wpml = wpml || {};

	wpml.updates = wpml_updates = {};

		wpml.updates.movies = wpml_update_movies = {};

			wpml.updates.movies.enqueue = function( id ) {

				var $link = $( '#queue-movie-' + id ),
				    $tr = $( 'tr#movie-' + id );

				$link.attr( 'onclick', 'wpml.updates.movies.dequeue( ' + id + ' ); return false;' );
				$link.find( '.dashicons' ).removeClass( 'dashicons-yes' ).addClass( 'dashicons-no-alt' );
				$tr.toggleClass( 'active' );

				wpml_update_progress.update_total( $( '#deprecated-movies tr.active' ).length );
			};

			wpml.updates.movies.dequeue = function( id ) {

				var $link = $( '#queue-movie-' + id ),
				    $tr = $( 'tr#movie-' + id );

				$link.attr( 'onclick', 'wpml.updates.movies.enqueue( ' + id + ' ); return false;' );
				$link.find( '.dashicons' ).removeClass( 'dashicons-no-alt' ).addClass( 'dashicons-yes' );
				$tr.toggleClass( 'active' );

				wpml_update_progress.update_total( $( '#deprecated-movies tr.active' ).length );
			};

			wpml.updates.movies.update = function( id ) {

				wpml._post({
					data: {
						action: 'wpml_update_movie',
						nonce: wpml.get_nonce( 'update-movie' ),
						movie_id: id
					},
					error: function( response ) {
						wpml_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpml_state.set( this, 'error' );
						});
						$( '#update-movies-log' ).append( '<span class="dashicons dashicons-no-alt"></span> Movie #' + id + ' « <em>' + $tr.find( '.movie-title' ).text() + '</em> » not updated' );
					},
					success: function( response ) {
						var $tr = $( 'tr#movie-' + id );

						$tr.find( '.dashicons-arrow-right-alt2' ).removeClass( 'dashicons-arrow-right-alt2' ).addClass( 'dashicons-yes' );
						$tr.find( '.update-movie, .queue-movie' ).remove();
						$( '#updated-movies' ).append( $tr );
						$( '#update-movies-log' ).append( '<span class="update-movies-log-entry"><span class="dashicons dashicons-yes"></span> Movie #' + id + ' « <em>' + $tr.find( '.movie-title' ).text() + '</em> » updated succesfully</span>' );
						$( '#update-movies-log' ).scrollTop( Math.round( $( '.update-movies-log-entry' ).last().position().top + $( '.update-movies-log-entry' ).last().height() ) );
					},
					complete: function( r ) {
						wpml.update_nonce( 'update-movie', r.responseJSON.nonce );
					}
				});
			};

			wpml.updates.movies.update_all = function() {
				
			};

		wpml.updates.progress = wpml_update_progress = {

			_number: '#update-movies-count',
			_total: '#update-movies-total'
		};

			wpml.updates.progress.update_counter = function( number ) {

				$( wpml_update_progress._number ).text( number );
			};

			wpml.updates.progress.update_total = function( total ) {

				$( wpml_update_progress._total ).text( total );
			};
