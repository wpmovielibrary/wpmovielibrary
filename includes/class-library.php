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
class Library {

	/**
	 * Plugin instance.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Library
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
	 * Settings instance.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var Settings
	 */
	private ?Settings $settings = null;

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
	 * @return Library
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
		add_action( 'plugins_loaded', [ $this, 'run' ] );

		add_action( 'wpmovielibrary/run', [ $this, 'rehearsal' ] );
		add_action( 'wpmovielibrary/run', [ $this, 'background' ] );
		add_action( 'wpmovielibrary/run', [ $this, 'foreground' ] );
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

		require_once WPMOLY_PATH . 'includes/support/helpers.php';

		require_once WPMOLY_PATH . 'includes/registrars/class-post-types.php';
		require_once WPMOLY_PATH . 'includes/registrars/class-post-statuses.php';
		require_once WPMOLY_PATH . 'includes/registrars/class-post-meta.php';
		require_once WPMOLY_PATH . 'includes/registrars/class-taxonomies.php';
		require_once WPMOLY_PATH . 'includes/registrars/class-term-meta.php';

		$registrars = [
			Registrars\Post_Types::class,
			Registrars\Post_Statuses::class,
			Registrars\Post_Meta::class,
			Registrars\Taxonomies::class,
			Registrars\Term_Meta::class,
		];

		foreach ( $registrars as $registrar ) {
			$registrar::get_instance();
		}

		// Register the dashboard taxonomy statistics cache invalidation. Done
		// here — always, not just in the admin — so movie saves and term changes
		// performed over the REST API (block editor, importer) bust the cache
		// too. The callbacks are static, so the page class is never instantiated
		// just to wire the hooks.
		require_once WPMOLY_PATH . 'admin/traits/trait-renderable.php';
		require_once WPMOLY_PATH . 'admin/pages/class-dashboard.php';

		Admin\Dashboard::register_cache_invalidation();
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
		require_once WPMOLY_PATH . 'includes/class-settings.php';

		$this->backstage = Backstage::get_instance();
		$this->settings  = Settings::get_instance();
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
		 * @param object $this Plugin class instance, passed by reference.
		 */
		do_action_ref_array( 'wpmovielibrary/run', [ $this ] );
	}

}
