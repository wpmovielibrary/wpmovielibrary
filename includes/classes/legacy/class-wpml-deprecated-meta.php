<?php
/**
 * WPMovieLibrary Deprecated Meta Class.
 * 
 * This class handles deprecated WPMovieLibrary Movie Metadata. Prior to WPML
 * version 1.3 movie metadata were stored in a unique post meta value which
 * blocked a lot of features and improvement. Current class handles the migration
 * from obsolete to new data format.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Deprecated_Meta' ) ) :

	class WPML_Deprecated_Meta extends WPML_Module {

		/**
		 * Constructor
		 *
		 * @since    1.3
		 */
		public function __construct() {

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.3
		 */
		public function register_hook_callbacks() {

			add_action( 'admin_notices', array( $this, 'deprecated_meta_notice' ) );

			add_action( 'admin_head', array( $this, 'admin_head' ) );

			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		}

		/**
		 * Register and enqueue admin-specific style sheet.
		 *
		 * @since    1.3
		 *
		 * @return   null    Return early if no settings page is registered.
		 */
		public function enqueue_admin_styles( $hook ) {

			if ( 'dashboard_page_wpml-update-movies' == $hook )
				wp_enqueue_style( 'roboto-font', '//fonts.googleapis.com/css?family=Roboto:100', array(), WPML_VERSION );
		}

		/**
		 * Register and enqueue admin-specific JavaScript.
		 * 
		 * @since    1.3
		 * 
		 * @return   null    Return early if no settings page is registered.
		 */
		public function enqueue_admin_scripts( $hook ) {

			if ( 'dashboard_page_wpml-update-movies' == $hook )
				wp_enqueue_script( WPML_SLUG . '-updates', WPML_URL . '/assets/js/wpml.updates.js', array( WPML_SLUG, 'jquery' ), WPML_VERSION, true );
		}

		/**
		 * Remove dashboard page links.
		 *
		 * @since    1.3
		 */
		public function admin_head() {

			remove_submenu_page( 'index.php', 'wpml-update-movies' );
			//remove_submenu_page( 'index.php', 'wc-credits' );
		}

		public function deprecated_meta_notice() {

			echo self::render_template( 'admin-notice.php', array( 'notice' => 'deprecated-meta' ) );
		}

		public static function update_movies_page() {

			$deprecated = self::get_movies();
			$updated    = self::get_updated_movies();

			echo self::render_template( 'update-movies.php', array( 'deprecated' => $deprecated, 'updated' => $updated ) );
		}

		/**
		 * Get a list of deprected Movie IDs.
		 * 
		 * Movie having an non-empty '_wpml_movie_data' custom field
		 * are considered deprecated and needing updating.
		 * 
		 * @since    1.3
		 * 
		 * @return   int|bool    False is no deprecated could be find, number of deprecated movie else.
		 */
		public static function get_deprecated_movies() {

			global $wpdb;

			$movies = $wpdb->get_results( "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wpml_movie_data' AND meta_value!=''" );
			$movies = ( ! $wpdb->num_rows ? false : $movies );

			return $movies;
		}

		/**
		 * Get a list of updated Movie IDs.
		 * 
		 * @since    1.3
		 * 
		 * @return   array    
		 */
		public static function get_updated_movies() {

			global $wpdb;

			$movies = $wpdb->get_results( "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wpml_movie_data' AND meta_value!=''" );

			foreach ( $movies as $i => $movie )
				$movies[ $i ] = $movie->post_id;

			$args = array(
				'posts_per_page' => -1,
				'orderby'        => 'post_title',
				'order'          => 'ASC',
				'post_type'      => 'movie',
				'post_status'    => 'publish',
				'post__not_in'   => $movies
			);

			$movies = get_posts( $args );

			return $movies;
		}

		/**
		 * Get a list of deprected Movie IDs.
		 * 
		 * Movie having an non-empty '_wpml_movie_data' custom field
		 * are considered deprecated and needing updating.
		 * 
		 * @since    1.3
		 * 
		 * @return   int|bool    False is no deprecated could be find, number of deprecated movie else.
		 */
		private static function get_movies() {

			$movies = self::get_deprecated_movies();
			if ( false === $movies )
				return false;

			foreach ( $movies as $i => $movie )
				$movies[ $i ] = $movie->post_id;

			$args = array(
				'posts_per_page' => -1,
				'orderby'        => 'post_title',
				'order'          => 'ASC',
				'post_type'      => 'movie',
				'post_status'    => 'publish',
				'post__in'       => $movies
			);

			$movies = get_posts( $args );

			return $movies;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.3
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {

			if ( ! wpml_has_deprecated_meta() )
				return false;

			$deprecated = self::get_deprecated_movies();
			if ( false !== $deprecated ) {

				delete_option( 'wpml_has_deprecated_meta' );
				add_option( 'wpml_has_deprecated_meta', count( $deprecated ), null, 'no' );
			}
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.3
		 */
		public function deactivate() {}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.3
		 */
		public static function uninstall() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.3
		 */
		public function init() {}

	}

endif;