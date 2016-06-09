<?php
/**
 * Define the options class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */

namespace wpmoly;

use wpmoly\Core\Core;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Options extends Core {

	/**
	 * Options slug.
	 * 
	 * @since    3.0
	 * 
	 * @var      string
	 */
	protected $options_name = 'wpmoly_settings';

	/**
	 * Options array.
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	private $options;

	/**
	 * Builtin restricted list for API support
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	protected $supported_languages = array();

	/**
	 * Builtin restricted list for API support
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	protected $supported_countries = array();

	/**
	 * Builtin, non-exhaustive iso_639_1 matching array for translation
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	protected $languages = array();

	/**
	 * Builtin iso_3166_1 matching array for translation
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	protected $countries = array();

	/**
	 * Allowed Movie metadata
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	protected $default_meta = array();

	/**
	 * Allowed Movie details
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	protected $default_details = array();

	/**
	 * ReduxFramework instance.
	 * 
	 * @since    3.0
	 * 
	 * @var      ReduxFramework
	 */
	public $redux;

	/**
	 * Initialize the instance.
	 * 
	 * @since    3.0
	 */
	public function __construct() {

		// Load config files
		$this->load();

		$this->options = $this->redux->options;
	}

	/**
	 * Load all required configuration files.
	 * 
	 * @since    3.0
	 */
	private function load() {

		require_once WPMOLY_PATH . 'includes/config/wpmoly-languages.php';
		require_once WPMOLY_PATH . 'includes/config/wpmoly-options.php';
		require_once WPMOLY_PATH . 'includes/config/wpmoly-movies.php';
		require_once WPMOLY_PATH . 'includes/config/wpmoly-admin-bar-menu.php';

		if ( is_admin() ) {
			require_once WPMOLY_PATH . 'includes/config/wpmoly-admin-menu.php';
			require_once WPMOLY_PATH . 'includes/config/wpmoly-admin-dashboard.php';
		}

		require_once WPMOLY_PATH . 'vendor/redux/framework.php';

		$this->redux = new \ReduxFramework( $redux_sections, $redux_args );

		// Set important defaults values
		$defaults = compact( 'languages', 'supported_languages', 'default_meta', 'default_details' );
		$this->set_defaults( $defaults );
	}

	/**
	 * Set a number of important, plugin-wide default data. Apply a filter
	 * on each default set of data.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $defaults Default data
	 * 
	 * @return   null
	 */
	private function set_defaults( $defaults ) {

		/**
		 * Filter the default languages list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $languages
		 */
		$this->languages = apply_filters( 'wpmoly/filter/options/languages', $defaults['languages'] );

		/**
		 * Filter the default supported languages list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $supported_languages
		 */
		$this->supported_languages = apply_filters( 'wpmoly/filter/options/supported_languages', $defaults['supported_languages'] );

		/**
		 * Filter the default movie meta list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_meta
		 */
		$this->default_meta = apply_filters( 'wpmoly/filter/movie/default/meta', $defaults['default_meta'] );

		/**
		 * Filter the default movie details list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_details
		 */
		$this->default_details = apply_filters( 'wpmoly/filter/movie/default/details', $defaults['default_details'] );
	}

	/**
	 * Retrieve a specific option.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name Option name
	 * @param    mixed     $default Option default value to return if needed
	 * 
	 * @return   mixed
	 */
	public function get( $name, $default = null ) {

		if ( in_array( $name, array( 'languages', 'supported_languages', 'default_meta', 'default_details' ) ) ) {
			return $this->$name;
		}

		$key = "wpmoly-" . sanitize_key( $name );
		if ( isset( $this->options[ $key ] ) ) {
			return $this->options[ $key ];
		}

		return $default;
	}

	/**
	 * Set a new value for a specific option.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name Option name
	 * @param    mixed     $value Option value
	 * 
	 * @return   void
	 */
	public function set( $name, $value ) {

		$key = "wpmoly-" . sanitize_key( $name );
		if ( isset( $this->options[ $key ] ) ) {
			$this->redux->set( $key, $value );
		}
	}

	/**
	 * Check if a specific option exists.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name Option name
	 * 
	 * @return   boolean
	 */
	public function __isset( $name ) {

		if ( in_array( $name, array( 'languages', 'supported_languages', 'default_meta', 'default_details' ) ) ) {
			return true;
		}

		$key = "wpmoly-" . sanitize_key( $name );

		return isset( $this->options[ $key ] );
	}

}
