<?php
/**
 * WPMovieLibrary Actor Class extension.
 * 
 * Add and manage an Actor Custom Taxonomy
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Actors' ) ) :

	class WPML_Actors extends WPML_Module {

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

			add_action( 'init', __CLASS__ . '::wpml_register_actor_taxonomy' );
		}

		/**
		 * Register an 'Actor' custom taxonomy.
		 * 
		 * Actors are Tag-like taxonomies: not-hierarchical, tagcloud, and
		 * are displayed by a custom way in admin columns. This is meant
		 * to override WordPress default ordering of Taxonomies.
		 * 
		 * @see https://github.com/Askelon/WPMovieLibrary/issues/7
		 * 
		 * @see wpml_movies_columns_head()
		 * @see wpml_movies_columns_content()
		 *
		 * @since    1.0.0
		 */
		public static function wpml_register_actor_taxonomy() {

			if ( ! WPML_Settings::wpml_o( 'wpml-settings-enable_actor' ) )
				return false;

			register_taxonomy(
				'actor',
				'movie',
				array(
					'labels'   => array(
						'name'          => __( 'Actors', 'wpml' ),
						'add_new_item'  => __( 'New Actor', 'wpml' )
					),
					'show_ui'           => true,
					'show_tagcloud'     => true,
					'show_admin_column' => true,
					'hierarchical'      => false,
					'query_var'         => true,
					'sort'              => true,
					'rewrite'           => array( 'slug' => 'actor' )
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

			$contents = $wpdb->get_results( 'SELECT term_id, slug FROM ' . $wpdb->terms . ' WHERE slug LIKE "wpml_actor%"' );
			$actors      = array();

			foreach ( $contents as $term )
				if ( false !== strpos( $term->slug, 'wpml_actor' ) )
					$actors[] = $term->term_id;

			if ( ! empty( $actors ) )
				$wpdb->query( 'UPDATE ' . $wpdb->term_taxonomy . ' SET taxonomy = "actor" WHERE term_id IN (' . implode( ',', $actors ) . ')' );

			$wpdb->query(
				'UPDATE ' . $wpdb->terms . ' SET slug = REPLACE(slug, "wpml_actor-", "")'
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
			$actors      = $o['wpml']['settings']['deactivate']['actors'];

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

		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;