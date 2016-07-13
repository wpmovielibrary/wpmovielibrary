<?php
/**
 * Define the API Core class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly\API;

use WP_Error;

/**
 * Handle the interactions with the TMDb API.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/api
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Core {

	private $id;
	private $type;

	/**
	 * API Key
	 *
	 * @var    string
	 */
	private $key;

	/**
	 * API URL
	 *
	 * @var    string
	 */
	private $url;

	/**
	 * API URL
	 *
	 * @var    string
	 */
	private $version = '3';

	/**
	 * API default URL
	 *
	 * @var    string
	 */
	private $default_url = 'api.wpmovielibrary.com';

	/**
	 * API internal URL
	 *
	 * @var    string
	 */
	private $internal_url = 'api.themoviedb.org';

	/**
	 * API Scheme
	 *
	 * @var    string
	 */
	private $scheme = 'https';

	/**
	 * API Status Codes
	 * 
	 * @var    array
	 */
	private $status_codes = array();

	/**
	 * API mode
	 * 
	 * @var    boolean
	 */
	private $internal;

	/**
	 * API Configuration
	 * 
	 * @var    object
	 */
	public $configuration;

	/**
	 * Current instance.
	 *
	 * @since    3.0
	 *
	 * @var      Library
	 */
	public static $instance;

	/**
	 * Define the API class.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function __construct() {

		self::$instance = $this;

		$this->key      = wpmoly_o( 'api-key' );
		$this->scheme   = wpmoly_o( 'api-scheme' );
		$this->internal = wpmoly_o( 'api-internal' );

		// No key, fallback to default
		if ( empty( $this->key ) ) {
			$this->internal = false;
		}

		// load configuration
		$this->configuration = $this->get_configuration();

		/**
		 * API status codes
		 * 
		 * @see https://www.themoviedb.org/documentation/api/status-codes
		 */
		$this->status_codes = array(
			1  => array( 'code' => 'success', 'http' => 200, 'message' => __( 'Success.', 'wpmovielibrary' ) ),
			2  => array( 'code' => 'invalid_service', 'http' => 501, 'message' => __( 'Invalid service: this service does not exist.', 'wpmovielibrary' ) ),
			3  => array( 'code' => 'permission_denied', 'http' => 401, 'message' => __( 'Authentication failed: You do not have permissions to access the service.', 'wpmovielibrary' ) ),
			4  => array( 'code' => 'invalid_format', 'http' => 405, 'message' => __( 'Invalid format: This service doesn\'t exist in that format.', 'wpmovielibrary' ) ),
			5  => array( 'code' => 'invalid_parameters', 'http' => 422, 'message' => __( 'Invalid parameters: Your request parameters are incorrect.', 'wpmovielibrary' ) ),
			6  => array( 'code' => 'invalid_id', 'http' => 404, 'message' => __( 'Invalid id: The pre-requisite id is invalid or not found.', 'wpmovielibrary' ) ),
			7  => array( 'code' => 'invalid_key', 'http' => 401, 'message' => __( 'Invalid API key: You must be granted a valid key.', 'wpmovielibrary' ) ),
			8  => array( 'code' => 'duplicate_entry', 'http' => 403, 'message' => __( 'Duplicate entry: The data you tried to submit already exists.', 'wpmovielibrary' ) ),
			9  => array( 'code' => 'service_offline', 'http' => 503, 'message' => __( 'Service offline: This service is temporarily offline, try again later.', 'wpmovielibrary' ) ),
			10 => array( 'code' => 'suspended_key', 'http' => 401, 'message' => __( 'Suspended API key: Access to your account has been suspended, contact TMDb.', 'wpmovielibrary' ) ),
			11 => array( 'code' => 'internal_error', 'http' => 500, 'message' => __( 'Internal error: Something went wrong, contact TMDb.', 'wpmovielibrary' ) ),
			12 => array( 'code' => 'item_updated', 'http' => 201, 'message' => __( 'The item/record was updated successfully.', 'wpmovielibrary' ) ),
			13 => array( 'code' => 'item_deleted', 'http' => 200, 'message' => __( 'The item/record was deleted successfully.', 'wpmovielibrary' ) ),
			14 => array( 'code' => 'authentication_failed', 'http' => 401, 'message' => __( 'Authentication failed.', 'wpmovielibrary' ) ),
			15 => array( 'code' => 'failed', 'http' => 500, 'message' => __( 'Failed.', 'wpmovielibrary' ) ),
			16 => array( 'code' => 'device_denied', 'http' => 401, 'message' => __( 'Device denied.', 'wpmovielibrary' ) ),
			17 => array( 'code' => 'session_denied', 'http' => 401, 'message' => __( 'Session denied.', 'wpmovielibrary' ) ),
			18 => array( 'code' => 'validation_denied', 'http' => 400, 'message' => __( 'Validation failed.', 'wpmovielibrary' ) ),
			19 => array( 'code' => 'invalid_header', 'http' => 406, 'message' => __( 'Invalid accept header.', 'wpmovielibrary' ) ),
			20 => array( 'code' => 'invalid_date_range', 'http' => 422, 'message' => __( 'Invalid date range: Should be a range no longer than 14 days.', 'wpmovielibrary' ) ),
			21 => array( 'code' => 'not_found', 'http' => 200, 'message' => __( 'Entry not found: The item you are trying to edit cannot be found.', 'wpmovielibrary' ) ),
			22 => array( 'code' => 'invalid_page', 'http' => 400, 'message' => __( 'Invalid page: Pages start at 1 and max at 1000. They are expected to be an integer.', 'wpmovielibrary' ) ),
			23 => array( 'code' => 'invalid_date_format', 'http' => 400, 'message' => __( 'Invalid date: Format needs to be YYYY-MM-DD.', 'wpmovielibrary' ) ),
			24 => array( 'code' => 'request_timeout', 'http' => 504, 'message' => __( 'Your request to the backend server timed out. Try again.', 'wpmovielibrary' ) ),
			25 => array( 'code' => 'request_limit', 'http' => 429, 'message' => __( 'Your request count (#) is over the allowed limit of (40).', 'wpmovielibrary' ) ),
			26 => array( 'code' => 'authentication_required', 'http' => 400, 'message' => __( 'You must provide a username and password.', 'wpmovielibrary' ) ),
			27 => array( 'code' => 'append_limit', 'http' => 400, 'message' => __( 'Too many append to response objects: The maximum number of remote calls is 20.', 'wpmovielibrary' ) ),
			28 => array( 'code' => 'invalid_timezone', 'http' => 400, 'message' => __( 'Invalid timezone: Please consult the documentation for a valid timezone.', 'wpmovielibrary' ) ),
			29 => array( 'code' => 'confirm_required', 'http' => 400, 'message' => __( 'You must confirm this action: Please provide a confirm=true parameter.', 'wpmovielibrary' ) ),
			30 => array( 'code' => 'invalid_credential', 'http' => 401, 'message' => __( 'Invalid username and/or password: You did not provide a valid login.', 'wpmovielibrary' ) ),
			31 => array( 'code' => 'disabled_account', 'http' => 401, 'message' => __( 'Account disabled: Your account is no longer active. Contact TMDb if this is an error.', 'wpmovielibrary' ) ),
			32 => array( 'code' => 'unverified_email', 'http' => 401, 'message' => __( 'Email not verified: Your email address has not been verified.', 'wpmovielibrary' ) ),
			33 => array( 'code' => 'invalid_token', 'http' => 401, 'message' => __( 'Invalid request token: The request token is either expired or invalid.', 'wpmovielibrary' ) ),
			34 => array( 'code' => 'resource_not_found', 'http' => 401, 'message' => __( 'The resource you requested could not be found.', 'wpmovielibrary' ) )
		);
	}

	/**
	 * Singleton.
	 *
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Set current ID.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $id ID value.
	 * @param    string    $source ID source, 'imdb' or 'tmdb'.
	 * 
	 * @return   WP_Error|string|int
	 */
	public function set_id( $id, $source = 'tmdb' ) {

		if ( 'imdb' == $source ) {
			$id = $this->validate_query( strval( $id ), 'imdb' );
		} else if ( 'tmdb' == $source ) {
			$id = $this->validate_query( intval( $id ), 'tmdb' );
		} else {
			$id = new WP_Error( 'invalid_id_type', __( 'Invalid ID type.', 'wpmovielibrary' ), $type );
		}

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		return $this->id = $id;
	}

	/**
	 * Set current type.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Type value.
	 * 
	 * @return   WP_Error|string
	 */
	public function set_type( $type ) {

		$type = $this->validate_type( $type );
		if ( is_wp_error( $type ) ) {
			return $type;
		}

		return $this->type = $type;
	}

	/**
	 * Reset $this->type, $this->id or both.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $data
	 * 
	 * @return   null
	 */
	public function reset( $data = null ) {

		if ( 'id' == $data || is_null( $data ) ) {
			$this->id   = null;
		} else if ( 'type' == $data || is_null( $data ) ) {
			$this->type = 'movie';
		}
	}

	/**
	 * Load API configuration.
	 * 
	 * Use a transient to avoid overquerying the API.
	 * 
	 * @since    3.0
	 * 
	 * @param    boolean    $force Force reloading API configuration instead of load transient.
	 * 
	 * @return   WP_Error|object
	 */
	public function get_configuration( $force = false ) {

		$configuration = get_transient( 'wpmoly-api-configuration' );
		if ( false !== $configuration && true !== $force ) {
			return $configuration;
		}

		$configuration = $this->call( 'configuration' );
		if ( ! is_wp_error( $configuration ) ) {
			set_transient( 'wpmoly-api-configuration', $configuration, WEEK_IN_SECONDS );
		}

		return $configuration;
	}

	/**
	 * Fetch images for a specific type.
	 * 
	 * If $image_type is not set or set to 'both', return all available
	 * backdrops and posters at the same time. The 'language' parameter
	 * should be empty if we want to fetch every available image without
	 * language filtering.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $image_type Image type, either 'backdrops', 'posters' or 'both' (default).
	 * @param    array     $params Query parameters.
	 * 
	 * @return   WP_Error|object
	 */
	protected function _get_images( $image_type = array(), $params = array() ) {

		$image_type = $this->validate_type( $image_type, 'image' );
		if ( is_wp_error( $image_type ) ) {
			return $image_type;
		}

		// Default language empty to fetch all available images
		$params = wp_parse_args( (array) $params, array( 'language' => '' ) );

		$request = $this->call( "{$this->type}/{$this->id}/images", $params );
		if ( isset( $request->$image_type ) ) {
			return $request->$image_type;
		}

		return $request;
	}

	/**
	 * Make sure provided append function are supported.
	 * 
	 * Movies, TVs and Persons support additional requests to fetch
	 * related data like images, alternative titles, releases...
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $append
	 * 
	 * @return   WP_Error|string $type
	 */
	private function validate_append( $append ) {

		$supported_functions = array( 'alternative_titles', 'credits', 'images', 'keywords', 'release_dates', 'videos' );

		if ( ! is_array( $append ) ) {
			$append = explode( ',', $append );
		}

		foreach ( $append as $function ) {
			$function = trim( $function );
			if ( in_array( $function, $supported_functions ) ) {
				$supported[] = $function;
			}
		}

		$supported = implode( ',', $supported );

		return $supported;
	}

	/**
	 * Make sure provided type is supported.
	 * 
	 * Make room for additional types like TV series, seasons, 
	 * episodes, people...
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type
	 * 
	 * @return   WP_Error|string $type
	 */
	protected function validate_type( $type, $data_type = 'movie' ) {

		if ( 'image' == $data_type ) {
			$supported_types = array( 'backdrops', 'posters', 'profiles', 'both' );
			if ( ! in_array( $type, $supported_types ) ) {
				return new WP_Error( 'invalid_image_type', __( 'Invalid image type.', 'wpmovielibrary' ), $type );
			}

			return $type;
		}

		$supported_types = array( 'movie' );
		if ( ! in_array( $type, $supported_types ) ) {
			return new WP_Error( 'invalid_query_type', __( 'Invalid query type.', 'wpmovielibrary' ), $type );
		}

		return $type;
	}

	/**
	 * Validate a search/get query.
	 * 
	 * Identify TMDb/IMDb ID and regular search queries.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query Query
	 * @param    string    $type Query type
	 * 
	 * @return   WP_Error|string
	 */
	protected function validate_query( $query, $type = 'search' ) {

		if ( ! is_string( $query ) && ! is_int( $query ) ) {
			return new WP_Error( 'invalid_query', __( 'Invalid query.', 'wpmovielibrary' ) );
		}

		$query = sanitize_text_field( $query );
		if ( '' == $query ) {
			return new WP_Error( 'empty_query', __( 'Empty query.', 'wpmovielibrary' ) );
		}

		if ( 'imdb' == $type && ! preg_match( '/^(tt\d{5,7})$/i', $query ) ) {
			return new WP_Error( 'invalid_imdb_query', __( 'Invalid query: query is not a valid IMDb ID.', 'wpmovielibrary' ) );
		} else if ( 'tmdb' == $type && ! preg_match( '/^\d{1,6}$/', $query ) ) {
			return new WP_Error( 'invalid_tmdb_query', __( 'Invalid query: query is not a valid TMDb ID.', 'wpmovielibrary' ) );
		}/* else {
			return preg_match( '/^(tt)?(\d{1,6})$/i', $query );
		}*/

		return $query;
	}

	/**
	 * Parse a response from the API.
	 * 
	 * Handle errors and status codes to provide generic results
	 * to the code.
	 * 
	 * @since    3.0
	 * 
	 * @param    mixed    $response Request response
	 * 
	 * @return   WP_Error|array
	 */
	private function parse( $response ) {

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! isset( $response['response'] ) || ! isset( $response['body'] ) ) {
			return new WP_Error( 'unknown_response', __( 'Unknown API response.', 'wpmovielibrary' ) );
		}

		$body = json_decode( $response['body'] );
		if ( ! $body ) {
			return new WP_Error( 'invalid_response', __( 'Invalid API response.', 'wpmovielibrary' ), $body );
		}

		$code    = $response['response']['code'];
		$message = $response['response']['message'];

		// Something wrong just happened
		if ( 200 != $code ) {

			$status_code    = $body->status_code;
			$status_message = $body->status_message;
			$status = $this->status_codes[ $status_code ];

			return new WP_Error( $status['code'], $status['message'] );
		}

		return $body;
	}

	/**
	 * Make calls to the API.
	 * 
	 * Prepare the query a launch a HTTP request.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query Query function
	 * @param    array     $params Query parameters
	 * 
	 * @return   WP_Error|array
	 */
	protected function call( $query, $params = array() ) {

		$query  = (string) $query;
		$params = (array) $params;

		if ( ! empty( $params['append_to_response'] ) ) {
			$params['append_to_response'] = $this->validate_append( $params['append_to_response'] );
		}

		if ( $this->internal ) {
			$url = "{$this->scheme}://{$this->internal_url}/{$this->version}/$query";
			$params['api_key'] = $this->key;
		} else {
			$url = "http://{$this->default_url}/$query";
		}

		$args = http_build_query( $params, '', '&' );
		$request = wp_remote_get( "$url?$args", array(
			'headers' => array( 'Accept' => 'application/json' )
		) );

		$response = $this->parse( $request );
		if ( is_wp_error( $response ) ) {
			$response->add_data( compact( 'query', 'url', 'params' ) );
		}

		// debug
		// print_r( $response );

		return $response;
	}
}
