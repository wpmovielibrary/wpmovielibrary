<?php
/**
 * REST API:Terms Controller class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\rest\endpoints;

use wpmoly\utils;
use wpmoly\rest\fields;
use WP_Term_Query;
use WP_REST_Server;
use WP_REST_Terms_Controller;

/**
 * Core class to access terms via the REST API.
 *
 * Simplify WP_REST_Terms_Controller to handle term meta directly.
 *
 * @see WP_REST_Posts_Controller
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
abstract class Terms extends WP_REST_Terms_Controller {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $taxonomy Taxonomy type.
	 */
	public function __construct( $taxonomy ) {}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/count' , array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items_count' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => array(
					'context'  => $this->get_context_param( array( 'default' => 'edit' ) ),
				),
			),
		) );

		/*register_rest_route( $this->namespace, '/' . $this->taxonomy, array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );*/

		register_rest_route( $this->namespace, '/' . $this->taxonomy . '/(?P<id>[\d]+)', array(
			'args' => array(
				'id' => array(
					'description' => __( 'Unique identifier for the term.' ),
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

	}

	/**
	 * Count number of posts.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items_count( $request ) {

		$count = (array) $this->count_items();
		$count = array_map( 'intval', $count );

		$response = rest_ensure_response( $count );

		return $response;
	}

	/**
	 * Count number of posts of a post type and if user has permissions to view.
	 *
	 * This function is a copy of get_terms() with minimal parameters.
	 *
	 * @see wp_count_terms()
	 * @see get_terms()
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @return object Number of posts for each status.
	 */
	private function count_items() {

		$term_query = new WP_Term_Query();

		$terms = $term_query->query( array(
			'taxonomy'   => $this->taxonomy,
			'fields'     => 'count',
			'hide_empty' => false,
		) );

		return $terms;
	}

	/**
	 * Retrieves the term's schema, conforming to JSON Schema.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->taxonomy,
			'type'       => 'object',
			'properties' => array(
				'id' => array(
					'type'        => 'integer',
					'description' => __( 'Unique identifier for the object.' ),
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
			),
		);
	}

}
