<?php
/**
 * The file that defines the dashboard class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly;

use wpmoly\admin\editors;
use wpmoly\admin\Library;
use wpmoly\dashboard\Block;
use wpmoly\templates\Admin;

/**
 * The dashboard class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Dashboard {

	/**
	 * Editing page?
	 *
	 * @access private
	 *
	 * @var boolean
	 */
	private $editing = false;

	/**
	 * Current object ID.
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $object_id = null;

	/**
	 * Menu subpage attributes.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $sub_pages = array();

	/**
	 * Menu subpage hooknames.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $subpage_hooks = array();

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		add_filter( 'admin_menu', array( &$this, 'admin_menu' ), 9 );

		$this->editing = $this->is_editing();
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
		add_action( 'admin_enqueue_scripts',       array( $assets, 'enqueue_admin_styles' ), 95 );
		add_action( 'admin_enqueue_scripts',       array( $assets, 'enqueue_admin_scripts' ), 95 );
		add_action( 'admin_footer',                array( $assets, 'enqueue_admin_templates' ), 95 );
		add_action( 'enqueue_block_editor_assets', array( $assets, 'enqueue_block_editor_scripts' ), 95 );
	}

	/**
	 * Register the Post Editor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_dashboard() {

		add_action( 'wpmoly/dashboard/block/discover/build', array( &$this, 'set_dashboard_discover_block_data' ) );
		add_action( 'wpmoly/dashboard/block/rating/build',   array( &$this, 'set_dashboard_rating_block_data' ) );
		add_action( 'wpmoly/dashboard/block/support/build',  array( &$this, 'set_dashboard_support_block_data' ) );
	}

	/**
	 * Register the Term Editor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_term_editor() {

		$term_editor = new editors\Terms;
		add_action( 'wpmoly/dashboard/block/discover-actors/build',      array( $term_editor, 'set_actor_browser_discover_block_data' ) );
		add_action( 'wpmoly/dashboard/block/discover-collections/build', array( $term_editor, 'set_collection_browser_discover_block_data' ) );
		add_action( 'wpmoly/dashboard/block/discover-genres/build',      array( $term_editor, 'set_genre_browser_discover_block_data' ) );
	}

	/**
	 * Register the Post Editor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_post_editor() {

		$post_editor = new editors\Posts;
		add_action( 'wpmoly/dashboard/block/discover-grids/build',   array( $post_editor, 'set_grid_editor_discover_block_data' ) );
		add_action( 'wpmoly/dashboard/block/discover-movies/build',  array( $post_editor, 'set_movie_editor_discover_block_data' ) );
		add_action( 'wpmoly/dashboard/block/discover-persons/build', array( $post_editor, 'set_person_editor_discover_block_data' ) );
		add_action( 'edit_form_after_title',                         array( $this, 'add_classic_editor_switch_button' ) );
	}

	/**
	 * Plugged on the 'admin_menu' action hook.
	 *
	 * Register the backstage library page.
	 *
	 * @since 3.0.0
	 */
	public function admin_menu() {

		global $plugin_page;

		add_menu_page( esc_html__( 'Movies Library' , 'wpmovielibrary' ), esc_html__( 'Movies Library' , 'wpmovielibrary' ), 'read', 'wpmovielibrary', array( $this, 'dashboard' ), 'dashicons-wpmoly', 2 );

		$sub_pages = array(
			'movies' => array(
				'edit_title' => esc_html__( 'Edit Movie', 'wpmovielibrary' ),
				'page_title' => esc_html__( 'Edit Movies', 'wpmovielibrary' ),
				'menu_title' => esc_html__( 'Movies', 'wpmovielibrary' ),
				'capability' => 'edit_posts',
				'object_type' => 'movie',
			),
			'grids' => array(
				'edit_title' => esc_html__( 'Edit Grid', 'wpmovielibrary' ),
				'page_title' => esc_html__( 'Edit Grids', 'wpmovielibrary' ),
				'menu_title' => esc_html__( 'Grids', 'wpmovielibrary' ),
				'capability' => 'edit_others_posts',
				'object_type' => 'grid',
			),
			'persons' => array(
				'edit_title' => esc_html__( 'Edit Person', 'wpmovielibrary' ),
				'page_title' => esc_html__( 'Edit Persons', 'wpmovielibrary' ),
				'menu_title' => esc_html__( 'Persons', 'wpmovielibrary' ),
				'capability' => 'edit_posts',
				'object_type' => 'person',
			),
			'actors' => array(
				'edit_title' => esc_html__( 'Edit Actor', 'wpmovielibrary' ),
				'page_title' => esc_html__( 'Edit Actors', 'wpmovielibrary' ),
				'menu_title' => esc_html__( 'Actors', 'wpmovielibrary' ),
				'capability' => 'edit_others_posts',
				'object_type' => 'actor',
			),
			'collections' => array(
				'edit_title' => esc_html__( 'Edit Collection', 'wpmovielibrary' ),
				'page_title' => esc_html__( 'Edit Collections', 'wpmovielibrary' ),
				'menu_title' => esc_html__( 'Collections', 'wpmovielibrary' ),
				'capability' => 'edit_others_posts',
				'object_type' => 'collection',
			),
			'genres' => array(
				'edit_title' => esc_html__( 'Edit Genre', 'wpmovielibrary' ),
				'page_title' => esc_html__( 'Edit Genres', 'wpmovielibrary' ),
				'menu_title' => esc_html__( 'Genres', 'wpmovielibrary' ),
				'capability' => 'edit_others_posts',
				'object_type' => 'genre',
			),
			'settings' => array(
				'page_title' => esc_html__( 'Settings', 'wpmovielibrary' ),
				'menu_title' => esc_html__( 'Settings', 'wpmovielibrary' ),
				'capability' => 'manage_options',
			),
		);

		/**
		 * Filter the plugin's admin menu subpages list.
		 *
		 * @since 3.0.0
		 *
		 * @param array $sub_pages Default admin menu subpages list.
		 */
		$this->sub_pages = apply_filters( 'wpmoly/filter/admin/menu/pages', $sub_pages );

		foreach ( $this->sub_pages as $slug => $page ) {

			$params = wp_parse_args( $page, array(
				'page_title' => '',
				'menu_title' => '',
				'capability' => 'manage_options',
				'callback'   => array( $this, 'dashboard' ),
			) );

			if ( 'wpmovielibrary-' . $slug === $plugin_page && $this->is_editing() ) {
				$params['page_title'] = $params['edit_title'];
			}

			$hook_name = add_submenu_page( 'wpmovielibrary', $params['page_title'], $params['menu_title'], $params['capability'], 'wpmovielibrary-' . $slug, $params['callback'] );
			if ( $hook_name ) {
				$this->subpage_hooks[ $slug ] = $hook_name;
			}
		}
	}

	/**
	 * Build the Dashboard view.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function dashboard() {

		$hook_name = current_action();
		if ( 'settings' === array_search( $hook_name, $this->subpage_hooks, true ) ) {
			$page = 'settings';
		} elseif ( false !== $editor = array_search( $hook_name, $this->subpage_hooks, true ) ) {
			$page = $editor;
		} else {
			$page = 'dashboard';
		}

		if ( ! empty( $editor ) ) {

			$editor = $this->sub_pages[ $editor ];
			$object_id = $this->object_id;

			$old_editors = array(
				'movie-browser'      => admin_url( 'edit.php?post_type=movie' ),
				'person-browser'     => admin_url( 'edit.php?post_type=person' ),
				'grid-browser'       => admin_url( 'edit.php?post_type=grid' ),
				'actor-browser'      => admin_url( 'edit-tags.php?taxonomy=actor' ),
				'collection-browser' => admin_url( 'edit-tags.php?taxonomy=collection' ),
				'genre-browser'      => admin_url( 'edit-tags.php?taxonomy=genre' ),
				'movie-editor'       => admin_url( 'edit.php?post_type=movie&post=' . $object_id ),
				'person-editor'      => admin_url( 'edit.php?post_type=person&post=' . $object_id ),
				'grid-editor'        => admin_url( 'edit.php?post_type=grid&post=' . $object_id ),
				'actor-editor'       => admin_url( 'edit-tags.php?taxonomy=actor&term=' . $object_id ),
				'collection-editor'  => admin_url( 'edit-tags.php?taxonomy=collection&term=' . $object_id ),
				'genre-editor'       => admin_url( 'edit-tags.php?taxonomy=genre&term=' . $object_id ),
			);

			$object_type = $editor['object_type'];
			if ( ! empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) {
				$dashboard_mode = 'editor';
			} else {
				$dashboard_mode = 'browser';
			}

			$page = "{$object_type}-{$dashboard_mode}";

			$noscript = '';
			if ( ! empty( $old_editors[ $page ] ) ) {
				$noscript = sprintf( __( 'JavaScript is required for this feature to work. Use the <a href="%s">default editor</a>?', 'wpmovielibrary' ), esc_url( $old_editors[ $page ] ) );
			}

			$data = array(
				'page'           => $page,
				'dashboard_mode' => $dashboard_mode,
				'object_type'    => $object_type,
				'object_id'      => $object_id,
				'noscript'       => $noscript,
			);

		} else {

			$noscript = esc_html__( 'JavaScript is required for this feature to work.', 'wpmovielibrary' );

			$data = array(
				'page'           => $page,
				'dashboard_mode' => 'dashboard',
				'noscript'       => $noscript,
			);
		}

		$template = new templates\Admin( 'dashboard/dashboard.php' );
		$template->set_data( $data );
		$template->render();
	}

	/**
	 * Register Blocks.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_blocks() {

		/**
		 * Filter the default editor blocks.
		 *
		 * @since 3.0.0
		 *
		 * @param array $blocks Default editor blocks list.
		 */
		$blocks = apply_filters( 'wpmoly/filter/editor/blocks', array(

			// Dashboard Blocks.
			'discover' => array(
				'args'           => array(
					'name'        => __( 'Discover Block', 'wpmovielibrary' ),
					'title'       => __( 'Welcome to your movie library!', 'wpmovielibrary' ),
					'description' => __( 'A dashboard Block with some useful insights on your library usage.', 'wpmovielibrary' ),
					'template'    => 'dashboard/blocks/discover.php',
				),
			),
			'documentation' => array(
				'args'           => array(
					'name'        => __( 'Documentation Block', 'wpmovielibrary' ),
					'title'       => __( 'Documentation', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'template'    => 'dashboard/blocks/documentation.php',
				),
			),
			'rating' => array(
				'args'           => array(
					'name'        => __( 'Rating Block', 'wpmovielibrary' ),
					'title'       => __( 'Say it loud!', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'template'    => 'dashboard/blocks/rating.php',
				),
			),
			'support' => array(
				'args'           => array(
					'name'        => __( 'Support Block', 'wpmovielibrary' ),
					'title'       => __( 'Can we help?', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'template'    => 'dashboard/blocks/support.php',
				),
			),

			// Movie Browser Blocks.
			'discover-movies' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Discover Movies', 'wpmovielibrary' ),
					'title'       => __( 'Movie Library', 'wpmovielibrary' ),
					'description' => __( 'Discover your movie library', 'wpmovielibrary' ),
					'controller'  => 'BrowserBlock',
					'template'    => 'editors/blocks/posts/discover.php',
				),
			),
			'add-new-movie' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Add New Movie', 'wpmovielibrary' ),
					'title'       => __( 'Add New', 'wpmovielibrary' ),
					'description' => __( 'Add a new movie to the library.', 'wpmovielibrary' ),
					'controller'  => 'AddNewBlock',
					'template'    => 'editors/blocks/posts/add-new.php',
				),
			),
			'movie-drafts' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Drafts', 'wpmovielibrary' ),
					'title'       => __( 'Drafts', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'controller'  => 'DraftsBlock',
					'template'    => 'editors/blocks/posts/drafts.php',
				),
			),
			'movie-trash' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Trash', 'wpmovielibrary' ),
					'title'       => __( 'Trash', 'wpmovielibrary' ),
					'description' => __( 'Move movies to the trash.', 'wpmovielibrary' ),
					'controller'  => 'TrashBlock',
					'template'    => 'editors/blocks/posts/trash.php',
				),
			),

			// Movie Editor Blocks.
			'submit-movie' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Submit Block', 'wpmovielibrary' ),
					'title'       => __( 'Submit Movie', 'wpmovielibrary' ),
					'description' => __( 'Movie Block to save, delete or update Movie.', 'wpmovielibrary' ),
					'controller'  => 'MenuBlock',
					'template'    => 'editors/blocks/posts/submit.php',
				),
			),
			'rename-movie' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Title Block', 'wpmovielibrary' ),
					'title'       => __( 'Rename Movie', 'wpmovielibrary' ),
					'description' => __( 'Movie Block to change movie title.', 'wpmovielibrary' ),
					'controller'  => 'RenameBlock',
					'template'    => 'editors/blocks/posts/rename.php',
				),
			),
			'movie-details' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Details Block', 'wpmovielibrary' ),
					'title'       => __( 'Details', 'wpmovielibrary' ),
					'description' => __( 'Movie Block to save, delete or update Movie details.', 'wpmovielibrary' ),
					'controller'  => 'DetailsBlock',
					'template'    => 'editors/blocks/movies/details.php',
				),
			),
			'movie-actors' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Actors Block', 'wpmovielibrary' ),
					'title'       => __( 'Actors', 'wpmovielibrary' ),
					'description' => __( 'Movie Block to quickly manage actors', 'wpmovielibrary' ),
					'controller'  => 'ActorsBlock',
					'template'    => 'editors/blocks/movies/actors.php',
				),
			),
			'movie-collections' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Collections Block', 'wpmovielibrary' ),
					'title'       => __( 'Collections', 'wpmovielibrary' ),
					'description' => __( 'Movie Block to quickly manage collections', 'wpmovielibrary' ),
					'controller'  => 'CollectionsBlock',
					'template'    => 'editors/blocks/movies/collections.php',
				),
			),
			'movie-genres' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Genres Block', 'wpmovielibrary' ),
					'title'       => __( 'Genres', 'wpmovielibrary' ),
					'description' => __( 'Movie Block to quickly manage genres', 'wpmovielibrary' ),
					'controller'  => 'GenresBlock',
					'template'    => 'editors/blocks/movies/genres.php',
				),
			),
			'movie-certifications' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Certifications &amp; Release Dates Block', 'wpmovielibrary' ),
					'title'       => __( 'Certifications &amp; Release Dates', 'wpmovielibrary' ),
					'description' => __( 'Movie Block to quickly manage certifications and release dates', 'wpmovielibrary' ),
					'controller'  => 'CertificationsBlock',
					'template'    => 'editors/blocks/movies/certifications.php',
				),
			),
			'movie-languages' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Languages Block', 'wpmovielibrary' ),
					'title'       => __( 'Spoken Languages', 'wpmovielibrary' ),
					'description' => __( 'Movie Block to quickly manage languages', 'wpmovielibrary' ),
					'controller'  => 'LanguagesBlock',
					'template'    => 'editors/blocks/movies/languages.php',
				),
			),
			'movie-production-countries' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Production Countries Block', 'wpmovielibrary' ),
					'title'       => __( 'Production Countries', 'wpmovielibrary' ),
					'description' => __( 'Movie Block to quickly manage production countries', 'wpmovielibrary' ),
					'controller'  => 'CountriesBlock',
					'template'    => 'editors/blocks/movies/countries.php',
				),
			),
			'movie-production-companies' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'movie',
				'args'           => array(
					'name'        => __( 'Movie Production Companies Block', 'wpmovielibrary' ),
					'title'       => __( 'Production Companies', 'wpmovielibrary' ),
					'description' => __( 'Movie Block to quickly manage production companies', 'wpmovielibrary' ),
					'controller'  => 'CompaniesBlock',
					'template'    => 'editors/blocks/movies/companies.php',
				),
			),

			// Person Browser Blocks.
			'discover-persons' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'person',
				'args'           => array(
					'name'        => __( 'Discover Persons', 'wpmovielibrary' ),
					'title'       => __( 'Person Library', 'wpmovielibrary' ),
					'description' => __( 'Discover your person library', 'wpmovielibrary' ),
					'controller'  => 'BrowserBlock',
					'template'    => 'editors/blocks/posts/discover.php',
				),
			),
			'add-new-person' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'person',
				'args'           => array(
					'name'        => __( 'Add New Person', 'wpmovielibrary' ),
					'title'       => __( 'Add New', 'wpmovielibrary' ),
					'description' => __( 'Add a new person to the library.', 'wpmovielibrary' ),
					'controller'  => 'AddNewBlock',
					'template'    => 'editors/blocks/posts/add-new.php',
				),
			),
			'person-drafts' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'person',
				'args'           => array(
					'name'        => __( 'Person Drafts', 'wpmovielibrary' ),
					'title'       => __( 'Drafts', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'controller'  => 'DraftsBlock',
					'template'    => 'editors/blocks/posts/drafts.php',
				),
			),
			'person-trash' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'person',
				'args'           => array(
					'name'        => __( 'Person Trash', 'wpmovielibrary' ),
					'title'       => __( 'Trash', 'wpmovielibrary' ),
					'description' => __( 'Move persons to the trash.', 'wpmovielibrary' ),
					'controller'  => 'TrashBlock',
					'template'    => 'editors/blocks/posts/trash.php',
				),
			),

			// Person Editor Blocks.
			'submit-person' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'person',
				'args'           => array(
					'name'        => __( 'Person Submit Block', 'wpmovielibrary' ),
					'title'       => __( 'Submit Person', 'wpmovielibrary' ),
					'description' => __( 'Person Block to save, delete or update Person.', 'wpmovielibrary' ),
					'controller'  => 'MenuBlock',
					'template'    => 'editors/blocks/posts/submit.php',
				),
			),
			'rename-person' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'person',
				'args'           => array(
					'name'        => __( 'Person Title Block', 'wpmovielibrary' ),
					'title'       => __( 'Rename Person', 'wpmovielibrary' ),
					'description' => __( 'Person Block to change person title.', 'wpmovielibrary' ),
					'controller'  => 'RenameBlock',
					'template'    => 'editors/blocks/posts/rename.php',
				),
			),

			// Grid Browser Blocks.
			'discover-grids' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'grid',
				'args'           => array(
					'name'        => __( 'Grid Browser', 'wpmovielibrary' ),
					'title'       => __( 'Grid Browser', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'controller'  => 'BrowserBlock',
					'template'    => 'editors/blocks/posts/discover.php',
				),
			),
			'add-new-grid' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'grid',
				'args'           => array(
					'name'        => __( 'Add New Grid', 'wpmovielibrary' ),
					'title'       => __( 'Add New', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'controller'  => 'AddNewBlock',
					'template'    => 'editors/blocks/posts/add-new.php',
				),
			),
			'grid-drafts' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'grid',
				'args'           => array(
					'name'        => __( 'Grid Drafts', 'wpmovielibrary' ),
					'title'       => __( 'Drafts', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'controller'  => 'DraftsBlock',
					'template'    => 'editors/blocks/posts/drafts.php',
				),
			),
			'grid-trash' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'post',
				'object_subtype' => 'grid',
				'args'           => array(
					'name'        => __( 'Grid Trash', 'wpmovielibrary' ),
					'title'       => __( 'Trash', 'wpmovielibrary' ),
					'description' => __( 'Move movies to the trash.', 'wpmovielibrary' ),
					'controller'  => 'TrashBlock',
					'template'    => 'editors/blocks/posts/trash.php',
				),
			),

			// Grid Editor Blocks.
			'submit-grid' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'grid',
				'args'           => array(
					'name'        => __( 'Grid Submit Block', 'wpmovielibrary' ),
					'title'       => __( 'Submit Grid', 'wpmovielibrary' ),
					'description' => __( 'Grid Block to save, delete or update Grid.', 'wpmovielibrary' ),
					'controller'  => 'SubmitBlock',
					'template'    => 'editors/blocks/posts/submit.php',
				),
			),
			'grid-parameters' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'grid',
				'args'           => array(
					'name'        => __( 'Grid Parameters Block', 'wpmovielibrary' ),
					'title'       => __( 'Parameters', 'wpmovielibrary' ),
					'description' => __( 'Grid Block to set grid parameters.', 'wpmovielibrary' ),
					'controller'  => 'ParametersBlock',
					'template'    => 'editors/blocks/grids/parameters.php',
				),
			),
			'rename-grid' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'grid',
				'args'           => array(
					'name'        => __( 'Grid Title Block', 'wpmovielibrary' ),
					'title'       => __( 'Rename Grid', 'wpmovielibrary' ),
					'description' => __( 'Grid Block to change grid title.', 'wpmovielibrary' ),
					'controller'  => 'RenameBlock',
					'template'    => 'editors/blocks/posts/rename.php',
				),
			),
			'grid-archives' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'post',
				'object_subtype' => 'grid',
				'args'           => array(
					'name'        => __( 'Grid Archives Block', 'wpmovielibrary' ),
					'title'       => __( 'Archives Grid', 'wpmovielibrary' ),
					'description' => __( 'Grid Block to configure archives grid.', 'wpmovielibrary' ),
					'controller'  => 'ArchivesBlock',
					'template'    => 'editors/blocks/grids/archives.php',
				),
			),

			// Actors Browser Blocks
			'discover-actors' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'term',
				'object_subtype' => 'actor',
				'args'           => array(
					'name'        => __( 'Actor Browser', 'wpmovielibrary' ),
					'title'       => __( 'Actor Browser', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'controller'  => 'BrowserBlock',
					'template'    => 'editors/blocks/actors/discover.php',
				),
			),
			'add-new-actor' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'term',
				'object_subtype' => 'actor',
				'args'           => array(
					'name'        => __( 'Add New Actor', 'wpmovielibrary' ),
					'title'       => __( 'Add New', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'controller'  => 'AddNewBlock',
					'template'    => 'editors/blocks/terms/add-new.php',
				),
			),

			// Actors Editor Blocks
			'submit-actor' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'term',
				'object_subtype' => 'actor',
				'args'           => array(
					'name'        => __( 'Actor Submit Block', 'wpmovielibrary' ),
					'title'       => __( 'Submit Actor', 'wpmovielibrary' ),
					'description' => __( 'Actor Block to save, delete or update Actor.', 'wpmovielibrary' ),
					'controller'  => 'SubmitBlock',
					'template'    => 'editors/blocks/terms/submit.php',
				),
			),
			'rename-actor' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'term',
				'object_subtype' => 'actor',
				'args'           => array(
					'name'        => __( 'Actor name Block', 'wpmovielibrary' ),
					'title'       => __( 'Rename Actor', 'wpmovielibrary' ),
					'description' => __( 'Actor Editor Block to change actor name.', 'wpmovielibrary' ),
					'controller'  => 'RenameBlock',
					'template'    => 'editors/blocks/terms/rename.php',
				),
			),
			'actor-related-person' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'term',
				'object_subtype' => 'actor',
				'args'           => array(
					'name'        => __( 'Actor Related Person Block', 'wpmovielibrary' ),
					'title'       => __( 'Related Person', 'wpmovielibrary' ),
					'description' => __( 'Actor Related Person Block.', 'wpmovielibrary' ),
					'controller'  => 'RelatedPersonBlock',
					'template'    => 'editors/blocks/actors/related-person.php',
				),
			),

			// Collections Browser Blocks
			'discover-collections' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'term',
				'object_subtype' => 'collection',
				'args'           => array(
					'name'        => __( 'Collection Browser', 'wpmovielibrary' ),
					'title'       => __( 'Collection Browser', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'controller'  => 'BrowserBlock',
					'template'    => 'editors/blocks/collections/discover.php',
				),
			),
			'add-new-collection' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'term',
				'object_subtype' => 'collection',
				'args'           => array(
					'name'        => __( 'Add New Collection', 'wpmovielibrary' ),
					'title'       => __( 'Add New', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'controller'  => 'AddNewBlock',
					'template'    => 'editors/blocks/terms/add-new.php',
				),
			),

			// Collections Editor Blocks
			'submit-collection' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'term',
				'object_subtype' => 'collection',
				'args'           => array(
					'name'        => __( 'Collection Submit Block', 'wpmovielibrary' ),
					'title'       => __( 'Submit Collection', 'wpmovielibrary' ),
					'description' => __( 'Collection Block to save, delete or update Collection.', 'wpmovielibrary' ),
					'controller'  => 'SubmitBlock',
					'template'    => 'editors/blocks/terms/submit.php',
				),
			),
			'rename-collection' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'term',
				'object_subtype' => 'collection',
				'args'           => array(
					'name'        => __( 'Collection name Block', 'wpmovielibrary' ),
					'title'       => __( 'Rename Collection', 'wpmovielibrary' ),
					'description' => __( 'Collection Editor Block to change collection name.', 'wpmovielibrary' ),
					'controller'  => 'RenameBlock',
					'template'    => 'editors/blocks/terms/rename.php',
				),
			),

			// Genres Browser Blocks
			'discover-genres' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'term',
				'object_subtype' => 'genre',
				'args'           => array(
					'name'        => __( 'Genre Browser', 'wpmovielibrary' ),
					'title'       => __( 'Genre Browser', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'controller'  => 'BrowserBlock',
					'template'    => 'editors/blocks/genres/discover.php',
				),
			),
			'add-new-genre' => array(
				'dashboard_type' => 'browser',
				'object_type'    => 'term',
				'object_subtype' => 'genre',
				'args'           => array(
					'name'        => __( 'Add New Genre', 'wpmovielibrary' ),
					'title'       => __( 'Add New', 'wpmovielibrary' ),
					'description' => __( '', 'wpmovielibrary' ),
					'controller'  => 'AddNewBlock',
					'template'    => 'editors/blocks/terms/add-new.php',
				),
			),

			// Genres Editor Blocks
			'submit-genre' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'term',
				'object_subtype' => 'genre',
				'args'           => array(
					'name'        => __( 'Genre Submit Block', 'wpmovielibrary' ),
					'title'       => __( 'Submit Genre', 'wpmovielibrary' ),
					'description' => __( 'Genre Block to save, delete or update Genre.', 'wpmovielibrary' ),
					'controller'  => 'SubmitBlock',
					'template'    => 'editors/blocks/terms/submit.php',
				),
			),
			'rename-genre' => array(
				'dashboard_type' => 'editor',
				'object_type'    => 'term',
				'object_subtype' => 'genre',
				'args'           => array(
					'name'        => __( 'Genre name Block', 'wpmovielibrary' ),
					'title'       => __( 'Rename Genre', 'wpmovielibrary' ),
					'description' => __( 'Genre Editor Block to change genre name.', 'wpmovielibrary' ),
					'controller'  => 'RenameBlock',
					'template'    => 'editors/blocks/terms/rename.php',
				),
			),

		) );

		foreach ( $blocks as $id => $block ) {
			$this->register_block( $id, $block );
		}

	}

	/**
	 * Register Blocks.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_block( $id, $args ) {

		$block = new Block( $id, array_merge( $args['args'], array(
			'object_type'    => ! empty( $args['object_type'] ) ? $args['object_type'] : null,
			'object_subtype' => ! empty( $args['object_subtype'] ) ? $args['object_subtype'] : null,
		) ) );

		$callback = array( $block, 'render' );

		if ( ! empty( $args['dashboard_type'] ) ) {
			if ( ! empty( $args['object_subtype'] ) ) {
				add_action( "wpmoly/dashboard/{$args['object_subtype']}/{$args['dashboard_type']}/blocks", $callback );
			} else {
				add_action( "wpmoly/dashboard/{$args['dashboard_type']}/blocks", $callback );
			}
		} else {
			add_action( 'wpmoly/dashboard/blocks', $callback );
		}
	}

	/**
	 * Is current page an edit page?
	 *
	 * @since 3.0.0
	 *
	 * @return boolean
	 */
	private function is_editing() {

		if ( empty( $_GET['id'] ) ) {
			$this->object_id = null;
			$this->editing = false;
		} else {
			$this->object_id = (int) $_GET['id'];
			$this->editing = true;
		}

		return $this->editing;
	}

	/**
	 * Set Dashboard 'Discover' Block data.
	 *
	 * @since 3.0.0
	 *
	 * @param Block $block Block instance.
	 */
	public function set_dashboard_discover_block_data( $block ) {

		$data = array();

		$grids   = wp_count_posts( 'grid' );
		$movies  = wp_count_posts( 'movie' );
		$persons = wp_count_posts( 'person' );
		$actors  = wp_count_terms( 'actor' );
		$genres  = wp_count_terms( 'genre' );

		$data['grids_url']   = admin_url( 'admin.php?page=wpmovielibrary-grids' );
		$data['movies_url']  = admin_url( 'admin.php?page=wpmovielibrary-movies' );
		$data['persons_url'] = admin_url( 'admin.php?page=wpmovielibrary-persons' );
		$data['actors_url']  = admin_url( 'admin.php?page=wpmovielibrary-actors' );
		$data['genres_url']  = admin_url( 'admin.php?page=wpmovielibrary-genres' );

		$data['grids']   = isset( $grids->publish ) ? (int) $grids->publish : 0;
		$data['movies']  = isset( $movies->publish ) ? (int) $movies->publish : 0;
		$data['persons'] = isset( $persons->publish ) ? (int) $persons->publish : 0;
		$data['actors']  = ! is_wp_error( $actors ) ? (int) $actors : 0;
		$data['genres']  = ! is_wp_error( $genres ) ? (int) $genres : 0;

		$license = wpmovielibrary()->settings->get( 'license_key', '' );
		if ( empty( $license ) ) {
			$data['license'] = 'missing';
			$data['license_url'] = admin_url( 'admin.php?page=wpmovielibrary-settings#general' );
		} else {
			$data['license'] = 'valid';
			$data['license_url'] = '#';
		}

		$block->set_data( $data );
	}

	/**
	 * Set Dashboard 'Rating' Block data.
	 *
	 * @since 3.0.0
	 *
	 * @param Block $block Block instance.
	 */
	public function set_dashboard_rating_block_data( $block ) {

		$block->set_data( array(
			'testimony_url' => 'https://wpmovielibrary.com/testify',
		) );
	}

	/**
	 * Set Dashboard 'Support' Block data.
	 *
	 * @since 3.0.0
	 *
	 * @param Block $block Block instance.
	 */
	public function set_dashboard_support_block_data( $block ) {

		$block->set_data( array(
			'contact_url' => 'https://wpmovielibrary.com/support',
			'hire_url'    => 'https://wpmovielibrary.com/hire-us',
		) );
	}

	/**
	 * Add a swtich button to the classic editor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param $post WP_Post Post object.
	 */
	public function add_classic_editor_switch_button( $post ) {

		// Exit if Gutenberg are active.
		if ( did_action( 'enqueue_block_editor_assets' ) ) {
			return;
		}

		$post_type = get_post_type( $post );
		if ( ! in_array( $post_type, array( 'grid', 'movie', 'person' ), true ) ) {
			return false;
		}
?>
		<div id="wpmoly-editor-switch" class="wpmoly editor-switch">
			<a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wpmovielibrary-' . $post_type . 's&id=' . $post_id . '&action=edit' ) ); ?>" class="wpmoly editor-switch-button"><?php _e( 'Edit with wpMovieLibrary', 'wpmovielibrary' ); ?></a>
		</div>
<?php
	}

}
