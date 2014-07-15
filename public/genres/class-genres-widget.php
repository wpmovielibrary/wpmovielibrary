<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */


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
			__( 'WPML Genres', WPML_SLUG ),
			array(
				'classname'	=>	'wpml-genres-widget',
				'description'	=>	__( 'Display Movie Genres Lists', WPML_SLUG )
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
		extract( $instance );

		$title = $before_title . apply_filters( 'widget_title', $title ) . $after_title;
		$list  = ( 1 == $list ? true : false );
		$css = ( 1 == $css ? true : false );
		$count = ( 1 == $count ? true : false );

		$genres = get_terms( array( 'genre' ) );

		echo $before_widget;

		include( plugin_dir_path( __FILE__ ) . '/views/genres-widget.php' );

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

		$title = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Movie Genres', WPML_SLUG ) );
		$list  = ( isset( $instance['list'] ) ? $instance['list'] : 1 );
		$count = ( isset( $instance['count'] ) ? $instance['count'] : 0 );
		$css   = ( isset( $instance['css'] ) ? $instance['css'] : 0 );

		// Display the admin form
		include( WPML_PATH . 'admin/common/views/genres-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}
