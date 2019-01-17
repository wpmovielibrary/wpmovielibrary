<?php
/**
 * REST API:Grids Controller class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\rest\endpoints;

use wpmoly\utils;

/**
 * Core class to access grids via the REST API.
 *
 * Simplify WP_REST_Posts_Controller to handle grid meta directly.
 *
 * @see WP_REST_Posts_Controller
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Grids extends Posts {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct( $post_type = 'grid' ) {

		$this->post_type = $post_type;
		$this->namespace = 'wpmoly/v1';
		$this->rest_base = 'grids';
	}

	/**
	 * Retrieve complete grid meta set.
	 *
	 * @since 3.0.0
	 *
	 * @param int $post_id Grid ID.
	 *
	 * @return array
	 */
	public function get_meta( $post_id ) {

		return utils\grid\get_meta( $post_id );
	}
}
