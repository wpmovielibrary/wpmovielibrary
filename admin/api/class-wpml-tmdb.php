<?php
/**
 * WPMovieLibrary_Admin Class extension.
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

			$tmdb_config = new TMDb();
			$tmdb_config = $tmdb_config->getConfig();

			if ( is_null( $tmdb_config ) ) {
				WPML_Utils::admin_notice( __( 'Unknown error, connection to TheMovieDB API failed.', WPML_SLUG ), 'error' );
				return false;
			}
			else if ( isset( $tmdb_config['status_code'] ) && in_array( $tmdb_config['status_code'], array( 7, 403 ) ) ) {
				WPML_Utils::admin_notice( sprintf( __( 'Connection to TheMovieDB API failed with message "%s" (code %s)', WPML_SLUG ), $tmdb_config['status_message'], $tmdb_config['status_code'] ), 'error' );
				return false;
			}

			$base_url = ( 'https' == WPML_Settings::tmdb__scheme() ? $tmdb_config['images']['secure_base_url'] : $tmdb_config['images']['base_url'] );

			$wpml_tmdb_config = array(
				'poster_url' => array(
					'xxx-small' => $base_url . $tmdb_config['images']['poster_sizes'][0],
					'xx-small'  => $base_url . $tmdb_config['images']['poster_sizes'][1],
					'x-small'   => $base_url . $tmdb_config['images']['poster_sizes'][2],
					'small'     => $base_url . $tmdb_config['images']['poster_sizes'][3],
					'medium'    => $base_url . $tmdb_config['images']['poster_sizes'][4],
					'full'      => $base_url . $tmdb_config['images']['poster_sizes'][5],
					'original'  => $base_url . $tmdb_config['images']['poster_sizes'][6]
				),
				'image_url' => array(
					'small'     => $base_url . $tmdb_config['images']['backdrop_sizes'][0],
					'medium'    => $base_url . $tmdb_config['images']['backdrop_sizes'][1],
					'full'      => $base_url . $tmdb_config['images']['backdrop_sizes'][2],
					'original'  => $base_url . $tmdb_config['images']['backdrop_sizes'][3]
				),
			);

			return $wpml_tmdb_config;
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
			$_tmdb = new TMDb( $config = true, $dummy = false );
			$data = $_tmdb->checkApiKey( $key );
			return $data;
		}

		/**
		 * Generate base url for requested image type and size.
		 *
		 * @since     1.0.0
		 *
		 * @return    string    base url
		 */
		public static function get_base_url( $type = null, $size = null ) {

			$config = self::tmdb_config();

			if ( is_null( $type ) && is_null( $size ) )
				return $config;
			else if ( ! is_null( $type ) && is_null( $size ) )
				return $config[ $type . '_url' ];
			else if ( ! is_null( $type ) && ! is_null( $size ) )
				return $config[ $type . '_url' ][ $size ];
		}

		/**
		 * Application/JSON headers content-type.
		 * If no header was sent previously, send new header.
		 *
		 * @since     1.0.0
		 */
		private static function json_header() {
			if ( false === headers_sent() )
				header('Content-type: application/json');
		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Callbacks
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * API check callback. Check key validity and return a status.
		 *
		 * @since     1.0.0
		 *
		 * @return    array    API check validity result
		 */
		public static function check_api_key_callback() {

			check_ajax_referer( 'wpml-callbacks-nonce', 'wpml_check' );

			if ( ! isset( $_GET['key'] ) || '' == $_GET['key'] || 32 !== strlen( $_GET['key'] ) )
				die();

			$data = self::check_api_key( esc_attr( $_GET['key'] ) );

			if ( isset( $data['status_code'] ) && 7 === $data['status_code'] )
				echo '<span id="api_status" class="invalid">'.__( 'Invalid API key - You must be granted a valid key', WPML_SLUG ).'</span>';
			else
				echo '<span id="api_status" class="valid">'.__( 'Valid API key - Save your settings and have fun!', WPML_SLUG ).'</span>';

			die();
		}

		/**
		 * Search callback
		 *
		 * @since     1.0.0
		 *
		 * @return    string    HTML output
		 */
		public static function search_movie_callback() {

			check_ajax_referer( 'wpml-callbacks-nonce', 'wpml_check' );

			$type = ( isset( $_GET['type'] ) && '' != $_GET['type'] ? $_GET['type'] : '' );
			$data = ( isset( $_GET['data'] ) && '' != $_GET['data'] ? $_GET['data'] : '' );
			$lang = ( isset( $_GET['lang'] ) && '' != $_GET['lang'] ? $_GET['lang'] : WPML_Settings::tmdb__lang() );
			$_id  = ( isset( $_GET['_id'] )  && '' != $_GET['_id']  ? $_GET['_id']  : null );

			if ( '' == $data || '' == $type )
				return false;

			if ( 'title' == $type )
				self::get_movie_by_title( $data, $lang, $_id );
			else if ( 'id' == $type )
				self::get_movie_by_id( $data, $lang, $_id );

			die();
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

				if ( true === WPML_Settings::tmdb__caching() ) {
					$expire = (int) ( 86400 * WPML_Settings::tmdb__caching_time() );
					set_transient( "wpml_movies_{$title}_{$lang}", $movies, $expire );
				}
			}

			self::json_header();
			echo json_encode( $movies );
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

			if ( isset( $data['status_code'] ) ) {
				$movies = array(
					'_result' => 'error',
					'p'      => '<p><strong>API returned Status '.$data['status_code'].':</strong> '.$data['status_message'].'</p>',
					'_id'    => $_id
				);
			}
			else if ( ! isset( $data['total_results'] ) ) {
				$movies = array(
					'_result' => 'empty',
					'p'      => '<p><strong><em>'.__( 'I&rsquo;m Jack&rsquo;s empty result.', WPML_SLUG ).'</em></strong></p><p>'.__( 'Sorry, your search returned no result. Try a more specific query?', WPML_SLUG ).'</p>',
					'_id'    => $_id
				);
			}
			else if ( 1 == $data['total_results'] ) {
				$movies = self::get_movie_by_id( $data['results'][0]['id'], $lang, $_id, false );
			}
			else if ( $data['total_results'] > 1 ) {

				$movies = array(
					'_result' => 'movies',
					'p'      => '<p><strong>'.__( 'Your request showed multiple results. Select your movie in the list or try another search:', WPML_SLUG ).'</strong></p>',
					'movies' => array(),
					'_id'    => $_id
				);

				foreach ( $data['results'] as $movie ) {
					$movies['movies'][] = array(
						'id'     => $movie['id'],
						'poster' => ( ! is_null( $movie['poster_path'] ) ? self::get_base_url( 'poster', 'small' ) . $movie['poster_path'] : WPML_DEFAULT_POSTER_URL ),
						'title'  => $movie['title'],
						'json'   => json_encode( $movie ),
						'_id'    => $_id
					);
				}
			}
			else {
				$movies = array(
					'_result' => 'empty',
					'p'      => '<p><strong><em>'.__( 'I&rsquo;m Jack&rsquo;s empty result.', WPML_SLUG ).'</em></strong></p><p>'.__( 'Sorry, your search returned no result. Try a more specific query?', WPML_SLUG ).'</p>',
					'_id'    => $_id
				);
			}

			return $movies;
		}

		/**
		 * Cache method for _get_movie_by_id.
		 * 
		 * @see _get_movie_by_id()
		 * 
		 * @since     1.0.0
		 */
		private static function get_movie_by_id( $id, $lang, $_id = null, $echo = true ) {

			$movie = ( WPML_Settings::tmdb__caching() ? get_transient( "wpml_movie_{$id}_{$lang}" ) : false );

			if ( false === $movie ) {
				$movie = self::_get_movie_by_id( $id, $lang, $_id );

				if ( true === WPML_Settings::tmdb__caching() ) {
					$expire = (int) ( 86400 * WPML_Settings::tmdb__caching_time() );
					set_transient( "wpml_movie_{$id}_{$lang}", $movie, 3600 * 24 );
				}
			}

			$movie['_id'] = $_id;

			if ( true === $echo ) {
				self::json_header();
				echo json_encode( $movie );
			}
			else {
				return $movie;
			}
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
		private static function _get_movie_by_id( $id, $lang, $_id = null ) {

			$tmdb = new TMDb;

			$movie  = $tmdb->getMovie( $id, $lang );
			$casts  = $tmdb->getMovieCast( $id );
			$images = $tmdb->getMovieImages( $id, '' );
			$images = $images['backdrops'];

			// Keep only limited number of images
			$images_max = WPML_Settings::tmdb__images_max();
			if ( $images_max > 0 && count( $images ) > $images_max )
				$images = array_slice( $images, 0, $images_max );

			$_images = array( 'images' => $images );
			$_full = array_merge( $movie, $casts, $images );
			$_movie = array(
				'_id'     => $_id,
				'_tmdb_id' => $id,
				'meta'    => apply_filters( 'wpml_filter_meta_data', $movie ),
				'crew'    => apply_filters( 'wpml_filter_crew_data', $casts ),
				'images'  => $images,
				'poster_path'  => $movie['poster_path'],
				'_result' => 'movie',
				'_full'   => $_full,
			);

			// Prepare Custom Taxonomy
			if ( 1 == WPML_Settings::wpml__taxonomy_autocomplete() ) {

				$_movie['taxonomy'] = array(
					'actors' => array(),
					'genres' => array()
				);

				if ( ! empty( $casts['cast'] ) && 1 == WPML_Settings::wpml__enable_actor() ) {
					foreach ( $casts['cast'] as $actor ) {
						$_movie['taxonomy']['actors'][] = $actor['name'];
					}
				}
				if ( ! empty( $movie['genres'] ) && 1 == WPML_Settings::wpml__enable_genre() ) {
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