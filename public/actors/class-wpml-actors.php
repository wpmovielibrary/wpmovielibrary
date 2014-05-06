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

		protected $widgets;

		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

			$this->register_hook_callbacks();

			$this->widgets = array(
				'WPML_Actors_Widget'
			);
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', __CLASS__ . '::register_actor_taxonomy' );
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
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
		public static function register_actor_taxonomy() {

			if ( ! WPML_Settings::taxonomies__enable_actor() )
				return false;

			$slug = WPML_Settings::taxonomies__actor_rewrite();
			$slug = ( '' != $slug ? $slug : 'actor' );

			register_taxonomy(
				'actor',
				'movie',
				array(
					'labels'   => array(
						'name'          => __( 'Actors', WPML_SLUG ),
						'add_new_item'  => __( 'New Actor', WPML_SLUG )
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
		public static function clean_actors( $action ) {

			if ( ! in_array( $action, array( 'deactivate', 'uninstall' ) ) )
				return false;

			global $wpdb;

			$_action = get_option( 'wpml_settings' );
			if ( ! $_action || ! isset( $_action[ $action ] ) || ! isset( $_action[ $action ]['actors'] ) )
				return false;

			$action = $_action[ $action ]['actors'];
			if ( is_array( $action ) )
				$action = $action[0];

			$contents = get_terms( array( 'actor' ), array() );

			switch ( $action ) {
				case 'convert':
					foreach ( $contents as $term ) {
						wp_update_term( $term->term_id, 'actor', array( 'slug' => 'wpml_actor-' . $term->slug ) );
						$wpdb->update(
							$wpdb->term_taxonomy,
							array( 'taxonomy' => 'post_tag' ),
							array( 'taxonomy' => 'actor' ),
							array( '%s' )
						);
					}
					break;
				case 'delete':
					foreach ( $contents as $term ) {
						wp_delete_term( $term->term_id, 'actor' );
					}
					break;
				default:
					break;
			}
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {

			global $wpdb, $wp_rewrite;

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

			self::register_actor_taxonomy();

		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {

			self::clean_actors( 'deactivate' );
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.0.0
		 */
		public static function uninstall() {

			self::clean_actors( 'uninstall' );
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;