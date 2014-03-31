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
		public static function activate( $network_wide ) {

			global $wpdb;

			$contents = new WP_Query(
				array(
					'post_type'      => 'post',
					'posts_per_page' => -1,
					'meta_key'       => '_wpml_content_type',
					'meta_value'     => 'movie'
				)
			);

			foreach ( $contents->posts as $post ) {
				set_post_type( $post->ID, 'movie' );
				delete_post_meta( $post->ID, '_wpml_content_type', 'movie' );
			}

			$contents = $wpdb->get_results( 'SELECT term_id, slug FROM ' . $wpdb->terms . ' WHERE slug LIKE "wpml_%"' );

			$collections = array();
			$genres      = array();
			$actors      = array();

			foreach ( $contents as $term ) {
				if ( false !== strpos( $term->slug, 'wpml_collection' ) ) {
					$collections[] = $term->term_id;
				}
				else if ( false !== strpos( $term->slug, 'wpml_genre' ) ) {
					$genres[] = $term->term_id;
				}
				else if ( false !== strpos( $term->slug, 'wpml_actor' ) ) {
					$actors[] = $term->term_id;
				}
			}

			if ( ! empty( $collections ) )
				$wpdb->query( 'UPDATE ' . $wpdb->term_taxonomy . ' SET taxonomy = "collection" WHERE term_id IN (' . implode( ',', $collections ) . ')' );

			if ( ! empty( $genres ) )
				$wpdb->query( 'UPDATE ' . $wpdb->term_taxonomy . ' SET taxonomy = "genre" WHERE term_id IN (' . implode( ',', $genres ) . ')' );

			if ( ! empty( $actors ) )
				$wpdb->query( 'UPDATE ' . $wpdb->term_taxonomy . ' SET taxonomy = "actor" WHERE term_id IN (' . implode( ',', $actors ) . ')' );

			$wpdb->query(
				'UPDATE ' . $wpdb->terms . '
				SET slug = REPLACE(slug, "wpml_collection-", ""),
				    slug = REPLACE(slug, "wpml_genre-", ""),
				    slug = REPLACE(slug, "wpml_actor-", "")'
			);

			set_transient( '_wpml_just_activated', 1, HOUR_IN_SECONDS );

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
		public static function deactivate( $network_wide ) {

			global $wpdb;

			$o           = get_option( 'wpml_settings' );
			$movies      = $o['wpml']['settings']['deactivate']['movies'];
			$collections = $o['wpml']['settings']['deactivate']['collections'];
			$genres      = $o['wpml']['settings']['deactivate']['genres'];
			$actors      = $o['wpml']['settings']['deactivate']['actors'];
			$cache       = $o['wpml']['settings']['deactivate']['cache'];

			// Handling Movie Custom Post Type on WPML deactivation

			$contents = new WP_Query(
				array(
					'post_type'      => 'movie',
					'posts_per_page' => -1
				)
			);

			if ( 'convert' == $movies ) {
				foreach ( $contents->posts as $post ) {
					set_post_type( $post->ID, 'post' );
					add_post_meta( $post->ID, '_wpml_content_type', 'movie', true );
				}
			}
			else if ( 'remove' == $movies ) {
				foreach ( $contents->posts as $post ) {
					wp_delete_post( $post->ID, true );
				}
			}
			else if ( 'delete' == $movies ) {
				foreach ( $contents->posts as $post ) {
					wp_delete_post( $post->ID, true );
					$attachments = get_children( array( 'post_parent' => $post->ID ) );
					foreach ( $attachments as $a ) {
						wp_delete_post( $a->ID, true );
					}
				}
			}

			// Handling Custom Category-like Taxonomies on WPML deactivation

			$contents = get_terms( array( 'collection' ), array() );

			if ( 'convert' == $collections ) {
				foreach ( $contents as $term ) {
					wp_update_term( $term->term_id, 'collection', array( 'slug' => 'wpml_collection-' . $term->slug ) );
					$wpdb->update(
						$wpdb->term_taxonomy,
						array( 'taxonomy' => 'category' ),
						array( 'taxonomy' => 'collection' ),
						array( '%s' )
					);
				}
			}
			else if ( 'remove' == $collections || 'delete' == $collections ) {
				foreach ( $contents as $term ) {
					wp_delete_term( $term->term_id, 'collection' );
				}
			}

			// Handling Genres Taxonomies on WPML deactivation

			$contents = get_terms( array( 'genre' ), array() );

			if ( 'convert' == $genres ) {
				foreach ( $contents as $term ) {
					wp_update_term( $term->term_id, 'genre', array( 'slug' => 'wpml_genre-' . $term->slug ) );
					$wpdb->update(
						$wpdb->term_taxonomy,
						array( 'taxonomy' => 'post_tag' ),
						array( 'taxonomy' => 'genre' ),
						array( '%s' )
					);
				}
			}
			else if ( 'remove' == $genres || 'delete' == $genres ) {
				foreach ( $contents as $term ) {
					wp_delete_term( $term->term_id, 'genre' );
				}
			}

			// Handling Actors Taxonomies on WPML deactivation

			$contents = get_terms( array( 'actor' ), array() );

			if ( 'convert' == $actors ) {
				foreach ( $contents as $term ) {
					wp_update_term( $term->term_id, 'actor', array( 'slug' => 'wpml_actor-' . $term->slug ) );
					$wpdb->update(
						$wpdb->term_taxonomy,
						array( 'taxonomy' => 'post_tag' ),
						array( 'taxonomy' => 'actor' ),
						array( '%s' )
					);
				}
			}
			else if ( 'remove' == $actors || 'delete' == $actors ) {
				foreach ( $contents as $term ) {
					wp_delete_term( $term->term_id, 'actor' );
				}
			}

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

			//TODO: remove permastruct. 
			/*global $wp_rewrite;
			$wp_rewrite->add_permastruct( 'movie', '');
			$wp_rewrite->add_permastruct( 'movies', '');
			$wp_rewrite->flush_rules();
			print_r( $wp_rewrite ); die();*/

		}

		/**
		* Register and enqueue public-facing style sheet.
		*
		* @since    1.0.0
		*/
		public function enqueue_styles() {

			wp_enqueue_style( WPML_SLUG, WPML_URL . '/public/assets/css/public.css', array(), WPML_VERSION );
		}

		/**
		* Register and enqueue public-facing style sheet.
		*
		* @since    1.0.0
		*/
		public function enqueue_scripts() {

			wp_enqueue_script( WPML_SLUG, WPML_URL . '/public/assets/js/public.js', array( 'jquery' ), WPML_VERSION, true );
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

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		*
		*                       Action and Filter Hooks
		* 
		* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

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
				$args['title'] = '<img src="' . WPML_URL . '/admin/assets/img/icon-movie.png" alt="" />' . $args['title'];
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
		public function init() {}

	}
endif;