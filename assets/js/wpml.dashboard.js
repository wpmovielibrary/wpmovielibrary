
$ = $ || jQuery;

wpml = wpml || {};

wpml.dashboard = wpml_dashboard = {

	_home: '#wpml-home',
	_movies: '.wpml-movie',
	_screen_options: '#adv-settings input',
	_welcome_panel: '#wpml-welcome-panel',
	_welcome_panel_show: '#show_wpml_welcome_panel',
	_welcome_panel_close: '#wpml-welcome-panel-close',

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
	wpml.dashboard.modal = wpml_modal = {

		_modal: '#wpml-movie-modal',
		_modal_bg: '#wpml-movie-modal-bg',
		_modal_open: '.wpml-movie > a',
		_modal_close: '#wpml-movie-modal-close',

		init: function() {},
		_open: function() {},
		_close: function() {},
		_resize: function() {},
		_update: function() {},
	};

		/**
		 * Slides in and shows the modal to visible area
		 */
		wpml.dashboard.modal._open = function() {
			$( wpml_modal._modal_bg ).show().animate( { right: 0 }, 250 );
		};

		/**
		 * Slides out and hide the modal
		 */
		wpml.dashboard.modal._close = function() {
			$( wpml_modal._modal_bg ).animate( { right: ( 0 - window.innerWidth ) }, 250, function() { $( this ).hide() } );
		};

		/**
		 * Automatically adapt the divs to the window's size
		 */
		wpml.dashboard.modal._resize = function() {

			$( wpml_modal._modal ).css({
				width: ( window.innerWidth - 214 ),
				height: ( window.innerHeight - 74 )
			});
		};

		/**
		 * Update modal box with wanted movie data
		 */
		wpml.dashboard.modal._update = function( link ) {
			var $link = $( link ),
			     data = $.parseJSON( $link.attr( 'data-movie-meta' ) ),
			   poster = $link.attr( 'data-movie-poster' ),
			 backdrop = $link.attr( 'data-movie-backdrop' ),
			   rating = $link.attr( 'data-movie-rating' ),
			 editlink = $link.attr( 'data-movie-edit-link' );

			data.overview = data.overview.replace( '&amp;', '&' ).replace( '&lt;', '<' ).replace( '&gt;','>' );

			$( '#wpml-movie-modal-title' ).html( data.title );
			$( '#wpml-movie-modal-runtime' ).text( data.runtime );
			$( '#wpml-movie-modal-release_date' ).text( data.release_date );
			$( '#wpml-movie-modal-genres' ).text( data.genres );
			$( '#wpml-movie-modal-overview' ).html( data.overview );
			$( '#wpml-movie-modal-inner' ).css( { backgroundImage: 'url( ' + backdrop + ' )' } );
			$( '#wpml-movie-modal-poster img' ).attr( 'src', poster ).attr( 'alt', data.title );
			$( '#wpml-movie-modal-rating' ).empty().append( '<div id="movie-rating-display" class="movie_rating_title stars stars-' + rating.replace( '.', '-' ) + '"></div>' );
			$( '#wpml-movie-modal-edit' ).attr( 'href', editlink );
			$( '#wpml-movie-modal-view' ).attr( 'href', link.href );
		};

		/**
		 * Showcase and events init
		 */
		wpml.dashboard.modal.init = function() {

			$( wpml_modal._modal_open ).unbind( 'click' ).on( 'click', function( e ) {
				if ( ! $( this ).parent( '.wpml-movie' ).hasClass( 'modal' ) )
					return;
				e.preventDefault();
				wpml_modal._update( this );
				wpml_modal._open();
			});

			$( wpml_modal._modal_close ).unbind( 'click' ).on( 'click', function( e ) {
				e.preventDefault();
				wpml_modal._close();
			});

			$( window ).unbind( 'resize' ).on( 'resize', function() {
				wpml_modal._resize();
			});

			wpml_modal._resize();
		};

	/**
	 * Plugin Dashboard Widgets
	 */
	wpml.dashboard.widgets = wpml_widgets = {

		_edit: '.edit-box',
		_handle: '.handlediv',
		_metabox: '.meta-box-sortables',

		widget_toggle: function() {},
		init: function() {}
	};

		/**
		 * Latest Movies Widget
		 */
		wpml.dashboard.widgets.latest_movies = wpml_latest_movies = {

			_year: '.movie-year',
			_rating: '.movie-rating',
			_movies: '.wpml-movie',
			_loadmore: '#latest_movies_load_more',
			_quickedit: '.movie-quickedit',
			_checkbox: '#wpml-latest-movies-widget-config input[type=checkbox]',
			_show_year: '#latest_movies_show_year',
			_container: '#wpml_dashboard_latest_movies_widget',
			_container_main: '#wpml_dashboard_latest_movies_widget .main',
		};

			/**
			 * Toggle Widget's settings
			 * 
			 * TODO: Nonce
			 * 
			 * @since    1.0.0
			 * 
			 * @param    string     Setting ID
			 * @param    boolean    Toggle status
			 */
			wpml.dashboard.widgets.latest_movies.toggle_setting = function( id, status ) {

				var action = id.replace( 'latest_movies_', '' );

				switch ( action ) {
					case 'show_year':
						$( wpml_latest_movies._year, wpml_latest_movies._container_main ).toggle( status );
						$( wpml_latest_movies._movies, wpml_latest_movies._container_main ).toggleClass( 'with-year', status );
						break;
					case 'show_rating':
						$( wpml_latest_movies._rating, wpml_latest_movies._container_main ).toggle( status );
						$( wpml_latest_movies._movies, wpml_latest_movies._container_main ).toggleClass( 'with-rating', status );
						break;
					case 'style_posters':
						console.log( status );
						$( wpml_latest_movies._movies, wpml_latest_movies._container_main ).toggleClass( 'stylized', status );
						break;
					case 'style_metabox':
						$( wpml_latest_movies._container ).toggleClass( 'no-style', status );
						break;
					case 'show_more':
						$( wpml_latest_movies._loadmore, wpml_latest_movies._container_main ).toggleClass( 'hide-if-js hide-if-no-js', ! status );
						break;
					case 'show_modal':
						$( wpml_latest_movies._movies, wpml_latest_movies._container_main ).toggleClass( 'modal', status );
						if ( ! status )
							$( wpml_modal._modal_open ).unbind( 'click' );
						else
							wpml.dashboard.modal.init();
						break;
					case 'show_quickedit':
						$( wpml_latest_movies._quickedit, wpml_latest_movies._container_main ).toggleClass( 'hide-if-js hide-if-no-js', ! status );
						break;
					default:
						break;
				};

				wpml._post({
					data: {
						action: 'wpml_save_dashboard_widget_settings',
						widget: 'WPML_Dashboard_Latest_Movies_Widget',
						setting: action,
						value: ( true === status ? 1 : 0 )
					}
				});
			};

			/**
			 * Init Widget Events
			 */
			wpml.dashboard.widgets.latest_movies.init = function() {

				$( wpml_latest_movies._checkbox ).on( 'click', function() {
					wpml_latest_movies.toggle_setting( this.id, this.checked );
				});
			};

		/**
		* Activate toggle for dashboard page widgets
		* 
		* @since    1.0.0
		* 
		* @param    object     Link's DOM Element
		*/
		wpml_widgets.toggle = function( link ) {

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
		* @since    1.0.0
		* 
		* @param    object     Link's DOM Element
		* @param    boolean    True to show config part, false to hide
		*/
		wpml_widgets.config_toggle = function( link, status ) {

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
		wpml_widgets.init = function() {

			$( wpml_widgets._handle ).on( 'click', function() {
				wpml_widgets.toggle( this );
			});

			$( wpml_widgets._edit, wpml_dashboard._home ).on( 'click', function( e ) {
				e.preventDefault();
				wpml_widgets.config_toggle( this, $( this ).hasClass( 'open-box' ) );
			});

			$( wpml_widgets._metabox ).sortable();

			wpml.dashboard.widgets.latest_movies.init();
		};

	/**
	 * Update Plugin's Dashboard screen options
	 * 
	 * @since    1.0.0
	 * 
	 * @param    string     Option ID
	 * @param    boolean    Option value
	 */
	wpml.dashboard.update_screen_option = function( option, status ) {

		var option = option.replace( 'show_wpml_', '' )
		     $elem = $( '#wpml_dashboard_' + option + '_widget' ),
		    $input = $( '#show_wpml_' + option );

		if ( null == status )
			status = $input.prop( 'checked' );

		var visible = ( true === status ? 1 : 0 );

		if ( undefined == $elem )
			return;

		$elem.toggleClass( 'hidden hide-if-js', ! status );
		$input.prop( 'checked', status );

		wpml._post({
			data: {
				action: 'wpml_save_screen_option',
				screenoptionnonce: $( '#screenoptionnonce' ).val(),
				screenid: adminpage,
				option: option,
				visible: visible
			}
		});
	};

	/**
	 * Resize Dashboard movie posters to fit screen size
	 */
	wpml.dashboard.resize_posters = function() {

		var $movies = $( wpml_dashboard._movies )
		  container = $movies.parents('.postbox').width(),
		      width = $movies.width(),
		     height = $movies.height();

		if ( 1200 < container )
			var _width = '21.6%';
		else if ( 700 < container )
			var _width = '24.2%';
		else if ( 700 >= container )
			var _width = '32.2%';

		$movies.css( { width: _width } );
		$movies.css( { height: Math.ceil( $movies.width() * 1.5 ) } );
	};

	/**
	 * Init Landing page
	 */
	wpml.dashboard.init = function() {

		$( wpml_dashboard._screen_options ).on( 'click', function() {
			wpml_dashboard.update_screen_option( this.id, this.checked );
		});

		$( wpml_dashboard._welcome_panel_close, wpml_dashboard._welcome_panel ).on( 'click', function( e ) {
			e.preventDefault();
			wpml_dashboard.update_screen_option( 'welcome_panel', false );
		});

		$( window ).on( 'resize', function() {
			wpml_dashboard.resize_posters();
		});

		

		wpml_dashboard.resize_posters();
		wpml_dashboard.modal.init();
		wpml_dashboard.widgets.init();
	};

wpml_dashboard.init();