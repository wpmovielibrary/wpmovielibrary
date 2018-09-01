<?php
/**
 * Define the language helper class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\helpers;

use wpmoly\core\L10n;

/**
 * Handle languages translation, localization and flags.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Language {

	/**
	 * Language ISO 639-1 Code.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var string
	 */
	public $code = '';

	/**
	 * Language native name.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var string
	 */
	public $native_name = '';

	/**
	 * Language standard name.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var string
	 */
	public $standard_name = '';

	/**
	 * Language translated name.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var string
	 */
	public $localized_name = '';

	/**
	 * Restricted list for API support.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $supported = array();

	/**
	 * ISO 639-1 table of native languages names.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $native = array();

	/**
	 * ISO 639-1 table of standard languages names.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $standard = array();

	/**
	 * Initialize the instance.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		$this->supported = L10n::$supported_languages;
		$this->native    = L10n::$native_languages;
		$this->standard  = L10n::$standard_languages;
	}

	/**
	 * Match a language by its name or code.
	 *
	 * Perform a strict match to find languages by code, standard and
	 * native names, then try an approximative match with sanitized name.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param string $data
	 *
	 * @return Language
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

		// Strict native language name match
		$code = array_search( $data, $this->native );
		if ( false !== $code ) {
			$this->code = $code;
			$this->native_name   = $data;
			$this->standard_name = $this->standard[ $code ];
			$this->localize();

			return $this;
		}

		// Strict standard language name match
		$code = array_search( $data, $this->standard );
		if ( false !== $code ) {
			$this->code = $code;
			$this->native_name   = $this->standard[ $code ];
			$this->standard_name = $data;
			$this->localize();

			return $this;
		}

		// Approximative native language name match
		foreach ( $this->native as $code => $native ) {
			$language = sanitize_title_with_dashes( $native );
			if ( ! strcasecmp( $language, $data ) ) {
				$this->code = $code;
				$this->native_name   = $native;
				$this->standard_name = $this->standard[ $code ];
				$this->localize();

				return $this;
			}
		}

		// Approximative standard language name match
		foreach ( $this->standard as $code => $standard ) {
			$language = sanitize_title_with_dashes( strtolower( $standard ) );
			$localized = sanitize_title_with_dashes( __( $standard, 'wpmovielibrary-iso' ) );
			if ( ! strcasecmp( $language, $data ) || ! strcasecmp( $localized, $data ) ) {
				$this->code = $code;
				$this->native_name   = $this->native[ $code ];
				$this->standard_name = $standard;
				$this->localize();

				return $this;
			}
		}

		return $this;
	}

	/**
	 * Set the translated name of the language.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected function localize() {

		$localized_name = '';
		if ( ! empty( $this->code ) && isset( $this->standard[ $this->code ] ) ) {
			$localized_name = __( $this->standard[ $this->code ], 'wpmovielibrary-iso' );
		}

		$this->localized_name = $localized_name;

		return $this->localized_name;
	}

	/**
	 * Get a language.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $language
	 *
	 * @return Language
	 */
	public static function get( $language ) {

		$language = trim( (string) $language );

		$instance = new static;
		$instance->match( $language );

		return $instance;
	}

}
