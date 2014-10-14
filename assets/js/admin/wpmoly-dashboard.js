
$ = $ || jQuery;

wpmoly = wpmoly || {};

wpmoly.dashboard = wpmoly_dashboard = {

	_home: '#wpmoly-home',
	_movies: '.wpmoly-movie',
	_screen_options: '#adv-settings input',
	_welcome_panel: '#wpmoly-welcome-panel',

	modal: {},
	widgets: {},

	init: function() {}
};

	/**
	 * Movie Showcase on Landing page
	 * 
	 * Display a nice popup that slides from the right of the screen to
	 * show some informations about movies: poster, title, meta, overview...
	 */
	wpmoly.dashboard.modal = wpmoly_modal = {

		_modal: '#wpmoly-movie-modal',
		_modal_bg: '#wpmoly-movie-modal-bg',
		_modal_open: '.wpmoly-movie > a',
		_modal_close: '#wpmoly-movie-modal-close',

		init: function() {},
		_open: function() {},
		_close: function() {},
		_resize: function() {},
		_update: function() {},
	};

		/**
		 * Slides in and shows the modal to visible area
		 */
		wpmoly.dashboard.modal._open = function() {
			$( wpmoly_modal._modal_bg ).show().animate( { right: 0 }, 250 );
		};

		/**
		 * Slides out and hide the modal
		 */
		wpmoly.dashboard.modal._close = function() {
			$( wpmoly_modal._modal_bg ).animate( { right: ( 0 - window.innerWidth ) }, 250, function() { $( this ).hide() } );
		};

		/**
		 * Automatically adapt the divs to the window's size
		 */
		wpmoly.dashboard.modal._resize = function() {

			$( wpmoly_modal._modal ).css({
				width: ( window.innerWidth - 214 ),
				height: ( window.innerHeight - 74 )
			});
		};

		/**
		 * Update modal box with wanted movie data
		 */
		wpmoly.dashboard.modal._update = function( link ) {
			var $link = $( link ),
			     data = $.parseJSON( $link.attr( 'data-movie-meta' ) ),
			       id = $link.parent( 'div' ).prop( 'id' ).replace( 'movie-', '' ),
			   poster = $link.attr( 'data-movie-poster' ),
			 backdrop = $link.attr( 'data-movie-backdrop' ),
			   rating = $link.attr( 'data-movie-rating' ),
			permalink = $link.attr( 'data-movie-permalink' );

			data.overview = data.overview.replace( '&amp;', '&' ).replace( '&lt;', '<' ).replace( '&gt;','>' );

			$( '#wpmoly-movie-modal-title' ).html( data.title );
			$( '#wpmoly-movie-modal-runtime' ).text( data.runtime );
			$( '#wpmoly-movie-modal-release_date' ).text( data.release_date );
			$( '#wpmoly-movie-modal-genres' ).text( data.genres );
			$( '#wpmoly-movie-modal-overview' ).html( data.overview );
			$( '#wpmoly-movie-modal-inner' ).css( { backgroundImage: 'url( ' + backdrop + ' )' } );
			$( '#wpmoly-movie-modal-poster img' ).attr( 'src', poster ).attr( 'alt', data.title );
			$( '#wpmoly-movie-modal-edit' ).attr( 'href', link.href );
			$( '#wpmoly-movie-modal-view' ).attr( 'href', permalink );

			$( '#wpmoly-movie-modal-rating .wpmoly-movie-rating' ).prop( 'id', 'wpmoly-movie-modal-rating-' + id );
			$( '#wpmoly-movie-modal-' + id ).removeClass().addClass( 'wpmoly-movie-rating wpmoly-movie-rating-' + rating.replace( '.', '-' ) );
			wpmoly_rating.update( $( '#wpmoly-movie-modal-rating-' + id ), rating );
		};

		/**
		 * Showcase and events init
		 */
		wpmoly.dashboard.modal.init = function() {

			$( wpmoly_modal._modal_open ).unbind( 'click' ).on( 'click', function( e ) {
				if ( ! $( this ).parent( '.wpmoly-movie' ).hasClass( 'modal' ) )
					return;
				e.preventDefault();
				wpmoly_modal._update( this );
				wpmoly_modal._open();
			});

			$( wpmoly_modal._modal_close ).unbind( 'click' ).on( 'click', function( e ) {
				e.preventDefault();
				wpmoly_modal._close();
			});

			$( window ).on( 'resize', function() {
				wpmoly_modal._resize();
			});

			wpmoly_modal._resize();
		};

	/**
	 * Plugin Dashboard Widgets
	 */
	wpmoly.dashboard.widgets = wpmoly_widgets = {

		_edit: '.edit-box',
		_handle: '.handlediv',
		_metabox: '.meta-box-sortables',

		widget_toggle: function() {},
		init: function() {}
	};

		/**
		 * Latest Movies Widget
		 */
		wpmoly.dashboard.widgets.latest_movies = wpmoly_latest_movies = {

			timer: undefined,
			delay: 500,
			action: 'wpmoly_save_dashboard_widget_settings',
			widget: 'WPMOLY_Dashboard_Latest_Movies_Widget',
			nonce_name: 'save-wpmoly-dashboard-latest-movies-widget',

			_year: '.movie-year',
			_rating: '.movie-rating',
			_movies: '.wpmoly-movie',
			_movies_per_page: '#latest_movies_movies_per_page',
			_loadmore: '#latest_movies_load_more',
			_quickedit: '.movie-quickedit',
			_checkbox: '#wpmoly-latest-movies-widget-config input[type=checkbox]',
			_show_year: '#latest_movies_show_year',
			_container: '#wpmoly_dashboard_latest_movies_widget',
			_container_main: '#wpmoly_dashboard_latest_movies_widget .main',
		};

			/**
			 * Toggle Widget's settings
			 * 
			 * TODO: Nonce
			 * 
			 * @since    1.0
			 * 
			 * @param    string     Setting ID
			 * @param    boolean    Toggle status
			 */
			wpmoly.dashboard.widgets.latest_movies.toggle_setting = function( id, status ) {

				var action = id.replace( 'latest_movies_', '' );

				switch ( action ) {
					case 'show_year':
						$( wpmoly_latest_movies._year, wpmoly_latest_movies._container_main ).toggle( status );
						$( wpmoly_latest_movies._movies, wpmoly_latest_movies._container_main ).toggleClass( 'with-year', status );
						break;
					case 'show_rating':
						$( wpmoly_latest_movies._rating, wpmoly_latest_movies._container_main ).toggle( status );
						$( wpmoly_latest_movies._movies, wpmoly_latest_movies._container_main ).toggleClass( 'with-rating', status );
						break;
					case 'style_posters':
						console.log( status );
						$( wpmoly_latest_movies._movies, wpmoly_latest_movies._container_main ).toggleClass( 'stylized', status );
						break;
					case 'style_metabox':
						$( wpmoly_latest_movies._container ).toggleClass( 'no-style', status );
						break;
					case 'show_more':
						$( wpmoly_latest_movies._loadmore, wpmoly_latest_movies._container ).toggleClass( 'hide-if-js hide-if-no-js', ! status );
						break;
					case 'show_modal':
						$( wpmoly_latest_movies._movies, wpmoly_latest_movies._container_main ).toggleClass( 'modal', status );
						if ( ! status )
							$( wpmoly_modal._modal_open ).unbind( 'click' );
						else
							wpmoly.dashboard.modal.init();
						break;
					case 'show_quickedit':
						$( wpmoly_latest_movies._quickedit, wpmoly_latest_movies._container_main ).toggleClass( 'hide-if-js hide-if-no-js', ! status );
						break;
					default:
						break;
				};

				wpmoly._post({
					data: {
						action: wpmoly_latest_movies.action,
						widget: wpmoly_latest_movies.widget,
						nonce: wpmoly.get_nonce( wpmoly_latest_movies.nonce_name ),
						setting: action,
						value: ( true === status ? 1 : 0 )
					},
					complete: function( r ) {
						wpmoly.update_nonce( wpmoly_latest_movies.nonce_name, r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Load more movies
			 * 
			 * Default limit is 8; if no offset is set, use the
			 * total number of movies currently showed in the Widget.
			 * 
			 * TODO: Nonce
			 * 
			 * @since    1.0
			 * 
			 * @param    int    Number of movies to load
			 * @param    int    Starting at which offset
			 */
			wpmoly.dashboard.widgets.latest_movies.load_more = function( limit, offset, replace ) {

				if ( null == limit )
					var limit = 8;
				if ( null == offset )
					var offset = $( wpmoly_latest_movies._movies, wpmoly_latest_movies._container_main ).length;

				var replace = ( true === replace ? true : false );

				wpmoly._get({
					data: {
						action: 'wpmoly_load_more_movies',
						widget: wpmoly_latest_movies.widget,
						nonce: wpmoly.get_nonce( 'load-more-widget-movies' ),
						offset: offset,
						limit: limit
					},
					beforeSend: function() {
						$( wpmoly_latest_movies._loadmore ).find( 'span' ).css( { opacity: 0 } );
						$( wpmoly_latest_movies._loadmore ).append( '<span class="spinner"></span>' );
					},
					success: function( data ) {
						if ( '2' == data ) {
							$( wpmoly_latest_movies._loadmore ).addClass( 'disabled' );
							return true;
						}

						if ( replace )
							$( wpmoly_latest_movies._container_main ).empty();

						$( wpmoly_latest_movies._container_main ).append( data );
						wpmoly_dashboard.resize_posters();
					},
					complete: function( r ) {
						$( wpmoly_latest_movies._loadmore ).find( 'span' ).css( { opacity: 1.0 } );
						$( wpmoly_latest_movies._loadmore ).find( '.spinner' ).remove();
						wpmoly.update_nonce( wpmoly_latest_movies.nonce_name, r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Update movies per page value
			 * 
			 * TODO: Nonce
			 * 
			 * @since    1.0
			 * 
			 * @param    int    Movies per page
			 */
			wpmoly.dashboard.widgets.latest_movies.movies_per_page = function( n ) {

				var offset = $( wpmoly_latest_movies._movies, wpmoly_latest_movies._container_main ).length;
				if ( 0 > n || 999 < n || isNaN( n ) )
					var n = 8;
				
				if ( n < offset ) {
					$( wpmoly_latest_movies._movies, wpmoly_latest_movies._container_main ).each(function( i, movie ) {
						if ( i >= n )
							$(movie).remove();
					});
				}
				else {
					$( wpmoly_latest_movies._movies, wpmoly_latest_movies._container_main ).remove();
					wpmoly_latest_movies.load_more( n, 0, true );
				}

				wpmoly._post({
					data: {
						action: wpmoly_latest_movies.action,
						widget: wpmoly_latest_movies.widget,
						nonce: wpmoly.get_nonce( wpmoly_latest_movies.nonce_name ),
						setting: 'movies_per_page',
						value: n
					},
					complete: function( r ) {
						wpmoly.update_nonce( wpmoly_latest_movies.nonce_name, r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Init Widget Events
			 */
			wpmoly.dashboard.widgets.latest_movies.init = function() {

				$( wpmoly_latest_movies._checkbox ).on( 'click', function() {
					wpmoly_latest_movies.toggle_setting( this.id, this.checked );
				});

				$( wpmoly_latest_movies._loadmore ).on( 'click', function( e ) {
					e.preventDefault();
					if ( $( this ).hasClass( 'disabled' ) )
						return;
					wpmoly_latest_movies.load_more( '', null, false );
				});

				$( wpmoly_latest_movies._movies_per_page ).on( 'input', function() {
					var n = this.value;
					window.clearTimeout( wpmoly_latest_movies.timer );
					wpmoly_latest_movies.timer = window.setTimeout( function() {
						wpmoly_latest_movies.movies_per_page( n );
					}, wpmoly_latest_movies.delay );
				});
			};

		/**
		 * Latest Movies Widget
		 */
		wpmoly.dashboard.widgets.most_rated_movies = wpmoly_most_rated_movies = {

			timer: undefined,
			delay: 500,
			action: 'wpmoly_save_dashboard_widget_settings',
			widget: 'WPMOLY_Dashboard_Most_Rated_Movies_Widget',
			nonce_name: 'save-wpmoly-dashboard-most-rated-movies-widget',

			_year: '.movie-year',
			_rating: '.movie-rating',
			_movies: '.wpmoly-movie',
			_movies_per_page: '#most_rated_movies_movies_per_page',
			_loadmore: '#most_rated_movies_load_more',
			_quickedit: '.movie-quickedit',
			_checkbox: '#wpmoly-most-rated-movies-widget-config input[type=checkbox]',
			_show_year: '#most_rated_movies_show_year',
			_container: '#wpmoly_dashboard_most_rated_movies_widget',
			_container_main: '#wpmoly_dashboard_most_rated_movies_widget .main',
		};

			/**
			 * Toggle Widget's settings
			 * 
			 * TODO: Nonce
			 * 
			 * @since    1.0
			 * 
			 * @param    string     Setting ID
			 * @param    boolean    Toggle status
			 */
			wpmoly.dashboard.widgets.most_rated_movies.toggle_setting = function( id, status ) {

				var action = id.replace( 'most_rated_movies_', '' );

				switch ( action ) {
					case 'show_year':
						$( wpmoly_most_rated_movies._year, wpmoly_most_rated_movies._container_main ).toggle( status );
						$( wpmoly_most_rated_movies._movies, wpmoly_most_rated_movies._container_main ).toggleClass( 'with-year', status );
						break;
					case 'show_rating':
						$( wpmoly_most_rated_movies._rating, wpmoly_most_rated_movies._container_main ).toggle( status );
						$( wpmoly_most_rated_movies._movies, wpmoly_most_rated_movies._container_main ).toggleClass( 'with-rating', status );
						break;
					case 'style_posters':
						console.log( status );
						$( wpmoly_most_rated_movies._movies, wpmoly_most_rated_movies._container_main ).toggleClass( 'stylized', status );
						break;
					case 'style_metabox':
						$( wpmoly_most_rated_movies._container ).toggleClass( 'no-style', status );
						break;
					case 'show_more':
						$( wpmoly_most_rated_movies._loadmore, wpmoly_most_rated_movies._container ).toggleClass( 'hide-if-js hide-if-no-js', ! status );
						break;
					case 'show_modal':
						$( wpmoly_most_rated_movies._movies, wpmoly_most_rated_movies._container_main ).toggleClass( 'modal', status );
						if ( ! status )
							$( wpmoly_modal._modal_open ).unbind( 'click' );
						else
							wpmoly.dashboard.modal.init();
						break;
					case 'show_quickedit':
						$( wpmoly_most_rated_movies._quickedit, wpmoly_most_rated_movies._container_main ).toggleClass( 'hide-if-js hide-if-no-js', ! status );
						break;
					default:
						break;
				};

				wpmoly._post({
					data: {
						action: wpmoly_most_rated_movies.action,
						widget: wpmoly_most_rated_movies.widget,
						nonce: wpmoly.get_nonce( wpmoly_most_rated_movies.nonce_name ),
						setting: action,
						value: ( true === status ? 1 : 0 )
					},
					complete: function( r ) {
						wpmoly.update_nonce( wpmoly_most_rated_movies.nonce_name, r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Load more movies
			 * 
			 * Default limit is 8; if no offset is set, use the
			 * total number of movies currently showed in the Widget.
			 * 
			 * TODO: Nonce
			 * 
			 * @since    1.0
			 * 
			 * @param    int    Number of movies to load
			 * @param    int    Starting at which offset
			 */
			wpmoly.dashboard.widgets.most_rated_movies.load_more = function( limit, offset, replace ) {

				if ( null == limit )
					var limit = 4;
				if ( null == offset )
					var offset = $( wpmoly_most_rated_movies._movies, wpmoly_most_rated_movies._container_main ).length;

				var replace = ( true === replace ? true : false );

				wpmoly._get({
					data: {
						action: 'wpmoly_load_more_movies',
						widget: wpmoly_most_rated_movies.widget,
						nonce: wpmoly.get_nonce( 'load-more-widget-movies' ),
						offset: offset,
						limit: limit
					},
					beforeSend: function() {
						$( wpmoly_most_rated_movies._loadmore ).find( 'span' ).css( { opacity: 0 } );
						$( wpmoly_most_rated_movies._loadmore ).append( '<span class="spinner"></span>' );
					},
					success: function( data ) {
						if ( '2' == data ) {
							$( wpmoly_most_rated_movies._loadmore ).addClass( 'disabled' );
							return true;
						}

						if ( replace )
							$( wpmoly_most_rated_movies._container_main ).empty();

						$( wpmoly_most_rated_movies._container_main ).append( data );
						wpmoly_dashboard.resize_posters();
					},
					complete: function( r ) {
						$( wpmoly_most_rated_movies._loadmore ).find( 'span' ).css( { opacity: 1.0 } );
						$( wpmoly_most_rated_movies._loadmore ).find( '.spinner' ).remove();
					}
				});
			};

			/**
			 * Update movies per page value
			 * 
			 * TODO: Nonce
			 * 
			 * @since    1.0
			 * 
			 * @param    int    Movies per page
			 */
			wpmoly.dashboard.widgets.most_rated_movies.movies_per_page = function( n ) {

				var offset = $( wpmoly_most_rated_movies._movies, wpmoly_most_rated_movies._container_main ).length;
				if ( 0 > n || 999 < n || isNaN( n ) )
					var n = 8;
				
				if ( n < offset ) {
					$( wpmoly_most_rated_movies._movies, wpmoly_most_rated_movies._container_main ).each(function( i, movie ) {
						if ( i >= n )
							$(movie).remove();
					});
				}
				else {
					$( wpmoly_most_rated_movies._movies, wpmoly_most_rated_movies._container_main ).remove();
					wpmoly_most_rated_movies.load_more( n, 0, true );
				}

				wpmoly._post({
					data: {
						action: wpmoly_most_rated_movies.action,
						widget: wpmoly_most_rated_movies.widget,
						nonce: wpmoly.get_nonce( wpmoly_most_rated_movies.nonce_name ),
						setting: 'movies_per_page',
						value: n
					},
					complete: function( r ) {
						wpmoly.update_nonce( wpmoly_most_rated_movies.nonce_name, r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Init Widget Events
			 */
			wpmoly.dashboard.widgets.most_rated_movies.init = function() {

				$( wpmoly_most_rated_movies._checkbox ).on( 'click', function() {
					wpmoly_most_rated_movies.toggle_setting( this.id, this.checked );
				});

				$( wpmoly_most_rated_movies._loadmore ).on( 'click', function( e ) {
					e.preventDefault();
					if ( $( this ).hasClass( 'disabled' ) )
						return;
					wpmoly_most_rated_movies.load_more( '', null, false );
				});

				$( wpmoly_most_rated_movies._movies_per_page ).on( 'input', function() {
					var n = this.value;
					window.clearTimeout( wpmoly_most_rated_movies.timer );
					wpmoly_most_rated_movies.timer = window.setTimeout( function() {
						wpmoly_most_rated_movies.movies_per_page( n );
					}, wpmoly_most_rated_movies.delay );
				});
			};

		/**
		* Activate toggle for dashboard page widgets
		* 
		* @since    1.0
		* 
		* @param    object     Link's DOM Element
		*/
		wpmoly_widgets.toggle = function( link ) {

			var $link = $( link ),
			    $thisParent = $link.parent(),
			    $thisContent = $thisParent.find( '.inside' );

			if ( ! $thisParent.hasClass( 'exclude' ) ) {
				$( '.hndle' ).each( function() {
					var $parent = $link.parent();
					if ( ! $parent.hasClass( 'exclude' ) && ! $parent.hasClass( 'closed' ) ) {
						$parent.find( '.inside' ).slideUp( 250, function() {
							$parent.addClass( 'closed' );
						});
					}
				});
			}

			if ( $thisParent.hasClass( 'closed' ) )
				$thisContent.slideDown( 250, function() { $thisParent.removeClass( 'closed' ); });
			else
				$thisContent.slideUp( 250, function() { $thisParent.addClass( 'closed' ); });
		};

		/**
		* Show/Hide plugin Widgets config part 
		* 
		* @since    1.0
		* 
		* @param    object     Link's DOM Element
		* @param    boolean    True to show config part, false to hide
		*/
		wpmoly_widgets.config_toggle = function( link, status ) {

			var status = ( true === status ? true : false );

			var $link = $( link ),
			    $thisParent = $link.parents( '.postbox' ),
			    $main = $thisParent.find('.main'),
			$config = $thisParent.find('.main-config');

			if ( status ) {
				$config.slideDown( 250 );
				$thisParent.find( '.close-box' ).css( { display: 'inline' } );
				$thisParent.find( '.open-box' ).css( { display: 'none' } );
			}
			else {
				$config.slideUp( 250 );
				$thisParent.find( '.close-box' ).css( { display: 'none' } );
				$thisParent.find( '.open-box' ).css( { display: '' } );
			}
		};

		/**
		 * Init Widgets Events
		 */
		wpmoly_widgets.init = function() {

			$( wpmoly_widgets._handle ).on( 'click', function() {
				wpmoly_widgets.toggle( this );
			});

			$( wpmoly_widgets._edit, wpmoly_dashboard._home ).on( 'click', function( e ) {
				e.preventDefault();
				wpmoly_widgets.config_toggle( this, $( this ).hasClass( 'open-box' ) );
			});

			$( wpmoly_widgets._metabox ).sortable();

			wpmoly.dashboard.widgets.latest_movies.init();
			wpmoly.dashboard.widgets.most_rated_movies.init();
		};

	/**
	 * Update Plugin's Dashboard screen options
	 * 
	 * @since    1.0
	 * 
	 * @param    string     Option ID
	 * @param    boolean    Option value
	 */
	wpmoly.dashboard.update_screen_option = function( option, status ) {

		var option = option.replace( 'show_wpmoly_', '' )
		     $elem = $( '#wpmoly_dashboard_' + option + '_widget' ),
		    $input = $( '#show_wpmoly_' + option );

		if ( null == status )
			status = $input.prop( 'checked' );

		var visible = ( true === status ? 1 : 0 );

		if ( undefined == $elem )
			return;

		$elem.toggleClass( 'hidden hide-if-js', ! status );
		$input.prop( 'checked', status );

		wpmoly._post({
			data: {
				action: 'wpmoly_save_screen_option',
				screenoptionnonce: $( '#screenoptionnonce' ).val(),
				screenid: adminpage,
				option: option,
				visible: visible
			},
			complete: function( r ) {
				wpmoly.update_nonce( 'screenoptionnonce', r.responseJSON.nonce );
			}
		});
	};

	/**
	 * Resize Dashboard movie posters to fit screen size
	 */
	wpmoly.dashboard.resize_posters = function() {

		var $movies = $( wpmoly_dashboard._movies ),
		  container = $movies.parents('.postbox').width(),
		      width = $movies.width(),
		     height = $movies.height();

		if  ( 420 >= container )
			var _width = '49.5%';
		else if ( 700 >= container )
			var _width = '32.2%';
		else if ( 700 < container && 1024 >= container )
			var _width = '22%';
		else if ( 1024 < container )
			var _width = '18%';

		$movies.css( { width: _width } );
		$movies.css( { height: Math.ceil( $movies.width() * 1.5 ) } );
	};

	/**
	 * Init Landing page
	 */
	wpmoly.dashboard.init = function() {

		$( wpmoly_dashboard._screen_options ).on( 'click', function() {
			wpmoly_dashboard.update_screen_option( this.id, this.checked );
		});

		$( window ).on( 'resize', function() {
			wpmoly_dashboard.resize_posters();
		});

		wpmoly_dashboard.resize_posters();
		wpmoly_dashboard.modal.init();
		wpmoly_dashboard.widgets.init();
	};

wpmoly_dashboard.init();