<?php
/**
 * WPMovieLibrary_Admin Class extension.
 * 
 * Layer for TMDb Class.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_TMDb' ) ) :

	class WPML_TMDb extends WPML_Module {

		/**
		 * TMDb API Config
		 *
		 * @since   1.0.0
		 * @var     array
		 */
		protected $config = null;

		/**
		 * TMDb API
		 *
		 * @since   1.0.0
		 * @var     string
		 */
		protected $tmdb = '';

		/**
		 * TMDb Error notify
		 *
		 * @since   1.0.0
		 * @var     string
		 */
		protected $error = '';

		public function __construct() {

			if ( ! is_admin() )
				return false;

			$this->register_hook_callbacks();

			if ( '' == WPML_Settings::tmdb__apikey() ) {
				WPML_Utils::admin_notice( __( '', WPML_SLUG ), 'error' );
				return false;
			}
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'admin_init', array( $this, 'init' ) );

			add_action( 'wp_ajax_wpml_search_movie', __CLASS__ . '::search_movie_callback' );
			add_action( 'wp_ajax_wpml_check_api_key', __CLASS__ . '::check_api_key_callback' );
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {
		}

		/**
		 * Set up TMDb config.
		 * Sends a request to the API to fetch images and posters default sizes
		 * and generate various size-based urls for posters and backdrops.
		 *
		 * @since     1.0.0
		 *
		 * @return    array    TMDb config
		 */
		private static function tmdb_config() {

			$tmdb = new TMDb();
			$config = $tmdb->getConfig();

			if ( is_null( $config ) ) {
				WPML_Utils::admin_notice( __( 'Unknown error, connection to TheMovieDB API failed.', WPML_SLUG ), 'error' );
				return false;
			}
			else if ( isset( $config['status_code'] ) && in_array( $config['status_code'], array( 7, 403 ) ) ) {
				WPML_Utils::admin_notice( sprintf( __( 'Connection to TheMovieDB API failed with message "%s" (code %s)', WPML_SLUG ), $config['status_message'], $config['status_code'] ), 'error' );
				return false;
			}

			return $config;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Methods
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Test the submitted API key using a dummy TMDb instance to fetch
		 * API's configuration. Return the request result array.
		 *
		 * @since     1.0.0
		 *
		 * @return    array    API configuration request result
		 */
		private static function check_api_key( $key ) {
			$tmdb = new TMDb( $config = true, $dummy = false );
			$check = $tmdb->checkApiKey( $key );
			return $check;
		}

		/**
		 * Generate base url for requested image type and size.
		 *
		 * @since     1.0.0
		 *
		 * @return    string    base url
		 */
		public static function get_image_url( $filepath = null, $imagetype = null, $size = null ) {

			$tmdb = new TMDb();
			return $tmdb->getImageUrl( $filepath, $imagetype, $size );
		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Callbacks
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * API check callback. Check key validity and return a status.
		 * 
		 * An invalid key will result in an error from the API with the
		 * status code '7'. If we get that error, use a WP_Error instance
		 * to handle the error and add it to the WPML_Ajax instance we
		 * use to pass data to the JS part.
		 * 
		 * If the key appears to be valid, send a validation message.
		 *
		 * @since     1.0.0
		 */
		public static function check_api_key_callback() {

			WPML_Utils::check_ajax_referer( 'check-api-key' );

			if ( ! isset( $_GET['key'] ) || '' == $_GET['key'] || 32 !== strlen( $_GET['key'] ) )
				return new WP_Error( 'invalid', __( 'Invalid API key - the key should be an alphanumerica 32 chars long string.', WPML_SLUG ) );

			$check = self::check_api_key( esc_attr( $_GET['key'] ) );

			if ( is_wp_error( $check ) )
				$response = new WP_Error( 'invalid', __( 'Invalid API key - You must be granted a valid key', WPML_SLUG ) );
			else
				$response = array( 'message' => __( 'Valid API key - Save your settings and have fun!', WPML_SLUG ) );

			WPML_Utils::ajax_response( $response );
		}

		/**
		 * Search callback
		 *
		 * @since     1.0.0
		 */
		public static function search_movie_callback() {

			WPML_Utils::check_ajax_referer( 'search-movies' );

			$type = ( isset( $_GET['type'] ) && '' != $_GET['type'] ? $_GET['type'] : '' );
			$data = ( isset( $_GET['data'] ) && '' != $_GET['data'] ? $_GET['data'] : '' );
			$lang = ( isset( $_GET['lang'] ) && '' != $_GET['lang'] ? $_GET['lang'] : WPML_Settings::tmdb__lang() );
			$_id  = ( isset( $_GET['post_id'] ) && '' != $_GET['post_id'] ? $_GET['post_id'] : null );

			if ( '' == $data || '' == $type )
				return false;

			if ( 'title' == $type )
				$response = self::get_movie_by_title( $data, $lang, $_id );
			else if ( 'id' == $type )
				$response = self::get_movie_by_id( $data, $lang, $_id );

			WPML_Utils::ajax_response( $response );
		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Internal
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Cache method for _get_movie_by_title.
		 * 
		 * @see _get_movie_by_title()
		 * 
		 * @since     1.0.0
		 */
		private static function get_movie_by_title( $title, $lang, $_id = null ) {

			$movies = ( WPML_Settings::tmdb__caching() ? get_transient( "wpml_movie_{$title}_{$lang}" ) : false );

			if ( false === $movies ) {
				$movies = self::_get_movie_by_title( $title, $lang, $_id );

				if ( true === WPML_Settings::tmdb__caching() && ! is_wp_error( $movies ) ) {
					$expire = (int) ( 86400 * WPML_Settings::tmdb__caching_time() );
					set_transient( "wpml_movies_{$title}_{$lang}", $movies, $expire );
				}
			}

			return $movies;
		}

		/**
		 * List all movies matching submitted title using the API's search
		 * method.
		 * 
		 * If no result were returned, display a notification. More than one
		 * results means the search is not accurate, display first results in
		 * case one of them matches the search and add a notification to try a
		 * more specific search. If only on movie showed up, it should be the
		 * one, call the API using the movie ID.
		 * 
		 * If more than one result, all movies listed will link to a new AJAX
		 * call to load the movie by ID.
		 *
		 * @since     1.0.0
		 */
		public static function _get_movie_by_title( $title, $lang, $_id = null ) {

			$tmdb = new TMDb;
			$config = $tmdb->getConfig();

			$title  = WPML_Utils::clean_search_title( $title );
			$data   = $tmdb->searchMovie( $title, 1, FALSE, NULL, $lang );

			if ( is_wp_error( $data ) )
				return $data;

			$_result  = 'empty';
			$_message = __( 'Sorry, your search returned no result. Try a more specific query?', WPML_SLUG );
			$_movies  = array();
			$_post_id = $_id;

			if ( isset( $data['status_code'] ) ) {
				return new WP_Error( esc_attr( $data['status_code'] ), esc_attr( $data['status_message'] ), array( '_id' => $_id ) );
			}
			else if ( ! isset( $data['total_results'] ) ) {

				$_result  = 'empty';
				$_message = __( 'Sorry, your search returned no result. Try a more specific query?', WPML_SLUG );
				$_post_id = $_id;
			}
			else if ( 1 == $data['total_results'] ) {

				$_result   = 'movie';
				$_message  = null;
				$_movie    = self::get_movie_by_id( $data['results'][0]['id'], $lang, $_id );
				if ( is_wp_error( $_movie ) )
					return $_movie;
				$_movies[] = $_movie;
				$_post_id  = $_id;
			}
			else if ( $data['total_results'] > 1 ) {

				$_result  = 'movies';
				$_message = __( 'Your request showed multiple results. Select your movie in the list or try another search:', WPML_SLUG );
				$_movies  = array();
				$_post_id = $_id;

				foreach ( $data['results'] as $movie ) {
					$_movies[] = array(
						'id'     => $movie['id'],
						'poster' => ( ! is_null( $movie['poster_path'] ) ? self::get_image_url( $movie['poster_path'], 'poster', 'small' ) : WPML_DEFAULT_POSTER_URL ),
						'title'  => $movie['title'],
						'json'   => json_encode( $movie ),
						'_id'    => $_id
					);
				}
			}

			$movies = array(
				'result'  => $_result,
				'message' => $_message,
				'movies'  => $_movies,
				'post_id' => $_post_id
			);

			return $movies;
		}

		/**
		 * Cache method for _get_movie_by_id.
		 * 
		 * @see _get_movie_by_id()
		 * 
		 * @since     1.0.0
		 */
		public static function get_movie_by_id( $id, $lang, $_id = null ) {

			$movie = ( WPML_Settings::tmdb__caching() ? get_transient( "wpml_movie_{$id}_{$lang}" ) : false );

			if ( false === $movie ) {
				$movie = self::_get_movie_by_id( $id, $lang, $_id );

				if ( true === WPML_Settings::tmdb__caching() && ! is_wp_error( $movie ) ) {
					$expire = (int) ( 86400 * WPML_Settings::tmdb__caching_time() );
					set_transient( "wpml_movie_{$id}_{$lang}", $movie, 3600 * 24 );
				}
			}

			if ( ! is_wp_error( $movie ) )
				$movie['_id'] = $_id;

			return $movie;
		}

		/**
		 * Get movie by ID. Load casts and images too.
		 * 
		 * Return a JSON string containing fetched data. Apply some filtering
		 * to extract specific crew jobs like director or producer.
		 *
		 * @since     1.0.0
		 *
		 * @return    string    JSON formatted results.
		 */
		public static function _get_movie_by_id( $id, $lang, $_id = null ) {

			$tmdb = new TMDb;

			$data = array(
				'movie'  => $tmdb->getMovie( $id, $lang ),
				'casts'  => $tmdb->getMovieCast( $id ),
				'images' => $tmdb->getMovieImages( $id, '' )
			);

			foreach ( $data as $d )
				if ( is_wp_error( $d ) )
					return $d;

			extract( $data, EXTR_SKIP );

			$_images = array( 'images' => $images['backdrops'] );
			$_full = array_merge( $movie, $casts, $images );
			$_movie = array(
				'_id'     => $_id,
				'_tmdb_id' => $id,
				'meta'    => apply_filters( 'wpml_filter_meta_data', $movie ),
				'crew'    => apply_filters( 'wpml_filter_crew_data', $casts ),
				'images'  => $images,
				'poster' => ( ! is_null( $movie['poster_path'] ) ? self::get_image_url( $movie['poster_path'], 'poster', 'small' ) : WPML_DEFAULT_POSTER_URL ),
				'poster_path'  => ( ! is_null( $movie['poster_path'] ) ? $movie['poster_path'] : WPML_DEFAULT_POSTER_URL ),
				'_result' => 'movie',
				'_full'   => $_full,
			);

			$_movie['taxonomy'] = array();

			// Prepare Custom Taxonomy
			if ( 1 == WPML_Settings::taxonomies__actor_autocomplete() ) {

				$_movie['taxonomy']['actors'] = array();
				if ( ! empty( $casts['cast'] ) && 1 == WPML_Settings::taxonomies__enable_actor() ) {
					foreach ( $casts['cast'] as $actor ) {
						$_movie['taxonomy']['actors'][] = $actor['name'];
					}
				}
			}

			// Prepare Custom Taxonomy
			if ( 1 == WPML_Settings::taxonomies__genre_autocomplete() ) {

				$_movie['taxonomy']['genres'] = array();
				if ( ! empty( $movie['genres'] ) && 1 == WPML_Settings::taxonomies__enable_genre() ) {
					foreach ( $movie['genres'] as $genre ) {
						$_movie['taxonomy']['genres'][] = $genre['name'];
					}
				}
			}

			return $_movie;
		}

		/**
		 * Load all available Images for a movie.
		 * 
		 * Filter the images returned by the API to exclude the ones we
		 * have already imported.
		 *
		 * @since     1.0.0
		 *
		 * @param    int    Movie TMDb ID
		 * 
		 * @return   array  All fetched images minus the ones already imported
		 */
		public static function get_movie_images( $tmdb_id ) {

			$tmdb = new TMDb;

			if ( is_null( $tmdb_id ) )
				return false;

			$images = $tmdb->getMovieImages( $tmdb_id, '' );
			$images = $images['backdrops'];

			foreach ( $images as $i => $image ) {
				$file_path = substr( $image['file_path'], 1 );
				$exists = apply_filters( 'wpml_check_for_existing_images', $tmdb_id, 'image', $file_path );
				if ( false !== $exists )
					unset( $images[ $i ] );
			}

			return $images;
		}

		/**
		 * Load all available Posters for a movie.
		 * 
		 * Filter the posters returned by the API to exclude the ones we
		 * have already imported.
		 *
		 * @since     1.0.0
		 *
		 * @param    int    Movie TMDb ID
		 * 
		 * @return   array  All fetched posters minus the ones already imported
		 */
		public static function get_movie_posters( $tmdb_id ) {

			$tmdb = new TMDb;

			if ( is_null( $tmdb_id ) )
				return false;

			$images = $tmdb->getMovieImages( $tmdb_id, '' );
			$images = $images['posters'];

			foreach ( $images as $i => $image ) {
				$file_path = substr( $image['file_path'], 1 );
				$exists = apply_filters( 'wpml_check_for_existing_images', $tmdb_id, 'poster', $file_path );
				if ( false !== $exists )
					unset( $images[ $i ] );
			}

			return $images;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {}

	}

endif;