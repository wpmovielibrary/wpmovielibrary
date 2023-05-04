
wpmoly = wpmoly || {};

wpmoly.settings = wpmoly_settings = {}

	wpmoly.settings.init = function() {

		$( '#wpmoly-sort-meta_used h3, #wpmoly-sort-details_used h3, #wpmoly-movie-archives-movies-meta_used h3' ).text( wpmoly_lang.used );
		$( '#wpmoly-sort-meta_available h3, #wpmoly-sort-details_available h3, #wpmoly-movie-archives-movies-meta_available h3' ).text( wpmoly_lang.available );

		$( '#wpmoly-sort-details_disabled li' ).appendTo( '#wpmoly-sort-details_available' );
		$( '#wpmoly-sort-details_disabled' ).remove();

	};

	wpmoly.settings.init();
