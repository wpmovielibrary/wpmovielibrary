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
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;