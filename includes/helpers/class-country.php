<?php
/**
 * Define the country helper class.
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
 * Handle countries names translation, localization and flags.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/helpers
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Country {

	/**
	 * Country ISO 3166-1 Code.
	 * 
	 * @var    string
	 */
	public $code = '';

	/**
	 * Country standard name.
	 * 
	 * @var    string
	 */
	public $standard_name = '';

	/**
	 * Country translated name.
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
	 * ISO 3166-1 table.
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

		$this->supported = l10n::$supported_countries;
		$this->standard  = l10n::$standard_countries;
	}

	/**
	 * Match a country by its name or code.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $data
	 * 
	 * @return   void
	 */
	protected function match( $data ) {

		$data = (string) $data;

		if ( isset( $this->standard[ $data ] ) ) {
			$this->code = $data;
			$this->standard_name = $this->standard[ $data ];
			$this->localize();

			return $this;
		}

		$code = array_search( $data, $this->standard );
		if ( false !== $code ) {
			$this->code = $code;
			$this->standard_name = $data;
			$this->localize();
		}

		return $this;
	}

	/**
	 * Set the translated name of the country.
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
	 * Generate country's flag.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function flag() {

		$flag = sprintf( '<span class="flag flag-%s" title="%s"></span>', strtolower( $this->code ), sprintf( '%s (%s)', $this->localized_name, $this->standard_name ) );

		/**
		* Apply filter to the rendered country flag
		* 
		* @since    2.0
		* 
		* @param    string    $flag HTML markup
		* @param    string    $country Country
		*/
		return apply_filters( 'wpmoly/filter/country/flag/html', $flag, $this );
	}

	/**
	 * Get a country.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $country
	 * 
	 * @return   Country
	 */
	public static function get( $country ) {

		$country = trim( (string) $country );

		$instance = new static;
		$instance->match( $country );

		return $instance;
	}
}