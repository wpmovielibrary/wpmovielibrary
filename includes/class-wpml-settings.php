<?php
/**
 * WPMovieLibrary Settings Class extension.
 * 
 * Manage WPMovieLibrary settings
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
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
	 * @author  Charlie MERLAND <charlie@caercam.org>
	 */
	class WPML_Settings extends WPML_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0
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
		 * @since    1.0
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
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_filter( 'wpml_get_available_movie_media', __CLASS__ . '::get_available_movie_media' );
			add_filter( 'wpml_get_available_movie_status', __CLASS__ . '::get_available_movie_status' );
			add_filter( 'wpml_get_available_movie_rating', __CLASS__ . '::get_available_movie_rating' );
		}

		/**
		 * Return the plugin settings.
		 *
		 * @since    1.0
		 *
		 * @return   array    Plugin Settings
		 */
		public static function get_settings() {

			$settings = get_option( WPML_SETTINGS_SLUG, array() );
			if ( empty( $settings ) || ! isset( $settings[ WPML_SETTINGS_REVISION_NAME ] ) || $settings[ WPML_SETTINGS_REVISION_NAME ] < WPML_SETTINGS_REVISION )
				self::update_settings();

			$settings = shortcode_atts(
				self::get_default_settings(),
				$settings
			);

			return $settings;
		}

		/**
		 * Load WPML default settings. If no current settings can be found,
		 * or if existing settings are outdated, update.
		 * 
		 * @since    1.0
		 *
		 * @return   array    The Plugin Settings
		 */
		public static function get_default_settings() {

			global $wpml_settings;

			if ( is_null( $wpml_settings ) )
				require( WPML_PATH . 'includes/wpml-config.php' );

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
		 * @since    1.0
		 *
		 * @param    boolean    $force Force to restore the default settings
		 * 
		 * @return   boolean    Updated status: success or failure
		 */
		protected static function update_settings( $force = false ) {

			$default_settings = self::get_default_settings();
			$settings = get_option( WPML_SETTINGS_SLUG );
			$status  = false;

			if ( ( false === $settings || ! is_array( $settings ) ) || true == $force ) {
				delete_option( WPML_SETTINGS_SLUG );
				$status = add_option( WPML_SETTINGS_SLUG, $default_settings, '', 'no' );
			}
			else if ( ! isset( $settings[ WPML_SETTINGS_REVISION_NAME ] ) || WPML_SETTINGS_REVISION > $settings[ WPML_SETTINGS_REVISION_NAME ] ) {
				$updated_settings = self::merge_settings( $settings, $default_settings );
				if ( ! empty( $updated_settings ) ) {
					$updated_settings[ WPML_SETTINGS_REVISION_NAME ] = WPML_SETTINGS_REVISION;
					delete_option( WPML_SETTINGS_SLUG );
					$status = add_option( WPML_SETTINGS_SLUG, $updated_settings, '', 'no' );
				}
			}

			return $status;
		}

		/**
		 * Filter the submitted settings to detect unsupported data.
		 * 
		 * Most fields can be tested easily, but the default movie meta
		 * and details need a for specific test using array_intersect()
		 * to avoid storing unsupported values.
		 * 
		 * Settings submitted as array when there's no use to are converted
		 * to simpler types.
		 *
		 * @since    1.0
		 * 
		 * @param    array    $settings Settings array to filter
		 * @param    array    $defaults Default Settings to match against submitted settings
		 * 
		 * @return   array    Filtered Settings
		 */
		protected static function validate_settings( $settings, $defaults = array() ) {

			require_once( WPML_PATH . 'includes/wpml-config.php' );

			$defaults = ( ! empty( $defaults ) ? $defaults : self::get_default_settings() );
			$_settings = array();

			if ( is_null( $settings ) || ! is_array( $settings ) )
				return $settings;

			// Loop through settings
			foreach ( $settings as $slug => $setting ) {

				// Is the setting valid?
				if ( isset( $defaults[ $slug ] ) ) {

					if ( in_array( $slug, array( 'default_movie_meta', 'default_movie_details' ) ) ) {
						$allowed = array_keys( call_user_func( __CLASS__ . '::get_supported_' . str_replace( 'default_', '', $slug ) ) );
						$_settings[ $slug ] = array_intersect( $setting, $allowed );
					}
					else {
						if ( is_array( $setting ) && 1 == count( $setting ) )
							$setting = $setting[0];

						if ( is_array( $setting ) )
							$setting = self::validate_settings( $setting, $defaults[ $slug ] );
						else if ( is_numeric( $setting ) )
							$setting = filter_var( $setting, FILTER_VALIDATE_INT );
						else
							$setting = sanitize_text_field( $setting );
						$_settings[ $slug ] = $setting;
					}
				}
			}

			return $_settings;
		}

		/**
		 * Merge the default Settings with the current one. This is used
		 * only when updating the Plugin Settings to take into account
		 * the potentially new settings added by the last Plugin update.
		 * 
		 * This method is pretty similar to validate_settings(), the main
		 * difference being that merge_settings() loops through the default
		 * settings array, adding new key/value when needing, when the
		 * validate_settings() loops through the existing settings to
		 * avoid storing invalid settings.
		 * 
		 * @since    1.0
		 * 
		 * @param    array    Current Settings array
		 * @param    array    Default Settings array
		 * 
		 * @return   array    The update Settings array
		 */
		private static function merge_settings( $settings, $defaults = array() ) {

			$defaults = ( ! empty( $defaults ) ? $defaults : self::get_default_settings() );
			$_settings = array();

			if ( is_null( $settings ) || ! is_array( $settings ) )
				return $settings;

			// Loop through settings
			foreach ( $defaults as $slug => $setting ) {

				// Is the setting already set?
				if ( isset( $settings[ $slug ] ) ) {
					if ( in_array( $slug, array( 'default_movie_meta', 'default_movie_details' ) ) ) {
						$allowed = array_keys( call_user_func( __CLASS__ . '::get_supported_' . str_replace( 'default_', '', $slug ) ) );
						if ( ! is_array( $default ) )
							$default = array( $default );
						$_settings[ $slug ] = array_intersect( $setting, $allowed );
					}
					else {
						if ( is_array( $setting ) && 1 == count( $setting ) )
							$setting = $setting[0];

						if ( is_array( $setting ) )
							$_settings[ $slug ] = self::merge_settings( $setting, $settings[ $slug ] );
						else
							$_settings[ $slug ] = $setting;
					}
				}
				else {
					$_settings[ $slug ] = $setting;
				}
			}

			$_settings = array_merge( $settings, $_settings );

			return $_settings;
		}

		/**
		 * Delete stored settings.
		 * 
		 * This is irreversible, but shouldn't be used anywhere else than
		 * when uninstalling the plugin.
		 * 
		 * @since    1.0
		 */
		public static function clean_settings() {

			delete_option( 'wpml_settings' );
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
		 * @since    1.0
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

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                         Accessors
		 *
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Return the default Movie Media
		 *
		 * @since    1.0
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
		 * @since    1.0
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
		 * @since    1.0
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
		 * @since    1.0
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
		 * @since    1.0
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
		 * Return all available languages for TMDb API
		 *
		 * @since    1.0.1
		 *
		 * @return   array    Supported languages
		 */
		public static function get_available_languages() {

			global $wpml_settings;

			return $wpml_settings['tmdb']['settings']['lang']['values'];
		}

		/**
		 * Return all available shortcodes
		 *
		 * @since    1.1
		 *
		 * @return   array    Available shortcodes
		 */
		public static function get_available_shortcodes() {

			global $wpml_shortcodes;

			/**
			 * Filter the Shortcodes list to add/remove shortcodes.
			 *
			 * This should be used through Plugins to create additionnal
			 * Shortcodes.
			 *
			 * @since    1.2
			 *
			 * @param    array    $wpml_shortcodes Existing Shortcodes
			 */
			$wpml_shortcodes = apply_filters( 'wpml_filter_shortcodes', $wpml_shortcodes );

			return $wpml_shortcodes;
		}

		/**
		 * Return all supported Movie Details fields
		 *
		 * @since    1.0
		 *
		 * @return   array    WPML Supported Movie Details fields.
		 */
		public static function get_supported_movie_details( $type = null ) {

			global $wpml_movie_details;

			if ( is_null( $wpml_movie_details ) )
				require( WPML_PATH . 'includes/wpml-config.php' );

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
		 * @since    1.0
		 *
		 * @return   array    WPML Supported Movie Meta fields.
		 */
		public static function get_supported_movie_meta( $type = null, $merge = true ) {

			global $wpml_movie_meta;

			if ( is_null( $wpml_movie_meta ) )
				require( WPML_PATH . 'includes/wpml-config.php' );

			if ( is_null( $type ) && false === $merge )
				return $wpml_movie_meta;
			else if ( ! is_null( $type ) && ! $merge && isset( $wpml_movie_meta[ $type ] ) )
				return $wpml_movie_meta[ $type ]['data'];
			else
				return array_merge( $wpml_movie_meta['meta']['data'], $wpml_movie_meta['crew']['data'] );
		}

		/**
		 * Return all supported Shortcodes aliases
		 *
		 * @since    1.1
		 *
		 * @return   array    WPML Supported Shortcodes aliases.
		 */
		public static function get_supported_movie_meta_aliases() {

			global $wpml_movie_meta_aliases;

			return $wpml_movie_meta_aliases;
		}

		/**
		 * Return Metaboxes data
		 *
		 * @since    1.2
		 *
		 * @return   array    WPML Metaboxes
		 */
		public static function get_metaboxes() {

			global $wpml_metaboxes;

			/**
			 * Filter the Metaboxes list to add/remove metaboxes.
			 *
			 * This should be used through Plugins to create additionnal
			 * Metaboxes.
			 *
			 * @since    1.2
			 *
			 * @param    array    $wpml_metaboxes Existing Metaboxes
			 */
			$wpml_metaboxes = apply_filters( 'wpml_filter_metaboxes', $wpml_metaboxes );

			return $wpml_metaboxes;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {

			self::update_settings();
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.0
		 */
		public static function uninstall() {

			self::clean_settings();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}

endif;