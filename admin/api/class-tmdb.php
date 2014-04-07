<?php
/**
 * TMDb PHP API class - API 'themoviedb.org'
 * API Documentation: http://help.themoviedb.org/kb/api/
 * Documentation and usage in README file
 *
 * @author Jonas De Smet - Glamorous
 * @since 09.11.2009
 * @date 16.11.2012
 * @copyright Jonas De Smet - Glamorous
 * @version 1.5.1
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 */

if ( ! class_exists( 'TMDb' ) ) :

	class TMDb
	{
		
		const POST           = 'post';
		const GET            = 'get';
		const HEAD           = 'head';

		const IMAGE_BACKDROP = 'backdrop';
		const IMAGE_POSTER   = 'poster';
		const IMAGE_PROFILE  = 'profile';

		const API_VERSION    = '3';
		const API_URL        = '://api.themoviedb.org';
		const API_DUMMY_URL  = '://tmdb.caercam.org/api';

		const VERSION        = '1.5.1';

		/**
		 * The TMDb-config
		 *
		 * @var object
		 */
		protected $_config;

		protected $_api_key = '';
		protected $_dummy = false;

		/**
		 * Default constructor
		 *
		 * @param    string    $apikey API-key recieved from TMDb
		 * @param    string    $default Lang Default language (ISO 3166-1)
		 * @param    boolean   $config Load the TMDb-config
		 * 
		 * @return void
		 */
		public function __construct( $config = false, $dummy = false ) {

			if ( true === $config )
				self::getConfiguration();

			$this->_api_key = WPML_Settings::tmdb__apikey();
			$this->_dummy = WPML_Settings::tmdb__dummy();
		}

		public function checkApiKey( $key ) {

			$this->_api_key = esc_attr( $key );
			$this->_dummy = false;

			return self::_makeCall( 'configuration' );
		}

		/**
		 * Search a movie by querystring
		 *
		 * @param    string    $text Query to search after in the TMDb database
		 * @param    int       $page Number of the page with results (default first page)
		 * @param    bool      $adult Whether of not to include adult movies in the results (default false)
		 * @param    mixed     $lang Filter the result with a language (ISO 3166-1) other then default, use false to retrieve results from all languages
		 * 
		 * @return   array     TMDb result 
		 */
		public function searchMovie( $query, $page = 1, $adult = false, $year = null, $lang = null ) {

			$params = array(
				'query'         => $query,
				'page'          => (int) $page,
				'language'      => is_null( $lang ) ? WPML_Settings::tmdb__lang() : $lang,
				'include_adult' => (bool) $adult,
				'year'          => $year,
			);
			return self::_makeCall( 'search/movie', $params );
		}

		/**
		 * Search a person by querystring
		 *
		 * @param    string    $text Query to search after in the TMDb database
		 * @param    int       $page Number of the page with results (default first page)
		 * @param    bool      $adult Whether of not to include adult movies in the results (default false)
		 * 
		 * @return   array     TMDb result 
		 */
		public function searchPerson( $query, $page = 1, $adult = false ) {

			$params = array(
				'query'         => $query,
				'page'          => (int) $page,
				'include_adult' => (bool) $adult,
			);

			return self::_makeCall( 'search/person', $params );
		}

		/**
		 * Search a company by querystring
		 *
		 * @param    string    $text Query to search after in the TMDb database
		 * @param    int       $page Number of the page with results (default first page)
		 * 
		 * @return   array     TMDb result 
		 */
		public function searchCompany( $query, $page = 1 ) {

			$params = array(
				'query' => $query,
				'page'  => $page,
			);
			return self::_makeCall( 'search/company', $params );
		}

		/**
		 * Retrieve information about a collection
		 *
		 * @param    int      $id Id from a collection (retrieved with getMovie)
		 * @param    mixed    $langFilter the result with a language (ISO 3166-1) other then default, use false to retrieve results from all languages
		 * 
		 * @return   array    TMDb result
		 */
		public function getCollection( $id, $lang = null ) {

			$params = array( 'language' => is_null( $lang ) ? WPML_Settings::tmdb__lang() : $lang );
			return self::_makeCall( 'collection/' . $id, $params );
		}

		/**
		 * Retrieve all basic information for a particular movie
		 *
		 * @param    mixed    $idTMDb-id or IMDB-id
		 * @param    mixed    $lang Filter the result with a language (ISO 3166-1) other then default, use false to retrieve results from all languages
		 * 
		 * @return   array    TMDb result 
		 */
		public function getMovie( $id, $lang = null ) {

			$params = array( 'language' => is_null( $lang ) ? WPML_Settings::tmdb__lang() : $lang );
			return self::_makeCall( 'movie/' . $id, $params );
		}

		/**
		 * Retrieve alternative titles for a particular movie
		 *
		 * @param    mixed     $id TMDb-id or IMDB-id
		 * @param    string    $country Only include titles for a particular country (ISO 3166-1)
		 * 
		 * @return   array     TMDb result 
		 */
		public function getMovieTitles( $id, $country = null ) {

			$params = array( 'country' => $country );
			return self::_makeCall( 'movie/' . $id . '/alternative_titles', $params );
		}

		/**
		 * Retrieve all of the movie cast information for a particular movie
		 *
		 * @param mixed $id					TMDb-id or IMDB-id
		 * @return TMDb result array
		 */
		public function getMovieCast( $id ) {

			return self::_makeCall( 'movie/' . $id . '/casts' );
		}

		/**
		 * Retrieve all images for a particular movie
		 *
		 * @param mixed $id					TMDb-id or IMDB-id
		 * @param mixed $lang				Filter the result with a language (ISO 3166-1) other then default, use false to retrieve results from all languages
		 * @return TMDb result array
		 */
		public function getMovieImages( $id, $lang = null ) {

			$params = array( 'language' => is_null( $lang ) ? WPML_Settings::tmdb__lang() : $lang );
			return self::_makeCall( 'movie/' . $id . '/images', $params );
		}

		/**
		 * Get configuration from TMDb
		 *
		 * @return TMDb result array
		 */
		public function getConfiguration() {

			$config = $this->_makeCall( 'configuration' );

			if ( ! empty( $config ) && null == $this->_config )
				$this->_config = $config;

			return $config;
		}

		/**
		 * Get Image URL
		 *
		 * @param    string    $filepath Filepath to image
		 * @param    const     $imagetype Image type: TMDb::IMAGE_BACKDROP, TMDb::IMAGE_POSTER, TMDb::IMAGE_PROFILE
		 * @param    string    $size Valid size for the image
		 * @return   string
		 */
		public function getImageUrl( $filepath, $imagetype, $size ) {

			$config = self::getConfig();

			if ( isset( $config['images'] ) ) {

				$base_url = $config['images']['base_url'];
				$available_sizes = self::getAvailableImageSizes( $imagetype );

				if ( in_array( $size, $available_sizes ) )
					return $base_url . $size . $filepath;
				else
					return sprintf( __( 'The size "%s" is not supported by TMDb', 'wpml' ), $size );
			}
			else
				return __( 'No configuration available for image URL generation', 'wpml' );
		}

		/**
		 * Get available image sizes for a particular image type
		 *
		 * @param    const    $imagetype Image type: TMDb::IMAGE_BACKDROP, TMDb::IMAGE_POSTER, TMDb::IMAGE_PROFILE
		 * @return   array
		 */
		public function getAvailableImageSizes( $imagetype ) {

			$config = self::getConfig();

			if ( isset( $config['images'][$imagetype.'_sizes'] ) )
				return $config['images'][$imagetype.'_sizes'];
			else
				return __( 'No configuration available to retrieve available image sizes', 'wpml' );
		}

		/**
		 * Get ETag to keep track of state of the content
		 *
		 * @param    string    $uri Use an URI to know the version of it. For example: 'movie/550'
		 * @return   string
		 */
		public function getVersion( $uri ) {

			$headers = self::_makeCall( $uri, null, null, TMDb::HEAD );
			return isset( $headers['Etag'] ) ? $headers['Etag'] : '';
		}

		/**
		 * Makes the call to the API
		 *
		 * @param    string    $function API specific function name for in the URL
		 * @param    array     $params Unencoded parameters for in the URL
		 * @param    string    $session_id Session_id for authentication to the API for specific API methods
		 * 
		 * @return   array     TMDb result
		 */
		private function _makeCall( $function, $params = null, $session_id = null, $method = TMDb::GET ) {

			$params = ( ! is_array( $params ) ) ? array() : $params;
			$auth_array = array( 'api_key' => $this->_api_key );

			if ( ! is_null( $session_id ) )
				$auth_array['session_id'] = $session_id;

			$url = WPML_Settings::tmdb__scheme() . TMDb::API_URL . '/' . TMDb::API_VERSION . '/' . $function . '?' . http_build_query( $auth_array, '', '&' );

			if ( isset($params['language'] ) && false === $params['language'] )
				unset($params['language']);

			$url .= ( ! empty( $params ) ) ? '&' . http_build_query( $params, '', '&' ) : '';

			if ( true === $this->_dummy ) {
				$url = 'http' . TMDb::API_DUMMY_URL . '/' . $function;
				if ( isset( $params['query'] ) && '' != $params['query'] )
					$url .= '/' . rawurlencode( $params['query'] );
				if ( isset( $params['language'] ) && false !== $params['language'] )
					$url .= '/' . $params['language'];
			}

			$results = '{}';
			$request  = new WP_Http;
			$headers  = array( 'Accept' => 'application/json' );
			$response = $request->request( $url, array( 'headers' => $headers ) );

			if ( is_wp_error( $response ) ) {
				return sprintf( __( 'Server error: %s', 'wpml' ), $response->get_error_message() );
			}

			$header = $response['headers'];
			$body   = $response['body'];

			$results = json_decode( $body, true );

			if ( isset( $body['status_code'] ) && isset( $body['status_message'] ) ) {
				return sprintf( __( 'Connection to TheMovieDB API failed with message "%s" (code %s)', 'wpml' ), $body['status_code'], $body['status_message'] );
			}

			if ( false !== strpos( $function, 'authentication/token/new' ) ) {

				$parsed_headers = self::_http_parse_headers( $header );
				$results['Authentication-Callback'] = $parsed_headers['Authentication-Callback'];
			}

			if ( null !== $results )
				return $results;
			else if ( TMDb::HEAD == $method )
				return self::_http_parse_headers( $header );
			else
				return sprintf( __( 'Server error on "%s": %s', 'wpml' ), $url, print_r( $response, true ) );
		}

		/**
		 * Getter for the TMDB-config
		 *
		 * @return    array
		 */
		public function getConfig() {
			return ( ! is_null( $this->_config ) ? $this->_config : self::getConfiguration() );
		}

		/**
		 * Internal function to parse HTTP headers because of lack of PECL
		 * extension installed by many
		 *
		 * @param    string    $header
		 * 
		 * @return    array
		 */
		protected function _http_parse_headers($header) {

			$return = array();
			$fields = explode( "\r\n", preg_replace( '/\x0D\x0A[\x09\x20]+/', ' ', $header ) );

			foreach ( $fields as $field ) {

				if ( preg_match( '/([^:]+): (.+)/m', $field, $match ) ) {

					$match[1] = preg_replace( '/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower( trim( $match[1] ) ) );
					$return[ $match[1] ] = isset( $return[ $match[1] ] ) ? array( $return[ $match[1] ], $match[2] ) : trim( $match[2] );
				}
			}

			return $return;
		}
	}

endif;