<?php
/**
 * REST API:Posts_Controller Controller class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\rest\endpoints;

use WP_REST_Server;
use WP_REST_Posts_Controller;
use wpmoly\rest\fields;

/**
 * Core class to access posts via the REST API.
 *
 * Simplify WP_REST_Posts_Controller to handle post meta directly.
 *
 * @see WP_REST_Posts_Controller
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Posts_Controller extends WP_REST_Posts_Controller {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct( $post_type ) {

		$this->post_type = $post_type;
		$this->namespace = 'wp/v2';
		$obj = get_post_type_object( $post_type );
		$this->rest_base = ! empty( $obj->rest_base ) ? $obj->rest_base : $obj->name;

		$this->meta = new fields\Post_Meta_Fields( $this->post_type );
	}
}
