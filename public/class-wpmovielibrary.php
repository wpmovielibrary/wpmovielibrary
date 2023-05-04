<?php
/**
 * WPMovieLibrary
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 Charlie MERLAND
 */

if ( ! class_exists( 'WPMovieLibrary' ) ) :

	/**
	* Plugin class
	*
	* @package WPMovieLibrary
	* @author  Charlie MERLAND <charlie@caercam.org>
	*/
	class WPMovieLibrary extends WPMOLY_Module {

		protected $modules;

		protected $widgets;

		/**
		 * Initialize the plugin by setting localization and loading public scripts
		 * and styles.
		 *
		 * @since    1.0
		 */
		protected function __construct() {

			$this->register_hook_callbacks();

			$this->modules = array(
				'WPMOLY_Settings'    => WPMOLY_Settings::get_instance(),
				'WPMOLY_Cache'       => WPMOLY_Cache::get_instance(),
				'WPMOLY_L10n'        => WPMOLY_L10n::get_instance(),
				'WPMOLY_Utils'       => WPMOLY_Utils::get_instance(),
				'WPMOLY_Movies'      => WPMOLY_Movies::get_instance(),
				'WPMOLY_Headbox'     => WPMOLY_Headbox::get_instance(),
				'WPMOLY_Search'      => WPMOLY_Search::get_instance(),
				'WPMOLY_Collections' => WPMOLY_Collections::get_instance(),
				'WPMOLY_Genres'      => WPMOLY_Genres::get_instance(),
				'WPMOLY_Actors'      => WPMOLY_Actors::get_instance(),
				'WPMOLY_Archives'    => WPMOLY_Archives::get_instance(),
				'WPMOLY_Shortcodes'  => WPMOLY_Shortcodes::get_instance(),
				'WPMOLY_Legacy'      => WPMOLY_Legacy::get_instance()
			);

			$this->widgets = array(
				'WPMOLY_Statistics_Widget',
				'WPMOLY_Taxonomies_Widget',
				'WPMOLY_Details_Widget',
				'WPMOLY_Movies_Widget'
			);

		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			// Widgets
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );

			// Enqueue scripts and styles
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Add link to WP Admin Bar
			add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar_menu' ), 999 );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                     Plugin  Activate/Deactivate
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Fired when the plugin is activated.
		 * 
		 * Restore previously converted contents. If WPMOLY was previously
		 * deactivated or uninstalled using the 'convert' option, Movies and
		 * Custom Taxonomies should still be in the database. If they are, we
		 * convert them back to WPMOLY contents.
		 * 
		 * Call Movie Custom Post Type and Collections, Genres and Actors custom
		 * Taxonomies' registering functions and flush rewrite rules to update
		 * the permalinks.
		 *
		 * @since    1.0
		 *
		 * @param    boolean    $network_wide    True if WPMU superadmin uses
		 *                                       "Network Activate" action, false if
		 *                                       WPMU is disabled or plugin is
		 *                                       activated on an individual blog.
		 */
		public function activate( $network_wide ) {

			global $wpdb;

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				if ( $network_wide ) {
					$blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

					foreach ( $blogs as $blog ) {
						switch_to_blog( $blog );
						$this->single_activate( $network_wide );
					}

					restore_current_blog();
				} else {
					$this->single_activate( $network_wide );
				}
			} else {
				$this->single_activate( $network_wide );
			}

		}

		/**
		 * Fired when the plugin is deactivated.
		 * 
		 * When deactivatin/uninstalling WPMOLY, adopt different behaviors depending
		 * on user options. Movies and Taxonomies can be kept as they are,
		 * converted to WordPress standars or removed. Default is conserve on
		 * deactivation, convert on uninstall.
		 *
		 * @since    1.0
		 */
		public function deactivate() {

			foreach ( $this->modules as $module )
				$module->deactivate();
		}

		/**
		 * Runs activation code on a new WPMS site when it's created
		 *
		 * @since    1.0
		 *
		 * @param    int    $blog_id
		 */
		public function activate_new_site( $blog_id ) {
			switch_to_blog( $blog_id );
			$this->single_activate( true );
			restore_current_blog();
		}

		/**
		 * Prepares a single blog to use the plugin
		 *
		 * @since    1.0
		 *
		 * @param    bool    $network_wide
		 */
		protected function single_activate( $network_wide ) {

			foreach ( $this->modules as $module )
				$module->activate( $network_wide );

			flush_rewrite_rules();
		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since    1.0
		 */
		public function enqueue_styles() {

			wp_enqueue_style( WPMOLY_SLUG, WPMOLY_URL . '/assets/css/public/wpmoly.css', array(), WPMOLY_VERSION );
			wp_enqueue_style( WPMOLY_SLUG . '-flags', WPMOLY_URL . '/assets/css/public/wpmoly-flags.css', array(), WPMOLY_VERSION );
			wp_enqueue_style( WPMOLY_SLUG . '-font', WPMOLY_URL . '/assets/fonts/wpmovielibrary/style.css', array(), WPMOLY_VERSION );
		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since    1.0
		 */
		public function enqueue_scripts() {

			wp_enqueue_script( WPMOLY_SLUG, WPMOLY_URL . '/assets/js/public/wpmoly.js', array( 'jquery' ), WPMOLY_VERSION, true );
			wp_localize_script(
				WPMOLY_SLUG, 'wpmoly',
				array(
					'lang' => array(
						'grid' => __( 'grid', 'wpmovielibrary' )
					)
				)
			);
		}

		/**
		 * Register the Class Widgets
		 * 
		 * @since    1.0 
		 */
		public function register_widgets() {

			foreach ( $this->widgets as $widget )
				if ( class_exists( $widget ) )
					register_widget( $widget );
		}

		/**
		 * Add a New Movie link to WP Admin Bar.
		 * 
		 * WordPress 3.8 introduces Dashicons, for older versions we use
		 * a PNG icon instead.
		 * 
		 * This method is in the public part because the Admin Bar shows
		 * on the frontend as well, so although it is admin related this
		 * must be public.
		 *
		 * @since    1.0
		 */
		public function admin_bar_menu() {

			global $wp_admin_bar;
			$admin_bar_menu = WPMOLY_Settings::get_admin_bar_menu();

			$wp_admin_bar->add_menu( $admin_bar_menu['menu'] );

			foreach ( $admin_bar_menu['submenu'] as $menu )
				if ( ! isset( $menu['condition'] ) || ( isset( $menu['condition'] ) && false != $menu['condition'] ) )
					$wp_admin_bar->add_menu( $menu );

			foreach ( $admin_bar_menu['group'] as $group )
				$wp_admin_bar->add_group( $group );
		}

		/**
		 * Uninstall the plugin, network wide.
		 *
		 * @since    1.0
		 */
		public static function uninstall() {

			global $wpdb;

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {

				$blogs = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

				foreach ( $blogs as $blog ) {
					switch_to_blog( $blog );
					self::_uninstall();
				}

				restore_current_blog();
			}
			else {
				self::_uninstall();
			}
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.0
		 */
		private static function _uninstall() {

			WPMOLY_Utils::uninstall();
			WPMOLY_Movies::uninstall();
			WPMOLY_Collections::uninstall();
			WPMOLY_Genres::uninstall();
			WPMOLY_Actors::uninstall();
			WPMOLY_Settings::uninstall();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}
endif;