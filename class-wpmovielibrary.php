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
	 * @access protected
	 *
	 * @var string
	 */
	protected $version = WPMOLY_VERSION;

	/**
	 * Front stage instance.
	 *
	 * @since 6.0.0
	 *
	 * @access protected
	 *
	 * @var Frontstage
	 */
	protected $frontstage = null;

	/**
	 * Back stage instance.
	 *
	 * @since 6.0.0
	 *
	 * @access protected
	 *
	 * @var Backstage
	 */
	protected $backstage = null;

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
			self::$_instance = new static;
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

		add_action( 'wpmovielibrary/run', [ &$this, 'load_front_stage' ] );
		add_action( 'wpmovielibrary/run', [ &$this, 'load_back_stage' ] );

		// Activation hook.
		register_activation_hook( WPMOLY_PATH, [ &$this, 'plugin_activate' ] );
	}

	/**
	 * Load public features.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function load_front_stage() {

		require_once WPMOLY_PATH . 'public/class-frontstage.php';

		$this->frontstage = Frontstage::get_instance();
	}

	/**
	 * Load admin features.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function load_back_stage() {

		if ( ! is_admin() ) {
			return false;
		}

		require_once WPMOLY_PATH . 'admin/class-backstage.php';

		$this->backstage = Backstage::get_instance();
	}

	/**
	 * Handle inital installation and upgrading of the plugin.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function plugin_activate() {

		$db_version = get_option( 'WPMOLY_version' );
		if ( version_compare( $db_version, $this->version ) ) {
			// Save new version.
			update_option( 'WPMOLY_version', $this->version );
		}
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
