<?php
/**
 * WP_Widget Class extension.
 * 
 * WPML provides specific Widgets: Recent Movies, Most Rated Movies,
 * Collections, Genres, Actors…
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Recent_Movies_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Most_Rated_Movies_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Collections_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Genres_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Actors_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Media_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Status_Widget");' ) );

/**
 * Recent Movies Widget.
 * 
 * Display a list of the most recently added Movies. Options: Title, Description,
 * Number of movies to show.
 * 
 * @since    1.0.0
 */
class WPML_Recent_Movies_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			'wpml-recent-movies-widget',
			__( 'WPML Recent Movies', 'wpml' ),
			array(
				'classname'	=>	'wpml-recent-movies-widget',
				'description'	=>	__( 'Display most recently added Movies.', 'wpml' )
			)
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;

		include( plugin_dir_path( __FILE__ ) . '../views/recent-movies-widget.php' );

		echo $after_widget;
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['description'] = strip_tags( $new_instance['description'] );
		$instance['number']      = intval( $new_instance['number'] );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance
		);

		$title        = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Recent Movies', 'wpml' ) );
		$description  = ( isset( $instance['description'] ) ? $instance['description'] : __( 'Movies I recently added to my library', 'wpml' ) );
		$number       = ( isset( $instance['number'] ) ? $instance['number'] : 4 );

		// Display the admin form
		include( plugin_dir_path(__FILE__) . '../views/recent-movies-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}


/**
 * Most Rated Movies Widget.
 * 
 * Display a list of the most rated Movies. Options: Title, Description,
 * Number of movies to show, where to show the rating.
 * 
 * @since    1.0.0
 */
class WPML_Most_Rated_Movies_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			'wpml-most-rated-movies-widget',
			__( 'WPML Most Rated Movies', 'wpml' ),
			array(
				'classname'	=>	'wpml-most-rated-movies-widget',
				'description'	=>	__( 'Display most rated Movies.', 'wpml' )
			)
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;

		include( plugin_dir_path( __FILE__ ) . '../views/most-rated-movies-widget.php' );

		echo $after_widget;
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['description']    = strip_tags( $new_instance['description'] );
		$instance['number']         = intval( $new_instance['number'] );
		$instance['display_rating'] = strip_tags( $new_instance['display_rating'] );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance
		);

		$title          = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Most Rated Movies', 'wpml' ) );
		$description    = ( isset( $instance['description'] ) ? $instance['description'] : __( 'Movies I really enjoyed', 'wpml' ) );
		$number         = ( isset( $instance['number'] ) ? $instance['number'] : 4 );
		$display_rating = ( isset( $instance['display_rating'] ) ? $instance['display_rating'] : 'no' );

		// Display the admin form
		include( plugin_dir_path(__FILE__) . '../views/most-rated-movies-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}


/**
 * Collections Widget.
 * 
 * Display a list of the Movies Collections. Options: Title, Show as dropdown,
 * Show Movie count, custom style.
 * 
 * @since    1.0.0
 */
class WPML_Collections_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			'wpml-collections-widget',
			__( 'WPML Collections', 'wpml' ),
			array(
				'classname'	=>	'wpml-collections-widget',
				'description'	=>	__( 'Display Movie Collections Lists', 'wpml' )
			)
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;

		include( plugin_dir_path( __FILE__ ) . '../views/collections-widget.php' );

		echo $after_widget;
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['list']  = intval( $new_instance['list'] );
		$instance['count'] = intval( $new_instance['count'] );
		$instance['css']   = intval( $new_instance['css'] );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance
		);

		$title = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Movie Collections', 'wpml' ) );
		$list  = ( isset( $instance['list'] ) ? $instance['list'] : 1 );
		$count = ( isset( $instance['count'] ) ? $instance['count'] : 0 );
		$css   = ( isset( $instance['css'] ) ? $instance['css'] : 0 );

		// Display the admin form
		include( plugin_dir_path(__FILE__) . '../views/collections-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}


/**
 * Genres Widget.
 * 
 * Display a list of the Movies Genres. Options: Title, Show as dropdown,
 * Show Movie count, custom style.
 * 
 * @since    1.0.0
 */
class WPML_Genres_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			'wpml-genres-widget',
			__( 'WPML Genres', 'wpml' ),
			array(
				'classname'	=>	'wpml-genres-widget',
				'description'	=>	__( 'Display Movie Genres Lists', 'wpml' )
			)
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;

		include( plugin_dir_path( __FILE__ ) . '../views/genres-widget.php' );

		echo $after_widget;
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['list']  = intval( $new_instance['list'] );
		$instance['count'] = intval( $new_instance['count'] );
		$instance['css']   = intval( $new_instance['css'] );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance
		);

		$title = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Movie Genres', 'wpml' ) );
		$list  = ( isset( $instance['list'] ) ? $instance['list'] : 1 );
		$count = ( isset( $instance['count'] ) ? $instance['count'] : 0 );
		$css   = ( isset( $instance['css'] ) ? $instance['css'] : 0 );

		// Display the admin form
		include( plugin_dir_path(__FILE__) . '../views/genres-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}


/**
 * Actors Widget.
 * 
 * Display a list of the Movies Actors. Options: Title, Show as dropdown,
 * Show Movie count, custom style.
 * 
 * @since    1.0.0
 */
class WPML_Actors_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			'wpml-actors-widget',
			__( 'WPML Actors', 'wpml' ),
			array(
				'classname'	=>	'wpml-actors-widget',
				'description'	=>	__( 'Display Movie Actors Lists', 'wpml' )
			)
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;

		include( plugin_dir_path( __FILE__ ) . '../views/actors-widget.php' );

		echo $after_widget;
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['list']  = intval( $new_instance['list'] );
		$instance['count'] = intval( $new_instance['count'] );
		$instance['css']   = intval( $new_instance['css'] );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance
		);

		$title = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Movie Actors', 'wpml' ) );
		$list  = ( isset( $instance['list'] ) ? $instance['list'] : 1 );
		$count = ( isset( $instance['count'] ) ? $instance['count'] : 0 );
		$css   = ( isset( $instance['css'] ) ? $instance['css'] : 0 );

		// Display the admin form
		include( plugin_dir_path(__FILE__) . '../views/actors-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}


/**
 * Media Widget.
 * 
 * Display a list of the Movies from a specific Media. Options: Title, Description,
 * Media type, Show as dropdown.
 * 
 * @since    1.0.0
 */
class WPML_Media_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			'wpml-media-widget',
			__( 'WPML Media', 'wpml' ),
			array(
				'classname'	=>	'wpml-media-widget',
				'description'	=>	__( 'Display Movies from a specific media', 'wpml' )
			)
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;

		include( plugin_dir_path( __FILE__ ) . '../views/media-widget.php' );

		echo $after_widget;
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['description'] = strip_tags( $new_instance['description'] );
		$instance['type'] = strip_tags( $new_instance['type'] );
		$instance['list'] = intval( $new_instance['list'] );
		$instance['thumbnails'] = intval( $new_instance['thumbnails'] );
		$instance['css']   = intval( $new_instance['css'] );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance
		);

		$title = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Movie by Media', 'wpml' ) );
		$description = ( isset( $instance['description'] ) ? $instance['description'] : '' );
		$type  = ( isset( $instance['type'] ) ? $instance['type'] : null );
		$list  = ( isset( $instance['list'] ) ? $instance['list'] : 0 );
		$thumbnails = ( isset( $instance['thumbnails'] ) ? $instance['thumbnails'] : 0 );
		$css   = ( isset( $instance['css'] ) ? $instance['css'] : 0 );

		// Display the admin form
		require_once( plugin_dir_path(__FILE__) . '../class-wpmovielibrary.php' );
		$wpml = WPMovieLibrary::get_instance();
		include( plugin_dir_path(__FILE__) . '../views/media-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}


/**
 * Status Widget.
 * 
 * Display a list of the Movies from a specific Status. Options: Title, Description,
 * Status, Show as dropdown.
 * 
 * @since    1.0.0
 */
class WPML_Status_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			'wpml-status-widget',
			__( 'WPML Status', 'wpml' ),
			array(
				'classname'	=>	'wpml-status-widget',
				'description'	=>	__( 'Display Movies from a specific status', 'wpml' )
			)
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;

		include( plugin_dir_path( __FILE__ ) . '../views/status-widget.php' );

		echo $after_widget;
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['description'] = strip_tags( $new_instance['description'] );
		$instance['type'] = strip_tags( $new_instance['type'] );
		$instance['list'] = intval( $new_instance['list'] );
		$instance['thumbnails'] = intval( $new_instance['thumbnails'] );
		$instance['css']   = intval( $new_instance['css'] );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance
		);

		$title = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Movie by Status', 'wpml' ) );
		$description = ( isset( $instance['description'] ) ? $instance['description'] : '' );
		$type  = ( isset( $instance['type'] ) ? $instance['type'] : null );
		$list  = ( isset( $instance['list'] ) ? $instance['list'] : 0 );
		$thumbnails = ( isset( $instance['thumbnails'] ) ? $instance['thumbnails'] : 0 );
		$css   = ( isset( $instance['css'] ) ? $instance['css'] : 0 );

		// Display the admin form
		require_once( plugin_dir_path(__FILE__) . '../class-wpmovielibrary.php' );
		$wpml = WPMovieLibrary::get_instance();
		include( plugin_dir_path(__FILE__) . '../views/status-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}
