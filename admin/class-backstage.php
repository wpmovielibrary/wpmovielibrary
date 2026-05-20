<?php
/**
 * Define the admin plugin class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

/**
 * Load the plugin's admin features.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Backstage {

	/**
	 * Plugin version.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private string $version = WPMOLY_VERSION;

	/**
	 * Static instance.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Backstage
	 */
	private static $_instance = null;

	/**
	 * Registered admin pages.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private array $pages = [];

	/**
	 * Constructor.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Get the instance of this class, insantiating it if it doesn't exist
	 * yet.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Backstage
	 */
	public static function get_instance() {

		if ( ! is_object( self::$_instance ) ) {
			self::$_instance = new self;
			self::$_instance->init();
		}

		return self::$_instance;
	}

	/**
	 * Initialize.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function init() {

		$this->load_dependencies();

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'admin_menu',             [ $this, 'menu' ] );
		add_filter( 'dashboard_glance_items', [ $this, 'dashboard_glance_items' ] );
		add_filter( 'admin_body_class',       [ $this, 'body_class' ] );
		add_action( 'all_admin_notices',      [ $this, 'indexes' ], 9999 );
	}

	/**
	 * Load plugin's public requirements.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function load_dependencies() {

		require_once WPMOLY_PATH . 'includes/class-template.php';
	}

	/**
	 * Enqueue admin-side styles.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles( $hook_suffix ) {

		$this->register_styles();

		if ( in_array( $hook_suffix, [ 'toplevel_page_wpmovielibrary', 'library_page_wpmovielibrary' ] ) ) {
			wp_enqueue_style( 'wpmovielibrary-common' );
		}

		if ( 'toplevel_page_wpmovielibrary' === $hook_suffix ) {
			wp_enqueue_style( 'wpmovielibrary-dashboard' );
		} elseif ( 'library_page_wpmovielibrary-importer' === $hook_suffix ) {
			wp_enqueue_style( 'wpmovielibrary-importer' );
		} elseif ( 'edit.php' === $hook_suffix ) {
			wp_enqueue_style( 'wpmovielibrary-index' );
		} elseif ( 'library_page_wpmovielibrary-settings' === $hook_suffix ) {
			wp_enqueue_style( 'wpmovielibrary-settings' );
		}
	}

	/**
	 * Enqueue admin-side scripts.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts( $hook_suffix ) {

		$this->register_scripts();

		if ( in_array( $hook_suffix, [ 'toplevel_page_wpmovielibrary', 'library_page_wpmovielibrary' ] ) ) {
			wp_enqueue_script( 'wpmovielibrary-common' );
		}
	}

	/**
	 * Register admin-side styles.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function register_styles() {

		wp_register_style( 'wpmovielibrary-common', WPMOLY_URL . 'admin/assets/css/common.css', [], $this->version );
		wp_register_style( 'wpmovielibrary-dashboard', WPMOLY_URL . 'admin/assets/css/dashboard.css', [ 'wpmovielibrary-common' ], $this->version );
		wp_register_style( 'wpmovielibrary-importer', WPMOLY_URL . 'admin/assets/css/importer.css',   [ 'wpmovielibrary-common' ], $this->version );
		wp_register_style( 'wpmovielibrary-index', WPMOLY_URL . 'admin/assets/css/index.css',         [ 'wpmovielibrary-common' ], $this->version );
		wp_register_style( 'wpmovielibrary-settings', WPMOLY_URL . 'admin/assets/css/settings.css',   [ 'wpmovielibrary-common' ], $this->version );
	}

	/**
	 * Register admin-side scripts.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function register_scripts() {

		wp_register_script( 'wpmovielibrary-common', WPMOLY_URL . 'admin/assets/js/common.js', [], $this->version, true );
	}

	/**
	 * Register the plugin dashboard page.
	 *
	 * @since 6.0.0
	 * 
	 * @access public
	 */
	public function menu() {

		$this->pages = [
			add_menu_page( __( 'My Library', 'wpmovielibrary' ), __( 'Library', 'wpmovielibrary' ), 'manage_options', 'wpmovielibrary', [ $this, 'dashboard' ], 'dashicons-video-alt3', 30 ),
			add_submenu_page( 'wpmovielibrary', __( 'My Library', 'wpmovielibrary' ), __( 'My Library', 'wpmovielibrary' ), 'manage_options', 'wpmovielibrary', [ $this, 'dashboard' ], 0 ),
			add_submenu_page( 'wpmovielibrary', __( 'Import Movies', 'wpmovielibrary' ), __( 'Import Movies', 'wpmovielibrary' ), 'manage_options', 'wpmovielibrary-importer', [ $this, 'importer' ], 50 ),
			add_submenu_page( 'wpmovielibrary', __( 'Settings', 'wpmovielibrary' ), __( 'Settings', 'wpmovielibrary' ), 'manage_options', 'wpmovielibrary-settings', [ $this, 'settings' ], 100 ),
		];
	}

	/**
	 * Plugin dashboard page callback.
	 *
	 * @since 6.0.0
	 * 
	 * @access public
	 */
	public function dashboard() {

		$movies_count = (array) wp_count_posts( 'movie' );
		$totals = [
			'movies'      => $movies_count['publish'] ?? 0,
			'imported'    => $movies_count['import-draft'] ?? 0,
			'queued'      => $movies_count['import-queued'] ?? 0,
			'drafts'      => $movies_count['draft'] ?? 0,
			'total'       => 0,
		];
		$totals['collections'] = wp_count_terms( 'wpmoly_movie_collection', [ 'hide_empty' => false ] );
		$totals['genres'] = wp_count_terms( 'wpmoly_movie_genre', [ 'hide_empty' => false ] );
		$totals['actors'] = wp_count_terms( 'wpmoly_movie_actor', [ 'hide_empty' => false ] );
		$totals = array_map( 'intval', $totals );

		echo $this->template()->render( 'dashboard', [
			'plugin_page' => 'wpmovielibrary',
			'hook_suffix' => get_current_screen()->id,
			'post_type'   => get_current_screen()->post_type ?? '',
			'totals' => $totals,
		] );
	}

	/**
	 * Plugin importer page callback.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function importer() {

		$movies_count = (array) wp_count_posts( 'movie' );
		$totals = [
			'imported' => $movies_count['import-draft'] ?? 0,
			'queued' => $movies_count['import-queued'] ?? 0,
		];

		echo $this->template()->render( 'importer', [
			'plugin_page' => 'wpmovielibrary-importer',
			'hook_suffix' => get_current_screen()->id,
			'post_type'   => get_current_screen()->post_type ?? '',
			'totals' => $totals,
		] );
	}

	/**
	 * Plugin settings page callback.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function settings() {

		echo $this->template()->render( 'settings', [
			'plugin_page' => 'wpmovielibrary-settings',
			'hook_suffix' => get_current_screen()->id,
			'post_type'   => get_current_screen()->post_type ?? '',
		] );
	}

	/**
	 * Plugin post indexes.
	 *
	 * @since 6.0.0
	 * @access public
	 */
	public function indexes() {

		global $current_screen, $post_type, $post_type_object;

		$mode = $_GET['mode'] ?? '';
		if ( 'classic' === $mode ) {
			return;
		}

		if ( 'edit' === $current_screen->base && 'wpmovielibrary' === $current_screen->parent_base && in_array( $current_screen->post_type, [ 'movie' ] ) ) {

			$posts_per_page = get_user_meta( get_current_user_id(), 'edit_movie_per_page', true );
			if ( empty( $posts_per_page ) ) {
				$posts_per_page = 20;
			}

			echo $this->template()->render( 'index', [
				'plugin_page' => 'wpmovielibrary-index',
				'hook_suffix' => get_current_screen()->id,
				'post_type' => $post_type,
				'post_type_label' => $post_type_object->label,
				'post_type_labels' => $post_type_object->labels,
				'posts_per_page' => $posts_per_page,
			] );
		}
	}

	/**
	 * Add custom body classes.
	 *
	 * @since 6.0.0
	 * 
	 * @access public
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	public function body_class( $classes ) {
		
		foreach ( $this->pages as $page ) {
			if ( ! is_string( $page ) || '' === $page ) {
				continue;
			}

			if ( false !== stripos( get_current_screen()->id, $page ) ) {
				$classes .= ' wpmovielibrary-dashboard';
				return $classes;
			}
		}

		return $classes;
	}

	/**
	 * Add a new item to the Right Now Dashboard Widget
	 *
	 * @since 6.0.0
	 * 
	 * @access public
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public function dashboard_glance_items( $items = [] ) {

		$movies = wp_count_posts( 'movie' );

		$items[] = sprintf( '<a class="movie-count" href="%s">%s</a>', admin_url( 'edit.php?post_type=movie' ), sprintf( _n( '%d movie', '%d movies', $movies->publish, 'wpmovielibrary' ), $movies->publish ) );

		return $items;
	}

	/**
	 * Get the template engine instance.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @return Template
	 */
	private function template() {

		return new Template(
			template_dir: WPMOLY_PATH . 'admin/templates',
			cache_dir: wp_upload_dir()['basedir'] . '/wpmovielibrary/cache',
			cache: false
		);
	}
}
