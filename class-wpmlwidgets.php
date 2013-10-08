<?php
/**
 * WP_Widget Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <contact@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2013 CaerCam.org
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

		include( plugin_dir_path( __FILE__ ) . '/views/recent-movies-widget.php' );

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
		include( plugin_dir_path(__FILE__) . '/views/recent-movies-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}

add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Recent_Movies_Widget");' ) );


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

		include( plugin_dir_path( __FILE__ ) . '/views/most-rated-movies-widget.php' );

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
		include( plugin_dir_path(__FILE__) . '/views/most-rated-movies-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}

add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Most_Rated_Movies_Widget");' ) );


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

		include( plugin_dir_path( __FILE__ ) . '/views/collections-widget.php' );

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

		// Display the admin form
		include( plugin_dir_path(__FILE__) . '/views/collections-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}

add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Collections_Widget");' ) );