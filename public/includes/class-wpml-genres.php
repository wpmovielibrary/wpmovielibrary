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

			add_action( 'init', __CLASS__ . '::wpml_register_genre_taxonomy' );
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

			if ( ! WPML_Settings::wpml_o( 'wpml-settings-enable_genre' ) )
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
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;