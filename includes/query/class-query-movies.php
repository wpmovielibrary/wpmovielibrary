<?php
/**
 * Define the Movies Query class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/query
 */

namespace wpmoly\Query;

use WP_Query;

/**
 * Find Movies in various ways.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/query
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Movies {

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 * 
	 * @param    mixed    $params
	 * 
	 * @return   Movies
	 */
	public function __construct() {

		
	}

	/**
	 * Define a default preset for this Query.
	 * 
	 * The default preset should only run an existing preset callback.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function default_preset() {

		/**
		 * Filter default preset callback.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $callback
		 */
		$callback = apply_filters( 'wpmoly/filter/query/movies/default_preset', 'last_added_movies' );

		return self::$callback();
	}

	/**
	 * 'last-added-movies' Grid preset.
	 * 
	 * Retrieve the last 20 movies added to the library.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function last_added_movies() {

		$movies = array();
		$query = new WP_Query( array(
			'post_type'      => 'movie',
			'post_status'    => 'publish',
			'posts_per_page' => 20
		) );

		if ( ! $query->have_posts() ) {
			return $movies;
		}

		foreach ( $query->posts as $post ) {
			$movies[] = get_movie( $post );
		}

		return $movies;
	}

	/**
	 * Perform the query.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function query() {

		
	}
}
