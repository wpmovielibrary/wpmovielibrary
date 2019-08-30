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

use wpmoly\utils;

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

		$this->enqueue_script( 'admin' );

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

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-persons' ) ) {
				if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
					wp_enqueue_media();
					wp_enqueue_script( 'media-grid' );
					$this->enqueue_script( 'person-editor' );
				} else {
					$this->enqueue_script( 'person-browser' );
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
	 * Enqueue Gutenberg editor scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_block_editor_scripts() {

		if ( ! is_admin() ) {
			return false;
		}

		$this->register_block_editor_scripts();

		$this->enqueue_script( 'gutenberg' );
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

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-movies' ) ) {
				if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
					$this->enqueue_style( 'movie-editor' );
				} else {
					$this->enqueue_style( 'movie-browser' );
				}
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-persons' ) ) {
				if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
					$this->enqueue_style( 'person-editor' );
				} else {
					$this->enqueue_style( 'person-browser' );
				}
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-grids' ) ) {
				if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
					$this->enqueue_style( 'grid-editor' );
				} else {
					$this->enqueue_style( 'grid-browser' );
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
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-persons' ) ||
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
				$this->enqueue_template( 'post-browser-context-menu' );
				$this->enqueue_template( 'post-browser-item' );
				$this->enqueue_template( 'post-browser-pagination' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-movies' ) ) {
				$this->enqueue_template( 'movie-editor' );
				$this->enqueue_template( 'movie-editor-menu' );
				$this->enqueue_template( 'movie-editor-preview' );
				$this->enqueue_template( 'movie-editor-search-loading' );
				$this->enqueue_template( 'movie-editor-search-form' );
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
				$this->enqueue_template( 'movie-editor-submit' );
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
				$this->enqueue_template( 'movie-browser-context-menu' );
				$this->enqueue_template( 'movie-browser-item' );
				$this->enqueue_template( 'movie-modal' );
				$this->enqueue_template( 'movie-modal-preview' );
				$this->enqueue_template( 'movie-modal-editor' );
				$this->enqueue_template( 'movie-modal-editor-images' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-persons' ) ) {
				$this->enqueue_template( 'person-editor' );
				$this->enqueue_template( 'person-editor-menu' );
				$this->enqueue_template( 'person-editor-preview' );
				$this->enqueue_template( 'person-editor-search-loading' );
				$this->enqueue_template( 'person-editor-search-form' );
				$this->enqueue_template( 'person-editor-search-results' );
				$this->enqueue_template( 'person-editor-snapshot' );
				$this->enqueue_template( 'person-editor-submit' );
				$this->enqueue_template( 'person-meta-editor' );
				$this->enqueue_template( 'person-credits-editor' );
				$this->enqueue_template( 'person-credits-editor-item' );
				$this->enqueue_template( 'person-backdrops-editor' );
				$this->enqueue_template( 'person-backdrops-editor-menu' );
				$this->enqueue_template( 'person-backdrops-editor-item' );
				$this->enqueue_template( 'person-backdrops-editor-uploader' );
				$this->enqueue_template( 'person-pictures-editor' );
				$this->enqueue_template( 'person-pictures-editor-menu' );
				$this->enqueue_template( 'person-pictures-editor-item' );
				$this->enqueue_template( 'person-pictures-editor-uploader' );
				$this->enqueue_template( 'person-browser-item' );
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
				$this->enqueue_template( 'grid-person-grid' );
				$this->enqueue_template( 'grid-person-list' );
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
				$this->enqueue_template( 'grid-person-grid' );

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
				$this->enqueue_template( 'term-editor' );
				$this->enqueue_template( 'term-meta-editor' );
				$this->enqueue_template( 'term-thumbnail-editor' );
				$this->enqueue_template( 'term-thumbnail-downloader' );
				$this->enqueue_template( 'term-thumbnail-uploader' );
				$this->enqueue_template( 'actor-editor' );
				$this->enqueue_template( 'actor-thumbnail-picker' );
				$this->enqueue_template( 'actor-thumbnail-downloader' );
				$this->enqueue_template( 'actor-thumbnail-uploader' );
				$this->enqueue_template( 'actor-related-person' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-collections' ) ) {
				$this->enqueue_template( 'term-editor' );
				$this->enqueue_template( 'term-meta-editor' );
				$this->enqueue_template( 'term-thumbnail-editor' );
				$this->enqueue_template( 'term-thumbnail-downloader' );
				$this->enqueue_template( 'term-thumbnail-uploader' );
				$this->enqueue_template( 'collection-editor' );
				$this->enqueue_template( 'collection-thumbnail-picker' );
				$this->enqueue_template( 'collection-thumbnail-downloader' );
				$this->enqueue_template( 'collection-thumbnail-uploader' );
				$this->enqueue_template( 'collection-related-person' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-genres' ) ) {
				$this->enqueue_template( 'term-editor' );
				$this->enqueue_template( 'term-meta-editor' );
				$this->enqueue_template( 'term-thumbnail-editor' );
				$this->enqueue_template( 'term-thumbnail-downloader' );
				$this->enqueue_template( 'term-thumbnail-uploader' );
				$this->enqueue_template( 'genre-editor' );
				$this->enqueue_template( 'genre-thumbnail-picker' );
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
		$this->enqueue_template( 'grid-person-grid' );
		$this->enqueue_template( 'grid-person-list' );
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
		$this->add_public_js( 'sprintf-js',        'sprintf-js.min.js', array( 'underscore' ) );
		$this->add_public_js( 'renderjson-js',     'renderjson.min.js' );

		// Base
		$this->add_admin_js( 'admin', 'admin.js',  array( 'jquery' ) );
		$this->add_public_js( 'utils', 'utils.js',  array( 'wpmoly-underscore-string', 'wpmoly-renderjson-js', 'wpmoly-toasts' ) );
		$this->add_public_js( 'core',  'wpmoly.js', array( 'jquery', 'wpmoly-underscore-string', 'wp-backbone', 'wp-api', 'wpmoly-utils' ) );
		$this->add_public_js( 'api',   'api.js',    array( 'jquery', 'wpmoly-underscore-string', 'wp-backbone', 'wp-api', 'wpmoly-utils' ) );

		// Runners
		$this->add_public_js( 'grids',    'grids.js',     array( 'wpmoly-core', 'wpmoly-api' ) );
		$this->add_admin_js( 'dashboard', 'dashboard.js', array( 'wpmoly-core', 'wpmoly-api' ) );

		// Posts browser.
		$this->add_admin_js( 'post-browser',   'post-browser.js',  array( 'wpmoly-core', 'wpmoly-api' ) );
		$this->add_admin_js( 'movie-browser',  'movie-browser.js', array( 'wpmoly-post-browser' ) );
		$this->add_admin_js( 'person-browser', 'person-browser.js', array( 'wpmoly-post-browser' ) );
		$this->add_admin_js( 'grid-browser',   'grid-browser.js',  array( 'wpmoly-post-browser' ) );

		// Posts editor.
		$this->add_admin_js( 'post-editor',   'post-editor.js',  array( 'wpmoly-core', 'wpmoly-api' ) );
		$this->add_admin_js( 'movie-editor',  'movie-editor.js', array( 'wpmoly-post-editor', 'wpmoly-tmdb', 'wp-plupload', 'plupload-handlers' ) );
		$this->add_admin_js( 'person-editor', 'person-editor.js', array( 'wpmoly-post-editor', 'wpmoly-tmdb', 'wp-plupload', 'plupload-handlers' ) );
		$this->add_admin_js( 'grid-editor',   'grid-editor.js',  array( 'wpmoly-post-editor', 'wpmoly-grids' ) );

		// Terms browser.
		$this->add_admin_js( 'term-browser',       'term-browser.js',       array( 'wpmoly-core', 'wpmoly-api' ) );
		$this->add_admin_js( 'actor-browser',      'actor-browser.js',      array( 'wpmoly-term-browser' ) );
		$this->add_admin_js( 'collection-browser', 'collection-browser.js', array( 'wpmoly-term-browser' ) );
		$this->add_admin_js( 'genre-browser',      'genre-browser.js',      array( 'wpmoly-term-browser' ) );

		// Terms editor.
		$this->add_admin_js( 'term-editor',       'term-editor.js',       array( 'wpmoly-core', 'wpmoly-api', 'wpmoly-grids' ) );
		$this->add_admin_js( 'actor-editor',      'actor-editor.js',      array( 'wpmoly-term-editor', 'wpmoly-tmdb', 'wp-plupload', 'plupload-handlers' ) );
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
		$this->add_public_js( 'sprintf-js',        'sprintf-js.min.js', array( 'underscore' ) );

		// Base
		$this->add_public_js( 'utils', 'utils.js',  array( 'wpmoly-underscore-string' ) );
		$this->add_public_js( 'core',  'wpmoly.js', array( 'jquery', 'wpmoly-underscore-string', 'wp-backbone', 'wp-api', 'wpmoly-utils' ) );
		$this->add_public_js( 'api',   'api.js',    array( 'jquery', 'wpmoly-underscore-string', 'wp-backbone', 'wp-api', 'wpmoly-utils' ) );

		// Runners
		$this->add_public_js( 'grids',     'grids.js',     array( 'wpmoly-core', 'wpmoly-api' ) );
		$this->add_public_js( 'headboxes', 'headboxes.js', array( 'wpmoly-core' ) );
	}

	/**
	 * Register Gutenberg editor scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_block_editor_scripts() {

		$this->add_admin_js( 'gutenberg', 'gutenberg.js', array( 'wpmoly-core' ) );
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

		// Posts.
		$this->add_admin_css( 'post-browser',   'post-browser.css' );
		$this->add_admin_css( 'movie-browser',  'movie-browser.css',  array( 'wpmoly-post-browser' ) );
		$this->add_admin_css( 'person-browser', 'person-browser.css', array( 'wpmoly-post-browser' ) );
		$this->add_admin_css( 'grid-browser',   'grid-browser.css',   array( 'wpmoly-post-browser' ) );

		$this->add_admin_css( 'post-editor',   'post-editor.css' );
		$this->add_admin_css( 'movie-editor',  'movie-editor.css',  array( 'wpmoly-post-editor' ) );
		$this->add_admin_css( 'person-editor', 'person-editor.css', array( 'wpmoly-post-editor' ) );
		$this->add_admin_css( 'grid-editor',   'grid-editor.css',   array( 'wpmoly-post-editor' ) );

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
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-persons' ) ||
			     false !== strpos( $hook_suffix, '_page_wpmovielibrary-grids' ) ) {
				$this->register_template( 'post-editor-categories',    'admin/assets/js/templates/editors/posts/blocks/categories.php' );
				$this->register_template( 'post-editor-tags',          'admin/assets/js/templates/editors/posts/blocks/tags.php' );
				$this->register_template( 'post-editor-submit',        'admin/assets/js/templates/editors/posts/blocks/submit.php' );
				$this->register_template( 'post-editor-discover',      'admin/assets/js/templates/editors/posts/blocks/discover.php' );
				$this->register_template( 'post-editor-add-new',       'admin/assets/js/templates/editors/posts/blocks/add-new.php' );
				$this->register_template( 'post-editor-drafts',        'admin/assets/js/templates/editors/posts/blocks/drafts.php' );
				$this->register_template( 'post-editor-drafts-item',   'admin/assets/js/templates/editors/posts/blocks/drafts-item.php' );
				$this->register_template( 'post-editor-trash',         'admin/assets/js/templates/editors/posts/blocks/trash.php' );
				$this->register_template( 'post-editor-trash-item',    'admin/assets/js/templates/editors/posts/blocks/trash-item.php' );
				$this->register_template( 'post-editor-rename',        'admin/assets/js/templates/editors/posts/blocks/rename.php' );
				$this->register_template( 'post-editor-headline',      'admin/assets/js/templates/editors/posts/headline.php' );
				$this->register_template( 'post-browser',              'admin/assets/js/templates/editors/posts/browser.php' );
				$this->register_template( 'post-browser-context-menu', 'admin/assets/js/templates/editors/posts/browser-context-menu.php' );
				$this->register_template( 'post-browser-item',         'admin/assets/js/templates/editors/posts/browser-item.php' );
				$this->register_template( 'post-browser-pagination',   'admin/assets/js/templates/editors/posts/pagination.php' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-movies' ) ) {
				$this->register_template( 'movie-editor',                    'admin/assets/js/templates/editors/movies/editor.php' );
				$this->register_template( 'movie-editor-menu',               'admin/assets/js/templates/editors/movies/editor-menu.php' );
				$this->register_template( 'movie-editor-preview',            'admin/assets/js/templates/editors/movies/editor-preview.php');
				$this->register_template( 'movie-editor-search-loading',     'admin/assets/js/templates/editors/movies/editor-search-loading.php' );
				$this->register_template( 'movie-editor-search-form',        'admin/assets/js/templates/editors/movies/editor-search-form.php' );
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
				$this->register_template( 'movie-editor-submit',             'admin/assets/js/templates/editors/movies/blocks/submit.php' );
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
				$this->register_template( 'movie-browser-context-menu',      'admin/assets/js/templates/editors/movies/browser-context-menu.php' );
				$this->register_template( 'movie-browser-item',              'admin/assets/js/templates/editors/movies/browser-item.php' );
				$this->register_template( 'movie-modal',                     'admin/assets/js/templates/editors/movies/modal.php' );
				$this->register_template( 'movie-modal-preview',             'admin/assets/js/templates/editors/movies/modal-preview.php' );
				$this->register_template( 'movie-modal-editor',              'admin/assets/js/templates/editors/movies/modal-editor.php' );
				$this->register_template( 'movie-modal-editor-images',       'admin/assets/js/templates/editors/movies/modal-editor-images.php' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-persons' ) ) {
				$this->register_template( 'person-editor',                    'admin/assets/js/templates/editors/persons/editor.php' );
				$this->register_template( 'person-editor-menu',               'admin/assets/js/templates/editors/persons/editor-menu.php' );
				$this->register_template( 'person-editor-preview',            'admin/assets/js/templates/editors/persons/editor-preview.php');
				$this->register_template( 'person-editor-search-loading',     'admin/assets/js/templates/editors/persons/editor-search-loading.php' );
				$this->register_template( 'person-editor-search-form',        'admin/assets/js/templates/editors/persons/editor-search-form.php' );
				$this->register_template( 'person-editor-search-results',     'admin/assets/js/templates/editors/persons/editor-search-results.php' );
				$this->register_template( 'person-editor-snapshot',           'admin/assets/js/templates/editors/persons/editor-snapshot.php' );
				$this->register_template( 'person-editor-submit',             'admin/assets/js/templates/editors/persons/blocks/submit.php' );
				$this->register_template( 'person-meta-editor',               'admin/assets/js/templates/editors/persons/meta-editor.php' );
				$this->register_template( 'person-credits-editor',            'admin/assets/js/templates/editors/persons/credits-editor.php' );
				$this->register_template( 'person-credits-editor-item',       'admin/assets/js/templates/editors/persons/credits-editor-item.php' );
				$this->register_template( 'person-backdrops-editor',          'admin/assets/js/templates/editors/persons/backdrops-editor.php' );
				$this->register_template( 'person-backdrops-editor-menu',     'admin/assets/js/templates/editors/persons/backdrops-editor-menu.php' );
				$this->register_template( 'person-backdrops-editor-item',     'admin/assets/js/templates/editors/persons/backdrops-editor-item.php' );
				$this->register_template( 'person-backdrops-editor-uploader', 'admin/assets/js/templates/editors/persons/backdrops-editor-uploader.php' );
				$this->register_template( 'person-pictures-editor',           'admin/assets/js/templates/editors/persons/pictures-editor.php' );
				$this->register_template( 'person-pictures-editor-menu',      'admin/assets/js/templates/editors/persons/pictures-editor-menu.php' );
				$this->register_template( 'person-pictures-editor-item',      'admin/assets/js/templates/editors/persons/pictures-editor-item.php' );
				$this->register_template( 'person-pictures-editor-uploader',  'admin/assets/js/templates/editors/persons/pictures-editor-uploader.php' );
				$this->register_template( 'person-browser-item',              'admin/assets/js/templates/editors/persons/browser-item.php' );
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
				$this->register_template( 'grid-person-grid',          'public/assets/js/templates/grid/content/person-grid.php' );
				$this->register_template( 'grid-person-list',          'public/assets/js/templates/grid/content/person-list.php' );
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
				$this->register_template( 'grid-person-grid',        'public/assets/js/templates/grid/content/person-grid.php' );

				$this->register_template( 'term-editor',               'admin/assets/js/templates/editors/terms/editor.php' );
				$this->register_template( 'term-meta-editor',          'admin/assets/js/templates/editors/terms/meta-editor.php' );
				$this->register_template( 'term-thumbnail-editor',     'admin/assets/js/templates/editors/terms/thumbnail-editor.php' );
				$this->register_template( 'term-thumbnail-picker',     'admin/assets/js/templates/editors/terms/thumbnail-picker.php' );
				$this->register_template( 'term-thumbnail-downloader', 'admin/assets/js/templates/editors/terms/thumbnail-downloader.php' );
				$this->register_template( 'term-thumbnail-uploader',   'admin/assets/js/templates/editors/terms/thumbnail-uploader.php' );
				$this->register_template( 'term-editor-discover',      'admin/assets/js/templates/editors/terms/blocks/discover.php' );
				$this->register_template( 'term-editor-add-new',       'admin/assets/js/templates/editors/terms/blocks/add-new.php' );
				$this->register_template( 'term-editor-rename',        'admin/assets/js/templates/editors/terms/blocks/rename.php' );
		 		$this->register_template( 'term-editor-submit',        'admin/assets/js/templates/editors/terms/blocks/submit.php' );

				$this->register_template( 'term-browser-menu',       'admin/assets/js/templates/editors/terms/menu.php' );
				$this->register_template( 'term-browser',            'admin/assets/js/templates/editors/terms/browser.php' );
				$this->register_template( 'term-browser-item',       'admin/assets/js/templates/editors/terms/browser-item.php' );
				$this->register_template( 'term-browser-pagination', 'admin/assets/js/templates/editors/terms/pagination.php' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-actors' ) ) {
				$this->register_template( 'actor-thumbnail-picker',     'admin/assets/js/templates/editors/actors/thumbnail-picker.php' );
				$this->register_template( 'actor-thumbnail-downloader', 'admin/assets/js/templates/editors/actors/thumbnail-downloader.php' );
				$this->register_template( 'actor-thumbnail-uploader',   'admin/assets/js/templates/editors/actors/thumbnail-uploader.php' );
				$this->register_template( 'actor-related-person',       'admin/assets/js/templates/editors/actors/blocks/related-person.php' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-collections' ) ) {
				$this->register_template( 'collection-thumbnail-picker',     'admin/assets/js/templates/editors/collections/thumbnail-picker.php' );
				$this->register_template( 'collection-thumbnail-downloader', 'admin/assets/js/templates/editors/collections/thumbnail-downloader.php' );
				$this->register_template( 'collection-thumbnail-uploader',   'admin/assets/js/templates/editors/collections/thumbnail-uploader.php' );
				$this->register_template( 'collection-related-person',       'admin/assets/js/templates/editors/collections/blocks/related-person.php' );
			}

			if ( false !== strpos( $hook_suffix, '_page_wpmovielibrary-genres' ) ) {
				$this->register_template( 'genre-thumbnail-picker',     'admin/assets/js/templates/editors/genres/thumbnail-picker.php' );
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
		$this->register_template( 'grid-person-grid',          'public/assets/js/templates/grid/content/person-grid.php' );
		$this->register_template( 'grid-person-list',          'public/assets/js/templates/grid/content/person-list.php' );
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

		$wpmoly_templates[ $handle ] = utils\get_js_template( $src );
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
