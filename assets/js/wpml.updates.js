
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
