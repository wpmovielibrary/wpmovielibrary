<?php
/**
 * Define the API Movie class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly\API;

use WP_Error;

/**
 * Handle the interactions with the TMDb API.
 * 
 * This class handles movies specifically.
 *
 * @package    WPMovieLibrary
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Movie extends Core {

	/**
	 * Call existing methods after setting query type.
	 * 
	 * All methods should be private or protected to trigger this magic
	 * method.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $method Method name
	 * @param    array     $arguments Method parameters
	 * 
	 * @return   mixed
	 */
	public function __call( $method, $arguments ) {

		$this->set_type( 'movie' );

		if ( method_exists( $this, $method ) ) {
			return call_user_func_array( array( $this, $method ), $arguments );
		}
	}

	/**
	 * Fetch backdrops for a specific movie.
	 * 
	 * Alias for API::_get_images( 'backdrops' ).
	 * 
	 * @since    3.0
	 * 
	 * @param    int      $id Movie ID.
	 * @param    array    $params Query parameters.
	 * 
	 * @return   WP_Error|object
	 */
	protected function get_backdrops( $id, $params = array() ) {

		$this->set_id( $id );

		return $this->_get_images( 'backdrops', $params );
	}

	/**
	 * Fetch posters for a specific movie.
	 * 
	 * Alias for API::_get_images( 'posters' ).
	 * 
	 * @since    3.0
	 * 
	 * @param    int      $id Movie ID.
	 * @param    array    $params Query parameters.
	 * 
	 * @return   WP_Error|object
	 */
	protected function get_posters( $id, $params = array() ) {

		$this->set_id( $id );

		return $this->_get_images( 'posters', $params );
	}

	/**
	 * Fetch images for a specific movie.
	 * 
	 * @since    3.0
	 * 
	 * @param    int       $id Movie ID.
	 * @param    string    $image_type Image type, either 'backdrops', 'posters' or 'both' (default).
	 * @param    array     $params Query parameters.
	 * 
	 * @return   WP_Error|object
	 */
	protected function get_images( $id, $image_type = 'both', $params = array() ) {

		$this->set_id( $id );

		return $this->_get_images( $image_type, $params );
	}

	protected function get_credits( $id, $params = array() ) {}

	protected function get_alternative_titles( $id, $params = array() ) {}

	protected function get_release_dates( $id, $params = array() ) {}

	protected function get_keywords( $id, $params = array() ) {}

	protected function get_videos( $id, $params = array() ) {}

	/**
	 * Get a movie from the API using a specific ID.
	 * 
	 * Support TMDb and IMDb IDs. Use $params['append_to_response']
	 * to fetch additional data.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query Query
	 * @param    array     $params Query parameters
	 * 
	 * @return   WP_Error|object
	 */
	protected function get( $query = '', $params = array() ) {

		$query = $this->validate_query( $query );
		if ( is_wp_error( $query ) ) {
			return $query;
		}

		$params = wp_parse_args( (array) $params, array( 'language' => wpmoly_o( 'api-language' ) ) );

		return $this->call( "movie/$query", $params );
	}

	/**
	 * Search movies.
	 * 
	 * If an IMDb ID is detected, simply get the movie.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query Search query
	 * @param    array     $params Search parameters
	 * 
	 * @return   WP_Error|object
	 */
	protected function search( $query = '', $params = array() ) {

		$query = $this->validate_query( $query );
		if ( is_wp_error( $query ) ) {
			return $query;
		}

		$params = (array) $params;
		$is_imdb = $this->validate_query( $query, 'imdb' );

		// IMDb ID detected
		if ( ! is_wp_error( $is_imdb ) ) {

			$params = wp_parse_args( $params, array( 'language' => wpmoly_o( 'api-language' ) ) );

			return $this->call( "movie/$query", $params );
		}

		$params = wp_parse_args( $params, array(
			'page'                 => 1,
			'language'             => wpmoly_o( 'api-language' ),
			'include_adult'        => wpmoly_o( 'api-adult' ),
			'year'                 => '',
			'primary_release_year' => '',
		) );
		$params['query'] = $query;

		return $this->call( "search/movie", $params );
	}
}
