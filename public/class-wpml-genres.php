<?php
/**
 * WPMovieLibrary Genre Class extension.
 * 
 * Add and manage a Genre Custom Taxonomy
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Genres' ) ) :

	class WPML_Genres extends WPML_Module {

		protected $widgets;

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', __CLASS__ . '::register_genre_taxonomy' );
		}

		/**
		 * Register a 'Genre' custom taxonomy to aggregate movies.
		 * 
		 * Genres are Tag-like taxonomies: not-hierarchical, tagcloud,
		 * showing in admin columns.
		 * 
		 * @see wpml_movies_columns_head()
		 * @see wpml_movies_columns_content()
		 *
		 * @since    1.0
		 */
		public static function register_genre_taxonomy() {

			if ( ! WPML_Settings::taxonomies__enable_genre() )
				return false;

			$slug = WPML_Settings::taxonomies__genre_rewrite();
			$slug = ( '' != $slug ? $slug : 'genre' );

			register_taxonomy(
				'genre',
				'movie',
				array(
					'labels'   => array(
						'name'          => __( 'Genres', 'wpmovielibrary' ),
						'add_new_item'  => __( 'New Genre', 'wpmovielibrary' )
					),
					'show_ui'           => true,
					'show_tagcloud'     => true,
					'show_admin_column' => true,
					'hierarchical'      => false,
					'query_var'         => true,
					'sort'              => true,
					'rewrite'           => array( 'slug' => $slug )
				)
			);

		}

		/**
		 * Handle Deactivation/Uninstallation actions.
		 * 
		 * Depending on the Plugin settings, conserve, convert, remove
		 * or delete completly all movies created while using the plugin.
		 * 
		 * @param    string    $action Are we deactivating or uninstalling
		 *                             the plugin?
		 * 
		 * @return   boolean   Did everything go smooth or not?
		 */
		public static function clean_genres( $action ) {

			if ( ! in_array( $action, array( 'deactivate', 'uninstall' ) ) )
				return false;

			global $wpdb;
			$wpdb->hide_errors();

			$_action = get_option( 'wpml_settings' );
			if ( ! $_action || ! isset( $_action[ $action ] ) || ! isset( $_action[ $action ]['genres'] ) )
				return false;

			$action = $_action[ $action ]['genres'];
			if ( is_array( $action ) )
				$action = $action[0];

			$contents = get_terms( array( 'genre' ), array() );

			switch ( $action ) {
				case 'convert':
					foreach ( $contents as $term ) {
						wp_update_term( $term->term_id, 'genre', array( 'slug' => 'wpml_genre-' . $term->slug ) );
						$wpdb->update(
							$wpdb->term_taxonomy,
							array( 'taxonomy' => 'post_tag' ),
							array( 'taxonomy' => 'genre' ),
							array( '%s' )
						);
					}
					break;
				case 'delete':
					foreach ( $contents as $term ) {
						wp_delete_term( $term->term_id, 'genre' );
					}
					break;
				default:
					break;
			}

		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {

			global $wpdb;
			$wpdb->hide_errors();

			$contents = $wpdb->get_results( 'SELECT term_id, slug FROM ' . $wpdb->terms . ' WHERE slug LIKE "wpml_genre%"' );
			$genres   = array();

			foreach ( $contents as $term )
				if ( false !== strpos( $term->slug, 'wpml_genre' ) )
					$genres[] = $term->term_id;

			if ( ! empty( $genres ) )
				$wpdb->query( 'UPDATE ' . $wpdb->term_taxonomy . ' SET taxonomy = "genre" WHERE term_id IN (' . implode( ',', $genres ) . ') AND taxonomy = "post_tag"' );

			$wpdb->query(
				'UPDATE ' . $wpdb->terms . ' SET slug = REPLACE(slug, "wpml_genre-", "")'
			);

			self::register_genre_taxonomy();

		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {

			self::clean_genres( 'deactivate' );
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.0
		 */
		public static function uninstall() {

			self::clean_genres( 'uninstall' );
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}

endif;