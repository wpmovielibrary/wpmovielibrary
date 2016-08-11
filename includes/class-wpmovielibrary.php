<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */

namespace wpmoly;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 * @author     Charlie Merland <charlie@caercam.org>
 */
final class Library {

	/**
	 * The single instance of the plugin.
	 *
	 * @var    Library
	 */
	private static $instance = null;

	/**
	 * The loader that's responsible for maintaining and registering all
	 * hooks that power the plugin.
	 *
	 * @since    3.0
	 *
	 * @var      Loader
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    3.0
	 *
	 * @var      string
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    3.0
	 *
	 * @var      string
	 */
	protected $version;

	/**
	 * Library options instance.
	 *
	 * @since    3.0
	 *
	 * @var      Options
	 */
	public $options;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout
	 * the plugin. Load the dependencies, define the locale, and set the hooks
	 * for the admin area and the public-facing side of the site.
	 *
	 * @since    3.0
	 * 
	 * @return    \wpmoly\Library
	 */
	public function __construct() {

		$this->plugin_name = WPMOLY_SLUG;
		$this->version     = WPMOLY_VERSION;

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		return $this;
	}

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   Singleton
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Initialize core.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	private function init() {

		$this->loader = Core\Loader::get_instance();

		// Load i18n/l10n before setting options
		$this->set_locale();

		$this->options = Core\Options::get_instance();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Loader. Orchestrates the hooks of the plugin.
	 * - i18n. Defines internationalization functionality.
	 * - Admin. Defines all hooks for the admin area.
	 * - Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    3.0
	 */
	private function load_dependencies() {

		// Core
		require_once WPMOLY_PATH . 'includes/core/class-loader.php';
		require_once WPMOLY_PATH . 'includes/core/class-i18n.php';
		require_once WPMOLY_PATH . 'includes/core/class-l10n.php';
		require_once WPMOLY_PATH . 'includes/core/class-registrar.php';
		require_once WPMOLY_PATH . 'includes/core/class-template.php';
		require_once WPMOLY_PATH . 'includes/core/class-options.php';

		$this->init();

		// Helpers
		require_once WPMOLY_PATH . 'includes/helpers/utils.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-country.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-language.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-permalink.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-permalinks.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-formatting.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-terms.php';

		// Nodes
		require_once WPMOLY_PATH . 'includes/node/class-collection.php';
		require_once WPMOLY_PATH . 'includes/node/class-node.php';
		//require_once WPMOLY_PATH . 'includes/node/class-meta.php';
		//require_once WPMOLY_PATH . 'includes/node/class-details.php';
		require_once WPMOLY_PATH . 'includes/node/class-image.php';
		require_once WPMOLY_PATH . 'includes/node/class-default-image.php';
		require_once WPMOLY_PATH . 'includes/node/class-default-backdrop.php';
		require_once WPMOLY_PATH . 'includes/node/class-default-poster.php';
		require_once WPMOLY_PATH . 'includes/node/class-movie.php';
		require_once WPMOLY_PATH . 'includes/node/class-grid.php';

		// API
		require_once WPMOLY_PATH . 'includes/api/class-api.php';
		require_once WPMOLY_PATH . 'includes/api/class-api-core.php';
		require_once WPMOLY_PATH . 'includes/api/class-api-movie.php';

		// Main
		require_once WPMOLY_PATH . 'public/class-frontend.php';

		// Ajax
		require_once WPMOLY_PATH . 'includes/ajax/class-ajax.php';
		require_once WPMOLY_PATH . 'includes/ajax/class-ajax-api.php';
		require_once WPMOLY_PATH . 'includes/ajax/class-ajax-meta.php';

		if ( is_admin() ) {
			require_once WPMOLY_PATH . 'admin/class-backstage.php';
			require_once WPMOLY_PATH . 'admin/class-grid-builder.php';
			require_once WPMOLY_PATH . 'admin/class-metaboxes.php';
			//require_once WPMOLY_PATH . 'admin/class-metabox.php';
			//require_once WPMOLY_PATH . 'admin/class-editor-metabox.php';
		}

		// Shortcodes
		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode-movies.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode-images.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode-metadata.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode-detail.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode-countries.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode-languages.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode-runtime.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode-release-date.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode-local-release-date.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    3.0
	 */
	private function set_locale() {

		$i18n = Core\i18n::get_instance();
		$l10n = Core\l10n::get_instance();

		$this->loader->add_action( 'init',                  $i18n, 'load_plugin_textdomain' );
		$this->loader->add_action( 'init',                  $i18n, 'load_additional_textdomains' );

		$this->loader->add_action( 'admin_enqueue_scripts', $l10n, 'localize_scripts', 20 );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    3.0
	 */
	private function define_admin_hooks() {

		if ( ! is_admin() ) {
			return false;
		}

		$admin = new Backstage( $this->get_plugin_name(), $this->get_version() );
		//$admin->set_default_filters();

		$this->loader->add_filter( 'admin_init',                $admin, 'admin_init' );
		$this->loader->add_filter( 'plupload_default_params',   $admin, 'plupload_default_params' );
		$this->loader->add_action( 'admin_enqueue_scripts',     $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts',     $admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_footer-post.php',     $admin, 'enqueue_templates' );
		$this->loader->add_action( 'admin_footer-post-new.php', $admin, 'enqueue_templates' );

		// Metaboxes
		//$metaboxes = new Metabox\Metaboxes;
		//$metaboxes->define_admin_hooks();

		$builder = new Admin\GridBuilder;
		$builder->add_metaboxes();

		$this->loader->add_action( 'edit_form_top',               $builder, 'header' );
		$this->loader->add_action( 'edit_form_after_editor',      $builder, 'preview' );
		$this->loader->add_action( 'dbx_post_sidebar',            $builder, 'footer' );

		$this->loader->add_action( 'load-post.php',               $builder, 'load' );
		$this->loader->add_action( 'load-post-new.php',           $builder, 'load' );
		$this->loader->add_action( 'butterbean_register',         $builder, 'register_butterbean', 10, 2 );

		// Admin-side Ajax
		/*$ajax = Ajax\Ajax::get_instance();
		$ajax->define_admin_hooks();*/
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    3.0
	 */
	private function define_public_hooks() {

		$public = new Frontend( $this->get_plugin_name(), $this->get_version() );
		$public->set_default_filters();

		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $public, 'register_shortcodes' );

		// Register Post Types, Taxonomies…
		$registrar = Core\Registrar::get_instance();
		$this->loader->add_action( 'init', $registrar, 'register_post_types' );
		$this->loader->add_action( 'init', $registrar, 'register_taxonomies' );
		$this->loader->add_action( 'init', $registrar, 'register_post_statuses' );

		// Public-side Ajax
		$ajax = Ajax\Ajax::get_instance();
		$ajax->define_public_hooks();

		$terms = Helpers\Terms::get_instance();
		$this->loader->add_filter( 'get_the_terms',       $terms, 'get_the_terms',            10, 3 );
		$this->loader->add_filter( 'wp_get_object_terms', $terms, 'get_ordered_object_terms', 10, 4 );
		$this->loader->add_filter( 'wpmoly/filter/post_type/movie', $terms, 'movie_standard_taxonomies' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    3.0
	 */
	public function run() {

		$this->loader->run();

		do_action( 'wpmoly/run' );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     3.0
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     3.0
	 *
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {

		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     3.0
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;
	}

}
