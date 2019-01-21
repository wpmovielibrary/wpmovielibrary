<?php
/**
 * REST API:Persons Controller class.
 *
 * @link https://wppersonlibrary.com
 * @since 3.0.0
 *
 * @package wpPersonLibrary
 */

namespace wpmoly\rest\endpoints;

use wpmoly\utils;
use WP_Error;
use WP_REST_Server;

/**
 * Core class to access persons via the REST API.
 *
 * Simplify WP_REST_Posts_Controller to handle person meta directly.
 *
 * @see WP_REST_Posts_Controller
 *
 * @since 3.0.0
 * @package wpPersonLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Persons extends Posts {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct( $post_type = 'person' ) {

		$this->post_type = $post_type;
		$this->namespace = 'wpmoly/v1';
		$this->rest_base = 'persons';
	}

	/**
	 * Prepare person for response.
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

		$response = parent::prepare_item_for_response( $post, $request );

		$response->data['id'] = (int) $post->ID;

		$schema = $this->get_item_schema();
		$fields = (array) $request['fields'];

		/*if ( ! empty( $schema['properties']['year'] ) && $this->is_requested_field( 'year', $fields ) ) {
			$year = utils\person\get_meta( $post->ID, 'release_date' );
			if ( ! empty( $year ) ) {
				$year = date( 'Y', strtotime( $year ) );
			}
			$response->data['year'] = $year;
		}*/

		if ( ! empty( $schema['properties']['permalink'] ) ) {
			$response->data['permalink'] = esc_url_raw( get_permalink( $post->ID ) );
		}

		if ( ! empty( $schema['properties']['picture'] ) ) {
			$response->data['picture'] = $this->get_picture( $post, $request );
		}

		if ( ! empty( $schema['properties']['pictures'] ) && $this->is_requested_field( 'pictures', $fields ) ) {
			$response->data['pictures'] = $this->get_pictures( $post, $request );
		}

		if ( ! empty( $schema['properties']['backdrop'] ) && $this->is_requested_field( 'backdrop', $fields ) ) {
			$response->data['backdrop'] = $this->get_backdrop( $post, $request );
		}

		if ( ! empty( $schema['properties']['backdrops'] ) && $this->is_requested_field( 'backdrops', $fields ) ) {
			$response->data['backdrops'] = $this->get_backdrops( $post, $request );
		}

		if ( 'view' === $request['context'] ) {
			foreach ( $response->data as $name => $value ) {
				if ( '' === $value || ! $value ) {
					$response->data[ $name ] = null;
				}
			}
		}

		// Wrap the data in a response object.
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Retrieves the person's schema, conforming to JSON Schema.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {

		$schema = parent::get_item_schema();

		$meta = utils\get_registered_person_meta();

		// Some meta should not be visible, although they may be editable.
		$protected = wp_filter_object_list( $meta, array( 'protected' => true ) );
		foreach ( $protected as $key => $value ) {
			if ( isset( $schema['properties'][ $key ] ) ) {
				unset( $schema['properties'][ $key ] );
			}
		}

		/*$schema['properties']['year'] = array(
			'type'        => 'integer',
			'description' => __( 'The Person release year.', 'wppersonlibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);*/

		$schema['properties']['picture'] = array(
			'type'        => 'object',
			'description' => __( 'The Person Picture object.', 'wppersonlibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);

		$schema['properties']['pictures'] = array(
			'type'        => 'array',
			'description' => __( 'The Person Pictures collection.', 'wppersonlibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);

		$schema['properties']['backdrop'] = array(
			'type'        => 'object',
			'description' => __( 'The Person Backdrop object.', 'wppersonlibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);

		$schema['properties']['backdrops'] = array(
			'type'        => 'array',
			'description' => __( 'The Person Backdrops collection.', 'wppersonlibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);

		$schema['properties']['permalink'] = array(
			'type'        => 'string',
			'format'      => 'uri',
			'description' => __( 'The Person permalink.', 'wppersonlibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);

		return $schema;
	}

	/**
	 * Add person picture to the data response.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param array           $post    Post object.
	 * @param WP_REST_Request $request Current REST Request.
	 *
	 * @return Image
	 */
	protected function get_picture( $post, $request ) {

		$person = utils\person\get( $post );

		return $person->get_picture();
	}

	/**
	 * Add person pictures list to the data response.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param array           $post    Post object.
	 * @param WP_REST_Request $request Current REST Request.
	 *
	 * @return Nodes
	 */
	protected function get_pictures( $post, $request ) {

		$person = utils\person\get( $post );
		$pictures = $person->get_pictures();

		return (array) $pictures->items;
	}

	/**
	 * Add person backdrop to the data response.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param array           $post    Post object.
	 * @param WP_REST_Request $request Current REST Request.
	 *
	 * @return Image
	 */
	protected function get_backdrop( $post, $request ) {

		$person = utils\person\get( $post );

		return $person->get_backdrop();
	}

	/**
	 * Add person backdrops list to the data response.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param array           $post    Post object.
	 * @param WP_REST_Request $request Current REST Request.
	 *
	 * @return Nodes
	 */
	protected function get_backdrops( $post, $request ) {

		$person = utils\person\get( $post );
		$backdrops = $person->get_backdrops();

		return (array) $backdrops->items;
	}

	/**
	 * Retrieve complete person meta set.
	 *
	 * @since 3.0.0
	 *
	 * @param int $post_id Person ID.
	 *
	 * @return array
	 */
	public function get_meta( $post_id ) {

		return utils\person\get_meta( $post_id );
	}
}
