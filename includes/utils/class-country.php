<?php
/**
 * Define the country helper class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\utils;

use wpmoly\core\L10n;

/**
 * Handle countries names translation, localization and flags.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Country {

	/**
	 * Country ISO 3166-1 Code.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var string
	 */
	public $code = '';

	/**
	 * Country standard name.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var string
	 */
	public $standard_name = '';

	/**
	 * Country translated name.
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
	 * ISO 3166-1 table.
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

		$this->supported = L10n::$supported_countries;
		$this->standard  = L10n::$standard_countries;
	}

	/**
	 * Match a country by its name or code.
	 *
	 * Perform a strict match to find languages by code and standard name,
	 * then try an approximative match with sanitized name.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param string $data
	 */
	protected function match( $data ) {

		$data = (string) $data;

		// Find country ISO code
		if ( isset( $this->standard[ $data ] ) ) {
			$this->code = $data;
			$this->standard_name = $this->standard[ $data ];
			$this->localize();

			return $this;
		}

		// Strict standard language name match
		$code = array_search( $data, $this->standard );
		if ( false !== $code ) {
			$this->code = $code;
			$this->standard_name = $data;
			$this->localize();

			return $this;
		}

		// Approximative standard country name match
		foreach ( $this->standard as $code => $standard ) {
			$country = sanitize_title_with_dashes( strtolower( $standard ) );
			$localized = sanitize_title_with_dashes( __( $standard, 'wpmovielibrary-iso' ) );
			if ( ! strcasecmp( $country, $data ) || ! strcasecmp( $localized, $data ) ) {
				$this->code = $code;
				$this->standard_name = $standard;
				$this->localize();

				return $this;
			}
		}

		return $this;
	}

	/**
	 * Set the translated name of the country.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
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
	 * Generate country's flag.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function flag() {

		$flag = sprintf( '<span class="flag flag-%1$s" title="%2$s"></span>', strtolower( $this->code ), sprintf( '%1$s (%2$s)', $this->localized_name, $this->standard_name ) );

		/**
		* Apply filter to the rendered country flag
		*
		* @since 3.0.0
		*
		* @param string $flag HTML markup
		* @param string $country Country
		*/
		return apply_filters( 'wpmoly/filter/country/flag/html', $flag, $this );
	}

	/**
	 * Get a country.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @param string $country
	 *
	 * @return Country
	 */
	public static function get( $country ) {

		$country = trim( (string) $country );

		$instance = new static;
		$instance->match( $country );

		return $instance;
	}

}
