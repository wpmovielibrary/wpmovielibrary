<?php
/**
 * WPMovieLibrary Dashboard Class extension.
 * 
 * Implement a simple custom Dashboard
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Dashboard' ) ) :

	class WPML_Dashboard extends WPML_Module {

		protected $widgets = array();

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 */
		public function __construct() {

			$this->init();
			$this->register_hook_callbacks();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {

			$this->widgets = array(
				'WPML_Dashboard_Stats_Widget' => WPML_Dashboard_Stats_Widget::get_instance(),
				'WPML_Dashboard_Movies_Widget' => WPML_Dashboard_Movies_Widget::get_instance(),
				'WPML_Dashboard_Most_Rated_Movies_Widget' => WPML_Dashboard_Most_Rated_Movies_Widget::get_instance(),
				'WPML_Dashboard_Quickaction_Widget' => WPML_Dashboard_Quickaction_Widget::get_instance(),
			);
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_filter( 'set-screen-option', __CLASS__ . '::set_option', 10, 3 );
			add_filter( 'screen_settings', __CLASS__ . '::screen_options', 10, 2 );

			add_action( 'wp_ajax_update_wpml_welcome_panel', __CLASS__ . '::update_wpml_welcome_panel_callback' );
		}

		/**
		 * AJAX Callback to update the plugin Welcome Panel show/hide
		 * option.
		 *
		 * @since     1.0.0
		 */
		public static function update_wpml_welcome_panel_callback() {

			check_ajax_referer( 'screen-options-nonce', 'screenoptionnonce' );

			$visible = ( isset( $_POST['visible'] ) && '1' == $_POST['visible'] ? '1' : '0' );
			$update = update_user_meta( get_current_user_id(), 'show_wpml_welcome_panel', $visible );
			$update = ( true === $update ? 1 : 0 );

			wp_die( $update );
		}

		/**
		 * Save newly set Movie Drafts number in Movie Import Page.
		 *
		 * @since    1.0.0
		 * 
		 * @param    bool|int    $status Screen option value. Default false to skip.
		 * @param    string      $option The option name.
		 * @param    int         $value The number of rows to use.
		 * 
		 * @return   bool|string
		 */
		public static function set_option( $status, $option, $value ) {
			if ( 'show_wpml_welcome_panel' == $option )
				return $value;
		}

		/**
		 * Show plugin Welcome panel screen option form.
		 *
		 * @since    1.0.0
		 * 
		 * @param    string    $screen_settings Screen settings.
		 * @param    object    WP_Screen object.
		 * 
		 * @return   string    Updated screen settings
		 */
		public static function screen_options( $status, $args ) {

			if ( $args->base != 'toplevel_page_wpmovielibrary' )
				return $status;

			$visible = get_user_meta( get_current_user_id(), 'show_wpml_welcome_panel', true );
			if ( '' == $visible ) {
				add_user_meta( get_current_user_id(), 'show_wpml_welcome_panel', '1' );
				$visible = '1';
			}

			$return  = '<h5>' . __( 'Show on screen', WPML_SLUG ) . '</h5>';
			$return .= '<label for="show_wpml_welcome_panel"><input id="show_wpml_welcome_panel" type="checkbox"' . checked( $visible, '1', false ) . ' />' . __( 'Welcome', WPML_SLUG ) . '</label>';
			$return .= get_submit_button( __( 'Apply', WPML_SLUG ), 'button hide-if-js', 'screen-options-apply', false );

			return $return;
		}

		/**
		 * Render WPML Dashboard Page.
		 * 
		 * Create a nice landing page for the plugin, displaying recent
		 * movies and other stuff like a simple shortcut menu.
		 * 
		 * @since    1.0.0
		 */
		public static function dashboard() {

			$visible = get_user_meta( get_current_user_id(), 'show_wpml_welcome_panel', true );
			$visible = ( '1' == $visible ? true : false );

			if ( current_user_can( 'edit_theme_options' ) && isset( $_GET['show_wpml_welcome_panel'] ) && 1 == $_GET['show_wpml_welcome_panel'] ) {
				if ( isset( $_GET['show_wpml_welcome_panel_nonce'] ) || wp_verify_nonce( $_GET['show_wpml_welcome_panel_nonce'], 'show-wpml-welcome-panel' ) ) {
					update_user_meta( get_current_user_id(), 'show_wpml_welcome_panel', empty( $_POST['visible'] ) ? 0 : 1 );
					$visible = false;
				}
			}

			include_once( plugin_dir_path( __FILE__ ) . '/views/dashboard.php' );
		}

		/**
		 * Show the default modal for movies
		 * 
		 * @since    1.0.0
		 */
		public function movie_showcase() {

			global $current_screen;

			if ( $current_screen->id != $this->plugin_screen_hook_suffix['dashboard'] )
				return false;

			include_once( plugin_dir_path( __FILE__ ) . '/views/dashboard-movie-modal.php' );
		}
 
		/**
		 * Adds a new widget to the Plugin's Dashboard.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    int       $widget_id Identifying slug for the widget. This will be used as its css class and its key in the array of widgets.
		 * @param    string    $widget_name Name the widget will display in its heading.
		 * @param    array     $callback Method that will display the actual contents of the widget.
		 * @param    array     $control_callback Method that will handle submission of widget options (configuration) forms, and will also display the form elements.
		 */
		public function add_dashboard_widget( $widget_id, $widget_name, $callback, $control_callback = null, $callback_args = null ) {

			global $wp_dashboard_control_callbacks;

			$screen = get_current_screen();
			$side_widgets = array( 'wpml_dashboard_stats_widget', 'wpml_dashboard_quickaction_widget' );
			$location = ( in_array( $widget_id, $side_widgets ) ? 'side' : 'normal' );
			$priority = 'core';

			add_meta_box( $widget_id, esc_attr__( $widget_name, WPML_SLUG ), $callback, $screen, $location, $priority, $callback_args );

		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {}

	}

endif;
