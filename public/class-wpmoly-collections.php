<?php
/**
 * WPMovieLibrary Collection Class extension.
 * 
 * Add and manage a Collection Custom Taxonomy
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Collections' ) ) :

	class WPMOLY_Collections extends WPMOLY_Module {

		protected $widgets;

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {

			if ( ! wpmoly_o( 'enable-collection' ) )
				return false;

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', array( $this, 'register_collection_taxonomy' ) );
			
		}

		/**
		 * Register a 'Collection' custom taxonomy to aggregate movies
		 * 
		 * Collections are Category-like taxonomies: hierarchical, no
		 * tagcloud, showing in admin columns.
		 * 
		 * @see wpmoly_movies_columns_head()
		 * @see wpmoly_movies_columns_content()
		 *
		 * @since    1.0
		 */
		public function register_collection_taxonomy() {

			$taxonomy    = 'collection';
			$object_type = array( 'movie' );

			$slug = 'collection';
			if ( '1' == wpmoly_o( 'rewrite-enable' ) ) {
				$rewrite = wpmoly_o( 'rewrite-collection' );
				if ( '' != $slug )
					$slug = $rewrite;
			}

			if ( wpmoly_o( 'collection-posts' ) )
				$object_type[] = 'post';

			$args = array(
				'labels'   => array(
					'name'          => __( 'Collections', 'wpmovielibrary' ),
					'add_new_item'  => __( 'New Movie Collection', 'wpmovielibrary' )
				),
				'show_ui'           => true,
				'show_tagcloud'     => false,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'query_var'         => true,
				'sort'              => true,
				'rewrite'           => array( 'slug' => $slug )
			);

			register_taxonomy( $taxonomy, $object_type, $args );

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
		public static function clean_collections( $action ) {

			if ( ! in_array( $action, array( 'deactivate', 'uninstall' ) ) )
				return false;

			global $wpdb;
			$wpdb->hide_errors();

			$_action = get_option( 'wpmoly_settings' );
			if ( ! $_action || ! isset( $_action[ "wpmoly-{$action}-collections" ] ) )
				return false;

			$action = $_action[ "wpmoly-{$action}-collections" ];
			if ( is_array( $action ) )
				$action = $action[0];

			$contents = get_terms( array( 'collection' ), array() );

			switch ( $action ) {
				case 'convert':
					foreach ( $contents as $term ) {
						wp_update_term( $term->term_id, 'collection', array( 'slug' => 'wpmoly_collection-' . $term->slug ) );
						$wpdb->update(
							$wpdb->term_taxonomy,
							array( 'taxonomy' => 'category' ),
							array( 'taxonomy' => 'collection' ),
							array( '%s' )
						);
					}
					break;
				case 'delete':
					foreach ( $contents as $term ) {
						wp_delete_term( $term->term_id, 'collection' );
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
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {

			global $wpdb;
			$wpdb->hide_errors();

			$contents = $wpdb->get_results( 'SELECT term_id, slug FROM ' . $wpdb->terms . ' WHERE slug LIKE "wpmoly_collection%"' );
			$collections = array();

			foreach ( $contents as $term )
				if ( false !== strpos( $term->slug, 'wpmoly_collection' ) )
					$collections[] = $term->term_id;

			if ( ! empty( $collections ) )
				$wpdb->query( 'UPDATE ' . $wpdb->term_taxonomy . ' SET taxonomy = "collection" WHERE term_id IN (' . implode( ',', $collections ) . ') AND taxonomy = "category"' );

			$wpdb->query(
				'UPDATE ' . $wpdb->terms . ' SET slug = REPLACE(slug, "wpmoly_collection-", "")'
			);

			self::register_collection_taxonomy();

		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {

			self::clean_collections( 'deactivate' );
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.0
		 */
		public static function uninstall() {

			self::clean_collections( 'uninstall' );
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}

endif;