<?php
/**
 * REST API:TMDb class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\rest\endpoints;

use WP_REST_Server;
use WP_REST_Controller;

/**
 * Core class to access TMDb API via the REST API.
 *
 * @see WP_REST_Controller
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class TMDb extends WP_REST_Controller {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct() {

		$this->namespace = 'tmdb/v3';
		$this->rest_base = 'schema';
	}

	/**
	 * Register routes.
	 *
	 * @since 3.0.0
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_schema' ),
				'permission_callback' => array( $this, 'get_schema_permissions_check' ),
				'args'                => array(
					array(
						'description'        => __( 'Scope under which the request is made; determines fields present in response.' ),
						'type'               => 'string',
						'sanitize_callback'  => 'sanitize_key',
						'validate_callback'  => 'rest_validate_request_arg',
					),
				),
			),
			'schema' => array( $this, 'get_schema' ),
		) );
	}

	public function get_schema_permissions_check( $request ) {

		return 'edit' === $request['context'] && current_user_can( 'edit_posts' );
	}

	public function get_schema( $request ) {

		$data = array(
			array(
				'route' => 'movie/{id}',
				'parameters' => array(
					'id' => array(
						'type'     => 'string',
						'pattern'  => '/^tt[\d+]|[\d+]$/',
						'required' => true,
					),
				),
				'options' => array(
					'api_key' => array(
						'type'     => 'string',
						'required' => false,
					),
					'language' => array(
						'type'     => 'string',
						'pattern'  => '/^([a-z]{2})-([A-Z]{2})$/',
						'required' => false,
					),
					'append_to_response' => array(
						'type'     => 'string',
						'pattern'  => '/^(\w)+$/',
						'required' => false,
					),
				),
			),
			array(
				'route' => 'search/movie',
				'parameters' => array(),
				'options' => array(
					'api_key' => array(
						'type'     => 'string',
						'required' => false,
					),
					'query' => array(
						'type'     => 'string',
						'required' => true,
					),
					'language' => array(
						'type'     => 'string',
						'pattern'  => '/^([a-z]{2})-([A-Z]{2})$/',
						'required' => false,
					),
					'append_to_response' => array(
						'type'     => 'string',
						'pattern'  => '/^(\w)+$/',
						'required' => false,
					),
				),
			),
			array(
				'route' => 'person/{id}',
				'parameters' => array(
					'id' => array(
						'type'     => 'string',
						'required' => true,
					),
				),
				'options' => array(
					'api_key' => array(
						'type'     => 'string',
						'required' => false,
					),
					'language' => array(
						'type'     => 'string',
						'pattern'  => '/^([a-z]{2})-([A-Z]{2})$/',
						'required' => false,
					),
					'append_to_response' => array(
						'type'     => 'string',
						'pattern'  => '/^(\w)+$/',
						'required' => false,
					),
				),
			),
		);

		$schema = rest_ensure_response( $data );

		return $schema;
	}
}
