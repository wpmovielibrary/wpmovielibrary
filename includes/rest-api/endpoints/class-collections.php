<?php
/**
 * REST API:Collections Controller class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\rest\endpoints;

/**
 * Core class to access collections via the REST API.
 *
 * Simplify WP_REST_Terms_Controller to handle collection meta directly.
 *
 * @see WP_REST_Terms_Controller
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Collections extends Terms {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $taxonomy Taxonomy type.
	 */
	public function __construct( $taxonomy = 'collection' ) {

		$this->taxonomy    = $taxonomy;
		$this->namespace   = 'wpmoly/v1';
		$this->rest_base   = 'collections';
	}

}
