
document.addEventListener( 'DOMContentLoaded', function() {
	[ 'wpmoly_movie_actor', 'wpmoly_movie_collection', 'wpmoly_movie_genre' ].forEach( ( taxonomy ) => {
		if ( `edit-${ taxonomy }` === pagenow ) {
			document.querySelectorAll( '.toplevel_page_wpmovielibrary' ).forEach( ( el ) => {
				el.classList.remove( 'wp-not-current-submenu' );
				el.classList.add( 'wp-has-current-submenu', 'wp-menu-open' );
			});
			document.querySelectorAll( `.wp-submenu [href*="edit-tags.php?taxonomy=${ taxonomy }&post_type=movie"]` ).forEach( ( el ) => {
				el.classList.add( 'current' );
				el.parentElement.classList.add( 'current' );
			});
		}
	} );
} );