<?php
/**
 * REST API:Terms_Controller Controller class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\rest\endpoints;

use WP_REST_Server;
use WP_REST_Terms_Controller;
use wpmoly\rest\fields;

/**
 * Core class to access terms via the REST API.
 *
 * Simplify WP_REST_Terms_Controller to handle term meta directly.
 *
 * @see WP_REST_Terms_Controller
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Terms_Controller extends WP_REST_Terms_Controller {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param string $taxonomy Taxonomy key.
	 */
	public function __construct( $taxonomy ) {

		$this->taxonomy = $taxonomy;
		$this->namespace = 'wp/v2';
		$tax_obj = get_taxonomy( $taxonomy );
		$this->rest_base = ! empty( $tax_obj->rest_base ) ? $tax_obj->rest_base : $tax_obj->name;

		$this->meta = new fields\Term_Meta_Fields( $this->taxonomy );
	}
}
