
$ = $ || jQuery;

wpml = wpml || {};

wpml.landing = wpml_landing = {

	showcase: {},
	
	init: function() {},
	widget_toggle: function() {}
};

	/**
	 * Movie Showcase on Landing page
	 * 
	 * Display a nice popup that slides from the right of the screen to
	 * show some informations about movies: poster, title, meta, overview...
	 */
	wpml.landing.showcase = wpml_showcase = {

		_showcase: '#wpml-movie-showcase',
		_showcase_bg: '#wpml-movie-showcase-bg',
		_showcase_open: '.wpml-movie > a',
		_showcase_close: '#wpml-movie-showcase-close',

		init: function() {},
		_open: function() {},
		_close: function() {},
		_resize: function() {},
		_update: function() {},
	};

		/**
		 * Slides in and shows the showcase to visible area
		 */
		wpml.landing.showcase._open = function() {
			$(wpml_showcase._showcase_bg).show().animate( { right: 0 }, 250 );
		};

		/**
		 * Slides out and hide the showcase
		 */
		wpml.landing.showcase._close = function() {
			$(wpml_showcase._showcase_bg).animate( { right: ( 0 - window.innerWidth ) }, 250, function() { $(this).hide() } );
		};

		/**
		 * Automatically adapt the divs to the window's size
		 */
		wpml.landing.showcase._resize = function() {

			$(wpml_showcase._showcase_bg).css({
				width: ( window.innerWidth - 175 ),
				height: ( window.innerHeight - 32 ),
				right: ( 0 - window.innerWidth )
			});

			$(wpml_showcase._showcase).css({
				width: ( window.innerWidth - 214 ),
				height: ( window.innerHeight - 74 )
			});
		};

		/**
		 * Update showcase box with wanted movie data
		 */
		wpml.landing.showcase._update = function( link ) {
			var $link = $(link),
			     data = $.parseJSON( $link.attr('data-movie-meta') ),
			     poster = $link.attr('data-movie-poster'),
			     backdrop = $link.attr('data-movie-backdrop'),
			     rating = $link.attr('data-movie-rating');

			data.overview = data.overview.replace('&amp;', '&').replace('&lt;', '<').replace('&gt;','>');

			$('#wpml-movie-showcase-title').html( data.title );
			$('#wpml-movie-showcase-runtime').text( data.runtime );
			$('#wpml-movie-showcase-release_date').text( data.release_date );
			$('#wpml-movie-showcase-overview').html( data.overview );
			$('#wpml-movie-showcase-inner').css( { backgroundImage: 'url(' + backdrop + ')' } );
			$('#wpml-movie-showcase-poster img').attr( 'src', poster ).attr( 'alt', data.title );
		};

		/**
		 * Showcase and events init
		 */
		wpml.landing.showcase.init = function() {

			$(wpml_showcase._showcase_open).on( 'click', function( e ) {
				e.preventDefault();
				wpml_showcase._update( this );
				wpml_showcase._open();
			});

			$(wpml_showcase._showcase_close).on( 'click', function( e ) {
				e.preventDefault();
				wpml_showcase._close();
			});

			$(window).on( 'resize', function() {
				wpml_showcase._resize();
			});

			wpml_showcase._resize();
		};

	/**
	 * Activate toggle for landing page widgets
	 */
	wpml.landing.widget_toggle = function( link ) {

		var $link = $(link),
		    $thisParent = $link.parent(),
		    $thisContent = $thisParent.find('.inside');

		if ( ! $thisParent.hasClass('exclude' ) ) {
			$('.hndle').each( function() {
				var $parent = $link.parent();
				if ( ! $parent.hasClass('exclude') && ! $parent.hasClass('closed') ) {
					$parent.find('.inside').slideUp( 250, function() {
						$parent.addClass('closed');
					});
				}
			});
		}

		if ( $thisParent.hasClass('closed') )
			$thisContent.slideDown( 250, function() { $thisParent.removeClass('closed'); });
		else
			$thisContent.slideUp( 250, function() { $thisParent.addClass('closed'); });
	};

	/**
	 * Init Landing page
	 */
	wpml.landing.init = function() {

		$('.handlediv').on( 'click', function() {
			wpml_landing.widget_toggle( this );
		});

		wpml_landing.showcase.init();
	};

wpml_landing.init();