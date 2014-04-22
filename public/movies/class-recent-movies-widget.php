<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

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
			__( 'WPML Recent Movies', WPML_SLUG ),
			array(
				'classname'	=>	'wpml-recent-movies-widget',
				'description'	=>	__( 'Display most recently added Movies.', WPML_SLUG )
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

		$title        = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Recent Movies', WPML_SLUG ) );
		$description  = ( isset( $instance['description'] ) ? $instance['description'] : __( 'Movies I recently added to my library', WPML_SLUG ) );
		$number       = ( isset( $instance['number'] ) ? $instance['number'] : 4 );

		// Display the admin form
		include( WPML_PATH . 'admin/common/views/recent-movies-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}