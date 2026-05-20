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
	 * Template engine instance.
	 * 
	 * @since 6.0.0
	 * 
	 * @access private
	 * 
	 * @var Template
	 */
	private Template $templateEngine;

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

		$this->templateEngine = new Template(
			templateDir: WPMOLY_PATH . 'admin/templates',
			cacheDir: wp_upload_dir()['basedir'] . '/wpmovielibrary/cache',
			cache: false
		);

		add_action( 'admin_enqueue_scripts', [ &$this, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ &$this, 'enqueue_scripts' ] );

		add_action( 'admin_menu', [ $this, 'menu' ] );
		add_filter( 'dashboard_glance_items', [ $this, 'dashboard_glance_items' ] );
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
	 * Enqueue admin-side scripts.
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
		}
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
	public function enqueue_scripts( $hook_suffix ) {

		$this->register_scripts();
	}

	/**
	 * Register admin-side scripts.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function register_styles() {

		wp_register_style( 'wpmovielibrary-common', WPMOLY_URL . 'admin/assets/css/common.css', [], $this->version );
		wp_register_style( 'wpmovielibrary-dashboard', WPMOLY_URL . 'admin/assets/css/dashboard.css', [ 'wpmovielibrary-common' ], $this->version );
		wp_register_style( 'wpmovielibrary-importer', WPMOLY_URL . 'admin/assets/css/importer.css', [ 'wpmovielibrary-common' ], $this->version );
	}

	/**
	 * Register admin-side styles.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function register_scripts() {}

	/**
	 * Register the plugin dashboard page.
	 *
	 * @since 4.0
	 * @access public
	 */
	public function menu() {

		$this->pages = [
			add_menu_page( __( 'My Library', 'wpmovielibrary' ), __( 'Library', 'wpmovielibrary' ), 'manage_options', 'wpmovielibrary', [ $this, 'dashboard' ], 'dashicons-video-alt3', 30 ),
			add_submenu_page( 'wpmovielibrary', __( 'My Library', 'wpmovielibrary' ), __( 'My Library', 'wpmovielibrary' ), 'manage_options', 'wpmovielibrary', [ $this, 'dashboard' ], 0 ),
			add_submenu_page( 'wpmovielibrary', __( 'Import Movies', 'wpmovielibrary' ), __( 'Import Movies', 'wpmovielibrary' ), 'manage_options', 'wpmovielibrary-importer', [ $this, 'importer' ], 50 ),
		];
	}

	/**
	 * Plugin dashboard page callback.
	 *
	 * @since 4.0
	 * @access public
	 */
	public function dashboard() {

		$name = 'wpmovielibrary';
		if ( ! empty( $page ) ) {
			$name .= "-$page";
		}

		$movies_count = (array) wp_count_posts( 'movie' );
		$totals = [
			'movies'      => $movies_count['publish'] ?? 0,
			'imported'    => $movies_count['import-draft'] ?? 0,
			'queued'      => $movies_count['import-queued'] ?? 0,
			'drafts'      => $movies_count['draft'] ?? 0,
			'total'       => 0,
		];
		$totals['collections'] = wp_count_terms( 'collection' );
		$totals['genres'] = wp_count_terms( 'genre' );
		$totals['actors'] = wp_count_terms( 'actor' );
		$totals = array_map( 'intval', $totals );

		echo $this->templateEngine->render( 'dashboard', [
			'plugin_page' => $name,
			'hook_suffix' => get_current_screen()->id,
			'totals' => $totals,
		] );
	}

	/**
	 * Plugin importer page callback.
	 *
	 * @since 4.0
	 * @access public
	 */
	public function importer() {

		$movies_count = (array) wp_count_posts( 'movie' );
		$totals = [
			'imported' => $movies_count['import-draft'],
			'queued' => $movies_count['import-queued'],
		];

		echo $this->templateEngine->render( 'importer', [
			'plugin_page' => 'wpmovielibrary-importer',
			'hook_suffix' => get_current_screen()->id,
			'totals' => $totals,
		] );
	}

	/**
	 * Add custom body classes.
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	public function body_class( $classes ) {
		
		foreach ( $this->pages as $page ) {
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
	 * @since 1.0.1
	 * @access public
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public function dashboard_glance_items( $items = [] ) {

		$movies = wp_count_posts( 'movie' );

		$items[] = sprintf( '<a class="movie-count" href="%s">%s</a>', admin_url( '/edit.php?post_type=movie' ), sprintf( _n( '%d movie', '%d movies', $movies->publish, 'wpmovielibrary' ), $movies->publish ) );

		return $items;
	}
}
