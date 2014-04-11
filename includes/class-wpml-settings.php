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
		 * Public accessor for every WPML settings
		 * 
		 * Allow to request WPML Settings using 'Class::setting_name' written
		 * calls. If the name passed matches a method it will be called and
		 * returned.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    $name Name of the wanted property/method
		 * @param    array     $arguments Arguments to pass to the mathod
		 * 
		 * @return   mixed     Wanted function's return value
		 */
		public static function __callStatic( $name, $arguments ) {

			if ( method_exists( __CLASS__, $name ) )
				return call_user_func_array( __CLASS__ . "::$name", $arguments );
			
			$name = str_replace( '__', '-', $name );
			$settings = self::wpml_o( $name );

			if ( 1 === $settings || '1' === $settings )
				$settings = true;
			else if ( 0 === $settings || '0' === $settings )
				$settings = false;
			else
				$settings = $settings;

			if ( is_array( $settings ) && 1 == count( $settings ) )
				$settings = $settings[0];

			return $settings;
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			// Load settings or register new ones
			add_action( 'init', __CLASS__ . '::get_default_settings', 9 );

			add_action( 'wpml_restore_default_settings', __CLASS__ . '::restore_default_settings', 10, 0 );
			add_filter( 'wpml_get_available_movie_media', __CLASS__ . '::get_available_movie_media' );
			add_filter( 'wpml_get_available_movie_status', __CLASS__ . '::get_available_movie_status' );
			add_filter( 'wpml_get_available_movie_rating', __CLASS__ . '::get_available_movie_rating' );
		}

		/**
		 * Retrieves all of the settings from the database
		 *
		 * @since    1.0.0
		 *
		 * @return   array
		 */
		public static function get_settings() {

			$settings = shortcode_atts(
				self::get_default_settings(),
				get_option( WPML_SETTINGS_SLUG, array() )
			);

			return $settings;
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
		public static function get_default_settings( $force = false ) {

			global $wpml_settings;

			$default_settings = apply_filters( 'wpml_summarize_settings', $wpml_settings );

			return $default_settings;
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
		protected static function update_settings() {

			$options = get_option( WPML_SETTINGS_SLUG );
			$status  = false;

			if ( ( false === $options || ! is_array( $options ) ) || true == $force ) {
				delete_option( WPML_SETTINGS_SLUG );
				$status = add_option( WPML_SETTINGS_SLUG, $default_settings );
			}
			else if ( ! isset( $options['settings_revision'] ) || WPML_SETTINGS_REVISION > $options['settings_revision'] ) {
				$updated_options = shortcode_atts( $default_settings, $wpml_settings );
				if ( ! empty( $updated_options ) ) {
					$updated_options['settings_revision'] = WPML_SETTINGS_REVISION;
					$status = update_option( WPML_SETTINGS_SLUG, $updated_options );
				}
			}

			return $status;
		}

		/**
		 * Restore default settings.
		 * 
		 * Action Hook to restore the Plugin's default settings.
		 * 
		 * @since    1.0.0
		 */
		public static function restore_default_settings() {
			self::get_default_settings( $force = true );
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
		public static function wpml_o( $search = '' ) {

			global $wpml_settings;

			$default_settings = apply_filters( 'wpml_summarize_settings', $wpml_settings );
			$options = get_option( WPML_SETTINGS_SLUG, $default_settings );

			if ( '' != $search ) {
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
		 * Return the default Movie Media
		 *
		 * @since    1.0.0
		 *
		 * @return   array    WPML Default Movie Media.
		 */
		public static function get_default_movie_media() {
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
		public static function get_default_movie_status() {
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
		public static function get_available_movie_media() {

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
		public static function get_available_movie_status() {

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
		public static function get_available_movie_rating() {

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
		public static function get_supported_movie_details( $type = null ) {

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
		public static function get_supported_movie_meta( $type = null, $merge = true ) {

			global $wpml_movie_meta;

			if ( is_null( $type ) && false === $merge )
				return $wpml_movie_meta;
			else if ( ! is_null( $type ) && ! $merge && isset( $wpml_movie_meta[ $type ] ) )
				return $wpml_movie_meta[ $type ]['data'];
			else
				return array_merge( $wpml_movie_meta['meta']['data'], $wpml_movie_meta['crew']['data'] );
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;