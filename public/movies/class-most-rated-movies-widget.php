<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

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
			__( 'WPML Most Rated Movies', WPML_SLUG ),
			array(
				'classname'	=>	'wpml-most-rated-movies-widget',
				'description'	=>	__( 'Display most rated Movies.', WPML_SLUG )
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

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['description'] = strip_tags( $new_instance['description'] );
		$instance['number'] = intval( $new_instance['number'] );
		$instance['display_rating'] = strip_tags( $new_instance['display_rating'] );
		$instance['rating_only'] = intval( $new_instance['rating_only'] );

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

		$title = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Most Rated Movies', WPML_SLUG ) );
		$description = ( isset( $instance['description'] ) ? $instance['description'] : __( 'Movies I really enjoyed', WPML_SLUG ) );
		$number = ( isset( $instance['number'] ) ? $instance['number'] : 4 );
		$display_rating = ( isset( $instance['display_rating'] ) ? $instance['display_rating'] : 'no' );
		$rating_only = ( isset( $instance['rating_only'] ) ? $instance['rating_only'] : 0 );

		// Display the admin form
		include( WPML_PATH . 'admin/common/views/most-rated-movies-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}
