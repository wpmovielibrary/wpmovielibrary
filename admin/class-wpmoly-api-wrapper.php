<?php
/**
 * WPMovieLibrary_Admin Class extension.
 * 
 * Layer for TMDb Class.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_TMDb' ) ) :

	class WPMOLY_TMDb extends WPMOLY_Module {

		/**
		 * TMDb API Config
		 *
		 * @since   1.0
		 * @var     array
		 */
		protected $config = null;

		/**
		 * TMDb API
		 *
		 * @since   1.0
		 * @var     string
		 */
		protected $tmdb = '';

		/**
		 * TMDb Error notify
		 *
		 * @since   1.0
		 * @var     string
		 */
		protected $error = '';

		public function __construct() {

			if ( ! is_admin() )
				return false;

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'admin_init', array( $this, 'init' ) );

			add_action( 'wp_ajax_wpmoly_search_movie', __CLASS__ . '::search_movie_callback' );
			add_action( 'wp_ajax_wpmoly_check_api_key', __CLASS__ . '::check_api_key_callback' );
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
		 * to handle the error and add it to the WPMOLY_Ajax instance we
		 * use to pass data to the JS part.
		 * 
		 * If the key appears to be valid, send a validation message.
		 *
		 * @since    1.0
		 */
		public static function check_api_key_callback() {

			wpmoly_check_ajax_referer( 'check-api-key' );

			if ( ! isset( $_GET['key'] ) || '' == $_GET['key'] || 32 !== strlen( $_GET['key'] ) )
				return new WP_Error( 'invalid', __( 'Invalid API key - the key should be an alphanumerica 32 chars long string.', 'wpmovielibrary' ) );

			$check = self::check_api_key( esc_attr( $_GET['key'] ) );

			if ( is_wp_error( $check ) )
				$response = new WP_Error( 'invalid', __( 'Invalid API key - You must be granted a valid key', 'wpmovielibrary' ) );
			else
				$response = array( 'message' => __( 'Valid API key - Save your settings and have fun!', 'wpmovielibrary' ) );

			wpmoly_ajax_response( $response );
		}

		/**
		 * Search callback
		 *
		 * @since    1.0
		 */
		public static function search_movie_callback() {

			wpmoly_check_ajax_referer( 'search-movies' );

			$type = ( isset( $_GET['type'] ) && '' != $_GET['type'] ? $_GET['type'] : '' );
			$data = ( isset( $_GET['data'] ) && '' != $_GET['data'] ? $_GET['data'] : '' );
			$lang = ( isset( $_GET['lang'] ) && '' != $_GET['lang'] ? $_GET['lang'] : wpmoly_o( 'api-language' ) );
			$_id  = ( isset( $_GET['post_id'] ) && '' != $_GET['post_id'] ? $_GET['post_id'] : null );

			if ( '' == $data || '' == $type )
				return false;

			if ( 'title' == $type )
				$response = self::get_movie_by_title( $data, $lang, $_id );
			else if ( 'id' == $type )
				$response = self::get_movie_by_id( $data, $lang, $_id );

			wpmoly_ajax_response( $response );
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
		 * @since    1.0
		 * 
		 * @param    string    $key API Key
		 *
		 * @return   array     API configuration request result
		 */
		private static function check_api_key( $key ) {

			$tmdb = new TMDb( $config = true, $dummy = false );
			$check = $tmdb->checkApiKey( $key );

			return $check;
		}

		/**
		 * Generate base url for requested image type and size.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $filepath Filepath to image
		 * @param    const     $imagetype Image type
		 * @param    string    $size Valid size for the image
		 * 
		 * @return   string    base url
		 */
		public static function get_image_url( $filepath = null, $imagetype = null, $size = null ) {

			$tmdb = new TMDb();

			$url = $tmdb->getImageUrl( $filepath, $imagetype, $size );

			return $url;
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
		 * @since    1.0
		 * 
		 * @param    string    $title Query to search after in the TMDb database
		 * @param    string    $lang Lang to use in the query
		 * @param    int       $post_id Related Post ID
		 */
		private static function get_movie_by_title( $title, $lang, $post_id = null ) {

			$movies = ( wpmoly_o( 'enable-cache' ) ? get_transient( "wpmoly_movie_{$title}_{$lang}" ) : false );

			if ( false === $movies ) {
				$movies = self::_get_movie_by_title( $title, $lang, $post_id );

				if ( true === wpmoly_o( 'enable-cache' ) && ! is_wp_error( $movies ) ) {
					$expire = (int) ( 86400 * wpmoly_o( 'cache-expire' ) );
					set_transient( "wpmoly_movies_{$title}_{$lang}", $movies, $expire );
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
		 * @since    1.0
		 * 
		 * @param    string    $title Query to search after in the TMDb database
		 * @param    string    $lang Lang to use in the query
		 * @param    int       $post_id Related Post ID
		 */
		public static function _get_movie_by_title( $title, $lang, $post_id = null ) {

			$tmdb = new TMDb;
			$config = $tmdb->getConfig();

			$title  = preg_replace( '/[^\p{L}\p{N}\s]/u', '', trim( $title ) );
			$data   = $tmdb->searchMovie( $title, 1, FALSE, NULL, $lang );

			if ( is_wp_error( $data ) )
				return $data;

			$_result  = 'empty';
			$_message = __( 'Sorry, your search returned no result. Try a more specific query?', 'wpmovielibrary' );
			$_movies  = array();
			$_post_id = $post_id;

			if ( isset( $data['status_code'] ) ) {
				return new WP_Error( esc_attr( $data['status_code'] ), esc_attr( $data['status_message'] ), array( '_id' => $post_id ) );
			}
			else if ( ! isset( $data['total_results'] ) ) {

				$_result  = 'empty';
				$_message = __( 'Sorry, your search returned no result. Try a more specific query?', 'wpmovielibrary' );
				$_post_id = $post_id;
			}
			else if ( 1 == $data['total_results'] ) {

				$_result   = 'movie';
				$_message  = null;
				$_movie    = self::get_movie_by_id( $data['results'][0]['id'], $lang, $post_id );
				if ( is_wp_error( $_movie ) )
					return $_movie;
				$_movies[] = $_movie;
				$_post_id  = $post_id;
			}
			else if ( $data['total_results'] > 1 ) {

				$_result  = 'movies';
				$_message = __( 'Your request showed multiple results. Select your movie in the list or try another search:', 'wpmovielibrary' );
				$_movies  = array();
				$_post_id = $post_id;

				foreach ( $data['results'] as $movie ) {

					if ( ! is_null( $movie['poster_path'] ) )
						$movie['poster_path'] = self::get_image_url( $movie['poster_path'], 'poster', 'small' );
					else
						$movie['poster_path'] = str_replace( '{size}', '-medium', WPMOLY_DEFAULT_POSTER_URL );

					$_movies[] = array(
						'id'     => $movie['id'],
						'poster' => $movie['poster_path'],
						'title'  => $movie['title'],
						'year'   => apply_filters( 'wpmoly_format_movie_date', $movie['release_date'], 'Y' ),
						'json'   => json_encode( $movie ),
						'_id'    => $post_id
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
		 * @since    1.0
		 * 
		 * @param    string    $id Id to search in the TMDb database
		 * @param    string    $lang Lang to use in the query
		 * @param    int       $post_id Related Post ID
		 */
		public static function get_movie_by_id( $id, $lang, $post_id = null ) {

			$movie = ( wpmoly_o( 'enable-cache' ) ? get_transient( "wpmoly_movie_{$id}_{$lang}" ) : false );

			if ( false === $movie ) {
				$movie = self::_get_movie_by_id( $id, $lang, $post_id );

				if ( true === wpmoly_o( 'enable-cache' ) && ! is_wp_error( $movie ) ) {
					$expire = (int) ( 86400 * wpmoly_o( 'cache-expire' ) );
					set_transient( "wpmoly_movie_{$id}_{$lang}", $movie, 3600 * 24 );
				}
			}

			if ( ! is_wp_error( $movie ) )
				$movie['_id'] = $post_id;

			return $movie;
		}

		/**
		 * Get movie by ID. Load casts and images too.
		 * 
		 * Return a JSON string containing fetched data. Apply some filtering
		 * to extract specific crew jobs like director or producer.
		 *
		 * @since    1.0
		 * 
		 * @param    string    $id Id to search in the TMDb database
		 * @param    string    $lang Lang to use in the query
		 * @param    int       $post_id Related Post ID
		 *
		 * @return   string    JSON formatted results.
		 */
		public static function _get_movie_by_id( $id, $lang, $post_id = null ) {

			$tmdb = new TMDb;

			$data = array(
				'movie'   => $tmdb->getMovie( $id, $lang ),
				'casts'   => $tmdb->getMovieCast( $id ),
				'images'  => $tmdb->getMovieImages( $id, '' ),
				'release' => $tmdb->getMovieRelease( $id )
			);

			foreach ( $data as $d )
				if ( is_wp_error( $d ) )
					return $d;

			extract( $data, EXTR_SKIP );

			$poster_path = $movie['poster_path'];
			$movie = apply_filters( 'wpmoly_filter_meta_data', $movie );
			$casts = apply_filters( 'wpmoly_filter_crew_data', $casts );
			$meta  = array_merge( $movie, $casts );
			$meta['tmdb_id'] = $id;
			$meta['certification'] = '';

			if ( isset( $release['countries'] ) ) {
				$certification_alt = '';
				foreach ( $release['countries'] as $country ) {
					if ( $country['iso_3166_1'] == wpmoly_o( 'api-country' ) ) {
						$meta['certification'] = $country['certification'];
						$meta['local_release_date'] = $country['release_date'];
					}
					else if ( $country['iso_3166_1'] == wpmoly_o( 'api-country-alt' ) ) {
						$certification_alt = $country['certification'];
					}
				}

				if ( '' == $meta['certification'] )
					$meta['certification'] = $certification_alt;

				if ( '' == $meta['local_release_date'] )
					$meta['local_release_date'] = '';
			}

			if ( is_null( $poster_path ) )
				$poster_path = str_replace( '{size}', '-medium', WPMOLY_DEFAULT_POSTER_URL );

			if ( is_null( $poster_path ) )
				$poster = $poster_path;
			else
				$poster = self::get_image_url( $poster_path, 'poster', 'small' );

			$_images = array( 'images' => $images['backdrops'] );
			$_full = array_merge( $movie, $casts, $images );
			$_movie = array(
				'_id'		=> $post_id,
				'_tmdb_id'	=> $id,
				'meta'		=> $meta,
				'images'	=> $images,
				'poster'	=> $poster,
				'poster_path'	=> $poster_path,
				'_result'	=> 'movie',
				'_full'		=> $_full
			);

			$_movie['taxonomy'] = array();

			// Prepare Custom Taxonomy
			if ( 1 == wpmoly_o( 'actor-autocomplete' ) ) {

				$_movie['taxonomy']['actors'] = array();
				if ( ! empty( $casts['cast'] ) && 1 == wpmoly_o( 'enable-actor' ) ) {
					foreach ( $casts['cast'] as $actor ) {
						$_movie['taxonomy']['actors'][] = $actor;
					}
				}
			}

			// Prepare Custom Taxonomy
			if ( 1 == wpmoly_o( 'genre-autocomplete' ) ) {

				$_movie['taxonomy']['genres'] = array();
				if ( ! empty( $movie['genres'] ) && 1 == wpmoly_o( 'enable-genre' ) ) {
					foreach ( $movie['genres'] as $genre ) {
						$_movie['taxonomy']['genres'][] = $genre;
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
		 * @since    1.0
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
				$exists = apply_filters( 'wpmoly_check_for_existing_images', $tmdb_id, 'image', $file_path );
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
		 * @since    1.0
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
				$exists = apply_filters( 'wpmoly_check_for_existing_images', $tmdb_id, 'poster', $file_path );
				if ( false !== $exists )
					unset( $images[ $i ] );
			}

			return $images;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}

endif;