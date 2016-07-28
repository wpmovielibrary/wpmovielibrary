<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Backstage {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0
	 *
	 * @var      string
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0
	 *
	 * @var      string
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0
	 *
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 *
	 * @return   null
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function register_styles() {

		wp_register_style( 'wpmoly-font', WPMOLY_URL . 'public/fonts/wpmovielibrary/style.css', array(), $this->version, 'all' );

		wp_register_style( 'wpmoly',         WPMOLY_URL . 'admin/css/wpmoly.css', array(), $this->version, 'all' );
		wp_register_style( 'wpmoly-common',  WPMOLY_URL . 'public/css/common.css', array(), $this->version, 'all' );
		wp_register_style( 'wpmoly-select2', WPMOLY_URL . 'admin/css/select2.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function register_scripts() {

		// Vendor
		wp_register_script( 'sprintf',                     WPMOLY_URL . 'public/js/sprintf.min.js',           array( 'jquery', 'underscore' ), '1.0.3', true );
		wp_register_script( 'underscore-string',           WPMOLY_URL . 'public/js/underscore.string.min.js', array( 'jquery', 'underscore' ), '3.3.4', true );

		// Base
		wp_register_script( 'wpmoly',                      WPMOLY_URL . 'public/js/wpmoly.js',               array( 'jquery', 'underscore', 'backbone' ), $this->version, true );
		wp_register_script( 'wpmoly-utils',                WPMOLY_URL . 'public/js/wpmoly-utils.js',         array( 'wpmoly' ), $this->version, true );

		// Models
		wp_register_script( 'wpmoly-settings-model',       WPMOLY_URL . 'admin/js/models/settings.js',       array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-status-model',         WPMOLY_URL . 'admin/js/models/status.js',         array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-results-model',        WPMOLY_URL . 'admin/js/models/results.js',        array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-search-model',         WPMOLY_URL . 'admin/js/models/search.js',         array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-meta-model',           WPMOLY_URL . 'admin/js/models/meta.js',           array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-modal-model',          WPMOLY_URL . 'admin/js/models/modal/modal.js',    array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-image-model',          WPMOLY_URL . 'admin/js/models/image.js',          array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-images-model',         WPMOLY_URL . 'admin/js/models/images.js',         array( 'wpmoly' ), $this->version, true );

		// Controllers
		wp_register_script( 'wpmoly-search-controller',    WPMOLY_URL . 'admin/js/controllers/search.js',    array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-editor-controller',    WPMOLY_URL . 'admin/js/controllers/editor.js',    array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-modal-controller',     WPMOLY_URL . 'admin/js/controllers/modal.js',     array( 'wpmoly' ), $this->version, true );

		// Views
		wp_register_script( 'wpmoly-frame-view',           WPMOLY_URL . 'public/js/views/frame.js',          array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-confirm-view',         WPMOLY_URL . 'public/js/views/confirm.js',        array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-metabox-view',         WPMOLY_URL . 'admin/js/views/metabox.js',         array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-search-view',          WPMOLY_URL . 'admin/js/views/search/search.js',   array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-search-history-view',  WPMOLY_URL . 'admin/js/views/search/history.js',  array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-search-settings-view', WPMOLY_URL . 'admin/js/views/search/settings.js', array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-search-status-view',   WPMOLY_URL . 'admin/js/views/search/status.js',   array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-search-results-view',  WPMOLY_URL . 'admin/js/views/search/results.js',  array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-editor-image-view',    WPMOLY_URL . 'admin/js/views/editor/image.js',    array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-editor-images-view',   WPMOLY_URL . 'admin/js/views/editor/images.js',   array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-editor-meta-view',     WPMOLY_URL . 'admin/js/views/editor/meta.js',     array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-editor-details-view',  WPMOLY_URL . 'admin/js/views/editor/details.js',  array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-editor-tagbox-view',   WPMOLY_URL . 'admin/js/views/editor/tagbox.js',   array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-editor-view',          WPMOLY_URL . 'admin/js/views/editor/editor.js',   array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-modal-view',           WPMOLY_URL . 'admin/js/views/modal/modal.js',     array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-modal-images-view',    WPMOLY_URL . 'admin/js/views/modal/images.js',    array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-modal-browser-view',   WPMOLY_URL . 'admin/js/views/modal/browser.js',   array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-modal-post-view',      WPMOLY_URL . 'admin/js/views/modal/post.js',      array( 'wpmoly' ), $this->version, true );

		// Runners
		wp_register_script( 'wpmoly-api',                  WPMOLY_URL . 'admin/js/wpmoly-api.js',            array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-metabox',              WPMOLY_URL . 'admin/js/wpmoly-metabox.js',        array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-editor',               WPMOLY_URL . 'admin/js/wpmoly-editor.js',         array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-search',               WPMOLY_URL . 'admin/js/wpmoly-search.js',         array( 'wpmoly' ), $this->version, true );
		wp_register_script( 'wpmoly-tester',               WPMOLY_URL . 'admin/js/wpmoly-tester.js',         array( 'wpmoly' ), $this->version, true );

		// Libraries
		wp_register_script( 'wpmoly-select2',              WPMOLY_URL . 'admin/js/select2.min.js',           array( 'jquery' ), '4.0.1',  true );
		wp_register_script( 'jquery-actual',               WPMOLY_URL . 'admin/js/jquery.actual.min.js',     array( 'jquery' ), '1.0.17', true );
	}

	/**
	 * Enqueue the stylesheets for the admin area.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function enqueue_styles() {

		$this->register_styles();

		wp_enqueue_style( 'wpmoly-font' );

		wp_enqueue_style( 'wpmoly' );
		wp_enqueue_style( 'wpmoly-common' );
		wp_enqueue_style( 'wpmoly-select2' );
	}

	/**
	 * Enqueue the JavaScript for the admin area.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $hook_suffix
	 * 
	 * @return   null
	 */
	public function enqueue_scripts( $hook_suffix ) {

		$this->register_scripts();

		if ( ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) && 'movie' == get_post_type() ) {

			// Vendor
			wp_enqueue_script( 'sprintf' );
			wp_enqueue_script( 'underscore-string' );

			// Base
			wp_enqueue_script( 'wpmoly' );
			wp_enqueue_script( 'wpmoly-utils' );

			// Models
			wp_enqueue_script( 'wpmoly-settings-model' );
			wp_enqueue_script( 'wpmoly-status-model' );
			wp_enqueue_script( 'wpmoly-results-model' );
			wp_enqueue_script( 'wpmoly-search-model' );
			wp_enqueue_script( 'wpmoly-meta-model' );
			wp_enqueue_script( 'wpmoly-modal-model' );
			wp_enqueue_script( 'wpmoly-image-model' );
			wp_enqueue_script( 'wpmoly-admin-image-model' );
			wp_enqueue_script( 'wpmoly-images-model' );

			// Controllers
			wp_enqueue_script( 'wpmoly-search-controller' );
			wp_enqueue_script( 'wpmoly-editor-controller' );
			wp_enqueue_script( 'wpmoly-modal-controller' );

			// Views
			wp_enqueue_script( 'wpmoly-frame-view' );
			wp_enqueue_script( 'wpmoly-confirm-view' );
			wp_enqueue_script( 'wpmoly-metabox-view' );
			wp_enqueue_script( 'wpmoly-search-view' );
			wp_enqueue_script( 'wpmoly-search-history-view' );
			wp_enqueue_script( 'wpmoly-search-settings-view' );
			wp_enqueue_script( 'wpmoly-search-status-view' );
			wp_enqueue_script( 'wpmoly-search-results-view' );
			wp_enqueue_script( 'wpmoly-editor-image-view' );
			wp_enqueue_script( 'wpmoly-editor-images-view' );
			wp_enqueue_script( 'wpmoly-editor-meta-view' );
			wp_enqueue_script( 'wpmoly-editor-details-view' );
			wp_enqueue_script( 'wpmoly-editor-tagbox-view' );
			wp_enqueue_script( 'wpmoly-editor-view' );
			wp_enqueue_script( 'wpmoly-modal-view' );
			wp_enqueue_script( 'wpmoly-modal-images-view' );
			wp_enqueue_script( 'wpmoly-modal-browser-view' );
			wp_enqueue_script( 'wpmoly-modal-post-view' );

			// Runners
			wp_enqueue_script( 'wpmoly-api' );
			wp_enqueue_script( 'wpmoly-metabox' );
			wp_enqueue_script( 'wpmoly-editor' );
			wp_enqueue_script( 'wpmoly-search' );
			wp_enqueue_script( 'wpmoly-tester' );

			// Libraries
			wp_enqueue_script( 'wpmoly-select2' );
			wp_enqueue_script( 'jquery-actual' );
		}

		if ( ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) && 'grid' == get_post_type() ) {

			// Vendor
			wp_enqueue_script( 'sprintf' );
			wp_enqueue_script( 'underscore-string' );

			// Base
			wp_enqueue_script( 'wpmoly' );
			wp_enqueue_script( 'wpmoly-utils' );

			// Libraries
			wp_enqueue_script( 'wpmoly-select2' );
		}

	}

	/**
	 * Print the JavaScript templates for the admin area.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function enqueue_templates() {

		if ( 'movie' == get_post_type() ) {
			$this->print_template( 'wpmoly-search',                'admin/js/templates/search/search.php' );
			$this->print_template( 'wpmoly-search-form',           'admin/js/templates/search/search-form.php' );
			$this->print_template( 'wpmoly-search-settings',       'admin/js/templates/search/settings.php' );
			$this->print_template( 'wpmoly-search-status',         'admin/js/templates/search/status.php' );
			$this->print_template( 'wpmoly-search-history',        'admin/js/templates/search/history.php' );
			$this->print_template( 'wpmoly-search-history-item',   'admin/js/templates/search/history-item.php' );
			$this->print_template( 'wpmoly-search-result',         'admin/js/templates/search/result.php' );
			$this->print_template( 'wpmoly-search-results',        'admin/js/templates/search/results.php' );
			$this->print_template( 'wpmoly-search-results-header', 'admin/js/templates/search/results-header.php' );
			$this->print_template( 'wpmoly-search-results-menu',   'admin/js/templates/search/results-menu.php' );

			$this->print_template( 'wpmoly-editor-image-editor',   'admin/js/templates/editor/image-editor.php' );
			$this->print_template( 'wpmoly-editor-image-more',     'admin/js/templates/editor/image-more.php' );
			$this->print_template( 'wpmoly-editor-image',          'admin/js/templates/editor/image.php' );

			$this->print_template( 'wpmoly-modal-browser',         'admin/js/templates/modal/browser.php' );
			$this->print_template( 'wpmoly-modal-sidebar',         'admin/js/templates/modal/sidebar.php' );
			$this->print_template( 'wpmoly-modal-toolbar',         'admin/js/templates/modal/toolbar.php' );
			$this->print_template( 'wpmoly-modal-image',           'admin/js/templates/modal/image.php' );
			$this->print_template( 'wpmoly-modal-selection',       'admin/js/templates/modal/selection.php' );

			$this->print_template( 'wpmoly-confirm-modal',         'public/js/templates/confirm.php' );
		}
	}

	/**
	 * Print a JavaScript template.
	 *
	 * @since    3.0
	 *
	 * @param    string    $handle Template slug
	 * @param    string    $src Template file path
	 *
	 * @return   null
	 */
	private function print_template( $handle, $src ) {

		if ( ! file_exists( WPMOLY_PATH . $src ) ) {
			return false;
		}

		echo "\n" . '<script type="text/html" id="tmpl-' . $handle . '">';
		require_once WPMOLY_PATH . $src;
		echo '</script>' . "\n";
	}

	/**
	 * Plugged on the 'admin_init' action hook.
	 *
	 * This is a workaround for adding images from URL to the Media Uploader.
	 *
	 * Filter the $_FILES array before it reaches the 'upload-attachment'
	 * Ajax callback to fix the filename. PlUpload send data with filename
	 * containing 'blob', causing errors as WordPress is --and shouldn't--
	 * using that value to check files names and extensions.
	 *
	 * @since    3.0
	 *
	 * @return   void
	 */
	public function admin_init() {

		if ( ! defined( 'DOING_AJAX' ) || true !== DOING_AJAX ) {
			return false;
		}

		if ( ! empty( $_FILES['async-upload']['name'] ) && 'blob' == $_FILES['async-upload']['name'] ) {
			if ( ! empty( $_REQUEST['name'] ) && ( ! empty( $_REQUEST['_wpmoly_nonce'] ) && wp_verify_nonce( $_REQUEST['_wpmoly_nonce'], 'wpmoly-blob-filename' ) ) ) {
				$_FILES['async-upload']['name'] = $_REQUEST['name'];
			}
		}
	}

	/**
	 * Add a custom nonce the default settings for PlUpload.
	 *
	 * @since    3.0
	 *
	 * @param    array    $params
	 *
	 * @return   array
	 */
	public function plupload_default_params( $params ) {

		global $pagenow;

		if ( ( empty( $pagenow ) || 'post.php' != $pagenow ) || 'movie' != get_post_type() ) {
			return $params;
		}

		$params['_wpmoly_nonce'] = wp_create_nonce( 'wpmoly-blob-filename' );

		return $params;
	}
}
