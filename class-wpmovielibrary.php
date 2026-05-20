<?php
/**
 * The file that defines the core plugin class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

/**
 * Define the main plugin class.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class WPMovieLibrary {

	/**
	 * Plugin instance.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var WPMovieLibrary
	 */
	private static $_instance = null;

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
	 * Front stage instance.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var Frontstage
	 */
	private ?Frontstage $frontstage = null;

	/**
	 * Back stage instance.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var Backstage
	 */
	private ?Backstage $backstage = null;

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
	 * @return WPMovieLibrary
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

		// Actions.
		add_action( 'plugins_loaded', [ &$this, 'run' ] );

		add_action( 'wpmovielibrary/run', [ &$this, 'rehearsal' ] );
		add_action( 'wpmovielibrary/run', [ &$this, 'background' ] );
		add_action( 'wpmovielibrary/run', [ &$this, 'foreground' ] );
	}

	/**
	 * Load features that should be available in both the admin and public
	 * areas of the plugin.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function rehearsal() {

		require_once WPMOLY_PATH . 'includes/helpers.php';

		require_once WPMOLY_PATH . 'includes/class-post-types.php';
		require_once WPMOLY_PATH . 'includes/class-post-statuses.php';
		require_once WPMOLY_PATH . 'includes/class-post-meta.php';
		require_once WPMOLY_PATH . 'includes/class-taxonomies.php';
		require_once WPMOLY_PATH . 'includes/class-term-meta.php';

		$post_types = Post_Types::get_instance();
		add_action( 'init', [ $post_types, 'register' ] );

		$post_statuses = Post_Statuses::get_instance();
		add_action( 'init', [ $post_statuses, 'register' ] );

		$post_meta = Post_Meta::get_instance();
		add_action( 'init', [ $post_meta, 'register' ] );

		$taxonomies = Taxonomies::get_instance();
		add_action( 'init', [ $taxonomies, 'register' ] );

		$term_meta = Term_Meta::get_instance();
		add_action( 'init', [ $term_meta, 'register' ] );
	}

	/**
	 * Load admin features.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function background() {

		if ( ! is_admin() ) {
			return false;
		}

		require_once WPMOLY_PATH . 'admin/class-backstage.php';

		$this->backstage = Backstage::get_instance();
	}

	/**
	 * Load public features.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function foreground() {

		require_once WPMOLY_PATH . 'public/class-frontstage.php';

		$this->frontstage = Frontstage::get_instance();
	}

	/**
	 * Handle final aspects of plugin setup, such as adding action hooks.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function run() {

		/**
		 * Let's get this party started.
		 *
		 * @since 6.0.0
		 *
		 * @param object &$this Plugin class instance, passed by reference.
		 */
		do_action_ref_array( 'wpmovielibrary/run', [ &$this ] );
	}

}
