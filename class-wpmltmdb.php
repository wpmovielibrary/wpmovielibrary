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

	public function __construct( $TMDb_settings ) {

		$this->APIKey = $TMDb_settings['APIKey'];
		$this->lang   = $TMDb_settings['lang'];
		$this->scheme = $TMDb_settings['scheme'];
		$this->scheme = $this->wpml_scheme_check();

		require_once plugin_dir_path( __FILE__ ) . 'class-tmdb.php';

		$this->tmdb   = new TMDb( $this->APIKey, $this->lang, false, $this->scheme );
		$this->config = $this->wpml_tmdb_config();
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

		if ( is_null( $tmdb_config ) )
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

		if ( '' == $data || '' == $type )
			return false;

		if ( 'title' == $type )
			$this->wpml_get_movie_by_title( $data, $lang );
		else if ( 'id' == $type )
			$this->wpml_get_movie_by_id( $data, $lang );

		die();
	}


	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                             Internal
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

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
	 *
	 * @return    string    HTML formatted results.
	 */
	private function wpml_get_movie_by_title( $title, $lang ) {

		$title = $this->wpml_clean_search_title( $title );

		$data = $this->tmdb->searchMovie( $title, 1, $lang );

		if ( ! isset( $data['total_results'] ) ) {
			echo '<p><strong><em>'.__( 'I&rsquo;m Jack&rsquo;s empty result.', 'wpml' ).'</em></strong></p>';
			echo '<p>'.__( 'Sorry, your search returned no result. Try a more specific query?', 'wpml' ).'</p>';
		}
		else if ( 1 == $data['total_results'] ) {
			$this->wpml_get_movie_by_id( $data['results'][0]['id'], $lang );
		}
		else if ( $data['total_results'] > 1 ) {

			$ret  = '<p><strong>';
			$ret .= __( 'Your request showed multiple results. Select your movie in the list or try another search:', 'wpml' );
			$ret .= '</strong></p>';

			foreach ( $data['results'] as $movie ) {

				$ret .= '<div class="tmdb_select_movie">';
				$ret .= '<a id="tmdb_'.$movie['id'].'" href="#">';

				if ( $movie['poster_path'] != null )
					$ret .= '<img src="'.$this->config['poster_url']['small'].$movie['poster_path'].'" alt="'.$movie['title'].'" />';
				else
					$ret .= '<img src="'.$this->wpml_o('wpml-url').'/assets/no_poster.png" alt="'.$movie['title'].'" />';

				$ret .= '<em>'.$movie['title'].'</em>';
				$ret .= '<input type=\'hidden\' value=\''.json_encode( $movie ).'\' />';
				$ret .= '</div>';
			}

			echo $ret;
		}
		else {
			echo '<p><strong><em>'.__( 'I&rsquo;m Jack&rsquo;s empty result.', 'wpml' ).'</em></strong></p>';
			echo '<p>'.__( 'Sorry, your search returned no result. Try a more specific query?', 'wpml' ).'</p>';
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
	private function wpml_get_movie_by_id( $id, $lang ) {

		$movie  = $this->tmdb->getMovie( $id, $lang );
		$casts  = $this->tmdb->getMovieCast( $id );
		$images = $this->tmdb->getMovieImages( $id, '' );

		$poster = $images['posters'][0];
		$images = $images['backdrops'];

		$images_max = $this->wpml_o('tmdb-settings-images_max');
		if ( $images_max > 0 && count( $images ) > $images_max )
			$images = array_slice( $images, 0, $images_max );

		$images = array( 'images' => $images );

		$crew = array();
		$_d = $this->wpml_o('tmdb-default_fields');
		$_c = array_keys( $_d );

		foreach ( $casts['crew'] as $c ) {
			$_r = array_search( $c['job'], array_values( $_d ) );
			if ( false !== $_r ) {
				$movie[ $_c[ $_r ] ][] = $c;
			}
		}

		$casts = array(
			'cast' => $casts['cast']
		);

		$movie = array_merge( $movie, $casts, $images );

		header('Content-type: application/json');
		echo json_encode( $movie );
	}


}