
(function($) {

	window.wpmoly = window.wpmoly || {};

		wpmoly.init = function() {

			$( '.wpmoly.list' ).change(function() {
				if ( this.options[ this.selectedIndex ].value.length > 0 )
					location.href = this.options[ this.selectedIndex ].value;
			});

			if ( undefined != $( '#wpmoly-movie-grid > .movie' ) )
				wpmoly.grid_resize();

			$( '.hide-if-js' ).hide();
			$( '.hide-if-no-js' ).removeClass( 'hide-if-no-js' );
		};

		wpmoly.headbox = wpmoly_headbox = {};

			wpmoly.headbox.toggle = function( item, post_id ) {

				var $tab = $( '#movie-headbox-' + item + '-' + post_id ),
				$parent = $( '#movie-headbox-' + post_id ),
				$tabs = $parent.find( '.wpmoly.headbox.movie.content > .content' ),
				$link = $( '#movie-headbox-' + item + '-link-' + post_id );

				if ( undefined != $tab ) {
					$tabs.hide();
					$tab.show();
					$parent.find( 'a.active' ).removeClass( 'active' );
					$link.addClass( 'active' );
				}
			};

		wpmoly.grid_resize = function() {

			var $movies = $( '#wpmoly-movie-grid > .movie' )
			 max_height = 0,
			  max_width = 0;

			$.each( $movies, function() {
				var $img = $( this ).find( 'img.wpmoly.grid.movie.poster' ),
				   width = $img.width(),
				  height = $img.height();

				if ( height > max_height )
					max_height = height;
				if ( width > max_width )
					max_width = width;

			});

			$movies.css({
				height: Math.round( max_width * 1.33 ),
				width: max_width
			});
		};

		wpmoly.init();
	
})(jQuery);