<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */


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
			__( 'WPML Status', WPML_SLUG ),
			array(
				'classname'	=>	'wpml-status-widget',
				'description'	=>	__( 'Display Movies from a specific status', WPML_SLUG )
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
		$description = esc_attr( $description );
		$type = esc_attr( $type );
		$list = ( 1 == $list ? true : false );
		$css = ( 1 == $css ? true : false );
		$thumbnails = ( 1 == $thumbnails ? true : false );
		$status_only = ( 1 == $status_only ? true : false );

		echo $before_widget;

		include( plugin_dir_path( __FILE__ ) . '/views/status-widget.php' );

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
		$instance['css'] = intval( $new_instance['css'] );
		$instance['status_only'] = intval( $new_instance['status_only'] );

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

		$title = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Movie by Status', WPML_SLUG ) );
		$description = ( isset( $instance['description'] ) ? $instance['description'] : '' );
		$type = ( isset( $instance['type'] ) ? $instance['type'] : null );
		$list = ( isset( $instance['list'] ) ? $instance['list'] : 0 );
		$thumbnails = ( isset( $instance['thumbnails'] ) ? $instance['thumbnails'] : 0 );
		$css = ( isset( $instance['css'] ) ? $instance['css'] : 0 );
		$status_only = ( isset( $instance['status_only'] ) ? $instance['status_only'] : 0 );

		// Display the admin form
		include( WPML_PATH . 'admin/common/views/status-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}
