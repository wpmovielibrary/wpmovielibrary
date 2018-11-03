<?php
/**
 * .
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\core;

/**
 * .
 *
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Assets {

	/**
	 * The single instance of the class.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Assets
	 */
	private static $_instance = null;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Get the instance of this class, insantiating it if it doesn't exist
	 * yet.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Assets
	 */
	public static function get_instance() {

		if ( ! is_object( self::$_instance ) ) {
			self::$_instance = new static;
			self::$_instance->init();
		}

		return self::$_instance;
	}

	/**
	 * Initialize core.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function init() {

		global $wpmoly_templates;

		if ( ! has_filter( 'wpmoly/filter/assets/handle' ) ) {
			add_filter( 'wpmoly/filter/assets/handle', array( $this, 'prefix_handle' ) );
		}

		if ( ! has_filter( 'wpmoly/filter/assets/src' ) ) {
			add_filter( 'wpmoly/filter/assets/src', array( $this, 'prefix_src' ) );
		}

		if ( ! has_filter( 'wpmoly/filter/assets/version' ) ) {
			add_filter( 'wpmoly/filter/assets/version', array( $this, 'default_version' ) );
		}

		add_filter( 'wpmoly/filter/admin/style/src',   array( $this, 'prefix_admin_style_src' ) );
		add_filter( 'wpmoly/filter/admin/script/src',  array( $this, 'prefix_admin_script_src' ) );
		add_filter( 'wpmoly/filter/admin/font/src',    array( $this, 'prefix_admin_font_src' ) );
		add_filter( 'wpmoly/filter/public/style/src',  array( $this, 'prefix_public_style_src' ) );
		add_filter( 'wpmoly/filter/public/script/src', array( $this, 'prefix_public_script_src' ) );
		add_filter( 'wpmoly/filter/public/font/src',   array( $this, 'prefix_public_font_src' ) );

		if ( ! isset( $wpmoly_templates ) ) {
			$wpmoly_templates = array();
		}
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_scripts( $hook_suffix ) {

		if ( ! is_admin() ) {
			return false;
		}

		$this->register_admin_scripts();

		if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary' ) ) {

			$this->enqueue_script( 'selectize' );
			$this->enqueue_script( 'dashboard' );

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-movies' ) ) {
				if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
					wp_enqueue_media();
					wp_enqueue_script( 'media-grid' );
					$this->enqueue_script( 'movie-editor' );
				} else {
					$this->enqueue_script( 'movie-browser' );
				}
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-grids' ) ) {
				if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
					$this->enqueue_script( 'grid-editor' );
				} else {
					$this->enqueue_script( 'grid-browser' );
				}
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-actors' ) ) {
				if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
					wp_enqueue_media();
					$this->enqueue_script( 'actor-editor' );
				} else {
					$this->enqueue_script( 'actor-browser' );
				}
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-collections' ) ) {
				if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
					wp_enqueue_media();
					$this->enqueue_script( 'collection-editor' );
				} else {
					$this->enqueue_script( 'collection-browser' );
				}
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-genres' ) ) {
				if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
					wp_enqueue_media();
					$this->enqueue_script( 'genre-editor' );
				} else {
					$this->enqueue_script( 'genre-browser' );
				}
			}
		}

		if ( 'options-permalink.php' == $hook_suffix ) {
			$this->enqueue_script( 'metaboxes' );
			$this->enqueue_script( 'permalinks-editor' );
		}
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_public_scripts() {

		$this->register_public_scripts();

		// Runners
		$this->enqueue_script( 'grids' );
		$this->enqueue_script( 'headboxes' );
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_styles( $hook_suffix ) {

		if ( ! is_admin() ) {
			return false;
		}

		$this->register_admin_styles();

		$this->enqueue_style( 'admin' );
		$this->enqueue_style( 'font' );
		$this->enqueue_style( 'common' );
		$this->enqueue_style( 'grids' );
		$this->enqueue_style( 'flags' );

		if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary' ) ) {

			$this->enqueue_style( 'selectize' );
			$this->enqueue_style( 'dashboard' );

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-movies' ) ||
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-grids' ) ) {
				if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
					$this->enqueue_style( 'post-editor' );
				} else {
					$this->enqueue_style( 'post-browser' );
				}
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-actors' ) ||
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-collections' ) ||
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-genres' ) ) {
				if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
					$this->enqueue_style( 'grids' );
					$this->enqueue_style( 'headboxes' );
					$this->enqueue_style( 'term-editor' );
				} else {
					$this->enqueue_style( 'term-browser' );
				}
			}
		}

		if ( 'options-permalink.php' == $hook_suffix ) {
			$this->enqueue_style( 'metaboxes' );
			$this->enqueue_style( 'permalinks-editor' );
		}
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_public_styles() {

		$this->register_public_styles();

		$this->enqueue_style( 'core' );
		$this->enqueue_style( 'common' );
		$this->enqueue_style( 'headboxes' );
		$this->enqueue_style( 'grids' );
		$this->enqueue_style( 'flags' );
		$this->enqueue_style( 'font' );
	}

	/**
	 * Enqueue templates.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_admin_templates() {

		if ( ! is_admin() ) {
			return false;
		}

		global $hook_suffix;

		$this->register_admin_templates();

		$this->enqueue_template( 'toast' );

		if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary' ) ) {
			$this->enqueue_template( 'settings-editor' );
			$this->enqueue_template( 'settings-menu' );
			$this->enqueue_template( 'settings-field' );
			$this->enqueue_template( 'settings-group' );

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-movies' ) ||
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-grids' ) ) {
				$this->enqueue_template( 'post-editor-categories' );
				$this->enqueue_template( 'post-editor-tags' );
				$this->enqueue_template( 'post-editor-submit' );
				$this->enqueue_template( 'post-editor-discover' );
				$this->enqueue_template( 'post-editor-add-new' );
				$this->enqueue_template( 'post-editor-drafts' );
				$this->enqueue_template( 'post-editor-drafts-item' );
				$this->enqueue_template( 'post-editor-trash' );
				$this->enqueue_template( 'post-editor-trash-item' );
				$this->enqueue_template( 'post-editor-rename' );
				$this->enqueue_template( 'post-editor-headline' );
				$this->enqueue_template( 'post-browser' );
				$this->enqueue_template( 'post-browser-menu' );
				$this->enqueue_template( 'post-browser-pagination' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-movies' ) ) {
				$this->enqueue_template( 'movie-editor' );
				$this->enqueue_template( 'movie-editor-menu' );
				$this->enqueue_template( 'movie-editor-preview' );
				$this->enqueue_template( 'movie-editor-search-form' );
				$this->enqueue_template( 'movie-editor-search-result' );
				$this->enqueue_template( 'movie-editor-search-results' );
				$this->enqueue_template( 'movie-editor-snapshot' );
				$this->enqueue_template( 'movie-editor-actors' );
				$this->enqueue_template( 'movie-editor-certifications' );
				$this->enqueue_template( 'movie-editor-collections' );
				$this->enqueue_template( 'movie-editor-companies' );
				$this->enqueue_template( 'movie-editor-countries' );
				$this->enqueue_template( 'movie-editor-details' );
				$this->enqueue_template( 'movie-editor-genres' );
				$this->enqueue_template( 'movie-editor-languages' );
				$this->enqueue_template( 'movie-meta-editor' );
				$this->enqueue_template( 'movie-credits-editor' );
				$this->enqueue_template( 'movie-backdrops-editor' );
				$this->enqueue_template( 'movie-backdrops-editor-menu' );
				$this->enqueue_template( 'movie-backdrops-editor-item' );
				$this->enqueue_template( 'movie-backdrops-editor-uploader' );
				$this->enqueue_template( 'movie-posters-editor' );
				$this->enqueue_template( 'movie-posters-editor-menu' );
				$this->enqueue_template( 'movie-posters-editor-item' );
				$this->enqueue_template( 'movie-posters-editor-uploader' );
				$this->enqueue_template( 'movie-browser-item' );
				$this->enqueue_template( 'movie-modal' );
				$this->enqueue_template( 'movie-modal-preview' );
				$this->enqueue_template( 'movie-modal-editor' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-grids' ) ) {
				$this->enqueue_template( 'grid-editor' );
				$this->enqueue_template( 'grid-editor-parameters' );
				$this->enqueue_template( 'grid-editor-archives' );
				$this->enqueue_template( 'grid-meta-editor' );
				$this->enqueue_template( 'grid-browser-item' );

				$this->enqueue_template( 'grid' );
				$this->enqueue_template( 'grid-menu' );
				$this->enqueue_template( 'grid-customs' );
				$this->enqueue_template( 'grid-settings' );
				$this->enqueue_template( 'grid-pagination' );

				$this->enqueue_template( 'grid-error' );
				$this->enqueue_template( 'grid-empty' );

				$this->enqueue_template( 'grid-movie-grid' );
				$this->enqueue_template( 'grid-movie-grid-variant-1' );
				$this->enqueue_template( 'grid-movie-grid-variant-2' );
				$this->enqueue_template( 'grid-movie-list' );
				$this->enqueue_template( 'grid-actor-grid' );
				$this->enqueue_template( 'grid-actor-list' );
				$this->enqueue_template( 'grid-collection-grid' );
				$this->enqueue_template( 'grid-collection-list' );
				$this->enqueue_template( 'grid-genre-grid' );
				$this->enqueue_template( 'grid-genre-list' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-actors' ) ||
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-collections' ) ||
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-genres' ) ) {
				$this->enqueue_template( 'grid' );
				$this->enqueue_template( 'grid-menu' );
				$this->enqueue_template( 'grid-customs' );
				$this->enqueue_template( 'grid-settings' );
				$this->enqueue_template( 'grid-pagination' );
				$this->enqueue_template( 'grid-error' );
				$this->enqueue_template( 'grid-empty' );
				$this->enqueue_template( 'grid-movie-grid' );

				$this->enqueue_template( 'term-editor-add-new' );
				$this->enqueue_template( 'term-editor-discover' );
				$this->enqueue_template( 'term-editor-rename' );
		 		$this->enqueue_template( 'term-editor-submit' );
				$this->enqueue_template( 'term-browser' );
				$this->enqueue_template( 'term-browser-menu' );
				$this->enqueue_template( 'term-browser-item' );
				$this->enqueue_template( 'term-browser-pagination' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-actors' ) ) {
				$this->enqueue_template( 'actor-editor' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-collections' ) ) {
				$this->enqueue_template( 'collection-editor' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-genres' ) ) {
				$this->enqueue_template( 'genre-editor' );
				$this->enqueue_template( 'genre-editor-menu' );
			}
		}
	}

	/**
	 * Enqueue templates.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_public_templates() {

		$this->register_public_templates();

		$this->enqueue_template( 'grid' );
		$this->enqueue_template( 'grid-menu' );
		$this->enqueue_template( 'grid-customs' );
		$this->enqueue_template( 'grid-settings' );
		$this->enqueue_template( 'grid-pagination' );

		$this->enqueue_template( 'grid-empty' );
		$this->enqueue_template( 'grid-error' );

		$this->enqueue_template( 'grid-movie-grid' );
		$this->enqueue_template( 'grid-movie-grid-variant-1' );
		$this->enqueue_template( 'grid-movie-grid-variant-2' );
		$this->enqueue_template( 'grid-movie-list' );
		$this->enqueue_template( 'grid-actor-grid' );
		$this->enqueue_template( 'grid-actor-list' );
		$this->enqueue_template( 'grid-collection-grid' );
		$this->enqueue_template( 'grid-collection-list' );
		$this->enqueue_template( 'grid-genre-grid' );
		$this->enqueue_template( 'grid-genre-list' );
	}

	/**
	 * Register scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_admin_scripts() {

		if ( ! is_admin() ) {
			return false;
		}

		// Vendor
		$this->add_admin_js( 'toasts',    'toasts.min.js',    array( 'jquery', 'underscore', 'wp-backbone' ), '1.0.0' );
		$this->add_admin_js( 'tmdb',      'tmdb.js',          array( 'jquery', 'underscore', 'wp-backbone' ), '1.0.0' );
		$this->add_admin_js( 'selectize', 'selectize.min.js', array( 'jquery' ), '0.12.4' );
		$this->add_public_js( 'underscore-string', 'underscore.string.min.js', array( 'underscore' ) );

		// Base
		$this->add_public_js( 'utils', 'utils.js',  array( 'wpmoly-underscore-string', 'wpmoly-toasts' ) );
		$this->add_public_js( 'core',  'wpmoly.js', array( 'jquery', 'wpmoly-underscore-string', 'wp-backbone', 'wp-api', 'wpmoly-utils' ) );
		$this->add_public_js( 'api',   'api.js',    array( 'jquery', 'wpmoly-underscore-string', 'wp-backbone', 'wp-api', 'wpmoly-utils' ) );

		// Runners
		$this->add_public_js( 'grids',            'grids.js',              array( 'wpmoly-core', 'wpmoly-api' ) );
		$this->add_admin_js( 'dashboard',         'dashboard.js',          array( 'wpmoly-core', 'wpmoly-api' ) );
		$this->add_admin_js( 'metaboxes',         'metaboxes.js',          array( 'wpmoly-core' ) );
		$this->add_admin_js( 'permalinks-editor', 'permalinks-editor.js',  array( 'wpmoly-core' ) );

		// Posts browser.
		$this->add_admin_js( 'post-browser',  'post-browser.js',  array( 'wpmoly-core', 'wpmoly-api' ) );
		$this->add_admin_js( 'movie-browser', 'movie-browser.js', array( 'wpmoly-post-browser' ) );
		$this->add_admin_js( 'grid-browser',  'grid-browser.js',  array( 'wpmoly-post-browser' ) );

		// Posts editor.
		$this->add_admin_js( 'post-editor',  'post-editor.js',  array( 'wpmoly-core', 'wpmoly-api' ) );
		$this->add_admin_js( 'movie-editor', 'movie-editor.js', array( 'wpmoly-post-editor', 'wpmoly-tmdb', 'wp-plupload', 'plupload-handlers' ) );
		$this->add_admin_js( 'grid-editor',  'grid-editor.js',  array( 'wpmoly-post-editor', 'wpmoly-grids', 'wpmoly-metaboxes' ) );

		// Terms browser.
		$this->add_admin_js( 'term-browser',       'term-browser.js',       array( 'wpmoly-core', 'wpmoly-api' ) );
		$this->add_admin_js( 'actor-browser',      'actor-browser.js',      array( 'wpmoly-term-browser' ) );
		$this->add_admin_js( 'collection-browser', 'collection-browser.js', array( 'wpmoly-term-browser' ) );
		$this->add_admin_js( 'genre-browser',      'genre-browser.js',      array( 'wpmoly-term-browser' ) );

		// Terms editor.
		$this->add_admin_js( 'term-editor',       'term-editor.js',       array( 'wpmoly-core', 'wpmoly-api', 'wpmoly-grids' ) );
		$this->add_admin_js( 'actor-editor',      'actor-editor.js',      array( 'wpmoly-term-editor', 'wp-plupload', 'plupload-handlers' ) );
		$this->add_admin_js( 'collection-editor', 'collection-editor.js', array( 'wpmoly-term-editor', 'wp-plupload', 'plupload-handlers' ) );
		$this->add_admin_js( 'genre-editor',      'genre-editor.js',      array( 'wpmoly-term-editor', 'wp-plupload', 'plupload-handlers' ) );
	}

	/**
	 * Register scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_public_scripts() {

		// Vendor
		$this->add_public_js( 'underscore-string', 'underscore.string.min.js', array( 'underscore' ) );

		// Base
		$this->add_public_js( 'utils', 'utils.js',  array( 'wpmoly-underscore-string' ) );
		$this->add_public_js( 'core',  'wpmoly.js', array( 'jquery', 'wpmoly-underscore-string', 'wp-backbone', 'wp-api', 'wpmoly-utils' ) );
		$this->add_public_js( 'api',   'api.js',    array( 'jquery', 'wpmoly-underscore-string', 'wp-backbone', 'wp-api', 'wpmoly-utils' ) );

		// Runners
		$this->add_public_js( 'grids',     'grids.js',     array( 'wpmoly-core', 'wpmoly-api' ) );
		$this->add_public_js( 'headboxes', 'headboxes.js', array( 'wpmoly-core' ) );
	}

	/**
	 * Register stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_admin_styles() {

		if ( ! is_admin() ) {
			return false;
		}

		// Font.
		$this->add_public_font( 'font', 'wpmovielibrary/css/wpmovielibrary.css' );

		// Vendor.
		$this->add_admin_css( 'toasts',    'toasts.css',    array(), '1.0.0' );
		$this->add_admin_css( 'selectize', 'selectize.css', array(), '0.12.4' );

		// Base.
		$this->add_admin_css( 'admin', 'wpmoly.css', array( 'wpmoly-toasts' ) );

		// Dashboard.
		$this->add_admin_css( 'dashboard', 'dashboard.css' );

		// Editors.
		$this->add_admin_css( 'metaboxes',          'metaboxes.css' );
		$this->add_admin_css( 'permalinks-editor',  'permalinks-editor.css' );

		// Posts.
		$this->add_admin_css( 'post-browser', 'post-browser.css' );
		$this->add_admin_css( 'post-editor',  'post-editor.css', array( 'wpmoly-metaboxes' ) );

		// Terms.
		$this->add_admin_css( 'term-browser', 'term-browser.css' );
		$this->add_admin_css( 'term-editor',  'term-editor.css' );

		// Public requirements.
		$this->add_public_css( 'common',    'common.css' );
		$this->add_public_css( 'headboxes', 'headboxes.css' );
		$this->add_public_css( 'grids',     'grids.css' );
		$this->add_public_css( 'flags',     'flags.css' );
	}

	/**
	 * Register stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_public_styles() {

		// Plugin icon font
		$this->add_public_font( 'font',     'wpmovielibrary/css/wpmovielibrary.css' );

		// Plugin-wide normalize
		$this->add_public_css( 'normalize', 'normalize-min.css' );

		// Main stylesheet
		$this->add_public_css( 'core',      'wpmoly.css' );

		// Common stylesheets
		$this->add_public_css( 'common',    'common.css' );
		$this->add_public_css( 'headboxes', 'headboxes.css' );
		$this->add_public_css( 'grids',     'grids.css' );
		$this->add_public_css( 'flags',     'flags.css' );
	}

	/**
	 * Register templates.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_admin_templates() {

		if ( ! is_admin() ) {
			return false;
		}

		global $hook_suffix;

		$this->register_template( 'toast', 'admin/assets/js/templates/toast.php' );

		if ( 'toplevel_page_wpmovielibrary' == $hook_suffix || false !== strpos( $hook_suffix, 'movies-library_page_wpmovielibrary' ) ) {
			$this->register_template( 'settings-editor', 'admin/assets/js/templates/dashboard/settings-editor.php' );
			$this->register_template( 'settings-menu',   'admin/assets/js/templates/dashboard/settings-menu.php' );
			$this->register_template( 'settings-field',  'admin/assets/js/templates/dashboard/settings-field.php' );
			$this->register_template( 'settings-group',  'admin/assets/js/templates/dashboard/settings-group.php' );

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-movies' ) ||
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-grids' ) ) {
				$this->register_template( 'post-editor-categories',  'admin/assets/js/templates/editors/posts/blocks/categories.php' );
				$this->register_template( 'post-editor-tags',        'admin/assets/js/templates/editors/posts/blocks/tags.php' );
				$this->register_template( 'post-editor-submit',      'admin/assets/js/templates/editors/posts/blocks/submit.php' );
				$this->register_template( 'post-editor-discover',    'admin/assets/js/templates/editors/posts/blocks/discover.php' );
				$this->register_template( 'post-editor-add-new',     'admin/assets/js/templates/editors/posts/blocks/add-new.php' );
				$this->register_template( 'post-editor-drafts',      'admin/assets/js/templates/editors/posts/blocks/drafts.php' );
				$this->register_template( 'post-editor-drafts-item', 'admin/assets/js/templates/editors/posts/blocks/drafts-item.php' );
				$this->register_template( 'post-editor-trash',       'admin/assets/js/templates/editors/posts/blocks/trash.php' );
				$this->register_template( 'post-editor-trash-item',  'admin/assets/js/templates/editors/posts/blocks/trash-item.php' );
				$this->register_template( 'post-editor-rename',      'admin/assets/js/templates/editors/posts/blocks/rename.php' );
				$this->register_template( 'post-editor-headline',    'admin/assets/js/templates/editors/posts/headline.php' );
				$this->register_template( 'post-browser',            'admin/assets/js/templates/editors/posts/browser.php' );
				$this->register_template( 'post-browser-menu',       'admin/assets/js/templates/editors/posts/menu.php' );
				$this->register_template( 'post-browser-pagination', 'admin/assets/js/templates/editors/posts/pagination.php' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-movies' ) ) {
				$this->register_template( 'movie-editor',                    'admin/assets/js/templates/editors/movies/editor.php' );
				$this->register_template( 'movie-editor-menu',               'admin/assets/js/templates/editors/movies/editor-menu.php' );
				$this->register_template( 'movie-editor-preview',            'admin/assets/js/templates/editors/movies/editor-preview.php');
				$this->register_template( 'movie-editor-search-form',        'admin/assets/js/templates/editors/movies/editor-search-form.php' );
				$this->register_template( 'movie-editor-search-result',      'admin/assets/js/templates/editors/movies/editor-search-result.php' );
				$this->register_template( 'movie-editor-search-results',     'admin/assets/js/templates/editors/movies/editor-search-results.php' );
				$this->register_template( 'movie-editor-snapshot',           'admin/assets/js/templates/editors/movies/editor-snapshot.php' );
				$this->register_template( 'movie-editor-actors',             'admin/assets/js/templates/editors/movies/blocks/actors.php' );
				$this->register_template( 'movie-editor-certifications',     'admin/assets/js/templates/editors/movies/blocks/certifications.php' );
				$this->register_template( 'movie-editor-collections',        'admin/assets/js/templates/editors/movies/blocks/collections.php' );
				$this->register_template( 'movie-editor-companies',          'admin/assets/js/templates/editors/movies/blocks/companies.php' );
				$this->register_template( 'movie-editor-countries',          'admin/assets/js/templates/editors/movies/blocks/countries.php' );
				$this->register_template( 'movie-editor-details',            'admin/assets/js/templates/editors/movies/blocks/details.php' );
				$this->register_template( 'movie-editor-genres',             'admin/assets/js/templates/editors/movies/blocks/genres.php' );
				$this->register_template( 'movie-editor-languages',          'admin/assets/js/templates/editors/movies/blocks/languages.php' );
				$this->register_template( 'movie-meta-editor',               'admin/assets/js/templates/editors/movies/meta-editor.php' );
				$this->register_template( 'movie-credits-editor',            'admin/assets/js/templates/editors/movies/credits-editor.php' );
				$this->register_template( 'movie-backdrops-editor',          'admin/assets/js/templates/editors/movies/backdrops-editor.php' );
				$this->register_template( 'movie-backdrops-editor-menu',     'admin/assets/js/templates/editors/movies/backdrops-editor-menu.php' );
				$this->register_template( 'movie-backdrops-editor-item',     'admin/assets/js/templates/editors/movies/backdrops-editor-item.php' );
				$this->register_template( 'movie-backdrops-editor-uploader', 'admin/assets/js/templates/editors/movies/backdrops-editor-uploader.php' );
				$this->register_template( 'movie-posters-editor',            'admin/assets/js/templates/editors/movies/posters-editor.php' );
				$this->register_template( 'movie-posters-editor-menu',       'admin/assets/js/templates/editors/movies/posters-editor-menu.php' );
				$this->register_template( 'movie-posters-editor-item',       'admin/assets/js/templates/editors/movies/posters-editor-item.php' );
				$this->register_template( 'movie-posters-editor-uploader',   'admin/assets/js/templates/editors/movies/posters-editor-uploader.php' );
				$this->register_template( 'movie-browser-item',              'admin/assets/js/templates/editors/movies/browser-item.php' );
				$this->register_template( 'movie-modal',                     'admin/assets/js/templates/editors/movies/modal.php' );
				$this->register_template( 'movie-modal-preview',             'admin/assets/js/templates/editors/movies/modal-preview.php' );
				$this->register_template( 'movie-modal-editor',              'admin/assets/js/templates/editors/movies/modal-editor.php' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-grids' ) ) {
				$this->register_template( 'grid-editor',             'admin/assets/js/templates/editors/grids/editor.php' );
				$this->register_template( 'grid-editor-parameters',  'admin/assets/js/templates/editors/grids/blocks/parameters.php' );
				$this->register_template( 'grid-editor-archives',    'admin/assets/js/templates/editors/grids/blocks/archives.php' );
				$this->register_template( 'grid-meta-editor',        'admin/assets/js/templates/editors/grids/meta-editor.php' );
				$this->register_template( 'grid-browser-item',       'admin/assets/js/templates/editors/grids/browser-item.php' );

				$this->register_template( 'grid',                      'public/assets/js/templates/grid/grid.php' );
				$this->register_template( 'grid-menu',                 'public/assets/js/templates/grid/menu.php' );
				$this->register_template( 'grid-customs',              'public/assets/js/templates/grid/customs.php' );
				$this->register_template( 'grid-settings',             'public/assets/js/templates/grid/settings.php' );
				$this->register_template( 'grid-pagination',           'public/assets/js/templates/grid/pagination.php' );

				$this->register_template( 'grid-error',                'public/assets/js/templates/grid/content/error.php' );
				$this->register_template( 'grid-empty',                'public/assets/js/templates/grid/content/empty.php' );

				$this->register_template( 'grid-movie-grid',           'public/assets/js/templates/grid/content/movie-grid.php' );
				$this->register_template( 'grid-movie-grid-variant-1', 'public/assets/js/templates/grid/content/movie-grid-variant-1.php' );
				$this->register_template( 'grid-movie-grid-variant-2', 'public/assets/js/templates/grid/content/movie-grid-variant-2.php' );
				$this->register_template( 'grid-movie-list',           'public/assets/js/templates/grid/content/movie-list.php' );
				$this->register_template( 'grid-actor-grid',           'public/assets/js/templates/grid/content/actor-grid.php' );
				$this->register_template( 'grid-actor-list',           'public/assets/js/templates/grid/content/actor-list.php' );
				$this->register_template( 'grid-collection-grid',      'public/assets/js/templates/grid/content/collection-grid.php' );
				$this->register_template( 'grid-collection-list',      'public/assets/js/templates/grid/content/collection-list.php' );
				$this->register_template( 'grid-genre-grid',           'public/assets/js/templates/grid/content/genre-grid.php' );
				$this->register_template( 'grid-genre-list',           'public/assets/js/templates/grid/content/genre-list.php' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-actors' ) ||
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-collections' ) ||
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-genres' ) ) {
				$this->register_template( 'grid',                    'public/assets/js/templates/grid/grid.php' );
				$this->register_template( 'grid-menu',               'public/assets/js/templates/grid/menu.php' );
				$this->register_template( 'grid-customs',            'public/assets/js/templates/grid/customs.php' );
				$this->register_template( 'grid-settings',           'public/assets/js/templates/grid/settings.php' );
				$this->register_template( 'grid-pagination',         'public/assets/js/templates/grid/pagination.php' );
				$this->register_template( 'grid-error',              'public/assets/js/templates/grid/content/error.php' );
				$this->register_template( 'grid-empty',              'public/assets/js/templates/grid/content/empty.php' );
				$this->register_template( 'grid-movie-grid',         'public/assets/js/templates/grid/content/movie-grid.php' );

				$this->register_template( 'term-editor-discover',    'admin/assets/js/templates/editors/terms/blocks/discover.php' );
				$this->register_template( 'term-editor-add-new',     'admin/assets/js/templates/editors/terms/blocks/add-new.php' );
				$this->register_template( 'term-editor-rename',      'admin/assets/js/templates/editors/terms/blocks/rename.php' );
		 		$this->register_template( 'term-editor-submit',      'admin/assets/js/templates/editors/terms/blocks/submit.php' );
				$this->register_template( 'term-browser-menu',       'admin/assets/js/templates/editors/terms/menu.php' );
				$this->register_template( 'term-browser',            'admin/assets/js/templates/editors/terms/browser.php' );
				$this->register_template( 'term-browser-item',       'admin/assets/js/templates/editors/terms/browser-item.php' );
				$this->register_template( 'term-browser-pagination', 'admin/assets/js/templates/editors/terms/pagination.php' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-actors' ) ) {
				$this->register_template( 'actor-editor',                    'admin/assets/js/templates/editors/actors/editor.php' );
				$this->register_template( 'actor-editor-menu',               'admin/assets/js/templates/editors/actors/editor-menu.php' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-collections' ) ) {
				$this->register_template( 'collection-editor',                    'admin/assets/js/templates/editors/collections/editor.php' );
				$this->register_template( 'collection-editor-menu',               'admin/assets/js/templates/editors/collections/editor-menu.php' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-genres' ) ) {
				$this->register_template( 'genre-editor',          'admin/assets/js/templates/editors/genres/editor.php' );
				$this->register_template( 'genre-editor-menu',     'admin/assets/js/templates/editors/genres/editor-menu.php' );
			}
		}
	}

	/**
	 * Register templates.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_public_templates() {

		$this->register_template( 'grid',                      'public/assets/js/templates/grid/grid.php' );
		$this->register_template( 'grid-menu',                 'public/assets/js/templates/grid/menu.php' );
		$this->register_template( 'grid-customs',              'public/assets/js/templates/grid/customs.php' );
		$this->register_template( 'grid-settings',             'public/assets/js/templates/grid/settings.php' );
		$this->register_template( 'grid-pagination',           'public/assets/js/templates/grid/pagination.php' );

		$this->register_template( 'grid-error',                'public/assets/js/templates/grid/content/error.php' );
		$this->register_template( 'grid-empty',                'public/assets/js/templates/grid/content/empty.php' );

		$this->register_template( 'grid-movie-grid',           'public/assets/js/templates/grid/content/movie-grid.php' );
		$this->register_template( 'grid-movie-grid-variant-1', 'public/assets/js/templates/grid/content/movie-grid-variant-1.php' );
		$this->register_template( 'grid-movie-grid-variant-2', 'public/assets/js/templates/grid/content/movie-grid-variant-2.php' );
		$this->register_template( 'grid-movie-list',           'public/assets/js/templates/grid/content/movie-list.php' );
		$this->register_template( 'grid-actor-grid',           'public/assets/js/templates/grid/content/actor-grid.php' );
		$this->register_template( 'grid-actor-list',           'public/assets/js/templates/grid/content/actor-list.php' );
		$this->register_template( 'grid-collection-grid',      'public/assets/js/templates/grid/content/collection-grid.php' );
		$this->register_template( 'grid-collection-list',      'public/assets/js/templates/grid/content/collection-list.php' );
		$this->register_template( 'grid-genre-grid',           'public/assets/js/templates/grid/content/genre-grid.php' );
		$this->register_template( 'grid-genre-list',           'public/assets/js/templates/grid/content/genre-list.php' );
	}

	/**
	 * Register an admin style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle  Style handle name.
	 * @param string $src     Style full URL.
	 * @param array  $deps    Style dependencies.
	 * @param string $version Style version.
	 * @param string $media   Style media.
	 */
	private function add_admin_css( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

		/**
		 * Filter the admin style URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/admin/style/src', $src );

		$this->register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * Register a public style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle  Style handle name.
	 * @param string $src     Style full URL.
	 * @param array  $deps    Style dependencies.
	 * @param string $version Style version.
	 * @param string $media   Style media.
	 */
	private function add_public_css( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

		/**
		 * Filter the public style URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/public/style/src', $src );

		$this->register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * Register an admin script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $handle    Script handle name.
	 * @param string  $src       Script full URL.
	 * @param array   $deps      Script dependencies.
	 * @param string  $version   Script version.
	 * @param boolean $in_footer Include in footer?
	 */
	private function add_admin_js( $handle, $src, $deps = array(), $version = false, $in_footer = true ) {

		/**
		 * Filter the admin script URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/admin/script/src', $src );

		$this->register_script( $handle, $src, $deps, $version, $in_footer );
	}

	/**
	 * Register a public script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $handle    Script handle name.
	 * @param string  $src       Script full URL.
	 * @param array   $deps      Script dependencies.
	 * @param string  $version   Script version.
	 * @param boolean $in_footer Include in footer?
	 */
	private function add_public_js( $handle, $src, $deps = array(), $version = false, $in_footer = true ) {

		/**
		 * Filter the public script URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/public/script/src', $src );

		$this->register_script( $handle, $src, $deps, $version, $in_footer );
	}

	/**
	 * Register a public font.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $handle    Font handle name.
	 * @param string  $src       Font full URL.
	 * @param array   $deps      Font dependencies.
	 * @param string  $version   Font version.
	 * @param boolean $media     Media
	 */
	private function add_public_font( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

		/**
		 * Filter the public font URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/public/font/src', $src );

		$this->register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * Register single script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $handle    Script handle name.
	 * @param string  $src       Script full URL.
	 * @param array   $deps      Script dependencies.
	 * @param string  $version   Script version.
	 * @param boolean $in_footer Include in footer?
	 *
	 * @return boolean
	 */
	private function register_script( $handle, $src, $deps = array(), $version = false, $in_footer = true ) {

		/**
		 * Filter the Asset handle.
		 *
		 * @since 3.0.0
		 *
		 * @param string $handle Asset handle.
		 */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		/**
		 * Filter the Asset URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/assets/src', $src );

		/**
		 * Filter the Asset version.
		 *
		 * @since 3.0.0
		 *
		 * @param string $version Asset version.
		 */
		$version = apply_filters( 'wpmoly/filter/assets/version', $version );

		return wp_register_script( $handle, $src, $deps, $version, $in_footer );
	}

	/**
	 * Register single style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle  Style handle name.
	 * @param string $src     Style full URL.
	 * @param array  $deps    Style dependencies.
	 * @param string $version Style version.
	 * @param string $media   Style media.
	 *
	 * @return boolean
	 */
	private function register_style( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		/** This filter is defined in includes/core/class-assets.php */
		$src = apply_filters( 'wpmoly/filter/assets/src', $src );

		/** This filter is defined in includes/core/class-assets.php */
		$version = apply_filters( 'wpmoly/filter/assets/version', $version );

		return wp_register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * Register single template.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Template handle name.
	 * @param string $src    Template URL.
	 */
	private function register_template( $handle, $src ) {

		global $wpmoly_templates;

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		$wpmoly_templates[ $handle ] = wpmoly_get_js_template( $src );
	}

	/**
	 * Enqueue single script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Script handle name.
	 */
	private function enqueue_script( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		wp_enqueue_script( $handle );
	}

	/**
	 * Enqueue single style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Style handle name.
	 */
	private function enqueue_style( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		wp_enqueue_style( $handle );
	}

	/**
	 * Enqueue single template.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Template handle name.
	 */
	private function enqueue_template( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		global $wpmoly_templates;

		if ( ! isset( $wpmoly_templates[ $handle ] ) || ! $wpmoly_templates[ $handle ] instanceof \wpmoly\templates\Template ) {
			return false;
		}
?>
	<script type="text/html" id="tmpl-<?php echo esc_attr( $handle ); ?>"><?php $wpmoly_templates[ $handle ]->render( 'always' ); ?>
	</script>

<?php
	}

	/**
	 * Prefix the Asset handle with plugin slug.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset handle.
	 *
	 * @return string
	 */
	public function prefix_handle( $handle ) {

		return "wpmoly-{$handle}";
	}

	/**
	 * Prefix the styles URL with admin folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_admin_style_src( $src ) {

		return "admin/assets/css/{$src}";
	}

	/**
	 * Prefix the scripts URL with admin folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_admin_script_src( $src ) {

		return "admin/assets/js/{$src}";
	}

	/**
	 * Prefix the fonts URL with admin folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_admin_font_src( $src ) {

		return "admin/assets/fonts/{$src}";
	}

	/**
	 * Prefix the styles URL with public folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_public_style_src( $src ) {

		return "public/assets/css/{$src}";
	}

	/**
	 * Prefix the scripts URL with public folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_public_script_src( $src ) {

		return "public/assets/js/{$src}";
	}

	/**
	 * Prefix the fonts URL with public folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_public_font_src( $src ) {

		return "public/assets/fonts/{$src}";
	}

	/**
	 * Prefix the Asset URL with plugin URL.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_src( $src ) {

		return WPMOLY_URL . $src;
	}

	/**
	 * Set a default Asset version is needed.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset Version.
	 *
	 * @return string
	 */
	public function default_version( $version ) {

		if ( empty( $version ) ) {
			$version = WPMOLY_VERSION;
		}

		return $version;
	}

}
