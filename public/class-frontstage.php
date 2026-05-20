<?php
/**
 * Define the public plugin class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

/**
 * Load the plugin's public features.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Frontstage {

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
	 * @var Frontstage
	 */
	private static $_instance = null;

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
	 * @return Frontstage
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

		add_action( 'wp_enqueue_scripts',  [ &$this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts',  [ &$this, 'enqueue_scripts' ] );
	}

	/**
	 * Load plugin's public requirements.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function load_dependencies() {

		//
	}

	/**
	 * Enqueue public-side styles.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function enqueue_styles() {

		$this->register_styles();
	}

	/**
	 * Enqueue public-side scripts.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function enqueue_scripts() {

		$this->register_scripts();
	}

	/**
	 * Register public-side styles.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function register_styles() {}

	/**
	 * Register public-side scripts.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function register_scripts() {}
}
