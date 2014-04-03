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

		/**
		* Handle Plugin Notices. Needs to be static so static methods
		* can call enqueue notices. Needs to be public so other modules
		* can enqueue notices.
		*
		* @since    1.0.0
		*
		* @var      object
		*/
		public static $notices;

		protected static $readable_properties  = array();

		protected static $writeable_properties = array();

		protected $modules;

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
				'WPML_Actors'      => WPML_Actors::get_instance()
			);

		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'init', __CLASS__ . '::wpml_register_permalinks' );

			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			// Enqueue scripts and styles
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Add link to WP Admin Bar
			add_action( 'wp_before_admin_bar_render', array( $this, 'wpml_admin_bar_menu' ), 999 );
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

			set_transient( '_wpml_just_activated', 'yup', HOUR_IN_SECONDS );
			self::wpml_register_permalinks();

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
		*
		* @param    boolean    $network_wide    True if WPMU superadmin uses
		*                                       "Network Deactivate" action, false if
		*                                       WPMU is disabled or plugin is
		*                                       deactivated on an individual blog.
		*/
		public function deactivate() {

			global $wpdb;

			foreach ( $this->modules as $module )
				$module->deactivate();

			$o           = get_option( 'wpml_settings' );
			$cache       = $o['wpml']['settings']['deactivate']['cache'];

			// Handling Cache cleanup on WPML deactivation
			// Adapted from Sébastien Corne's "purge-transient" snippet

			global $_wp_using_ext_object_cache;

			if ( ! $_wp_using_ext_object_cache && 'empty' == $cache ) {

				$sql = "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE \"_transient_%_movies_%\"";
				$transients = $wpdb->get_col( $sql );

				foreach ( $transients as $transient )
					$result = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE \"{$transient}\"" );

				$wpdb->query( 'OPTIMIZE TABLE ' . $wpdb->options );
			}

			delete_option( 'rewrite_rules' );

		}

		/**
		 * Runs activation code on a new WPMS site when it's created
		 *
		 * @since    1.0.0
		 *
		 * @param int $blog_id
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
		 * @param bool $network_wide
		 */
		protected function single_activate( $network_wide ) {

			foreach ( $this->modules as $module )
				$module->activate( $network_wide );
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
		 * Create a new set of permalinks for Movie Details
		 * 
		 * TODO: rename and add option to either add or remove permalinks
		 *
		 * @since    1.0.0
		 *
		 * @param    object     $wp_rewrite Instance of WordPress WP_Rewrite Class
		 */
		public static function wpml_register_permalinks() {

			if ( 'yup' != get_transient( '_wpml_just_activated' ) )
				return false;

			$new_rules = array(
				'movies/(dvd|vod|bluray|vhs|cinema|other)/?$' => 'index.php?post_type=movie&wpml_movie_media=$matches[1]',
				'movies/(dvd|vod|bluray|vhs|cinema|other)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_media=$matches[1]',
				'movies/(available|loaned|scheduled)/?$' => 'index.php?post_type=movie&wpml_movie_status=$matches[1]',
				'movies/(available|loaned|scheduled)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_status=$matches[1]' . '&paged=$matches[2]',
				'movies/(0.0|0.5|1.0|1.5|2.0|2.5|3.0|3.5|4.0|4.5|5.0)/?$' => 'index.php?post_type=movie&wpml_movie_rating=$matches[1]',
				'movies/(0.0|0.5|1.0|1.5|2.0|2.5|3.0|3.5|4.0|4.5|5.0)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_rating=$matches[1]' . '&paged=$matches[2]',
			);

			foreach ( $new_rules as $regex => $rule )
				add_rewrite_rule( $regex, $rule, 'top' );

			flush_rewrite_rules();
			delete_transient( '_wpml_just_activated' );
		}

		/**
		* Add a New Movie link to WP Admin Bar.
		* 
		* WordPress 3.8 introduces Dashicons, for older versions we use a PNG
		* icon instead.
		*
		* @since    1.0.0
		*/
		public function wpml_admin_bar_menu() {

			global $wp_admin_bar;

			$args = array(
				'id'    => 'wpmovielibrary',
				'title' => __( 'New Movie', 'wpml' ),
				'href'  => admin_url( 'post-new.php?post_type=movie' ),
				'meta'  => array(
					'title' => __( 'New Movie', 'wpml' )
				)
			);

			// Dashicons or PNG
			if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
				$args['title'] = '<img src="' . WPML_URL . '/assets/img/icon-movie.png" alt="" />' . $args['title'];
			}
			else {
				$args['meta']['class'] = 'haz-dashicon';
			}

			$wp_admin_bar->add_menu( $args );
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