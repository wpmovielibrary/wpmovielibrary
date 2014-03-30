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
			add_action( 'init', __CLASS__ . '::wpml_default_settings' );
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
			else if ( ! isset( $options['settings_revision'] ) || WPMovieLibrary::SETTINGS_REVISION > $options['settings_revision'] ) {
				$updated_options = WPML_Settings::wpml_update_settings( $wpml_settings, WPML_Settings::wpml_o() );
				if ( ! empty( $updated_options ) ) {
					$updated_options['settings_revision'] = WPMovieLibrary::SETTINGS_REVISION;
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
					$options[ $key ] = WPML_Settings::wpml_update_settings( $value, $default[ $key ] );
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
				WPML_Settings::wpml_o_( $options, $s, $value );
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

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;