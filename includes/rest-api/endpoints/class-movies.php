<?php
/**
 * REST API:Movies Controller class.
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

/**
 * Core class to access movies via the REST API.
 *
 * Simplify WP_REST_Posts_Controller to handle movie meta directly.
 *
 * @see WP_REST_Posts_Controller
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Movies extends Posts {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct( $post_type = 'movie' ) {

		$this->post_type = $post_type;
		$this->namespace = 'wpmoly/v1';
		$this->rest_base = 'movies';
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

		$response = parent::prepare_item_for_response( $post, $request );

		$response->data['id'] = (int) $post->ID;

		$schema = $this->get_item_schema();
		$fields = (array) $request['fields'];

		if ( ! empty( $schema['properties']['year'] ) && $this->is_requested_field( 'year', $fields ) ) {
			$year = utils\movie\get_meta( $post->ID, 'release_date' );
			if ( ! empty( $year ) ) {
				$year = date( 'Y', strtotime( $year ) );
			}
			$response->data['year'] = $year;
		}

		if ( ! empty( $schema['properties']['permalink'] ) ) {
			$response->data['permalink'] = esc_url_raw( get_permalink( $post->ID ) );
		}

		if ( ! empty( $schema['properties']['poster'] ) ) {
			$response->data['poster'] = $this->get_poster( $post, $request );
		}

		if ( ! empty( $schema['properties']['posters'] ) && $this->is_requested_field( 'posters', $fields ) ) {
			$response->data['posters'] = $this->get_posters( $post, $request );
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
	 * Retrieves the movie's schema, conforming to JSON Schema.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {

		$schema = parent::get_item_schema();

		$meta = utils\get_registered_movie_meta();

		// Some meta should not be visible, although they may be editable.
		$protected = wp_filter_object_list( $meta, array( 'protected' => true ) );
		foreach ( $protected as $key => $value ) {
			if ( isset( $schema['properties'][ $key ] ) ) {
				unset( $schema['properties'][ $key ] );
			}
		}

		$schema['properties']['year'] = array(
			'type'        => 'integer',
			'description' => __( 'The Movie release year.', 'wpmovielibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);

		$schema['properties']['poster'] = array(
			'type'        => 'object',
			'description' => __( 'The Movie Poster object.', 'wpmovielibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);

		$schema['properties']['posters'] = array(
			'type'        => 'array',
			'description' => __( 'The Movie Posters collection.', 'wpmovielibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);

		$schema['properties']['backdrop'] = array(
			'type'        => 'object',
			'description' => __( 'The Movie Backdrop object.', 'wpmovielibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);

		$schema['properties']['backdrops'] = array(
			'type'        => 'array',
			'description' => __( 'The Movie Backdrops collection.', 'wpmovielibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);

		$schema['properties']['permalink'] = array(
			'type'        => 'string',
			'format'      => 'uri',
			'description' => __( 'The Movie permalink.', 'wpmovielibrary' ),
			'context'     => array( 'view' ),
			'readonly'    => true,
		);

		return $schema;
	}

	/**
	 * Add movie poster to the data response.
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
	protected function get_poster( $post, $request ) {

		$movie = utils\movie\get( $post );

		return $movie->get_poster();
	}

	/**
	 * Add movie posters list to the data response.
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
	protected function get_posters( $post, $request ) {

		$movie = utils\movie\get( $post );
		$posters = $movie->get_posters();

		return (array) $posters->items;
	}

	/**
	 * Add movie backdrop to the data response.
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

		$movie = utils\movie\get( $post );

		return $movie->get_backdrop();
	}

	/**
	 * Add movie backdrops list to the data response.
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

		$movie = utils\movie\get( $post );
		$backdrops = $movie->get_backdrops();

		return (array) $backdrops->items;
	}

	/**
	 * Retrieve complete movie meta set.
	 *
	 * @since 3.0.0
	 *
	 * @param int $post_id Movie ID.
	 *
	 * @return array
	 */
	public function get_meta( $post_id ) {

		return utils\movie\get_meta( $post_id );
	}
}
