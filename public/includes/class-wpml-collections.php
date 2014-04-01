<?php
/**
 * WPMovieLibrary Collection Class extension.
 * 
 * Add and manage a Collection Custom Taxonomy
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Collections' ) ) :

	class WPML_Collections extends WPML_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', __CLASS__ . '::wpml_register_collection_taxonomy' );
		}

		/**
		 * Register a 'Collection' custom taxonomy to aggregate movies
		 * 
		 * Collections are Category-like taxonomies: hierarchical, no
		 * tagcloud, showing in admin columns.
		 * 
		 * @see wpml_movies_columns_head()
		 * @see wpml_movies_columns_content()
		 *
		 * @since    1.0.0
		 */
		public static function wpml_register_collection_taxonomy() {

			if ( ! WPML_Settings::wpml_o( 'wpml-settings-enable_collection' ) )
				return false;

			register_taxonomy(
				'collection',
				'movie',
				array(
					'labels'   => array(
						'name'          => __( 'Collections', 'wpml' ),
						'add_new_item'  => __( 'New Movie Collection', 'wpml' )
					),
					'show_ui'           => true,
					'show_tagcloud'     => false,
					'show_admin_column' => true,
					'hierarchical'      => true,
					'query_var'         => true,
					'sort'              => true,
					'rewrite'           => array( 'slug' => 'collection' )
				)
			);

		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {

			global $wpdb;

			$contents = $wpdb->get_results( 'SELECT term_id, slug FROM ' . $wpdb->terms . ' WHERE slug LIKE "wpml_collection%"' );
			$collections = array();

			foreach ( $contents as $term )
				if ( false !== strpos( $term->slug, 'wpml_collection' ) )
					$collections[] = $term->term_id;

			if ( ! empty( $collections ) )
				$wpdb->query( 'UPDATE ' . $wpdb->term_taxonomy . ' SET taxonomy = "collection" WHERE term_id IN (' . implode( ',', $collections ) . ')' );

			$wpdb->query(
				'UPDATE ' . $wpdb->terms . ' SET slug = REPLACE(slug, "wpml_collection-", "")'
			);

		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {

			global $wpdb;

			$o           = get_option( 'wpml_settings' );
			$collections = $o['wpml']['settings']['deactivate']['collections'];

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

		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;