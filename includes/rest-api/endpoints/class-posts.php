<?php
/**
 * REST API:Posts Controller class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\rest\endpoints;

use wpmoly\utils;
use WP_Error;
use WP_REST_Server;
use WP_REST_Posts_Controller;

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
abstract class Posts extends WP_REST_Posts_Controller {

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

		register_rest_route( $this->namespace, '/' . $this->post_type . '/(?P<id>[\d]+)', array(
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
				'args'                => array(
					'context'  => $this->get_context_param( array( 'default' => 'view' ) ),
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

		$counts = (array) $this->count_items();
		$counts = array_map( 'intval', $counts );

		$response = rest_ensure_response( $counts );

		return $response;
	}

	/**
	 * Count number of posts of a post type and if user has permissions to view.
	 *
	 * This function is a copy of wp_count_posts() without caching support. In
	 * some cases the REST API request returns before cache is updated, resulting
	 * in inconsistant data being shown to the user.
	 *
	 * @see wp_count_posts()
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return object Number of posts for each status.
	 */
	private function count_items() {

		global $wpdb;

		if ( ! post_type_exists( $this->post_type ) ) {
			return new stdClass;
		}

		$query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s GROUP BY post_status";

		$results = (array) $wpdb->get_results( $wpdb->prepare( $query, $this->post_type ), ARRAY_A );
		$counts = array_fill_keys( get_post_stati(), 0 );

		foreach ( $results as $row ) {
			$counts[ $row['post_status'] ] = $row['num_posts'];
		}

		$counts = (object) $counts;

		$cache_key = _count_posts_cache_key( $this->post_type );
		wp_cache_set( $cache_key, $counts, 'counts' );

		/** This filter is documented in wp-includes/post.php */
		return apply_filters( 'wp_count_posts', $counts, $this->post_type );
	}

	/**
	 * Checks if a given request has access to create a post.
	 *
	 * Copy WP_REST_Posts_Controller::update_item_permissions_check() but ignore
	 * term assignment permission as we are not dealing with terms here.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {

		$post = $this->get_post( $request['id'] );
		if ( is_wp_error( $post ) ) {
			return $post;
		}

		$post_type = get_post_type_object( $this->post_type );

		if ( $post && ! $this->check_update_permission( $post ) ) {
			return new WP_Error( 'rest_cannot_edit', __( 'Sorry, you are not allowed to edit this post.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		if ( ! empty( $request['author'] ) && get_current_user_id() !== $request['author'] && ! current_user_can( $post_type->cap->edit_others_posts ) ) {
			return new WP_Error( 'rest_cannot_edit_others', __( 'Sorry, you are not allowed to update posts as this user.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		if ( ! empty( $request['sticky'] ) && ! current_user_can( $post_type->cap->edit_others_posts ) ) {
			return new WP_Error( 'rest_cannot_assign_sticky', __( 'Sorry, you are not allowed to make posts sticky.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Prepare movie for response.
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
		$fields = (array) $request['fields'];

		if ( ! empty( $schema['properties']['id'] ) ) {
			$data['id'] = (int) $post->ID;
		}

		$meta = $this->get_meta( $post->ID );
		if ( ! empty( $schema['properties'] ) ) {
			foreach ( $schema['properties'] as $name => $args ) {

				if ( ! $this->is_requested_field( $name, $fields ) ) {
					continue;
				}

				$default = isset( $args['default'] ) ? $args['default'] : null;
				$value = array_key_exists( $name, $meta ) ? $meta[ $name ] : $default;

				$data[ $name ] = $this->prepare_value_for_response( $value, $request, $args );
			}
		}

		if ( 'edit' === $request['context'] ) {
			$data['edit_link'] = admin_url( 'admin.php?page=wpmovielibrary-' . $this->rest_base . '&id=' . $post->ID . '&action=edit' );
			$data['old_edit_link'] = get_edit_post_link( $post->ID, false );
		}

		if ( 'view' === $request['context'] ) {
			foreach ( $data as $name => $value ) {
				if ( '' === $value || ! $value ) {
					$data[ $name ] = null;
				}
			}
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
	 * Retrieves the movie's schema, conforming to JSON Schema.
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

		$registered = utils\get_registered_meta( $this->post_type );
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

	/**
	 * Check if a specific field was requested.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param string $field  Field name.
	 * @param array  $fields Requested fields.
	 *
	 * @return boolean
	 */
	protected function is_requested_field( $field, $fields ) {

		if ( empty( $fields ) ) {
			return true;
		}

		return in_array( $field, $fields, true );
	}

	/**
	 * Retrieve complete post meta set.
	 *
	 * @since 3.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return array
	 */
	abstract public function get_meta( $post_id );
}
