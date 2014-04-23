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
				'WPML_Actors'      => WPML_Actors::get_instance()
			);

			$this->widgets = array(
				'WPML_Recent_Movies_Widget',
				'WPML_Most_Rated_Movies_Widget',
				'WPML_Media_Widget',
				'WPML_Status_Widget'
			);

		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			// Add custom permalinks if anything flush the rewrite rules
			add_filter( 'rewrite_rules_array', __CLASS__ . '::register_permalinks', 10 );

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

			WPML_Movies::register_post_type();
			WPML_Collections::register_collection_taxonomy();
			WPML_Genres::register_genre_taxonomy();
			WPML_Actors::register_actor_taxonomy();
			self::register_permalinks();

			flush_rewrite_rules();

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

			global $wpdb, $_wp_using_ext_object_cache;

			foreach ( $this->modules as $module )
				$module->deactivate();

			$action = WPML_Settings::deactivate__cache();

			if ( ! $_wp_using_ext_object_cache && 'empty' == $action ) {
				$result = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE \"_transient_%_movies_%\"" );
				$wpdb->query( 'OPTIMIZE TABLE ' . $wpdb->options );
			}

			delete_option( 'rewrite_rules' );

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
		 * Create a new set of permalinks for Movie Details
		 * 
		 * We want to list movies by media, status and rating. This method is called
		 * during init but will not do anything unless
		 *
		 * @since    1.0.0
		 *
		 * @param    object     $wp_rewrite Instance of WordPress WP_Rewrite Class
		 */
		public static function register_permalinks( $rules = null ) {

			//if ( is_null( $wp_rewrite ) )

			$new_rules = array(
				'movies/(dvd|vod|bluray|vhs|cinema|other)/?$' => 'index.php?post_type=movie&wpml_movie_media=$matches[1]',
				'movies/(dvd|vod|bluray|vhs|cinema|other)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_media=$matches[1]',
				'movies/(available|loaned|scheduled)/?$' => 'index.php?post_type=movie&wpml_movie_status=$matches[1]',
				'movies/(available|loaned|scheduled)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_status=$matches[1]' . '&paged=$matches[2]',
				'movies/(0.0|0.5|1.0|1.5|2.0|2.5|3.0|3.5|4.0|4.5|5.0)/?$' => 'index.php?post_type=movie&wpml_movie_rating=$matches[1]',
				'movies/(0.0|0.5|1.0|1.5|2.0|2.5|3.0|3.5|4.0|4.5|5.0)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_rating=$matches[1]' . '&paged=$matches[2]',
			);

			if ( ! is_null( $rules ) )
				return $new_rules + $rules;

			foreach ( $new_rules as $regex => $rule )
				add_rewrite_rule( $regex, $rule, 'top' );

			
		}

		/**
		 * Add a New Movie link to WP Admin Bar.
		 * 
		 * WordPress 3.8 introduces Dashicons, for older versions we use a PNG
		 * icon instead.
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