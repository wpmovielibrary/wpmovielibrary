<?php
/**
 * WPMovieLibrary Movie Search Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Search' ) ) :

	class WPMOLY_Search extends WPMOLY_Movies {

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

		public static function __callStatic( $name, $arguments ) {

			$_name = str_replace( 'by_', '', $name );
			if ( isset( self::$filters[ $_name ] ) ) {
				extract( self::$filters[ $_name ] );
				$arguments = array( $_name, $arguments[0], $strict );
				$meta_query = call_user_func_array( __CLASS__ . "::by_$callback", $arguments );

				return $meta_query;
			}
		}

		public static function by_release_date( $date ) {

			if ( 4 == strlen( $date ) )
				return self::by_year( $date );

			$value = self::filter_interval( $date );
			$meta_query = self::by_interval( 'release_date', $value, $strict = true );

			return $meta_query;
		}

		public static function by_year( $value ) {

			$meta_query = self::by_interval( 'release_date', $value, $strict = false );

			return $meta_query;
		}

		public static function by_rating( $rating ) {

			$value = number_format( $rating, 1, '.', '');
			$value = self::filter_interval( $value );
			$meta_query = self::by_interval( 'rating', $value, $strict = true );

			return $meta_query;
		}

		private static function by_interval( $meta, $interval, $strict = false ) {

			if ( ! is_array( $interval ) )
				return self::by_value( $meta, $interval, $strict );

			if ( 2 != count( $interval ) )
				return self::by_value( $meta, implode( '-', $interval ), $strict );

			extract( $interval );

			$meta_key   = "_wpmoly_movie_$meta";
			$meta_query = array(
				'relation' => 'AND',
				array(
					'key'     => $meta_key,
					'value'   => $min,
					'compare' => '>='
				),
				array(
					'key'     => $meta_key,
					'value'   => $max,
					'compare' => '<='
				)
			);

			return $meta_query;
		}

		private static function by_value( $meta, $value, $strict = false ) {

			$compare = 'LIKE';
			if ( true === $strict )
				$compare = '=';

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

		private static function filter_interval( $param ) {

			$param = explode( '-', $param );

			if ( is_array( $param ) && 2 == count( $param ) )
				$param = array(
					'min' => intval( $param[0] ),
					'max' => intval( $param[1] ),
				);

			if ( 1 == count( $param ) )
				$param = $param[0];

			return $param;
		}

	}

endif;
