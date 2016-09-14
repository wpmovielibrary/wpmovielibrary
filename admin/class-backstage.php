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
	 * Single instance.
	 *
	 * @var    Backstage
	 */
	private static $instance = null;

	/**
	 * Admin stylesheets.
	 *
	 * @var    array
	 */
	private $styles = array();

	/**
	 * Admin scripts.
	 *
	 * @var    array
	 */
	private $scripts = array();

	/**
	 * Initialize the class.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function __construct() {

		$styles = array(
			''             => array( 'file' => WPMOLY_URL . 'admin/css/wpmoly.css' ),
			'metabox'      => array( 'file' => WPMOLY_URL . 'admin/css/wpmoly-metabox.css' ),
			'grid-builder' => array( 'file' => WPMOLY_URL . 'admin/css/wpmoly-grid-builder.css' ),

			'font'         => array( 'file' => WPMOLY_URL . 'public/fonts/wpmovielibrary/style.css' ),
			'common'       => array( 'file' => WPMOLY_URL . 'public/css/common.css' ),
			'grids'        => array( 'file' => WPMOLY_URL . 'public/css/wpmoly-grids.css' ),
			'select2'      => array( 'file' => WPMOLY_URL . 'admin/css/select2.min.css' )
		);

		/**
		 * Filter the default styles to register.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $styles
		 */
		$this->styles = apply_filters( 'wpmoly/filter/default/admin/styles', $styles );

		$scripts = array(

			// Vendor
			'sprintf' => array(
				'file'    => WPMOLY_URL . 'public/js/sprintf.min.js',
				'deps'    => array( 'jquery', 'underscore' ),
				'version' => '1.0.3'
			),
			'underscore-string' => array(
				'file' => WPMOLY_URL . 'public/js/underscore.string.min.js',
				'deps'    => array( 'jquery', 'underscore' ),
				'version' => '3.3.4'
			),

			// Base
			'' => array( 'file' => WPMOLY_URL . 'public/js/wpmoly.js', 'deps' => array( 'jquery', 'underscore', 'backbone' ) ),

			// Utils
			'utils'                   => array( 'file' => WPMOLY_URL . 'public/js/wpmoly-utils.js' ),

			// Libraries
			'select2'                 => array( 'file' => WPMOLY_URL . 'admin/js/select2.min.js' ),
			'jquery-actual'           => array( 'file' => WPMOLY_URL . 'admin/js/jquery.actual.min.js' ),

			// Models
			'settings-model'          => array( 'file' => WPMOLY_URL . 'admin/js/models/settings.js' ),
			'status-model'            => array( 'file' => WPMOLY_URL . 'admin/js/models/status.js' ),
			'results-model'           => array( 'file' => WPMOLY_URL . 'admin/js/models/results.js' ),
			'search-model'            => array( 'file' => WPMOLY_URL . 'admin/js/models/search.js' ),
			'meta-model'              => array( 'file' => WPMOLY_URL . 'admin/js/models/meta.js' ),
			'modal-model'             => array( 'file' => WPMOLY_URL . 'admin/js/models/modal/modal.js' ),
			'image-model'             => array( 'file' => WPMOLY_URL . 'admin/js/models/image.js' ),
			'images-model'            => array( 'file' => WPMOLY_URL . 'admin/js/models/images.js' ),
			'grid-builder-model'      => array( 'file' => WPMOLY_URL . 'admin/js/models/grid-builder.js' ),

			// Controllers
			'search-controller'       => array( 'file' => WPMOLY_URL . 'admin/js/controllers/search.js' ),
			'editor-controller'       => array( 'file' => WPMOLY_URL . 'admin/js/controllers/editor.js' ),
			'modal-controller'        => array( 'file' => WPMOLY_URL . 'admin/js/controllers/modal.js' ),
			'grid-builder-controller' => array( 'file' => WPMOLY_URL . 'admin/js/controllers/grid-builder.js' ),

			// Views
			'frame-view'              => array( 'file' => WPMOLY_URL . 'public/js/views/frame.js' ),
			'confirm-view'            => array( 'file' => WPMOLY_URL . 'public/js/views/confirm.js' ),
			'permalinks-view'         => array( 'file' => WPMOLY_URL . 'admin/js/views/permalinks.js' ),
			'metabox-view'            => array( 'file' => WPMOLY_URL . 'admin/js/views/metabox.js' ),
			'search-view'             => array( 'file' => WPMOLY_URL . 'admin/js/views/search/search.js' ),
			'search-history-view'     => array( 'file' => WPMOLY_URL . 'admin/js/views/search/history.js' ),
			'search-settings-view'    => array( 'file' => WPMOLY_URL . 'admin/js/views/search/settings.js' ),
			'search-status-view'      => array( 'file' => WPMOLY_URL . 'admin/js/views/search/status.js' ),
			'search-results-view'     => array( 'file' => WPMOLY_URL . 'admin/js/views/search/results.js' ),
			'editor-image-view'       => array( 'file' => WPMOLY_URL . 'admin/js/views/editor/image.js' ),
			'editor-images-view'      => array( 'file' => WPMOLY_URL . 'admin/js/views/editor/images.js' ),
			'editor-meta-view'        => array( 'file' => WPMOLY_URL . 'admin/js/views/editor/meta.js' ),
			'editor-details-view'     => array( 'file' => WPMOLY_URL . 'admin/js/views/editor/details.js' ),
			'editor-tagbox-view'      => array( 'file' => WPMOLY_URL . 'admin/js/views/editor/tagbox.js' ),
			'editor-view'             => array( 'file' => WPMOLY_URL . 'admin/js/views/editor/editor.js' ),
			'modal-view'              => array( 'file' => WPMOLY_URL . 'admin/js/views/modal/modal.js' ),
			'modal-images-view'       => array( 'file' => WPMOLY_URL . 'admin/js/views/modal/images.js' ),
			'modal-browser-view'      => array( 'file' => WPMOLY_URL . 'admin/js/views/modal/browser.js' ),
			'modal-post-view'         => array( 'file' => WPMOLY_URL . 'admin/js/views/modal/post.js' ),
			'grid-builder-view'       => array( 'file' => WPMOLY_URL . 'admin/js/views/grid/builder.js' ),
			'grid-type-view'          => array( 'file' => WPMOLY_URL . 'admin/js/views/grid/type.js' ),

			// Runners
			'api'                     => array( 'file' => WPMOLY_URL . 'admin/js/wpmoly-api.js' ),
			'metabox'                 => array( 'file' => WPMOLY_URL . 'admin/js/wpmoly-metabox.js' ),
			'permalinks'              => array( 'file' => WPMOLY_URL . 'admin/js/wpmoly-permalinks.js' ),
			'editor'                  => array( 'file' => WPMOLY_URL . 'admin/js/wpmoly-editor.js' ),
			'grid-builder'            => array( 'file' => WPMOLY_URL . 'admin/js/wpmoly-grid-builder.js' ),
			'search'                  => array( 'file' => WPMOLY_URL . 'admin/js/wpmoly-search.js' ),
			'tester'                  => array( 'file' => WPMOLY_URL . 'admin/js/wpmoly-tester.js' ),
		);

		/**
		 * Filter the default scripts to register.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $scripts
		 */
		$this->scripts = apply_filters( 'wpmoly/filter/default/admin/scripts', $scripts );
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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	private function register_styles() {

		foreach ( $this->styles as $id => $style ) {

			if ( ! empty( $id ) ) {
				$id = '-' . $id;
			}
			$id = WPMOLY_SLUG . $id;

			$style = wp_parse_args( $style, array(
				'file'    => '',
				'deps'    => array(),
				'version' => WPMOLY_VERSION,
				'media'   => 'all'
			) );

			wp_register_style( $id, $style['file'], $style['deps'], $style['version'], $style['media'] );
		}
	}

	/**
	 * Enqueue a specific style.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $id Script ID.
	 * 
	 * @return   void
	 */
	private function enqueue_style( $id = '' ) {

		if ( ! empty( $id ) ) {
			$id = '-' . $id;
		}
		$id = WPMOLY_SLUG . $id;

		wp_enqueue_style( $id );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	private function register_scripts() {

		foreach ( $this->scripts as $id => $script ) {

			if ( ! empty( $id ) ) {
				$id = '-' . $id;
			}
			$id = WPMOLY_SLUG . $id;

			$script = wp_parse_args( $script, array(
				'file'    => '',
				'deps'    => array(),
				'version' => WPMOLY_VERSION,
				'footer'  => true
			) );

			wp_register_script( $id, $script['file'], $script['deps'], $script['version'], $script['footer'] );
		}
	}

	/**
	 * Enqueue a specific script.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $id Style ID.
	 * 
	 * @return   void
	 */
	private function enqueue_script( $id = '' ) {

		if ( ! empty( $id ) ) {
			$id = '-' . $id;
		}
		$id = WPMOLY_SLUG . $id;

		wp_enqueue_script( $id );
	}

	/**
	 * Enqueue the stylesheets for the admin area.
	 *
	 * @since    3.0
	 * 
	 * @param    string    $hook_suffix
	 *
	 * @return   null
	 */
	public function enqueue_styles( $hook_suffix ) {

		$this->register_styles();

		$this->enqueue_style();
		$this->enqueue_style( 'font' );
		$this->enqueue_style( 'common' );
		$this->enqueue_style( 'grids' );
		$this->enqueue_style( 'select2' );

		if ( 'options-permalink.php' == $hook_suffix ) {
			$this->enqueue_style( 'metabox' );
		}

		if ( ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) && 'grid' == get_post_type() ) {
			$this->enqueue_style( 'grid-builder' );
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

		if ( 'options-permalink.php' == $hook_suffix ) {

			// Vendor
			$this->enqueue_script( 'sprintf' );
			$this->enqueue_script( 'underscore-string' );

			// Base
			$this->enqueue_script();

			// Metabox
			$this->enqueue_script( 'metabox-view' );
			$this->enqueue_script( 'metabox' );

			// Permalinks
			$this->enqueue_script( 'permalinks-view' );
			$this->enqueue_script( 'permalinks' );
		}

		if ( ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) && 'movie' == get_post_type() ) {

			// Vendor
			$this->enqueue_script( 'sprintf' );
			$this->enqueue_script( 'underscore-string' );

			// Base
			$this->enqueue_script();
			$this->enqueue_script( 'utils' );

			// Models
			$this->enqueue_script( 'settings-model' );
			$this->enqueue_script( 'status-model' );
			$this->enqueue_script( 'results-model' );
			$this->enqueue_script( 'search-model' );
			$this->enqueue_script( 'meta-model' );
			$this->enqueue_script( 'modal-model' );
			$this->enqueue_script( 'image-model' );
			$this->enqueue_script( 'admin-image-model' );
			$this->enqueue_script( 'images-model' );

			// Controllers
			$this->enqueue_script( 'search-controller' );
			$this->enqueue_script( 'editor-controller' );
			$this->enqueue_script( 'modal-controller' );

			// Views
			$this->enqueue_script( 'frame-view' );
			$this->enqueue_script( 'confirm-view' );
			$this->enqueue_script( 'metabox-view' );
			$this->enqueue_script( 'search-view' );
			$this->enqueue_script( 'search-history-view' );
			$this->enqueue_script( 'search-settings-view' );
			$this->enqueue_script( 'search-status-view' );
			$this->enqueue_script( 'search-results-view' );
			$this->enqueue_script( 'editor-image-view' );
			$this->enqueue_script( 'editor-images-view' );
			$this->enqueue_script( 'editor-meta-view' );
			$this->enqueue_script( 'editor-details-view' );
			$this->enqueue_script( 'editor-tagbox-view' );
			$this->enqueue_script( 'editor-view' );
			$this->enqueue_script( 'modal-view' );
			$this->enqueue_script( 'modal-images-view' );
			$this->enqueue_script( 'modal-browser-view' );
			$this->enqueue_script( 'modal-post-view' );

			// Runners
			$this->enqueue_script( 'api' );
			$this->enqueue_script( 'metabox' );
			$this->enqueue_script( 'editor' );
			$this->enqueue_script( 'search' );
			$this->enqueue_script( 'tester' );

			// Libraries
			$this->enqueue_script( 'select2' );
			$this->enqueue_script( 'jquery-actual' );
		}

		if ( ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) && 'grid' == get_post_type() ) {

			// Vendor
			$this->enqueue_script( 'sprintf' );
			$this->enqueue_script( 'underscore-string' );
			$this->enqueue_script( 'wp-backbone' );

			// Base
			$this->enqueue_script();
			$this->enqueue_script( 'utils' );

			// Libraries
			$this->enqueue_script( 'select2' );

			// Models
			$this->enqueue_script( 'grid-builder-model' );

			// Controllers
			$this->enqueue_script( 'grid-builder-controller' );

			// Views
			$this->enqueue_script( 'grid-builder-view' );
			$this->enqueue_script( 'grid-type-view' );

			// Runners
			$this->enqueue_script( 'grid-builder' );
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
