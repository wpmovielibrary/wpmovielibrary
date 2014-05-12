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
				'WPML_Dashboard_Stats_Widget' => WPML_Dashboard_Stats_Widget::get_instance()
			);
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

			/*if ( $control_callback && current_user_can( 'edit_dashboard' ) && is_callable( $control_callback ) ) {

				$wp_dashboard_control_callbacks[$widget_id] = $control_callback;

				if ( isset( $_GET['edit'] ) && $widget_id == $_GET['edit'] ) {
					list( $url ) = explode( '#', add_query_arg( 'edit', false ), 2 );
					$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '">' . __( 'Cancel' ) . '</a></span>';
					//$callback = array( $this, '_wp_dashboard_control_callback' );
					$callback = null;
				}
				else {
					list($url) = explode( '#', add_query_arg( 'edit', $widget_id ), 2 );
					$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( "$url#$widget_id" ) . '" class="edit-box open-box">' . __( 'Configure' ) . '</a></span>';
				}
			}*/

			$side_widgets = array( 'wpml_dashboard_stats_widget' );
			$location = ( in_array( $widget_id, $side_widgets ) ? 'side' : 'normal' );
			$priority = 'core';

			add_meta_box( $widget_id, $widget_name, $callback, $screen, $location, $priority, $callback_args );

		}

		public function _wp_dashboard_control_callback( $dashboard, $meta_box ) {
			echo '<form action="" method="post" class="dashboard-widget-control-form">';
			$this->wp_dashboard_trigger_widget_control( $meta_box['id'] );
			wp_nonce_field( 'edit-dashboard-widget_' . $meta_box['id'], 'dashboard-widget-nonce' );
			echo '<input type="hidden" name="widget_id" value="' . esc_attr($meta_box['id']) . '" />';
			submit_button( __('Submit') );
			echo '</form>';
		}

		/**
		 * Calls widget control callback.
		 *
		 * @since    1.0.0
		 *
		 * @param    int    $widget_control_id Registered Widget ID.
		 */
		public function wp_dashboard_trigger_widget_control( $widget_control_id = false ) {

			global $wp_dashboard_control_callbacks;

			if ( is_scalar( $widget_control_id ) && $widget_control_id && isset( $wp_dashboard_control_callbacks[ $widget_control_id ] ) && is_callable( $wp_dashboard_control_callbacks[ $widget_control_id ] ) ) {
				call_user_func( $wp_dashboard_control_callbacks[ $widget_control_id ], '', array( 'id' => $widget_control_id, 'callback' => $wp_dashboard_control_callbacks[ $widget_control_id ] ) );
			}
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {}

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
