<?php
/**
 * WPMovieLibrary
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

if ( ! class_exists( 'WPMovieLibrary' ) ) :

	/**
	* Plugin class
	*
	* @package WPMovieLibrary
	* @author  Charlie MERLAND <charlie.merland@gmail.com>
	*/
	class WPMovieLibrary extends WPML_Module {

		protected static $readable_properties  = array();

		protected static $writeable_properties = array();

		protected $modules;

		protected $widgets;

		/**
		 * Initialize the plugin by setting localization and loading public scripts
		 * and styles.
		 *
		 * @since     1.0.0
		 */
		protected function __construct() {

			$this->register_hook_callbacks();

			$this->modules = array(
				'WPML_Settings'    => WPML_Settings::get_instance(),
				'WPML_Utils'       => WPML_Utils::get_instance(),
				'WPML_Movies'      => WPML_Movies::get_instance(),
				'WPML_Collections' => WPML_Collections::get_instance(),
				'WPML_Genres'      => WPML_Genres::get_instance(),
				'WPML_Actors'      => WPML_Actors::get_instance(),
				'WPML_Shortcodes'  => WPML_Shortcodes::get_instance(),
			);

			$this->widgets = array(
				'WPML_Recent_Movies_Widget',
				'WPML_Most_Rated_Movies_Widget',
				'WPML_Media_Widget',
				'WPML_Status_Widget',
				'WPML_Statistics_Widget'
			);

		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

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
		 * Restore previously converted contents. If WPML was previously
		 * deactivated or uninstalled using the 'convert' option, Movies and
		 * Custom Taxonomies should still be in the database. If they are, we
		 * convert them back to WPML contents.
		 * 
		 * Call Movie Custom Post Type and Collections, Genres and Actors custom
		 * Taxonomies' registering functions and flush rewrite rules to update
		 * the permalinks.
		 *
		 * @since    1.0.0
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
		 * When deactivatin/uninstalling WPML, adopt different behaviors depending
		 * on user options. Movies and Taxonomies can be kept as they are,
		 * converted to WordPress standars or removed. Default is conserve on
		 * deactivation, convert on uninstall.
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {

			foreach ( $this->modules as $module )
				$module->deactivate();

		}

		/**
		 * Runs activation code on a new WPMS site when it's created
		 *
		 * @since    1.0.0
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
		 * @since    1.0.0
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
		 * @since    1.0.0
		 */
		public function enqueue_styles() {

			wp_enqueue_style( WPML_SLUG, WPML_URL . '/assets/css/public.css', array(), WPML_VERSION );
		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {

			wp_enqueue_script( WPML_SLUG, WPML_URL . '/assets/js/public.js', array( 'jquery' ), WPML_VERSION, true );
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since    1.0.0
		 */
		public function load_plugin_textdomain() {

			$domain = WPML_SLUG;
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

		}

		/**
		 * Register the Class Widgets
		 * 
		 * @since    1.0.0 
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
		 * @since    1.0.0
		 */
		public function admin_bar_menu() {

			global $wp_admin_bar;

			$args = array(
				'id'    => 'wpmovielibrary',
				'title' => __( 'New Movie', WPML_SLUG ),
				'href'  => admin_url( 'post-new.php?post_type=movie' ),
				'meta'  => array(
					'title' => __( 'New Movie', WPML_SLUG )
				)
			);

			// Dashicons or PNG
			if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
				$args['title'] = '<img src="' . WPML_URL . '/assets/img/legacy/icon-movie.png" alt="" />' . $args['title'];
			}
			else {
				$args['meta']['class'] = 'haz-dashicon';
			}

			$wp_admin_bar->add_menu( $args );
		}

		/**
		 * Uninstall the plugin, network wide.
		 *
		 * @since    1.0.0
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
		 * @since    1.0.0
		 */
		private static function _uninstall() {

			WPML_Utils::uninstall();
			WPML_Movies::uninstall();
			WPML_Collections::uninstall();
			WPML_Genres::uninstall();
			WPML_Actors::uninstall();
			WPML_Settings::uninstall();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {
		}

	}
endif;