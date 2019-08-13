<?php
/**
 * The file that defines the library class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly;

/**
 * The library class.
 *
 * Define public features: archive pages content and titles, public styles and
 * scripts, admin bar menu...
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Library {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		add_action( 'pre_get_posts', array( &$this, 'add_movies_to_frontpage' ) );
		add_action( 'pre_get_posts', array( &$this, 'add_persons_to_frontpage' ) );

		add_filter( 'wpmoly/filter/archive/page/wp_title',   array( &$this, 'filter_archive_title' ), 10, 3 );
		add_filter( 'wpmoly/filter/archive/page/post_title', array( &$this, 'filter_archive_title' ), 10, 3 );

		add_filter( 'wpmoly/filter/taxonomy/archive/page/content', array( &$this, 'filter_taxonomy_archive_page_content' ), 10, 3 );
		add_filter( 'wpmoly/filter/movie/archive/page/content',    array( &$this, 'filter_post_archive_page_content' ), 10, 2 );
		add_filter( 'wpmoly/filter/person/archive/page/content',   array( &$this, 'filter_post_archive_page_content' ), 10, 2 );

		add_filter( 'pre_update_option',                       array( &$this, 'pre_update_option' ), 10, 3 );
		add_filter( 'pre_update_option__wpmoly_archive_pages', array( &$this, 'pre_update_archive_pages' ), 10, 2 );

		add_filter( 'update_option__wpmoly_archive_pages', 'flush_rewrite_rules' );
	}

	/**
	 * Register the plugin's assets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_assets() {

		$assets = core\Assets::get_instance();
		add_action( 'wp_enqueue_scripts',      array( $assets, 'enqueue_public_styles' ), 95 );
		add_action( 'wp_enqueue_scripts',      array( $assets, 'enqueue_public_scripts' ), 95 );
		add_action( 'wp_print_footer_scripts', array( $assets, 'enqueue_public_templates' ), 95 );
	}

	/**
	 * Add a submenu to the 'Edit Post' menu to edit the grid related to an
	 * archive page.
	 *
	 * The admin bar is used on both front side and dashboard, we need to
	 * make this public.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public function admin_bar_menu( $wp_admin_bar ) {

		$post_id = get_the_ID();
		if ( ! $post_id || ! utils\is_archive_page( $post_id ) ) {
			return false;
		}

		// Missing edit menu
		if ( ! $wp_admin_bar->get_node( 'edit' ) ) {
			return false;
		}

		// Retrieve related grid
		$grid_id = get_post_meta( $post_id, '_wpmoly_grid_id', true );
		if ( empty( $grid_id ) ) {
			return false;
		}

		// Add a new node
		$wp_admin_bar->add_node( array(
			'id'     => 'edit-grid',
			'title'  => __( 'Edit Grid', 'wpmovielibrary' ),
			'parent' => 'edit',
			'href'   => get_edit_post_link( $grid_id ),
		) );
	}

	/**
	 * Try to include movies to the blog frontpage.
	 *
	 * This won't work on themes using custom hacky templates.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_Query $query
	 */
	public function add_movies_to_frontpage( $query ) {

		if ( ! utils\is_o( 'add_movies_to_frontpage' ) ) {
			return false;
		}

		if ( $query->is_main_query() && $query->is_home() ) {
			$post_types = array_merge( (array) $query->get( 'post_type' ), array( 'movie' ) );
			$query->set( 'post_type', $post_types );
		}
	}

	/**
	 * Try to include persons to the blog frontpage.
	 *
	 * This won't work on themes using custom hacky templates.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_Query $query
	 */
	public function add_persons_to_frontpage( $query ) {

		if ( ! utils\is_o( 'add_persons_to_frontpage' ) ) {
			return false;
		}

		if ( $query->is_main_query() && $query->is_home() ) {
			$post_types = array_merge( (array) $query->get( 'post_type' ), array( 'person' ) );
			$query->set( 'post_type', $post_types );
		}
	}

	/**
	 * Show the movie Headbox before post content.
	 *
	 * @TODO support custom integration of the Headbox inside post content.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $content The Post content.
	 */
	public function set_movie_post_content( $content = '' ) {

		if ( 'movie' != get_post_type() || ! utils\is_o( 'enable_movie_headbox' ) ) {
			return $content;
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return $content;
		}

		$movie = utils\movie\get( $post_id );
		$headbox = utils\get_headbox( $movie );

		if ( is_single() ) {
			$headbox->set_theme( 'extended' );
		} elseif ( is_archive() || is_search() ) {
			$headbox->set_theme( 'default' );
		}

		$template = utils\movie\get_headbox_template( $headbox );

		return $template->render() . $content;
	}

	/**
	 * Show the person Headbox before post content.
	 *
	 * @TODO support custom integration of the Headbox inside post content.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $content The Post content.
	 */
	public function set_person_post_content( $content = '' ) {

		if ( 'person' != get_post_type() || ! utils\is_o( 'enable_person_headbox' ) ) {
			return $content;
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return $content;
		}

		$person = utils\person\get( $post_id );
		$headbox = utils\get_headbox( $person );

		if ( is_single() ) {
			$headbox->set_theme( 'extended' );
		} elseif ( is_archive() || is_search() ) {
			$headbox->set_theme( 'default' );
		}

		$template = utils\person\get_headbox_template( $headbox );

		return $template->render() . $content;
	}

	/**
	 * Adapt Archive Page post titles to match content.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string  $post_title Page original post title.
	 * @param WP_Post $post       Archive page Post instance.
	 *
	 * @return string
	 */
	public function set_archive_page_title( $post_title, $post = null ) {

		if ( is_admin() || empty( $post->ID ) ) {
			return $post_title;
		}

		if ( ! utils\is_archive_page( $post->ID ) ) {
			return $post_title;
		}

		$adapt = get_post_meta( $post->ID, '_wpmoly_adapt_page_title', true );
		if ( ! utils\is_bool( $adapt ) ) {
			return $post_title;
		}

		/**
		 * Filter Archive Page titles to match content.
		 *
		 * @since 3.0.0
		 *
		 * @access public
		 *
		 * @param string $title   Page original title.
		 * @param int    $post_id Archive page Post ID.
		 * @param string $context Context.
		 */
		$new_title = apply_filters( 'wpmoly/filter/archive/page/wp_title', $post_title, $post->ID, 'wp_title' );

		return $new_title;
	}

	/**
	 * Adapt Archive Page titles to match content.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $post_title Page original post title.
	 * @param int    $post_id    Archive page Post ID.
	 *
	 * @return string
	 */
	public function set_archive_page_post_title( $post_title, $post_id ) {

		global $wp_query;

		if ( is_admin() || ! utils\is_archive_page( $post_id ) || ! in_the_loop() ) {
			return $post_title;
		}

		$adapt = get_post_meta( $post_id, '_wpmoly_adapt_post_title', true );
		if ( ! utils\is_bool( $adapt ) ) {
			return $post_title;
		}

		/**
		 * Filter Archive Page titles to match content.
		 *
		 * @since 3.0.0
		 *
		 * @access public
		 *
		 * @param string $title   Page original post title.
		 * @param int    $post_id Archive page Post ID.
		 * @param string $context Context.
		 */
		$new_title = apply_filters( 'wpmoly/filter/archive/page/post_title', $post_title, $post_id, 'post_title' );

		return $new_title;
	}

	/**
	 * Adapt Archive Page titles to match content.
	 *
	 * Mostly used to feature the term name in the page and post title when
	 * showing a single term archives.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $title   Page original post title.
	 * @param int    $post_id Archive page Post ID.
	 * @param string $context Context, either 'wp_title' (page title) or 'post_title' (page post title).
	 *
	 * @return string
	 */
	public function filter_archive_title( $title, $post_id, $context ) {

		$type = utils\get_archive_page_type( $post_id );
		$name = get_query_var( $type );
		if ( empty( $name ) ) {
			return $title;
		}

		$term = get_term_by( 'slug', $name, $type );
		if ( ! $term ) {
			return $title;
		}

		$title = sprintf( _x( '%1$s: %2$s', 'Archive page title', 'wpmovielibrary' ), $title, $term->name );

		return $title;
	}

	/**
	 * Filter post content to add grid to archive pages.
	 *
	 * Determine if we're dealing with a single item, ie. a term, or a real
	 * archive page.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $content Post content.
	 *
	 * @return string
	 */
	public function set_archive_page_content( $content ) {

		$post_id = get_the_ID();
		if ( is_admin() || ! utils\is_archive_page( $post_id ) ) {
			return $content;
		}

		$type = utils\get_archive_page_type( $post_id );
		if ( empty( $type ) || ( ! post_type_exists( $type ) && ! taxonomy_exists( $type ) ) ) {
			return $content;
		}

		if ( taxonomy_exists( $type ) ) {
			/**
			 * Filter taxonomy archive page content.
			 *
			 * @since 3.0.0
			 *
			 * @param string $content Current post content.
			 * @param int    $post_id Current Post ID.
			 * @param string $type    Archive page type.
			 */
			$content = apply_filters( 'wpmoly/filter/taxonomy/archive/page/content', $content, $post_id, $type );
		} else if ( post_type_exists( $type ) ) {
			/**
			 * Filter post archive page content.
			 *
			 * @since 3.0.0
			 *
			 * @param string $content Current post content.
			 * @param int    $post_id Current Post ID.
			 * @param string $type    Archive page type.
			 */
			$content = apply_filters( 'wpmoly/filter/post/archive/page/content', $content, $post_id, $type );
		}

		/**
		 * Filter archive page content.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Current content.
		 * @param int    $post_id Current Post ID.
		 */
		$content = apply_filters( "wpmoly/filter/{$type}/archive/page/content", $content, $post_id );

		return $content;
	}

	/**
	 * Handle single item content.
	 *
	 * Mostly used to show custom pages for taxonomy terms.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $content Post content.
	 * @param int    $post_id Current Post ID.
	 * @param string $type    Archive page type.
	 *
	 * @return string
	 */
	public function filter_taxonomy_archive_page_content( $content, $post_id, $type ) {

		$pre_content = '';

		$name = get_query_var( $type );
		$term = get_term_by( 'slug', $name, $type );

		if ( $term && utils\is_o( "enable_{$type}_headbox" ) ) {

			$name = $term->name;
			$node = call_user_func( "\wpmoly\utils\\{$term->taxonomy}\get", $term );

			$theme = get_post_meta( $post_id, '_wpmoly_headbox_theme', true );
			$headbox = utils\get_term_headbox( $node );
			$headbox->set_theme( $theme );

			$headbox_template = utils\get_headbox_template( $headbox );
			$pre_content = $headbox_template->render();
		}

		if ( $term ) {
			$archive_page_id = utils\get_archives_page_id( 'movie' );
		} else {
			$archive_page_id = utils\get_archives_page_id( $type );
		}

		if ( ! $archive_page_id ) {
			return $pre_content;
		}

		$grid_id = get_post_meta( $archive_page_id, '_wpmoly_grid_id', true );
		if ( empty( $grid_id ) ) {
			return $pre_content;
		}

		$grid = utils\grid\get( (int) $grid_id );

		if ( empty( $name ) ) {
			$grid->set_type( $type );
		} else {
			$grid->set_preset( array(
				$type => $name,
			) );
		}

		$grid_template = utils\grid\get_template( $grid );

		$pre_content .= $grid_template->render() . $content;

		return $pre_content;
	}

	/**
	 * Filter archive page content. Insert grid before or after regular post
	 * content depending on the archive page setting.
	 *
	 * @TODO support custom integration of the Headbox inside post content.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $content Post content.
	 * @param int    $post_id Current Post ID.
	 *
	 * @return string
	 */
	public function filter_post_archive_page_content( $content, $post_id ) {

		$pre_content = '';

		$grid_id = get_post_meta( $post_id, '_wpmoly_grid_id', true );
		if ( empty( $grid_id ) ) {
			return $pre_content;
		}

		$grid = utils\grid\get( (int) $grid_id );

		$preset = get_query_var( 'preset' );
		if ( ! empty( $preset ) ) {
			$preset = utils\prefix_meta_key( $preset, '', true );
			$grid->set_preset( array(
				$preset => get_query_var( $preset ),
			) );
		}

		$grid_template = utils\grid\get_template( $grid );

		$pre_content = $grid_template->render() . $pre_content;

		return $pre_content;
	}

	/**
	 *
	 *
	 * @since 3.0.0
	 *
	 * @param mixed  $value     The new, unserialized option value.
	 * @param string $option    Name of the option.
	 * @param mixed  $old_value The old option value.
	 */
		public function pre_update_option( $value, $option, $old_value ) {

			foreach ( get_registered_settings() as $name => $args ) {
				if ( $name === $option && false === $value ) {
					$value = 0;
				}
			}

			return $value;
		}

	/**
	 * Update the archive pages list.
	 *
	 * Avoid duplicates among archive pages.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $new_pages     New archive pages.
	 * @param array $current_pages Old archive pages.
	 *
	 * @return array
	 */
	public function pre_update_archive_pages( $new_pages, $current_pages ) {

		$archive_pages = $current_pages;
		if ( ! is_array( $archive_pages ) ) {
			$archive_pages = array();
		}

		foreach ( $new_pages as $page_type => $page_id ) {
			$archive_pages[ $page_type ] = $page_id;
		}

		return $archive_pages;
	}

	/**
	 * Replace edit post links.
	 *
	 * @since 3.0.0
	 *
	 * @param string $link    The edit link.
	 * @param int    $post_id Post ID.
	 * @param string $context The link context.
	 *
	 * @return string
	 */
	public function set_edit_post_link( $link, $post_id, $context ) {

		$post_type = get_post_type( $post_id );
		if ( ! in_array( $post_type, array( 'grid', 'movie', 'person' ), true ) ) {
			return $link;
		}

		$link = 'admin.php?page=wpmovielibrary-' . $post_type . 's&id=' . $post_id . '&action=edit';

		if ( 'display' === $context ) {
			$link = str_replace( '&', '&amp;', $link );
		}

		$link = admin_url( $link );

		return $link;
	}
}
