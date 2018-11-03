<?php
/**
 * Define the Taxonomies Registrar class.
 *
 * Register required .
 *
 * @link https://wpmovielibrary.com
 * @since 3.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\registrars;

/**
 * Register the plugin custom taxonomies.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Taxonomies {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		$this->taxonomies = array(
			'actor' => array(
				'slug'  => 'actor',
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
					),
				),
				'post_type' => array( 'movie' ),
				'archive'   => 'actors',
			),
			'collection' => array(
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
					),
				),
				'post_type' => array( 'movie' ),
				'archive'   => 'collections',
			),
			'genre' => array(
				'slug'  => 'genre',
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
					),
				),
				'post_type' => array( 'movie' ),
				'archive'   => 'genres',
			),
		);
	}

	/**
	 * Register Custom Taxonomies.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register() {

		/**
		 * Filter the custom taxonomies parameters prior to registration.
		 *
		 * @since 3.0.0
		 *
		 * @param array $taxonomies Taxonomies list.
		 */
		$taxonomies = apply_filters( 'wpmoly/filter/taxonomies', $this->taxonomies );

		if ( empty( $taxonomies ) ) {
			return false;
		}

		foreach ( $taxonomies as $slug => $taxonomy ) {

			/**
			 * Filter the custom taxonomy parameters prior to registration.
			 *
			 * @since 3.0.0
			 *
			 * @param array $taxonomy Taxonomy parameters.
			 */
			$args = apply_filters( "wpmoly/filter/taxonomy/{$slug}", $taxonomy['args'] );

			$args = array_merge( array(
				'show_ui'               => true,
				'show_tagcloud'         => true,
				'show_admin_column'     => true,
				'hierarchical'          => false,
				'query_var'             => true,
				'sort'                  => true,
				'show_in_rest'          => true,
				'rest_base'             => ! empty( $taxonomy['archive'] ) ? $taxonomy['archive'] : $slug,
				'rewrite'               => array(
					'slug' => $taxonomy['archive'],
				),
			), $args );

			foreach ( $taxonomy['post_type'] as $post_type ) {
				register_taxonomy( $slug, $post_type, $args );
			}
		} // End foreach().
	}

	/**
	 * Add 'Categories' Block to movies editor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $blocks Block list.
	 *
	 * @return array
	 */
	public function register_category_editor_block( $blocks ) {

		if ( wpmoly_o( 'enable-categories' ) ) {
			$blocks['movie-categories'] = array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Post Categories Block', 'wpmovielibrary' ),
					'title'       => __( 'Categories', 'wpmovielibrary' ),
					'description' => __( 'Post Block to quickly manage categories', 'wpmovielibrary' ),
					'controller'  => 'CategoriesBlock',
					'template'    => 'editors/blocks/posts/categories.php',
				),
			);
		}

		return $blocks;
	}

	/**
	 * Add 'Tags' Block to movies editor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $blocks Block list.
	 *
	 * @return array
	 */
	public function register_post_tag_editor_block( $blocks ) {

		if ( wpmoly_o( 'enable-tags' ) ) {
			$blocks['movie-tags'] = array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Post Tags Block', 'wpmovielibrary' ),
					'title'       => __( 'Tags', 'wpmovielibrary' ),
					'description' => __( 'Post Block to quickly manage tags', 'wpmovielibrary' ),
					'controller'  => 'TagsBlock',
					'template'    => 'editors/blocks/posts/tags.php',
				),
			);
		}

		return $blocks;
	}

	/**
	 * Filter term link.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $termlink Term link URL.
	 * @param object $term     Term object.
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return string
	 */
	public function filter_term_link( $termlink, $term, $taxonomy ) {

		if ( ! in_array( $taxonomy, array( 'actor', 'collection', 'genre' ) ) ) {
			return $termlink;
		}

		if ( ! has_archives_page( $taxonomy ) ) {
			$permalinks = get_option( 'wpmoly_permalinks', array() );
			if ( ! empty( $permalinks[ $taxonomy ] ) ) {
				$termlink = $permalinks[ $taxonomy ] . $term->slug;
			}
		} else {
			$baselink = trailingslashit( get_taxonomy_archive_link( $taxonomy ) );
			$termlink = trailingslashit( $baselink . $term->slug );
		}

		return $termlink;
	}

}
