<?php
/**
 * WPMovieLibrary Settings Class extension.
 * 
 * Manage WPMovieLibrary settings
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

require_once( plugin_dir_path( __FILE__ ) . 'wpml-config.php' );

if ( ! class_exists( 'WPML_Settings' ) ) :

	/**
	 * WPML Settings class
	 *
	 * @package WPMovieLibrary
	 * @author  Charlie MERLAND <charlie.merland@gmail.com>
	 */
	class WPML_Settings extends WPML_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			// Load settings or register new ones
			add_action( 'init', __CLASS__ . '::wpml_default_settings', 9 );

			add_action( 'wpml_restore_default_settings', __CLASS__ . '::wpml_restore_default_settings', 10, 0 );
			add_filter( 'wpml_get_available_movie_media', __CLASS__ . '::wpml_get_available_movie_media' );
			add_filter( 'wpml_get_available_movie_status', __CLASS__ . '::wpml_get_available_movie_status' );
			add_filter( 'wpml_get_available_movie_rating', __CLASS__ . '::wpml_get_available_movie_rating' );
		}

		/**
		 * Load WPML default settings if no current settings can be found. Match
		 * the existing settings against the default settings to check their
		 * validity; if the revision is outdated, update the revision field and
		 * add possible missing options.
		 * 
		 * @since    1.0.0
		 *
		 * @param    boolean    $force Force to restore the default settings
		 *
		 * @return   boolean    True if settings were successfully added/updated
		 *                      False if anything went wrong.
		 */
		public static function wpml_default_settings( $force = false ) {

			global $wpml_settings;

			$options = get_option( WPML_SETTINGS_SLUG );
			$status  = false;

			if ( ( false === $options || ! is_array( $options ) ) || true == $force ) {
				delete_option( WPML_SETTINGS_SLUG );
				$status = add_option( WPML_SETTINGS_SLUG, $wpml_settings );
			}
			else if ( ! isset( $options['settings_revision'] ) || WPML_SETTINGS_REVISION > $options['settings_revision'] ) {
				$updated_options = self::wpml_update_settings( $wpml_settings, self::wpml_o() );
				if ( ! empty( $updated_options ) ) {
					$updated_options['settings_revision'] = WPML_SETTINGS_REVISION;
					$status = update_option( WPML_SETTINGS_SLUG, $updated_options );
				}
			}

			return $status;
		}

		/**
		 * Update plugin settings.
		 * 
		 * Compare the current settings with the default settings array to find
		 * newly added options and update the exiting settings. If default settings
		 * differ from the currently stored settings, add the new options to the
		 * latter.
		 *
		 * @since    1.0.0
		 * 
		 * @param    array    $default Default Plugin Settings to be compared to
		 *                             currently stored settings.
		 * @param    array    $options Currently stored settings, supposedly out
		 *                             of date.
		 * 
		 * @return   array             Updated and possibly unchanged settings
		 *                             array if everything went right, empty array
		 *                             if something bad happened.
		 */
		protected static function wpml_update_settings( $default, $options ) {

			if ( ! is_array( $default ) || ! is_array( $options ) )
				return array();

			foreach ( $default as $key => $value ) {
				if ( isset( $options[ $key ] ) && is_array( $value ) )
					$options[ $key ] = self::wpml_update_settings( $value, $default[ $key ] );
				else if ( ! isset( $options[ $key ] ) ) {
					$a = array_search( $key, array_keys( $default ) );
					$options = array_merge(
						array_slice( $options, 0, $a ),
						array( $key => $value ),
						array_slice( $options, $a )
					);
				}
			}

			return $options;
		}

		/**
		* Restore default settings.
		* 
		* Action Hook to restore the Plugin's default settings.
		* 
		* @since    1.0.0
		*/
		public static function wpml_restore_default_settings() {
			self::wpml_default_settings( $force = true );
		}

		/**
		 * Built-in option finder/modifier
		 * Default behavior with no empty search and value params results in
		 * returning the complete WPML options' list.
		 * 
		 * If a search query is specified, navigate through the options'
		 * array and return the asked option if existing, empty string if it
		 * doesn't exist.
		 * 
		 * If a replacement value is specified and the search query is valid,
		 * update WPML options with new value.
		 * 
		 * Return can be string, boolean or array. If search, return array or
		 * string depending on search result. If value, return boolean true on
		 * success, false on failure.
		 *
		 * @since    1.0.0
		 * 
		 * @param    string        Search query for the option: 'aaa-bb-c'. Default none.
		 * @param    string        Replacement value for the option. Default none.
		 * 
		 * @return   string|boolean|array        option array of string, boolean on update.
		 */
		public static function wpml_o( $search = '', $value = null ) {

			global $wpml_settings;

			$options = get_option( WPML_SETTINGS_SLUG, $wpml_settings );

			if ( '' != $search && is_null( $value ) ) {
				$s = explode( '-', $search );
				$o = $options;
				while ( count( $s ) ) {
					$k = array_shift( $s );
					if ( isset( $o[ $k ] ) )
						$o = $o[ $k ];
					else
						$o = '';
				}
			}
			else if ( '' != $search && ! is_null( $value ) ) {
				$s = explode( '-', $search );
				self::wpml_o_( $options, $s, $value );
				$o = update_option( WPML_SETTINGS_SLUG, $options );
			}
			else {
				$o = $options;
			}

			return $o;
		}

		/**
		 * Built-in option modifier
		 * Navigate through WPML options to find a matching option and update
		 * its value.
		 *
		 * @since    1.0.0
		 * 
		 * @param    array         Options array passed by reference
		 * @param    string        key list to match the specified option
		 * @param    string        Replacement value for the option. Default none
		 */
		protected static function wpml_o_( &$array, $key, $value = '' ) {
			$a = &$array;
			foreach ( $key as $k )
				$a = &$a[ $k ];
			$a = $value;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                         Accessors
		 *
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Return the WPML Collection Taxonomy option status: enabled of not.
		 *
		 * @since    1.0.0
		 *
		 * @return   boolean    Taxonomy status: true if enabled, false if not.
		 */
		public static function wpml_use_collection() {
			return (boolean) ( 1 == self::wpml_o( 'wpml-settings-enable_collection' ) );
		}

		/**
		 * Return the WPML Genre Taxonomy option status: enabled of not.
		 *
		 * @since    1.0.0
		 *
		 * @return   boolean    Taxonomy status: true if enabled, false if not.
		 */
		public static function wpml_use_genre() {
			return (boolean) ( 1 == self::wpml_o( 'wpml-settings-enable_genre' ) );
		}

		/**
		 * Return the WPML Actor Taxonomy option status: enabled of not.
		 *
		 * @since    1.0.0
		 *
		 * @return   boolean    Taxonomy status: true if enabled, false if not.
		 */
		public static function wpml_use_actor() {
			return (boolean) ( 1 == self::wpml_o( 'wpml-settings-enable_actor' ) );
		}

		/**
		 * Return the WPML Taxonomy Autocomplete option status: enabled of not.
		 *
		 * @since    1.0.0
		 *
		 * @return   boolean    Taxonomy Autocomplete status: true if enabled, false if not.
		 */
		public static function wpml_taxonomy_autocomplete() {
			return (boolean) ( 1 == self::wpml_o( 'wpml-settings-taxonomy_autocomplete' ) );
		}

		/**
		 * Return the WPML Caching option status: enabled of not.
		 *
		 * @since    1.0.0
		 *
		 * @return   boolean    Caching status: true if enabled, false if not.
		 */
		public static function wpml_use_cache() {
			return (boolean) ( 1 == self::wpml_o( 'tmdb-settings-caching' ) );
		}

		/**
		 * Get TMDb API if available
		 *
		 * @since    1.0.0
		 */
		public static function wpml_get_api_key() {
			$api_key = self::wpml_o( 'tmdb-settings-APIKey' );
			return ( '' != $api_key ? $api_key : false );
		}

		/**
		 * Get TMDb API if available
		 *
		 * @since    1.0.0
		 */
		public static function wpml_get_api_lang() {
			return self::wpml_o( 'tmdb-settings-lang' );
		}

		/**
		 * Get TMDb API if available
		 *
		 * @since    1.0.0
		 */
		public static function wpml_get_api_scheme() {
			return self::wpml_o( 'tmdb-settings-scheme' );
		}

		/**
		 * Are we on TMDb dummy mode?
		 *
		 * @since    1.0.0
		 */
		public static function wpml_is_dummy_api() {
			return ( 1 == self::wpml_o( 'tmdb-settings-dummy' ) ? true : false );
		}

		/**
		 * Return the default Movie Media
		 *
		 * @since    1.0.0
		 *
		 * @return   array    WPML Default Movie Media.
		 */
		public static function wpml_get_default_movie_media() {
			global $wpml_movie_details;
			$default = $wpml_movie_details['movie_media']['default'];
			return $default;
		}

		/**
		 * Return the default Movie Status
		 *
		 * @since    1.0.0
		 *
		 * @return   array    WPML Default Movie Status.
		 */
		public static function wpml_get_default_movie_status() {
			global $wpml_movie_details;
			$default = $wpml_movie_details['movie_status']['default'];
			return $default;
		}

		/**
		 * Return available Movie Media
		 *
		 * @since    1.0.0
		 *
		 * @return   array    WPML Default Movie Media.
		 */
		public static function wpml_get_available_movie_media() {

			global $wpml_movie_details;

			$media = array();
			$items = $wpml_movie_details['movie_media']['options'];

			foreach ( $items as $slug => $title )
				$media[ $slug ] = $title;

			return $media;
		}

		/**
		 * Return available Movie Status
		 *
		 * @since    1.0.0
		 *
		 * @return   array    WPML Available Movie Status.
		 */
		public static function wpml_get_available_movie_status() {

			global $wpml_movie_details;

			$statuses = array();
			$items = $wpml_movie_details['movie_status']['options'];

			foreach ( $items as $slug => $title )
				$statuses[ $slug ] = $title;

			return $statuses;
		}

		/**
		 * Return available Movie Rating
		 *
		 * @since    1.0.0
		 *
		 * @return   array    WPML Available Movie Rating.
		 */
		public static function wpml_get_available_movie_rating() {

			global $wpml_movie_details;

			$ratings = array();
			$items = $wpml_movie_details['movie_rating']['options'];

			foreach ( $items as $slug => $title )
				$rating[ $slug ] = $title;

			return $rating;
		}

		/**
		 * Return all supported Movie Details fields
		 *
		 * @since    1.0.0
		 *
		 * @return   array    WPML Supported Movie Details fields.
		 */
		public static function wpml_get_supported_movie_details( $type = null ) {

			global $wpml_movie_details;

			if ( ! is_null( $type ) && isset( $wpml_movie_details[ $type ] ) )
				return $wpml_movie_meta[ $type ];

			$items = array();
			foreach ( $wpml_movie_details as $slug => $details )
				$items[ $slug ] = $details;

			return $items;
		}

		/**
		 * Return all supported Movie Meta fields
		 *
		 * @since    1.0.0
		 *
		 * @return   array    WPML Supported Movie Meta fields.
		 */
		public static function wpml_get_supported_movie_meta( $type = null, $merge = true ) {

			global $wpml_movie_meta;

			if ( is_null( $type ) && false === $merge )
				return $wpml_movie_meta;
			else if ( ! is_null( $type ) && ! $merge && isset( $wpml_movie_meta[ $type ] ) )
				return $wpml_movie_meta[ $type ]['data'];
			else
				return array_merge( $wpml_movie_meta['meta']['data'], $wpml_movie_meta['crew']['data'] );
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;