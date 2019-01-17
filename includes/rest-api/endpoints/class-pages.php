<?php
/**
 * REST API Pages Controller class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\rest\endpoints;

use wpmoly\utils;
use wpmoly\rest\fields;
use WP_REST_Server;
use WP_REST_Posts_Controller;

/**
 * Core class to access pages via the REST API.
 *
 * @see WP_REST_Posts_Controller
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Pages extends WP_REST_Posts_Controller {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct( $post_type = 'page' ) {

		$this->post_type = $post_type;
		$this->namespace = 'wpmoly/v1';
		$this->rest_base = 'page';
	}

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

		register_rest_route( $this->namespace, '/pages', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/page/(?P<id>[\d]+)', array(
			'args' => array(
				'id' => array(
					'description' => __( 'Unique identifier for the object.' ),
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => $this->get_context_param( array( 'default' => 'view' ) ),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	/**
	 * Prepare page for response.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param WP_Post         $post    Post object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $post, $request ) {

		$data = array();

		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties'] ) ) {
			foreach ( $schema['properties'] as $name => $args ) {

				$meta_key = utils\page\prefix( $name );
				$meta_value = get_post_meta( $post->ID, $meta_key, true );

				$data[ $name ] = $this->prepare_value_for_response( $meta_value, $request, $args );
			}
		}

		if ( ! empty( $schema['properties']['id'] ) ) {
			$data['id'] = (int) $post->ID;
		}

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Prepare meta value for response.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param mixed           $value   Meta value to prepare.
	 * @param WP_REST_Request $request Current request object.
	 * @param array           $args    Options for the field.
	 *
	 * @return mixed Prepared value.
	 */
	protected function prepare_value_for_response( $value, $request, $args ) {

		if ( is_object( $value ) && 'object' !== $args['type'] ) {
			$value = get_object_vars();
		}

		if ( is_array( $value ) && 'array' !== $args['type'] ) {
			$value = ! empty( $value[0] ) ? $value[0] : null;
		}

		if ( empty( $value ) && ! empty( $args['default'] ) ) {
			$value = $args['default'];
		}

		if ( empty( $args['type'] ) ) {
			$value = null;
		} elseif ( 'string' === $args['type'] ) {
			$value = (string) $value;
		} elseif ( 'integer' === $args['type'] ) {
			$value = (int) $value;
		} elseif ( 'boolean' === $args['type'] ) {
			$value = (boolean) $value;
		} elseif ( 'array' === $args['type'] ) {
			$value = (array) $value;
		} elseif ( 'object' === $args['type'] ) {
			$value = (object) $value;
		}

		if ( ! empty( $args['prepare_callback'] ) && is_callable( $args['prepare_callback'] ) ) {
			$value = call_user_func_array( $args['prepare_callback'], array( $value, $request['context'] ) );
		}

		return $value;
	}

	/**
	 * Retrieves the grid's schema, conforming to JSON Schema.
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
			'title'      => $this->post_type,
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

		$registered = utils\get_registered_page_meta();
		foreach ( $registered as $slug => $args ) {

			$args['schema']['type']        = isset( $args['type'] ) ? $args['type'] : 'string';
			$args['schema']['description'] = isset( $args['description'] ) ? $args['description'] : '';
			$args['schema']['default']     = isset( $args['default'] ) ? $args['default'] : null;

			if ( ! empty( $args['validate_callback'] ) ) {
				$args['schema']['validate_callback'] = $args['validate_callback'];
			}

			if ( ! empty( $args['show_in_rest']['context'] ) ) {
				$args['schema']['context'] = $args['show_in_rest']['context'];
			}

			if ( ! empty( $args['show_in_rest']['prepare_callback'] ) ) {
				$args['schema']['prepare_callback'] = $args['show_in_rest']['prepare_callback'];
			}

			$schema['properties'][ $slug ] = $args['schema'];
		}

		return $schema;
	}
}
