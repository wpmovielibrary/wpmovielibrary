
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	wpmoly.search = {

		runned: false,

		run: function() {

			var $search = $( '#wpmoly-movie-search' ),
			    post_id = $( '#post_ID' ).val();

			if ( $search.length && ! $search.hasClass( 'hidden' ) ) {
				wpmoly.search = new wpmoly.view.Search.Search({
					el         : $search,
					controller : new wpmoly.controller.Search( {}, {
						post_id: post_id
					} )
				});

				wpmoly.search.runned = true;
			}
		}
	};
})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.search );
