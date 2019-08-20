<?php
/**
 * Define the Post Types Registrar class.
 *
 * Register required Custom Post Types.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\registrars;

use wpmoly\utils;

/**
 * Register the plugin custom post types and custom post statuses.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Post_Types {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		$this->post_types = array(
			'grid' => array(
				'labels' => array(
					'name'               => __( 'Grids', 'wpmovielibrary' ),
					'singular_name'      => __( 'Grid', 'wpmovielibrary' ),
					'add_new'            => __( 'Add New', 'wpmovielibrary' ),
					'add_new_item'       => __( 'Add New Grid', 'wpmovielibrary' ),
					'edit_item'          => __( 'Edit Grid', 'wpmovielibrary' ),
					'new_item'           => __( 'New Grid', 'wpmovielibrary' ),
					'all_items'          => __( 'All Grids', 'wpmovielibrary' ),
					'view_item'          => __( 'View Grid', 'wpmovielibrary' ),
					'search_items'       => __( 'Search Grids', 'wpmovielibrary' ),
					'not_found'          => __( 'No grids found', 'wpmovielibrary' ),
					'not_found_in_trash' => __( 'No grids found in Trash', 'wpmovielibrary' ),
					'parent_item_colon'  => '',
					'menu_name'          => __( 'Grids', 'wpmovielibrary' ),
				),
				'rewrite'            => false,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'rest_base'          => 'grids',
				'show_in_menu'       => false,
				'has_archive'        => false,
				'supports'           => array( 'title', 'custom-fields' ),
				//'rest_controller_class' => '\wpmoly\rest\endpoints\Posts_Controller',
			),
			'movie' => array(
				'labels' => array(
					'name'               => __( 'Movies', 'wpmovielibrary' ),
					'singular_name'      => __( 'Movie', 'wpmovielibrary' ),
					'add_new'            => __( 'Add New', 'wpmovielibrary' ),
					'add_new_item'       => __( 'Add New Movie', 'wpmovielibrary' ),
					'edit_item'          => __( 'Edit Movie', 'wpmovielibrary' ),
					'new_item'           => __( 'New Movie', 'wpmovielibrary' ),
					'all_items'          => __( 'All Movies', 'wpmovielibrary' ),
					'view_item'          => __( 'View Movie', 'wpmovielibrary' ),
					'search_items'       => __( 'Search Movies', 'wpmovielibrary' ),
					'not_found'          => __( 'No movies found', 'wpmovielibrary' ),
					'not_found_in_trash' => __( 'No movies found in Trash', 'wpmovielibrary' ),
					'parent_item_colon'  => '',
					'menu_name'          => __( 'Movies', 'wpmovielibrary' ),
				),
				'rewrite' => array(
					'slug' => _x( 'movie', 'slug', 'wpmovielibrary' ),
				),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'rest_base'          => 'movies',
				'show_in_menu'       => false,
				'has_archive'        => _x( 'movies', 'slug', 'wpmovielibrary' ),
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
				//'rest_controller_class' => '\wpmoly\rest\endpoints\Posts_Controller',
				'menu_position'      => 2,
				'menu_icon'          => 'dashicons-wpmoly',
			),
			'person' => array(
				'labels' => array(
					'name'               => __( 'Persons', 'wpmovielibrary' ),
					'singular_name'      => __( 'Person', 'wpmovielibrary' ),
					'add_new'            => __( 'Add New', 'wpmovielibrary' ),
					'add_new_item'       => __( 'Add New Person', 'wpmovielibrary' ),
					'edit_item'          => __( 'Edit Person', 'wpmovielibrary' ),
					'new_item'           => __( 'New Person', 'wpmovielibrary' ),
					'all_items'          => __( 'All Persons', 'wpmovielibrary' ),
					'view_item'          => __( 'View Person', 'wpmovielibrary' ),
					'search_items'       => __( 'Search Persons', 'wpmovielibrary' ),
					'not_found'          => __( 'No persons found', 'wpmovielibrary' ),
					'not_found_in_trash' => __( 'No persons found in Trash', 'wpmovielibrary' ),
					'parent_item_colon'  => '',
					'menu_name'          => __( 'Persons', 'wpmovielibrary' ),
				),
				'rewrite' => array(
					'slug' => _x( 'person', 'slug', 'wpmovielibrary' ),
				),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'rest_base'          => 'persons',
				'show_in_menu'       => false,
				'has_archive'        => _x( 'persons', 'slug', 'wpmovielibrary' ),
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
				//'rest_controller_class' => '\wpmoly\rest\endpoints\Posts_Controller',
				'menu_position'      => 2,
				'menu_icon'          => 'dashicons-wpmoly',
			),
		);
	}

	/**
	 * Register Custom Post Types.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register() {

		/**
		 * Filter the Custom Post Types parameters prior to registration.
		 *
		 * @since 3.0.0
		 *
		 * @param array $post_types Post Types list.
		 */
		$post_types = apply_filters( 'wpmoly/filter/post_types', $this->post_types );

		if ( empty( $post_types ) ) {
			return false;
		}

		foreach ( $post_types as $slug => $params ) {

			/**
			 * Filter the Custom Post Type parameters prior to registration.
			 *
			 * @since 3.0.0
			 *
			 * @param array $args Post Type args
			 */
			$args = apply_filters( "wpmoly/filter/post_type/{$slug}", $params );

			$args = array_merge( array(
				'labels'             => array(),
				'rewrite'            => true,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'show_in_menu'       => true,
				'has_archive'        => true,
				'menu_position'      => null,
				'menu_icon'          => null,
				'taxonomies'         => array(),
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			), $args );

			register_post_type( $slug, $args );
		}
	}

	/**
	 * Add support for standard taxonomies to movies.
	 *
	 * Depending on user settings, movies can used with standard Post Tag and
	 * Categories.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $args 'movie' Custom Post Type parameters.
	 *
	 * @return array
	 */
	public function movie_standard_taxonomies( $args ) {

		if ( utils\o( 'enable-categories' ) ) {
			$args['taxonomies'][] = 'category';
		}

		if ( utils\o( 'enable-tags' ) ) {
			$args['taxonomies'][] = 'post_tag';
		}

		return $args;
	}

}
