<?php
/**
 * Define the API Ajax class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Ajax;

use WP_Error;

/**
 * Handle all the plugin's API AJAX callbacks.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class API {

	/**
	 * API Search.
	 * 
	 * Search TheMovieDB for movies matching the query. To get a specific
	 * movie using its ID use the 'wpmoly_fetch_remote_movie' Ajax action.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function search_movie() {

		$query = ! empty( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : false;
		if ( ! $query ) {
			wp_send_json_error( __( 'Invalid search query.', 'wpmovielibrary' ) );
		}

		$params = array();
		$params['page']                 = isset( $_POST['page'] ) ? sanitize_text_field( $_POST['page'] ) : null;
		$params['language']             = isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : null;
		$params['include_adult']        = isset( $_POST['include_adult'] ) ? sanitize_text_field( $_POST['include_adult'] ) : null;
		$params['year']                 = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : null;
		$params['primary_release_year'] = isset( $_POST['primary_release_year'] ) ? sanitize_text_field( $_POST['primary_release_year'] ) : null;

		$wpmoly = \get_wpmoly();
		$result = $wpmoly->api->movie->search( $query, $params );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result );
		}

		wp_send_json_success( $result );
	}

	/**
	 * API fetch.
	 * 
	 * Fetch a specific movie from TheMovieDB by its ID.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function fetch_movie() {

		$query = ! empty( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : false;
		if ( ! $query ) {
			wp_send_json_error( __( 'Invalid search query.', 'wpmovielibrary' ) );
		}

		$params = array(
			'append_to_response' => 'credits,images,release_dates',
			'language'           => sanitize_text_field( $_POST['language'] )
		);

		$wpmoly = \get_wpmoly();
		$result = $wpmoly->api->movie->get( $query, $params );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result );
		}

		wp_send_json_success( $result );
	}

	/**
	 * Remote Query Backdrops from TMDb for the current post.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function fetch_backdrops() {

		$this->fetch_images( 'backdrops' );
	}

	/**
	 * Remote Query Posters from TMDb for the current post.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function fetch_posters() {

		$this->fetch_images( 'posters' );
	}

	/**
	 * Remote Query Images from TMDb for the current post.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Images type, 'backdrops', 'posters' or 'both'
	 * 
	 * @return   array
	 */
	public function fetch_images( $type = 'both' ) {

		$tmdb_id = ! empty( $_POST['tmdb_id'] ) ? sanitize_text_field( $_POST['tmdb_id'] ) : false;
		if ( ! $tmdb_id ) {
			wp_send_json_error( __( 'Invalid search query.', 'wpmovielibrary' ) );
		}

		$wpmoly = \get_wpmoly();
		$images = $wpmoly->api->movie->get_images( $tmdb_id );
		if ( is_null( $images ) ) {
			wp_send_json_error( __( 'No image found.', 'wpmovielibrary' ) );
		}

		$names = array();

		if ( 'both' == $type ) {

			$backdrops = array();
			$posters   = array();

			if ( isset( $images->backdrops ) ) {
				foreach ( $images->backdrops as $image ) {
					$image->type = 'backdrop';
					$image->name = basename( $image->file_path );
					$image->thumbnail   = esc_url( 'https://image.tmdb.org/t/p/w300' . $image->file_path );
					$image->orientation = ( $image->width > $image->height ) ? 'landscape' : 'portrait';
					$backdrops[] = $image;

					$info = pathinfo( $image->name );
					$names[] = $info['filename'];
					// $names[] = basename( $image->name, '.' . $info['extension'] );
				}
			}

			if ( isset( $images->posters ) ) {
				foreach ( $images->posters as $image ) {
					$image->type = 'poster';
					$image->name = basename( $image->file_path );
					$image->thumbnail   = esc_url( 'https://image.tmdb.org/t/p/w300' . $image->file_path );
					$image->orientation = ( $image->width > $image->height ) ? 'landscape' : 'portrait';
					$posters[] = $image;

					$info = pathinfo( $image->name );
					$names[] = $info['filename'];
					// $names[] = basename( $image->name, '.' . $info['extension'] );
				}
			}

			$images = array_merge( $backdrops, $posters );
		} else {
			$images = isset( $images->$type ) ? $images->$type : array();
		}

		global $wpdb;

		$existing = $wpdb->get_results( "SELECT post_name FROM {$wpdb->posts} WHERE `post_name` IN ('" . implode( "','", esc_sql( $names ) ) . "')" );
		$existing = array_map( function( $result ) {
			return $result->post_name;
		}, $existing );
		foreach ( $images as $image ) {
			$info = pathinfo( $image->name );
			$name = sanitize_title( $info['filename'] );
			$image->existing = in_array( $name, $existing );
		}

		wp_send_json_success( $images );
	}

}
