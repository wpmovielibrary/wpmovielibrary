<?php
/**
 * Define the content registrator.
 *
 * Register required Custom Post Types, Custom Taxonomies.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

/**
 * Register the 'movie' Custom Post Type along with the 'import' post statuses.
 * 
 * Also register 'collection', 'actor' and 'genre' Custom Taxonomies.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Registrar {

	/**
	 * Singleton.
	 *
	 * @var    Registrar
	 */
	private static $instance = null;

	/**
	 * Default Custom Post Types.
	 * 
	 * @var    array
	 */
	private $post_types = array();

	/**
	 * Default Custom Post Statuses.
	 * 
	 * @var    array
	 */
	private $post_statuses = array();

	/**
	 * Default Custom Taxonomies.
	 * 
	 * @var    array
	 */
	private $taxonomies = array();

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   Options
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Register Custom Post Types.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register_post_types() {

		$permalinks = get_option( 'wpmoly_permalinks' );
		if ( ! $permalinks ) {
			$permalinks = array();
		}

		$post_types = array(
			array(
				'slug' => 'movie',
				'args' => array(
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
						'menu_name'          => __( 'Movie Library', 'wpmovielibrary' )
					),
					'rewrite' => array(
						'slug' => ! empty( $permalinks['movie_base'] ) ? $permalinks['movie_base'] : 'movies'
					),
					'public'             => true,
					'publicly_queryable' => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'has_archive'        => true,
					'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
					'menu_position'      => 2,
					'menu_icon'          => 'dashicons-wpmoly'
				)
			),
			array(
				'slug' => 'grid',
				'args' => array(
					'labels' => array(
						'name'               => __( 'Grids', 'wpmovielibrary' ),
						'singular_name'      => __( 'Grid', 'wpmovielibrary' ),
						'add_new'            => __( 'Add New', 'wpmovielibrary' ),
						'add_new_item'       => __( 'Add New Grid', 'wpmovielibrary' ),
						'edit_item'          => __( 'Edit Grid', 'wpmovielibrary' ),
						'new_item'           => __( 'New Grid', 'wpmovielibrary' ),
						'all_items'          => __( 'Grids', 'wpmovielibrary' ),
						'view_item'          => __( 'View Grid', 'wpmovielibrary' ),
						'search_items'       => __( 'Search Grids', 'wpmovielibrary' ),
						'not_found'          => __( 'No grids found', 'wpmovielibrary' ),
						'not_found_in_trash' => __( 'No grids found in Trash', 'wpmovielibrary' ),
						'parent_item_colon'  => '',
						'menu_name'          => __( 'Grids', 'wpmovielibrary' )
					),
					'rewrite'            => false,
					'public'             => false,
					'publicly_queryable' => false,
					'show_ui'            => true,
					'show_in_menu'       => 'edit.php?post_type=movie',
					'has_archive'        => false,
					'supports'           => array( 'title' )
				)
			)
		);

		/**
		 * Filter the Custom Post Types parameters prior to registration.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $post_types Post Types list
		 */
		$this->post_types = apply_filters( 'wpmoly/filter/post_types', $post_types );

		foreach ( $this->post_types as $post_type ) {

			/**
			 * Filter the Custom Post Type parameters prior to registration.
			 * 
			 * @since    3.0
			 * 
			 * @param    array    $args Post Type args
			 */
			$args = apply_filters( "wpmoly/filter/post_type/{$post_type['slug']}", $post_type['args'] );

			$args = array_merge( array(
				'labels'             => array(),
				'rewrite'            => true,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'has_archive'        => true,
				'menu_position'      => null,
				'menu_icon'          => null,
				'taxonomies'         => array(),
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' )
			), $args );

			register_post_type( $post_type['slug'], $args );
		}
	}

	/**
	 * Register Custom Post Statuses.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register_post_statuses() {

		$post_statuses = array(
			array(
				'slug' => 'import-draft',
				'args' => array(
					'label'       => _x( 'Imported Draft', 'wpmovielibrary' ),
					'label_count' => _n_noop( 'Imported Draft <span class="count">(%s)</span>', 'Imported Draft <span class="count">(%s)</span>', 'wpmovielibrary' ),
				)
			),
			array(
				'slug' => 'import-queued',
				'args' => array(
					'label'       => _x( 'Queued Movie', 'wpmovielibrary' ),
					'label_count' => _n_noop( 'Queued Movie <span class="count">(%s)</span>', 'Queued Movies <span class="count">(%s)</span>', 'wpmovielibrary' ),
				)
			)
		);

		/**
		 * Filter the Custom Post Statuses parameters prior to registration.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $post_statuses Post Statuses list
		 */
		$this->post_statuses = apply_filters( 'wpmoly/filter/post_statuses', $post_statuses );

		foreach ( $this->post_statuses as $post_status ) {

			/**
			 * Filter the Custom Post Status parameters prior to registration.
			 * 
			 * @since    3.0
			 * 
			 * @param    array    $args Post Status args
			 */
			$args = apply_filters( "wpmoly/filter/post_status/{$post_status['slug']}", $post_status['args'] );
			$args = array_merge( array(
				'label'                     => false,
				'label_count'               => false,
				'public'                    => false,
				'internal'                  => true,
				'private'                   => true,
				'publicly_queryable'        => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => false,
			), $args );

			register_post_status( $post_status['slug'], $args );
		}
	}

	/**
	 * Register Custom Taxonomies.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register_taxonomies() {

		$taxonomies = array(
			array(
				'slug'  => 'collection',
				'posts' => array( 'movie' ),
				'args'  => array(
					'labels' => array(
						'name'                       => __( 'Collections', 'wpmovielibrary' ),
						'add_new_item'               => __( 'New Collection', 'wpmovielibrary' ),
						'search_items'               => __( 'Search Collections', 'wpmovielibrary' ),
						'popular_items'              => __( 'Popular Collections', 'wpmovielibrary' ),
						'all_items'                  => __( 'All Collections', 'wpmovielibrary' ),
						'parent_item'                => __( 'Parent Collection', 'wpmovielibrary' ),
						'parent_item_colon'          => __( 'Parent Collection:', 'wpmovielibrary' ),
						'edit_item'                  => __( 'Edit Collection', 'wpmovielibrary' ),
						'view_item'                  => __( 'View Collection', 'wpmovielibrary' ),
						'update_item'                => __( 'Update Collection', 'wpmovielibrary' ),
						'add_new_item'               => __( 'Add New Collection', 'wpmovielibrary' ),
						'new_item_name'              => __( 'New Collection Name', 'wpmovielibrary' ),
						'separate_items_with_commas' => __( 'Separate collections with commas', 'wpmovielibrary' ),
						'add_or_remove_items'        => __( 'Add or remove collections', 'wpmovielibrary' ),
						'choose_from_most_used'      => __( 'Choose from the most used collections', 'wpmovielibrary' ),
						'not_found'                  => __( 'No collections found.', 'wpmovielibrary' ),
						'no_terms'                   => __( 'No collections', 'wpmovielibrary' ),
						'items_list_navigation'      => __( 'Collections list navigation', 'wpmovielibrary' ),
						'items_list'                 => __( 'Collections list', 'wpmovielibrary' ),
					)
				)
			),
			array(
				'slug'  => 'genre',
				'posts' => array( 'movie' ),
				'args'  => array(
					'labels' => array(
						'name'                       => __( 'Genres', 'wpmovielibrary' ),
						'add_new_item'               => __( 'New Genre', 'wpmovielibrary' ),
						'search_items'               => __( 'Search Genres', 'wpmovielibrary' ),
						'popular_items'              => __( 'Popular Genres', 'wpmovielibrary' ),
						'all_items'                  => __( 'All Genres', 'wpmovielibrary' ),
						'parent_item'                => __( 'Parent Genre', 'wpmovielibrary' ),
						'parent_item_colon'          => __( 'Parent Genre:', 'wpmovielibrary' ),
						'edit_item'                  => __( 'Edit Genre', 'wpmovielibrary' ),
						'view_item'                  => __( 'View Genre', 'wpmovielibrary' ),
						'update_item'                => __( 'Update Genre', 'wpmovielibrary' ),
						'add_new_item'               => __( 'Add New Genre', 'wpmovielibrary' ),
						'new_item_name'              => __( 'New Genre Name', 'wpmovielibrary' ),
						'separate_items_with_commas' => __( 'Separate genres with commas', 'wpmovielibrary' ),
						'add_or_remove_items'        => __( 'Add or remove genres', 'wpmovielibrary' ),
						'choose_from_most_used'      => __( 'Choose from the most used genres', 'wpmovielibrary' ),
						'not_found'                  => __( 'No genres found.', 'wpmovielibrary' ),
						'no_terms'                   => __( 'No genres', 'wpmovielibrary' ),
						'items_list_navigation'      => __( 'Genres list navigation', 'wpmovielibrary' ),
						'items_list'                 => __( 'Genres list', 'wpmovielibrary' ),
					)
				)
			),
			array(
				'slug'  => 'actor',
				'posts' => array( 'movie' ),
				'args'  => array(
					'labels' => array(
						'name'                       => __( 'Actors', 'wpmovielibrary' ),
						'add_new_item'               => __( 'New Actor', 'wpmovielibrary' ),
						'search_items'               => __( 'Search Actors', 'wpmovielibrary' ),
						'popular_items'              => __( 'Popular Actors', 'wpmovielibrary' ),
						'all_items'                  => __( 'All Actors', 'wpmovielibrary' ),
						'parent_item'                => __( 'Parent Actor', 'wpmovielibrary' ),
						'parent_item_colon'          => __( 'Parent Actor:', 'wpmovielibrary' ),
						'edit_item'                  => __( 'Edit Actor', 'wpmovielibrary' ),
						'view_item'                  => __( 'View Actor', 'wpmovielibrary' ),
						'update_item'                => __( 'Update Actor', 'wpmovielibrary' ),
						'add_new_item'               => __( 'Add New Actor', 'wpmovielibrary' ),
						'new_item_name'              => __( 'New Actor Name', 'wpmovielibrary' ),
						'separate_items_with_commas' => __( 'Separate actors with commas', 'wpmovielibrary' ),
						'add_or_remove_items'        => __( 'Add or remove actors', 'wpmovielibrary' ),
						'choose_from_most_used'      => __( 'Choose from the most used actors', 'wpmovielibrary' ),
						'not_found'                  => __( 'No actors found.', 'wpmovielibrary' ),
						'no_terms'                   => __( 'No actors', 'wpmovielibrary' ),
						'items_list_navigation'      => __( 'Actors list navigation', 'wpmovielibrary' ),
						'items_list'                 => __( 'Actors list', 'wpmovielibrary' ),
					)
				)
			)
		);

		/**
		 * Filter the custom taxonomies parameters prior to registration.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $taxonomies Taxonomies list
		 */
		$this->taxonomies = apply_filters( 'wpmoly/filter/taxonomies', $taxonomies );

		foreach ( $this->taxonomies as $taxonomy ) {

			if ( wpmoly_o( "{$taxonomy['slug']}-posts" ) ) {
				$taxonomy['args']['posts'][] = 'post';
			}

			$slug = $taxonomy['slug'];
			if ( ! empty( $permalinks["{$slug}_base"] ) ) {
				$slug = $permalinks["{$slug}_base"];
			}

			/**
			 * Filter the custom taxonomy parameters prior to registration.
			 * 
			 * @since    3.0
			 * 
			 * @param    array    $taxonomy Taxonomy parameters
			 */
			$args = apply_filters( "wpmoly/filter/taxonomy/{$taxonomy['slug']}", $taxonomy['args'] );

			$args = array_merge( array(
				'show_ui'           => true,
				'show_tagcloud'     => true,
				'show_admin_column' => true,
				'hierarchical'      => false,
				'query_var'         => true,
				'sort'              => true,
				'rewrite'           => array( 'slug' => $slug )
			), $args );

			register_taxonomy( $taxonomy['slug'], $taxonomy['posts'], $args );
		}
	}
}
