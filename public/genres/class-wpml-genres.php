<?php
/**
 * WPMovieLibrary Genre Class extension.
 * 
 * Add and manage a Genre Custom Taxonomy
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
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
		 * @since    1.0.0
		 */
		public function __construct() {

			$this->register_hook_callbacks();

			$this->widgets = array(
				'WPML_Genres_Widget'
			);
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', __CLASS__ . '::wpml_register_genre_taxonomy' );
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
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

			$contents = $wpdb->get_results( 'SELECT term_id, slug FROM ' . $wpdb->terms . ' WHERE slug LIKE "wpml_genre%"' );
			$genres   = array();

			foreach ( $contents as $term )
				if ( false !== strpos( $term->slug, 'wpml_collection' ) )
					$genres[] = $term->term_id;

			if ( ! empty( $genres ) )
				$wpdb->query( 'UPDATE ' . $wpdb->term_taxonomy . ' SET taxonomy = "genre" WHERE term_id IN (' . implode( ',', $genres ) . ')' );

			$wpdb->query(
				'UPDATE ' . $wpdb->terms . ' SET slug = REPLACE(slug, "wpml_genre-", "")'
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
			$genres      = $o['wpml']['settings']['deactivate']['genres'];

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
		 * @since    1.0.0
		 */
		public static function wpml_register_genre_taxonomy() {

			if ( ! WPML_Settings::wpml__enable_genre() )
				return false;

			register_taxonomy(
				'genre',
				'movie',
				array(
					'labels'   => array(
						'name'          => __( 'Genres', 'wpml' ),
						'add_new_item'  => __( 'New Genre', 'wpml' )
					),
					'show_ui'           => true,
					'show_tagcloud'     => true,
					'show_admin_column' => true,
					'hierarchical'      => false,
					'query_var'         => true,
					'sort'              => true,
					'rewrite'           => array( 'slug' => 'genre' )
				)
			);

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
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;