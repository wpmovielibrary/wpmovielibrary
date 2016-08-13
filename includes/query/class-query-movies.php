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
		$callback = apply_filters( 'wpmoly/filter/query/movies/defaults/preset', 'last_added_movies' );

		return self::$callback();
	}

	/**
	 * 'alphabetical-movies' Grid preset.
	 * 
	 * Default: retrieve the first 20 movies alphabetically.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function alphabetical_movies() {

		return self::preset_query( array(
			'meta_key'       => '_wpmoly_movie_title',
			'orderby'        => 'meta_value',
			'order'          => 'ASC'
		) );
	}

	/**
	 * 'unalphabetical-movies' Grid preset.
	 * 
	 * Default: retrieve the last 20 movies alphabetically.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function unalphabetical_movies() {

		return self::preset_query( array(
			'meta_key'       => '_wpmoly_movie_title',
			'orderby'        => 'meta_value',
			'order'          => 'DESC'
		) );
	}

	/**
	 * '-movies' Grid preset.
	 * 
	 * Default: retrieve 20 movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function current_year_movies() {

		return self::preset_query( array(
			'meta_key'   => '_wpmoly_movie_release_date',
			'meta_type'  => 'date',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => '_wpmoly_movie_release_date',
					'type'    => 'date',
					'value'   => sprintf( '%d-01-01', date( 'Y' ) ),
					'compare' => '>='
				),
				array(
					'key'     => '_wpmoly_movie_release_date',
					'type'    => 'date',
					'value'   => sprintf( '%d-12-31', date( 'Y' ) ),
					'compare' => '<='
				),
			),
			'orderby'    => 'meta_value',
			'order'      => 'DESC'
		) );
	}

	/**
	 * '-movies' Grid preset.
	 * 
	 * Default: retrieve 20 movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function last_year_movies() {

		return self::preset_query( array(
			'meta_key'   => '_wpmoly_movie_release_date',
			'meta_type'  => 'date',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => '_wpmoly_movie_release_date',
					'type'    => 'date',
					'value'   => sprintf( '%d-01-01', date( 'Y' ) - 1 ),
					'compare' => '>='
				),
				array(
					'key'     => '_wpmoly_movie_release_date',
					'type'    => 'date',
					'value'   => sprintf( '%d-12-31', date( 'Y' ) - 1 ),
					'compare' => '<='
				),
			),
			'orderby'    => 'meta_value',
			'order'      => 'DESC'
		) );
	}

	/**
	 * 'last-added-movies' Grid preset.
	 * 
	 * Default: retrieve the last 20 movies added to the library.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function last_added_movies() {

		return self::preset_query();
	}

	/**
	 * 'first-added-movies' Grid preset.
	 * 
	 * Default: retrieve the first 20 movies added to the library.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function first_added_movies() {

		return self::preset_query( array(
			'order' => 'ASC'
		) );
	}

	/**
	 * '-movies' Grid preset.
	 * 
	 * Default: retrieve the 20 last released movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function last_released_movies() {

		return self::preset_query( array(
			'meta_key'  => '_wpmoly_movie_release_date',
			'meta_type' => 'date',
			'orderby'   => 'meta_value',
			'order'     => 'DESC'
		) );
	}

	/**
	 * '-movies' Grid preset.
	 * 
	 * Default: retrieve the 20 first released movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function first_released_movies() {

		return self::preset_query( array(
			'meta_key'  => '_wpmoly_movie_release_date',
			'meta_type' => 'date',
			'orderby'   => 'meta_value',
			'order'     => 'ASC'
		) );
	}

	/**
	 * 'incoming-movies' Grid preset.
	 * 
	 * Default: retrieve the first 20 incoming movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function incoming_movies() {

		return self::preset_query( array(
			'meta_key'     => '_wpmoly_movie_release_date',
			'meta_type'    => 'date',
			'meta_value'   => sprintf( '%d-01-01', date( 'Y' ) + 1 ),
			'meta_compare' => '>=',
			'orderby'      => 'meta_value',
			'order'        => 'DESC'
		) );
	}

	/**
	 * 'most-rated-movies' Grid preset.
	 * 
	 * Default: retrieve the first 20 most movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function most_rated_movies() {

		return self::preset_query( array(
			'meta_key'     => '_wpmoly_movie_rating',
			//'meta_value'   => 0.0
			//'meta_compare' => '>',
			'orderby'      => 'meta_value_num',
			'order'        => 'DESC'
		) );
	}

	/**
	 * 'least-rated-movies' Grid preset.
	 * 
	 * Default: retrieve the first 20 least movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public static function least_rated_movies() {

		return self::preset_query( array(
			'meta_key'     => '_wpmoly_movie_rating',
			//'meta_value'   => 0.0
			//'meta_compare' => '>',
			'orderby'      => 'meta_value_num',
			'order'        => 'ASC'
		) );
	}

	/**
	 * Handle preset queries.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $args Query parameters
	 * 
	 * @return   array
	 */
	public static function preset_query( $args = array() ) {

		/**
		 * Filter default preset post status.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $post_status
		 */
		$post_status = apply_filters( 'wpmoly/filter/query/movies/defaults/post_status', array( 'publish' ) );

		/**
		 * Filter default number of posts per page.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $posts_per_page
		 */
		$posts_per_page = apply_filters( 'wpmoly/filter/query/movies/defaults/posts_per_page', 20 );

		/**
		 * Filter default query orderby.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $orderby
		 */
		$orderby = apply_filters( 'wpmoly/filter/query/movies/defaults/orderby', array( 'date' ) );

		/**
		 * Filter default query order.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $order
		 */
		$order = apply_filters( 'wpmoly/filter/query/movies/defaults/order', 'DESC' );

		/**
		 * Filter default query args.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $args
		 */
		$defaults = apply_filters( 'wpmoly/filter/query/movies/defaults/query_args', array(
			'post_type'      => 'movie',
			'post_status'    => $post_status,
			'posts_per_page' => $posts_per_page,
			'orderby'        => $orderby,
			'order'          => $order
		) );
		$args = wp_parse_args( $args, $defaults );

		$query = new WP_Query( $args );
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
