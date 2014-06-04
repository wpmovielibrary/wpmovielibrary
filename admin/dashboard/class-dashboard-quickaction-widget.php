<?php
/**
 * WPMovieLibrary Dashboard Class extension.
 * 
 * Create a Quick Action Widget.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Dashboard_Quickaction_Widget' ) ) :

	class WPML_Dashboard_Quickaction_Widget extends WPML_Dashboard {

		/**
		 * Widget ID
		 * 
		 * @since    1.0.0
		 * 
		 * @var      string
		 */
		protected $widget_id = '';

		/**
		 * Widget Name.
		 * 
		 * @since    1.0.0
		 * 
		 * @var      string
		 */
		protected $widget_name = '';

		/**
		 * Widget callback method.
		 * 
		 * @since    1.0.0
		 * 
		 * @var      array
		 */
		protected $callback = null;

		/**
		 * Widget Controls callback method.
		 * 
		 * @since    1.0.0
		 * 
		 * @var      array
		 */
		protected $control_callback = null;

		/**
		 * Widget callback method arguments.
		 * 
		 * @since    1.0.0
		 * 
		 * @var      array
		 */
		protected $callback_args = null;

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

			$this->widget_id = 'wpml_dashboard_quickaction_widget';
			$this->widget_name = __( 'Quick action', WPML_SLUG );
			$this->callback = array( $this, 'dashboard_widget' );
			$this->control_callback = null;
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'wpml_dashboard_setup', array( $this, '_add_dashboard_widget' ), 10 );
		}

		/**
		 * Register the Widget
		 * 
		 * @since    1.0.0
		 */
		public function _add_dashboard_widget() {

			$this->add_dashboard_widget( $this->widget_id, $this->widget_name, $this->callback, $this->control_callback );
		}

		public function dashboard_widget() {

			$links = array();
			$list = array(
				'new_movie' => array(
					'url'   => admin_url( 'post-new.php?post_type=movie' ),
					'title' => __( 'New movie', WPML_SLUG ),
					'icon'  => 'dashicons dashicons-welcome-write-blog'
				),
				'import'    => array(
					'url'   => admin_url( 'admin.php?page=wpml_import' ),
					'title' => __( 'Import movies', WPML_SLUG ),
					'icon'  => 'dashicons dashicons-list-view'
				),
				'settings'  => array(
					'url'   => admin_url( 'edit.php?post_type=movie' ),
					'title' => __( 'Manage movies', WPML_SLUG ),
					'icon'  => 'dashicons dashicons-format-video'
				)
			);

			foreach ( $list as $slug => $data )
				$links[] = sprintf( '<li><a href="%s"><span class="%s"></span><span class="link">%s</span></a></li>', $data['url'], $data['icon'], $data['title'] );

			$links = implode( '', $links );

			include_once( WPML_PATH . '/admin/dashboard/views/dashboard-quickaction-widget.php' );
		}

		public function dashboard_widget_handle() {

			
		}

	}

endif;