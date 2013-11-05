<?php

class WPML_TMDb extends WPMovieLibrary {

	/**
	 * TMDb API Key
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $APIKey = '';

	/**
	 * TMDb API Lang
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $lang = '';

	/**
	 * TMDb API Scheme
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $scheme = '';

	/**
	 * TMDb API Dummy
	 *
	 * @since   1.0.0
	 * @var     boolean
	 */
	protected $dummy = FALSE;

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
	 * TMDb Caching
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $caching = true;

	public function __construct( $TMDb_settings ) {

		$this->APIKey  = $TMDb_settings['APIKey'];
		$this->lang    = $TMDb_settings['lang'];
		$this->scheme  = $TMDb_settings['scheme'];
		$this->scheme  = $this->wpml_scheme_check();
		$this->dummy   = ( 1 == $TMDb_settings['dummy'] ? true : false );
		$this->caching = ( 1 == $this->wpml_o( 'tmdb-settings-caching' ) ? true : false );

		$this->wpml_tmdb();
		$this->config = $this->wpml_tmdb_config();
	}

	/**
	 * Init the TMDb API class
	 *
	 * @since     1.0.0
	 */
	private function wpml_tmdb() {

		require_once plugin_dir_path( __FILE__ ) . 'class-tmdb.php';

		if ( true == $this->dummy && '' == $this->APIKey ) {
			$this->tmdb = new TMDb( $this->APIKey, $this->lang, false, $this->scheme, $this->dummy );
		}
		else {
			$this->tmdb = new TMDb( $this->APIKey, $this->lang, false, $this->scheme );
		}
	}

	/**
	 * Validity check on selected API scheme.
	 *
	 * @since     1.0.0
	 *
	 * @return    string    Scheme if valid, default https.
	 */
	private function wpml_scheme_check() {
		if ( ! in_array( $this->scheme, array( 'http', 'https' ) ) )
			return 'https://';
		else
			return $this->scheme . '://';
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
	private function wpml_tmdb_config() {

		$tmdb_config = $this->tmdb->getConfig();

		if ( is_null( $tmdb_config ) || ( isset( $tmdb_config['status_code'] ) && in_array( $tmdb_config['status_code'], array( 7, 403 ) ) ) )
			return false;

		$base_url = ( 'https' == $this->scheme ? $tmdb_config['images']['secure_base_url'] : $tmdb_config['images']['base_url'] );

		$wpml_tmdb_config = array(
			'poster_url' => array(
				'xx-small' => $base_url . $tmdb_config['images']['poster_sizes'][0],
				'x-small'  => $base_url . $tmdb_config['images']['poster_sizes'][1],
				'small'    => $base_url . $tmdb_config['images']['poster_sizes'][2],
				'medium'   => $base_url . $tmdb_config['images']['poster_sizes'][3],
				'full'     => $base_url . $tmdb_config['images']['poster_sizes'][4],
				'original' => $base_url . $tmdb_config['images']['poster_sizes'][5]
			),
			'image_url' => array(
				'small'    => $base_url . $tmdb_config['images']['backdrop_sizes'][0],
				'medium'   => $base_url . $tmdb_config['images']['backdrop_sizes'][1],
				'full'     => $base_url . $tmdb_config['images']['backdrop_sizes'][2],
				'original' => $base_url . $tmdb_config['images']['backdrop_sizes'][3]
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
	private function wpml_api_key_check( $key ) {
		$_tmdb = new TMDb( $key, $this->lang, false, $this->scheme );
		$data = $_tmdb->getConfiguration();
		return $data;
	}

	/**
	 * Generate base url for requested image type and size.
	 *
	 * @since     1.0.0
	 *
	 * @return    string    base url
	 */
	public function wpml_tmdb_get_base_url( $type, $size ) {
		return $this->config[ $type . '_url' ][ $size ];
	}

	/**
	 * Application/JSON headers content-type.
	 * If no header was sent previously, send new header.
	 *
	 * @since     1.0.0
	 */
	private function wpml_json_header() {
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
	public function wpml_tmdb_api_key_check_callback() {

		if ( ! isset( $_GET['key'] ) || '' == $_GET['key'] || 32 !== strlen( $_GET['key'] ) )
			die();

		$data = $this->wpml_api_key_check( esc_attr( $_GET['key'] ) );

		if ( isset( $data['status_code'] ) && 7 === $data['status_code'] )
			echo '<span id="api_status" class="invalid">'.__( 'Invalid API key - You must be granted a valid key', 'wpml' ).'</span>';
		else
			echo '<span id="api_status" class="valid">'.__( 'Valid API key - Save your settings and have fun!', 'wpml' ).'</span>';

		die();
	}

	/**
	 * Search callback
	 *
	 * @since     1.0.0
	 *
	 * @return    string    HTML output
	 */
	public function wpml_tmdb_search_callback() {

		$type = ( isset( $_GET['type'] ) && '' != $_GET['type'] ? $_GET['type'] : '' );
		$data = ( isset( $_GET['data'] ) && '' != $_GET['data'] ? $_GET['data'] : '' );
		$lang = ( isset( $_GET['lang'] ) && '' != $_GET['lang'] ? $_GET['lang'] : $this->wpml_o('tmdb-settings-lang') );
		$_id  = ( isset( $_GET['_id'] )  && '' != $_GET['_id']  ? $_GET['_id']  : null );

		if ( '' == $data || '' == $type )
			return false;

		if ( 'title' == $type )
			$this->wpml_get_movie_by_title( $data, $lang, $_id );
		else if ( 'id' == $type )
			$this->wpml_get_movie_by_id( $data, $lang, $_id );

		die();
	}


	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                             Internal
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * Cache method for _wpml_get_movie_by_title.
	 * 
	 * @see _wpml_get_movie_by_title()
	 * 
	 * @since     1.0.0
	 */
	private function wpml_get_movie_by_title( $title, $lang, $_id = null ) {

		$movies = ( $this->caching ? get_transient( "movie_$title" ) : false );

		if ( false === $movies ) {
			$movies = $this->_wpml_get_movie_by_title( $title, $lang, $_id );

			if ( true === $this->caching ) {
				$expire = (int) ( 86400 * $this->wpml_o( 'tmdb-settings-caching_time' ) );
				set_transient( "movies_$title", $movies, $expire );
			}
		}

		$this->wpml_json_header();
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
	private function _wpml_get_movie_by_title( $title, $lang, $_id = null ) {

		$title  = $this->wpml_clean_search_title( $title );
		$data   = $this->tmdb->searchMovie( $title, 1, FALSE, NULL, $lang );

		if ( isset( $data['status_code'] ) ) {
			$movies = array(
				'result' => 'error',
				'p'      => '<p><strong>API returned Status '.$data['status_code'].':</strong> '.$data['status_message'].'</p>',
				'_id'    => $_id
			);
		}
		else if ( ! isset( $data['total_results'] ) ) {
			$movies = array(
				'result' => 'empty',
				'p'      => '<p><strong><em>'.__( 'I&rsquo;m Jack&rsquo;s empty result.', 'wpml' ).'</em></strong></p><p>'.__( 'Sorry, your search returned no result. Try a more specific query?', 'wpml' ).'</p>',
				'_id'    => $_id
			);
		}
		else if ( 1 == $data['total_results'] ) {
			$movies = $this->wpml_get_movie_by_id( $data['results'][0]['id'], $lang, $_id, false );
		}
		else if ( $data['total_results'] > 1 ) {

			$movies = array(
				'result' => 'movies',
				'p'      => '<p><strong>'.__( 'Your request showed multiple results. Select your movie in the list or try another search:', 'wpml' ).'</strong></p>',
				'movies' => array(),
				'_id'    => $_id
			);

			foreach ( $data['results'] as $movie ) {
				$movies['movies'][] = array(
					'id'     => $movie['id'],
					'poster' => ( ! is_null( $movie['poster_path'] ) ? $this->config['poster_url']['small'].$movie['poster_path'] : $this->wpml_o('wpml-url').'/assets/no_poster.png' ),
					'title'  => $movie['title'],
					'json'   => json_encode( $movie ),
					'_id'    => $_id
				);
			}
		}
		else {
			$movies = array(
				'result' => 'empty',
				'p'      => '<p><strong><em>'.__( 'I&rsquo;m Jack&rsquo;s empty result.', 'wpml' ).'</em></strong></p><p>'.__( 'Sorry, your search returned no result. Try a more specific query?', 'wpml' ).'</p>',
				'_id'    => $_id
			);
		}

		return $movies;
	}

	/**
	 * Cache method for _wpml_get_movie_by_id.
	 * 
	 * @see _wpml_get_movie_by_id()
	 * 
	 * @since     1.0.0
	 */
	private function wpml_get_movie_by_id( $id, $lang, $_id = null, $echo = true ) {

		$movie = ( $this->caching ? get_transient( "movie_$id" ) : false );

		if ( false === $movie ) {
			$movie = $this->_wpml_get_movie_by_id( $id, $lang, $_id );

			if ( true === $this->caching ) {
				$expire = (int) ( 86400 * $this->wpml_o( 'tmdb-settings-caching_time' ) );
				set_transient("movie_$id", $movie, 3600 * 24);
			}
		}

		$movie['_id'] = $_id;

		if ( true === $echo ) {
			$this->wpml_json_header();
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
	private function _wpml_get_movie_by_id( $id, $lang, $_id = null ) {

		$movie  = $this->tmdb->getMovie( $id, $lang );
		$casts  = $this->tmdb->getMovieCast( $id );
		$images = $this->tmdb->getMovieImages( $id, '' );

		$poster = $images['posters'][0];
		$images = $images['backdrops'];

		// Keep only limited number of images
		$images_max = $this->wpml_o('tmdb-settings-images_max');
		if ( $images_max > 0 && count( $images ) > $images_max )
			$images = array_slice( $images, 0, $images_max );

		$images = array( 'images' => $images );

		// Prepare default crew
		$crew = array();
		$_d = $this->wpml_o('tmdb-default_fields');
		$_c = array_keys( $_d );

		foreach ( $casts['crew'] as $c ) {
			$_r = array_search( $c['job'], array_values( $_d ) );
			if ( false !== $_r ) {
				$movie[ $_c[ $_r ] ][] = $c;
			}
		}

		// Prepare Actors
		$casts = array(
			'cast' => $casts['cast']
		);

		// Prepare Custom Taxonomy
		if ( 1 == $this->wpml_o( 'wpml-settings-taxonomy_autocomplete' ) ) {

			$movie['taxonomy'] = array(
				'actors' => array(),
				'genres' => array()
			);

			if ( ! empty( $casts['cast'] ) && 1 == $this->wpml_o( 'wpml-settings-enable_actor' ) ) {
				foreach ( $casts['cast'] as $actor ) {
					$movie['taxonomy']['actors'][] = $actor['name'];
				}
			}
			if ( ! empty( $movie['genres'] ) && 1 == $this->wpml_o( 'wpml-settings-enable_genre' ) ) {
				foreach ( $movie['genres'] as $genre ) {
					$movie['taxonomy']['genres'][] = $genre['name'];
				}
			}
		}

		$movie = array_merge( $movie, $casts, $images );
		$movie['result'] = 'movie';
		$movie['_id'] = $_id;

		return $movie;
	}


}