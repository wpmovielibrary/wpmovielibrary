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
