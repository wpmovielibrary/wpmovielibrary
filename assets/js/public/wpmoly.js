
(function($) {

	window.wpmoly = window.wpmoly || {};

		wpmoly.init = function() {

			$( 'select.wpmoly.list' ).change(function() {
				if ( this.options[ this.selectedIndex ].value.length > 0 )
					location.href = this.options[ this.selectedIndex ].value;
			});

			if ( undefined != $( '#wpmoly-movie-grid.grid > .movie' ) )
				wpmoly.grid_resize();

			$( "#wpmoly-grid-form" ).on( 'submit', function(e) {
				e.preventDefault();
				wpmoly.grid_edit();
			});

			$( "#wpmoly-grid-rows, #wpmoly-grid-columns" ).on( 'change', function() {
				wpmoly.grid_edit();
			});

			$( "#wpmoly-grid-sort-toggle" ).on( 'click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				$( "#wpmoly-movie-grid-menu-2-sorting" ).slideToggle( 200 );
				$( "#wpmoly-movie-grid-menu-2-sorting" ).on( 'click', function(e) {
					e.stopPropagation();
				});
				$( "body" ).addClass( 'waitee' ).on( 'click', function() {
					$( "#wpmoly-movie-grid-menu-2-sorting" ).slideUp( 200 );
					$( "body.waitee" ).removeClass( 'waitee' ).off( 'click' );
				});
			});

			$( "#wpmoly-grid-sort" ).on( 'click', function(e) {
				e.preventDefault();
				wpmoly.grid_sort();
			});

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

			var $movies = $( '#wpmoly-movie-grid.grid .movie' ),
			   $posters = $( '#wpmoly-movie-grid.grid .poster' ),
			 max_height = 0,
			  max_width = 0;

			$.each( $posters, function() {
				var $img = $( this ),
				   width = $img.width(),
				  height = $img.height();

				if ( height > max_height )
					max_height = height;
				if ( width > max_width )
					max_width = width;

			});

			if ( $( '#wpmoly-movie-grid.grid' ).hasClass( 'spaced' ) ) {
				var _poster = {
					height: Math.round( max_width * 1.33 ),
					width: max_width
				},
				     _movie = {
					width: max_width
				};
			} else {
				var _poster = {
					height: Math.round( max_width * 1.33 ),
					width: max_width
				},
				     _movie = _poster;
			}

			$posters.css( _poster );
			$movies.css( _movie );
		};

		wpmoly.grid_edit = function() {

			var rows = $( "#wpmoly-grid-rows" ).val(),
			 columns = $( "#wpmoly-grid-columns" ).val(),
			 columns = parseInt( columns ),
			    rows = parseInt( rows ),
			     url = document.location.href,
			  search = document.location.search;

			if ( '' != search ) {
				if ( ( new RegExp(/\/[0-9]{1,}\:[0-9]{1,}/i) ).test( search ) ) {
					search = search.replace(/columns=[0-9]{1,}/i, 'columns=' + columns );
					search = search.replace(/rows=[0-9]{1,}/i, 'rows=' + rows );
				}
				else {
					search += 'columns=' + columns + '&rows=' + rows;
				}
				document.location.search = search;
			}
			else if ( ( new RegExp(/\/[0-9]{1,}\:[0-9]{1,}/i) ).test( url ) ) {
				url = url.replace(/\/[0-9]{1,}\:[0-9]{1,}/i, '/' + columns + ':' + rows );
				document.location.href = url;
			}
			else {
				if ( -1 === url.indexOf( wpmoly.lang.grid ) )
					url = wpmoly.lang.grid + '/' + columns + ':' + rows + '/';
				else
					url = columns + ':' + rows + '/';
				document.location.href = url;
			}
		};

		wpmoly.grid_sort = function() {

			var order = document.querySelector( "#wpmoly-grid-order" ).value,
			  orderby = document.querySelector( "#wpmoly-grid-orderby" ).value,
			     rows = parseInt( ( document.querySelector( "#wpmoly-grid-rows" ) || {} ).value ),
			  columns = parseInt( ( document.querySelector( "#wpmoly-grid-columns" ) || {} ).value ),
			      url = document.location.href,
			   search = document.location.search;

			/*if ( null !== order )
				order = order.value;*/

			if ( '' != search ) {
				if ( ( new RegExp(/title|date|localdate|year|rating|asc|desc/i) ).test( search ) ) {
					search = search.replace(/title|date|localdate|year|rating/i, 'orderby=' + orderby );
					search = search.replace(/asc|desc/i, 'order=' + order );
				} else {
					search += 'orderby=' + orderby + '&order=' + order;
				}
				document.location.search = search;
			}
			else if ( ( new RegExp(/title|date|localdate|year|rating|asc|desc/i) ).test( url ) ) {
				url = url.replace(/title|date|localdate|year|rating/i, orderby );
				url = url.replace(/asc|desc/i, order );
				document.location.href = url;
			}
			else {

				var cols = '';
				if ( ! isNaN( columns ) && ! isNaN( rows ) )
					cols = columns + ':' + rows + '/';

				if ( -1 === url.indexOf( wpmoly.lang.grid ) )
					url = wpmoly.lang.grid + '/' + cols + orderby + '/' + order;
				else
					url = cols + orderby + '/' + order;

				document.location.href = url;
			}
		};

		wpmoly.init();
	
})(jQuery);