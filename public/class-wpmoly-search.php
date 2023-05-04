<?php
/**
 * WPMovieLibrary Movie Search Class extension.
 * 
 * Generates meta_query parameters for WP_Query to filter movies by specific
 * meta and values.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Search' ) ) :

	class WPMOLY_Search extends WPMOLY_Movies {

		/**
		 * Define meta callbacks and parameters. 'value' callback just
		 * matches the meta as a single value whereas the 'interval'
		 * callback explodes the value and uses the first two results
		 * to match an interval of values.
		 * 
		 * Strict mode is off by default but can be toggled to match
		 * values strictly (using '=') rather than losely (using 'LIKE').
		 * 
		 * @since    2.1
		 * @var      array
		 */
		protected static $filters = array(
			'tmdb_id'              => array( 'callback' => 'value', 'strict' => true ),
			'title'                => array( 'callback' => 'value', 'strict' => false ),
			'original_title'       => array( 'callback' => 'value', 'strict' => false ),
			'tagline'              => array( 'callback' => 'value', 'strict' => false ),
			'overview'             => array( 'callback' => 'value', 'strict' => false ),
			'year'                 => array( 'callback' => 'interval', 'strict' => false ),
			'release_date'         => array( 'callback' => 'value', 'strict' => true ),
			'local_release_date'   => array( 'callback' => 'value', 'strict' => true ),
			'runtime'              => array( 'callback' => 'interval', 'strict' => false ),
			'production_companies' => array( 'callback' => 'value', 'strict' => false ),
			'production_countries' => array( 'callback' => 'value', 'strict' => false ),
			'spoken_languages'     => array( 'callback' => 'value', 'strict' => false ),
			'genres'               => array( 'callback' => 'value', 'strict' => false ),
			'director'             => array( 'callback' => 'value', 'strict' => false ),
			'producer'             => array( 'callback' => 'value', 'strict' => false ),
			'cast'                 => array( 'callback' => 'value', 'strict' => false ),
			'photography'          => array( 'callback' => 'value', 'strict' => false ),
			'composer'             => array( 'callback' => 'value', 'strict' => false ),
			'author'               => array( 'callback' => 'value', 'strict' => false ),
			'writer'               => array( 'callback' => 'value', 'strict' => false ),
			'certification'        => array( 'callback' => 'value', 'strict' => true ),
			'budget'               => array( 'callback' => 'interval', 'strict' => false ),
			'revenue'              => array( 'callback' => 'interval', 'strict' => false ),
			'imdb_id'              => array( 'callback' => 'value', 'strict' => true ),
			'adult'                => array( 'callback' => 'value', 'strict' => true ),
			'homepage'             => array( 'callback' => 'value', 'strict' => false ),
			'status'               => array( 'callback' => 'value', 'strict' => false ),
			'media'                => array( 'callback' => 'value', 'strict' => false ),
			'language'             => array( 'callback' => 'value', 'strict' => false ),
			'subtitles'            => array( 'callback' => 'value', 'strict' => false ),
			'format'               => array( 'callback' => 'value', 'strict' => false )
		);

		/**
		 * Magic!
		 * 
		 * @since    2.1
		 * 
		 * @param    string    $name Called method name
		 * @param    array     $arguments Called method arguments
		 * 
		 * @return   mixed    Callback function return value
		 */
		public static function __callStatic( $name, $arguments ) {

			$_name = str_replace( 'by_', '', $name );
			if ( isset( self::$filters[ $_name ] ) ) {

				extract( self::$filters[ $_name ] );

				if ( 'interval' == $callback )
					$arguments[0] = self::filter_interval( $arguments[0] );

				$args = array( $_name, $arguments[0], $strict );
				$meta_query = call_user_func_array( __CLASS__ . "::by_$callback", $args );

				if ( isset( $arguments[1] ) && 'sql' == $arguments[1] )
					$meta_query = self::get_sql( $meta_query );

				return $meta_query;
			}
		}

		/**
		 * Filter movies by release date.
		 * 
		 * If submitted is 4 characters long, ie. a year, search by
		 * year.
		 * 
		 * @since    2.1
		 * 
		 * @param    string     $date Date to look for
		 * @param    boolean    $format Whether to return an SQL Query part of simple Meta Query Array
		 * @param    boolean    $local Local release date?
		 * 
		 * @return   array      Meta_query parameter for WP_Query
		 */
		public static function by_release_date( $date, $format = 'array', $local = false ) {

			if ( 4 == strlen( $date ) || 7 == strlen( $date ) )
				return self::by_year( $date, $format, $local );

			$key = 'release_date';
			if ( true === $local )
				$key = "local_$key";

			$value = self::filter_interval( $date );
			$meta_query = self::by_interval( $key, $value, $strict = true, $filter = false );

			if ( 'sql' === $format )
				$meta_query = self::get_sql( $meta_query );

			return $meta_query;
		}

		/**
		 * Filter movies by local release date.
		 * 
		 * This is an alias for self::by_release_date()
		 * 
		 * @since    2.1.1.3
		 * 
		 * @param    string     $date Date to look for
		 * @param    boolean    $format Whether to return an SQL Query part of simple Meta Query Array
		 * 
		 * @return   array      Meta_query parameter for WP_Query
		 */
		public static function by_local_release_date( $date, $format = 'array' ) {

			return self::by_release_date( $date, $format, $local = true );
		}

		/**
		 * Filter movies by year.
		 * 
		 * @since    2.1
		 * 
		 * @param    string     $value Year to match
		 * @param    boolean    $format Whether to return an SQL Query part of simple Meta Query Array
		 * @param    boolean    $local Local release date?
		 * 
		 * @return   array      Meta_query parameter for WP_Query
		 */
		public static function by_year( $value, $format = 'array', $local = false ) {

			$key = 'release_date';
			if ( true === $local )
				$key = "local_$key";

			$meta_query = self::by_interval( $key, $value, $strict = false, $filter = false );

			if ( 'sql' === $format )
				$meta_query = self::get_sql( $meta_query );

			return $meta_query;
		}

		/**
		 * Filter movies by rating.
		 * 
		 * @since    2.1
		 * 
		 * @param    string     $rating Rating to match
		 * @param    boolean    $format Whether to return an SQL Query part of simple Meta Query Array
		 * 
		 * @return   array      Meta_query parameter for WP_Query
		 */
		public static function by_rating( $rating, $format = 'array' ) {

			$value = self::filter_interval( $rating, 'number_format', array( 1, '.', '' ) );
			$meta_query = self::by_interval( 'rating', $value, $strict = true );

			if ( 'sql' === $format )
				$meta_query = self::get_sql( $meta_query );

			return $meta_query;
		}

		public static function by_production_countries( $country, $format = 'array' ) {

			$value = self::filter_value( $country );
			$value = WPMOLY_L10n::get_country_standard_name( $value );
			$meta_query = self::by_interval( 'production_countries', $value, $strict = false, $filter = false );

			if ( 'sql' === $format )
				$meta_query = self::get_sql( $meta_query );

			return $meta_query;
		}

		public static function by_spoken_languages( $language, $format = 'array' ) {

			$value = self::filter_value( $language );
			$value = WPMOLY_L10n::get_language_native_name( $value );
			$meta_query = self::by_interval( 'spoken_languages', $value, $strict = false, $filter = false );

			if ( 'sql' === $format )
				$meta_query = self::get_sql( $meta_query );

			return $meta_query;
		}

		public static function by_status( $status, $format = 'array' ) {

			$value = self::filter_value( $status );
			$meta_query = self::by_interval( 'status', $value, $strict = true, $filter = false );

			if ( 'sql' === $format )
				$meta_query = self::get_sql( $meta_query );

			return $meta_query;
		}

		public static function by_language( $language, $format = 'array' ) {

			$value = self::filter_value( $language );
			$value = WPMOLY_L10n::untranslate_rewrite( $value );
			$meta_query = self::by_interval( 'language', $value, $strict = false, $filter = false );

			if ( 'sql' === $format )
				$meta_query = self::get_sql( $meta_query );

			return $meta_query;
		}

		public static function by_subtitles( $subtitles, $format = 'array' ) {

			$value = self::filter_value( $subtitles );
			$value = WPMOLY_L10n::untranslate_rewrite( $value );
			$meta_query = self::by_interval( 'subtitles', $value, $strict = false, $filter = false );

			if ( 'sql' === $format )
				$meta_query = self::get_sql( $meta_query );

			return $meta_query;
		}

		/**
		 * Filter movies by an interval of values.
		 * 
		 * Interval must have two values, if not, fall back to filter by
		 * single value.
		 * 
		 * @since    2.1
		 * 
		 * @param    string     $meta Meta key to look for
		 * @param    array      $interval Meta values to match
		 * @param    boolean    $strict Strict mode
		 * 
		 * @return   array      Meta_query parameter for WP_Query
		 */
		private static function by_interval( $meta, $interval, $strict = false, $filter = true ) {

			if ( ! is_array( $interval ) )
				return self::by_value( $meta, $interval, $strict, $filter );

			if ( 2 != count( $interval ) )
				return self::by_value( $meta, implode( '-', $interval ), $strict, $filter );

			extract( $interval );

			$meta_key   = "_wpmoly_movie_$meta";
			$meta_query = array(
				'relation' => 'AND',
				array(
					'key'     => $meta_key,
					'value'   => $min,
					'compare' => '>=',
					'type'    => 'SIGNED'
				),
				array(
					'key'     => $meta_key,
					'value'   => $max,
					'compare' => '<=',
					'type'    => 'SIGNED'
				)
			);

			return $meta_query;
		}

		/**
		 * Filter movies by value. If strict mode is on, matching will
		 * be strict using the '=' comparator; if strict mode off, match
		 * losely using 'LIKE'.
		 * 
		 * @since    2.1
		 * 
		 * @param    string     $meta Meta key to look for
		 * @param    string     $value Meta value to match
		 * @param    boolean    $strict Strict mode
		 * 
		 * @return   array      Meta_query parameter for WP_Query
		 */
		private static function by_value( $meta, $value, $strict = false, $filter = true ) {

			$compare = 'LIKE';
			if ( true === $strict )
				$compare = '=';

			if ( false !== $filter )
				$value = self::filter_value( $value );

			$meta_query = array(
				'relation' => 'OR',
				array(
					'key'     => "_wpmoly_movie_$meta",
					'value'   => $value,
					'compare' => $compare
				),
				array(
					'key'     => "_wpmoly_movie_$meta",
					'value'   => str_replace( '-', ' ', $value ),
					'compare' => $compare
				)
			);

			return $meta_query;
		}

		/**
		 * Determine if value if an interval or a single value. Interval
		 * must have strictly two values separated with by '-'. If more
		 * than two values are found, fall back to self::by_value(). The
		 * same occurs if only one value is found.
		 * 
		 * @since    2.1
		 * 
		 * @param    string    $param Value to filter
		 * @param    string    $callback Optional. Callback function.
		 * @param    array     $callback_args Optional. Callback parameters.
		 * 
		 * @return   mixed     
		 */
		private static function filter_interval( $param, $callback = 'intval', $callback_args = array() ) {

			$param = explode( '-', $param );

			if ( is_array( $param ) && 2 == count( $param ) )
				$param = array(
					'min' => call_user_func_array( $callback, array_merge( (array) $param[0], $callback_args ) ),
					'max' => call_user_func_array( $callback, array_merge( (array) $param[1], $callback_args ) ),
				);

			if ( 1 == count( $param ) )
				$param = call_user_func_array( $callback, array_merge( (array) $param[0], $callback_args ) );

			return $param;
		}

		private static function filter_value( $value ) {

			if ( '1' == wpmoly_o( 'rewrite-enable' ) ) {
				$value = WPMOLY_L10n::untranslate_rewrite( $value );
			} else {
				$value = WPMOLY_L10n::translate_rewrite( $value );
			}

			return $value;
		}

		/**
		 * Convert a Meta Query array to SQL.
		 * 
		 * @since    2.1
		 * 
		 * @param    array    $query Meta Query parameters
		 * 
		 * @return   string   SQL query part
		 */
		private static function get_sql( $query ) {

			global $wpdb;

			$meta_query = new WP_Meta_Query( $query );
			$meta_query = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );

			return $meta_query;
		}

	}

endif;
