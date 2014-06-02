
$ = $ || jQuery;

wpml = wpml || {};

wpml.dashboard = wpml_dashboard = {

	_handle: '.handlediv',
	_metabox: '.meta-box-sortables',
	_screen_options: '#adv-settings input',
	_welcome_panel: '#wpml-welcome-panel',
	_welcome_panel_show: '#show_wpml_welcome_panel',
	_welcome_panel_close: '#wpml-welcome-panel-close',

	modal: {},
	
	init: function() {},
	widget_toggle: function() {}
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

			$( wpml_modal._modal_open ).on( 'click', function( e ) {
				e.preventDefault();
				wpml_modal._update( this );
				wpml_modal._open();
			});

			$( wpml_modal._modal_close ).on( 'click', function( e ) {
				e.preventDefault();
				wpml_modal._close();
			});

			$( window ).on( 'resize', function() {
				wpml_modal._resize();
			});

			wpml_modal._resize();
		};

	/**
	 * Activate toggle for dashboard page widgets
	 * 
	 * @since    1.0.0
	 * 
	 * @param    object     Link's DOM Element
	 */
	wpml.dashboard.widget_toggle = function( link ) {

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
	 * Init Landing page
	 */
	wpml.dashboard.init = function() {

		$( wpml_dashboard._welcome_panel_close, wpml_dashboard._welcome_panel ).on( 'click', function( e ) {
			e.preventDefault();
			wpml_dashboard.update_screen_option( 'welcome_panel', false );
		});

		$( wpml_dashboard._screen_options ).on( 'click', function() {
			wpml_dashboard.update_screen_option( this.id, this.checked );
		});

		$( wpml_dashboard._handle ).on( 'click', function() {
			wpml_dashboard.widget_toggle( this );
		});

		$( wpml_dashboard._metabox ).sortable();

		wpml_dashboard.modal.init();
	};

wpml_dashboard.init();