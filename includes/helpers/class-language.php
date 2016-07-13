<?php
/**
 * Define the language helper class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/helpers
 */

namespace wpmoly\Helpers;

use wpmoly\Core\l10n;

/**
 * Handle languages translation, localization and flags.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/helpers
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Language {

	/**
	 * Language ISO 639-1 Code.
	 * 
	 * @var    string
	 */
	public $code = '';

	/**
	 * Language native name.
	 * 
	 * @var    string
	 */
	public $native_name = '';

	/**
	 * Language standard name.
	 * 
	 * @var    string
	 */
	public $standard_name = '';

	/**
	 * Language translated name.
	 * 
	 * @var    string
	 */
	public $localized_name = '';

	/**
	 * Restricted list for API support
	 * 
	 * @var    array
	 */
	protected $supported = array();

	/**
	 * ISO 639-1 table of native languages names.
	 * 
	 * @var    array
	 */
	protected $native = array();

	/**
	 * ISO 639-1 table of standard languages names.
	 * 
	 * @var    array
	 */
	protected $standard = array();

	/**
	 * Initialize the instance.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function __construct() {

		$this->supported = l10n::$supported_languages;
		$this->native    = l10n::$native_languages;
		$this->standard  = l10n::$standard_languages;
	}

	/**
	 * Match a language by its name or code.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $data
	 * 
	 * @return   void
	 */
	protected function match( $data ) {

		$data = (string) $data;

		// Find language ISO code
		if ( isset( $this->standard[ $data ] ) ) {
			$this->code = $data;
			$this->native_name   = $this->native[ $data ];
			$this->standard_name = $this->standard[ $data ];
			$this->localize();

			return $this;
		}

		$code = array_search( $data, $this->native );
		if ( false !== $code ) {
			$this->code = $code;
			$this->native_name   = $data;
			$this->standard_name = $this->standard[ $code ];
			$this->localize();

			return $this;
		}

		$code = array_search( $data, $this->standard );
		if ( false !== $code ) {
			$this->code = $code;
			$this->native_name   = $this->standard[ $code ];
			$this->standard_name = $data;
			$this->localize();
		}

		return $this;
	}

	/**
	 * Set the translated name of the language.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function localize() {

		if ( empty( $this->code ) ) {
			return false;
		}

		if ( ! isset( $this->standard[ $this->code ] ) ) {
			return false;
		}

		$this->localized_name = __( $this->standard[ $this->code ], 'wpmovielibrary-iso' );
	}

	/**
	 * Get a language.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $language
	 * 
	 * @return   Language
	 */
	public static function get( $language ) {

		$language = trim( (string) $language );

		$instance = new static;
		$instance->match( $language );

		return $instance;
	}
}