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

namespace wpmoly\Core;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Options {

	/**
	 * Singleton.
	 *
	 * @var    Options
	 */
	private static $instance = null;

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
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   Options
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Load all required configuration files and frameworks.
	 * 
	 * @since    3.0
	 */
	private function load() {

		require_once WPMOLY_PATH . 'includes/config/wpmoly-options.php';
		require_once WPMOLY_PATH . 'includes/config/wpmoly-movies.php';
		require_once WPMOLY_PATH . 'vendor/redux/framework.php';

		// Load ReduxFramework
		$this->redux = new \ReduxFramework( $redux_sections, $redux_args );

		// Set important defaults values
		$this->set_defaults( array(
			'countries'           => l10n::$standard_countries,
			'supported_countries' => l10n::$supported_countries,
			'languages'           => l10n::$standard_languages,
			'supported_languages' => l10n::$supported_languages,
			'default_meta'        => $default_meta,
			'default_details'     => $default_details
		) );
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
		 * Filter the default countries list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $countries
		 */
		$this->countries = apply_filters( 'wpmoly/filter/options/countries/stantard', $defaults['countries'] );

		/**
		 * Filter the default supported countries list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $supported_countries
		 */
		$this->supported_countries = apply_filters( 'wpmoly/filter/options/countries/supported', $defaults['supported_countries'] );

		/**
		 * Filter the default languages list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $languages
		 */
		$this->languages = apply_filters( 'wpmoly/filter/options/languages/stantard', $defaults['languages'] );

		/**
		 * Filter the default supported languages list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $supported_languages
		 */
		$this->supported_languages = apply_filters( 'wpmoly/filter/options/languages/supported', $defaults['supported_languages'] );

		/**
		 * Filter the default movie meta list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_meta
		 */
		$this->default_meta = apply_filters( 'wpmoly/filter/options/movie/meta', $defaults['default_meta'] );

		/**
		 * Filter the default movie details list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_details
		 */
		$this->default_details = apply_filters( 'wpmoly/filter/options/movie/details', $defaults['default_details'] );
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

		if ( in_array( $name, array( 'countries', 'supported_countries', 'languages', 'supported_languages', 'default_meta', 'default_details' ) ) ) {
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
