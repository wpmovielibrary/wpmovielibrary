
$ = jQuery;

wpml = wpml || {};

	wpml.updates = wpml_updates = {};

		wpml.updates.enqueue_movie = function( id ) {

			var $link = $( '#queue-movie-' + id );
			    $link.attr( 'onclick', 'wpml.updates.dequeue_movie( ' + id + ' ); return false;' );
			    $link.find( '.dashicons' ).removeClass( 'dashicons-yes' ).addClass( 'dashicons-no-alt' );
		};

		wpml.updates.dequeue_movie = function( id ) {

			var $link = $( '#queue-movie-' + id );
			    $link.attr( 'onclick', 'wpml.updates.enqueue_movie( ' + id + ' ); return false;' );
			    $link.find( '.dashicons' ).removeClass( 'dashicons-no-alt' ).addClass( 'dashicons-yes' );
		};

		wpml.updates.update_movie = function( id ) {
			
		};

		wpml.updates.update_movies = function() {
			
		};
