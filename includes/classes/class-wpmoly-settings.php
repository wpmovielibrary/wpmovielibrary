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

require_once( WPMOLY_PATH . 'includes/wpmoly-config.php' );

if ( ! class_exists( 'WPMOLY_Settings' ) ) :

	/**
	 * WPMOLY Settings class
	 *
	 * @package WPMovieLibrary
	 * @author  Charlie MERLAND <charlie@caercam.org>
	 */
	class WPMOLY_Settings extends WPMOLY_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {}

		/**
		 * Return the plugin settings.
		 *
		 * @since    1.0
		 *
		 * @return   array    Plugin Settings
		 */
		public static function get_settings() {

			global $wpmoly_settings;

			if ( is_null( $wpmoly_settings ) )
				$wpmoly_settings = get_option( 'wpmoly_settings' );

			return $wpmoly_settings;
		}

		/**
		 * Load default settings.
		 * 
		 * @since    1.0
		 * 
		 * @param    boolean    $minify Should we return only default values?
		 *
		 * @return   array      The Plugin Settings
		 */
		public static function get_default_settings( $minify = false ) {

			global $wpmoly_config;

			if ( is_null( $wpmoly_config ) )
				require WPMOLY_PATH . 'includes/config/wpmoly-settings.php';

			if ( true !== $minify )
				return $wpmoly_config;

			$defauts = array();
			foreach ( $wpmoly_config as $section ) {
				if ( isset( $section['fields'] ) ) {
					foreach ( $section['fields'] as $slug => $field ) {
						if ( 'sorter' == $field['type'] )
							$defauts[ $slug ] = $field['used'];
						else
							$defauts[ $slug ] = $field['default'];
					}
				}
			}

			return $defauts;
		}

		/**
		 * General settings accessor
		 *
		 * @since    1.0
		 * 
		 * @param    string        $setting Requested setting slug
		 * 
		 * @return   mixed         Requested setting
		 */
		public static function get( $setting = '' ) {

			$wpmoly_settings = self::get_settings();

			$shorter = str_replace( 'wpmoly-', '', $setting );
			if ( isset( $wpmoly_settings[ $shorter ] ) )
				return $wpmoly_settings[ $shorter ];

			$longer = "wpmoly-$setting";
			if ( isset( $wpmoly_settings[ $longer ] ) )
				return $wpmoly_settings[ $longer ];

			if ( isset( $wpmoly_settings[ $setting ] ) )
				return $wpmoly_settings[ $setting ];

			return false;
		}

		/**
		 * Check for changes on the URL Rewriting of Taxonomies to
		 * update the Rewrite Rules if needed. We need this to avoid
		 * users to get 404 when they try to access their content if they
		 * didn't previously reload the Dashboard Permalink page.
		 * 
		 * TODO: find a better way to do this
		 * 
		 * @since    2.0
		 * 
		 * @param    array    $new_settings Array containing the new settings
		 * @param    array    $old_settings Array containing the old settings
		 * 
		 * @return   array    Validated settings
		 */
		public static function notify_permalinks_change( $field, $value, $existing_value ) {

			$rewrites = array(
				'wpmoly-translate-movie_rewrite',
				'wpmoly-translate-collection_rewrite',
				'wpmoly-translate-genre_rewrite',
				'wpmoly-translate-actor_rewrite',
			);

			if ( ! isset( $field['id'] ) || ! in_array( $field['id'], $rewrites ) )
				return $value;

			if ( $existing_value == $value )
				return array( 'error' => false, 'value' => $value );

			$field['msg'] = sprintf( __( 'You update the ???? URL rewrite. You should visit <a href="%s">WordPress Permalink</a> page to update the Rewrite rules; you may experience errors when trying to load pages using the new URL if the structures are not update correctly. Tip: you don\'t need to change anything in the Permalink page: simply loading it will update the rules.', 'wpmovielibrary' ), admin_url( '/options-permalink.php' ) );

			return array( 'error' => $field, 'value' => $value );

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
		 * @return   array    WPMOLY Default Movie Media.
		 */
		public static function get_default_movie_media() {
			global $wpmoly_movie_details;
			$default = $wpmoly_movie_details['movie_media']['default'];
			return $default;
		}

		/**
		 * Return the default Movie Status
		 *
		 * @since    1.0
		 *
		 * @return   array    WPMOLY Default Movie Status.
		 */
		public static function get_default_movie_status() {
			global $wpmoly_movie_details;
			$default = $wpmoly_movie_details['movie_status']['default'];
			return $default;
		}

		/**
		 * Return available Movie Media
		 *
		 * @since    1.0
		 *
		 * @return   array    WPMOLY Default Movie Media.
		 */
		public static function get_available_movie_media() {

			global $wpmoly_movie_details;

			$media = array();
			$items = $wpmoly_movie_details['movie_media']['options'];

			foreach ( $items as $slug => $title )
				$media[ $slug ] = $title;

			return $media;
		}

		/**
		 * Return available Movie Status
		 *
		 * @since    1.0
		 *
		 * @return   array    WPMOLY Available Movie Status.
		 */
		public static function get_available_movie_status() {

			global $wpmoly_movie_details;

			$statuses = array();
			$items = $wpmoly_movie_details['movie_status']['options'];

			foreach ( $items as $slug => $title )
				$statuses[ $slug ] = $title;

			return $statuses;
		}

		/**
		 * Return available Movie Rating
		 *
		 * @since    1.0
		 *
		 * @return   array    WPMOLY Available Movie Rating.
		 */
		public static function get_available_movie_rating() {

			global $wpmoly_movie_details;

			$ratings = array();
			$items = $wpmoly_movie_details['movie_rating']['options'];

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

			global $wpmoly_settings;

			return $wpmoly_settings['tmdb']['settings']['lang']['values'];
		}

		/**
		 * Return all available shortcodes
		 *
		 * @since    1.1
		 *
		 * @return   array    Available shortcodes
		 */
		public static function get_available_shortcodes() {

			global $wpmoly_shortcodes;

			/**
			 * Filter the Shortcodes list to add/remove shortcodes.
			 *
			 * This should be used through Plugins to create additionnal
			 * Shortcodes.
			 *
			 * @since    1.2
			 *
			 * @param    array    $wpmoly_shortcodes Existing Shortcodes
			 */
			$wpmoly_shortcodes = apply_filters( 'wpmoly_filter_shortcodes', $wpmoly_shortcodes );

			return $wpmoly_shortcodes;
		}

		/**
		 * Return all supported Movie Details fields
		 *
		 * @since    1.0
		 *
		 * @return   array    WPMOLY Supported Movie Details fields.
		 */
		public static function get_supported_movie_details( $type = null ) {

			global $wpmoly_movie_details;

			if ( is_null( $wpmoly_movie_details ) )
				require( WPMOLY_PATH . 'includes/wpmoly-config.php' );

			if ( ! is_null( $type ) && isset( $wpmoly_movie_details[ $type ] ) )
				return $wpmoly_movie_meta[ $type ];

			$items = array();
			foreach ( $wpmoly_movie_details as $slug => $details )
				$items[ $slug ] = $details;

			return $items;
		}

		/**
		 * Return all supported Movie Meta fields
		 *
		 * @since    1.0
		 *
		 * @return   array    WPMOLY Supported Movie Meta fields.
		 */
		public static function get_supported_movie_meta( $type = null ) {

			global $wpmoly_movie_meta;

			if ( is_null( $wpmoly_movie_meta ) )
				require( WPMOLY_PATH . 'includes/wpmoly-config.php' );

			if ( ! is_null( $type ) ) {
				$meta = array();
				foreach ( $wpmoly_movie_meta as $slug => $data )
					if ( $data['group'] == $type )
						$meta[ $slug ] = $data;

				return $meta;
			}

			return $wpmoly_movie_meta;
		}

		/**
		 * Return all supported Shortcodes aliases
		 *
		 * @since    1.1
		 *
		 * @return   array    WPMOLY Supported Shortcodes aliases.
		 */
		public static function get_supported_movie_meta_aliases() {

			global $wpmoly_movie_meta_aliases;

			return $wpmoly_movie_meta_aliases;
		}

		/**
		 * Return Metaboxes data
		 *
		 * @since    1.2
		 *
		 * @return   array    WPMOLY Metaboxes
		 */
		public static function get_metaboxes() {

			global $wpmoly_metaboxes;

			/**
			 * Filter the Metaboxes list to add/remove metaboxes.
			 *
			 * This should be used through Plugins to create additionnal
			 * Metaboxes.
			 *
			 * @since    1.2
			 *
			 * @param    array    $wpmoly_metaboxes Existing Metaboxes
			 */
			$wpmoly_metaboxes = apply_filters( 'wpmoly_filter_metaboxes', $wpmoly_metaboxes );

			return $wpmoly_metaboxes;
		}

		/**
		 * Return Metaboxes data
		 *
		 * @since    1.2
		 *
		 * @return   array    WPMOLY Metaboxes
		 */
		public static function get_admin_menu() {

			global $wpmoly_admin_menu;

			/**
			 * Filter the Admin menu list to edit/add/remove subpages.
			 *
			 * This should be used through Plugins to create additionnal
			 * subpages.
			 *
			 * @since    1.3
			 *
			 * @param    array    $wpmoly_admin_menu Admin menu
			 */
			$wpmoly_admin_menu = apply_filters( 'wpmoly_filter_admin_menu', $wpmoly_admin_menu );

			return $wpmoly_admin_menu;
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

			delete_option( 'wpmoly_settings' );
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
		 * Set the uninstallation instructions
		 *
		 * @since    1.0
		 */
		public static function uninstall() {

			self::clean_settings();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}

endif;

/**
 * General settings accessor
 *
 * @since    2.0
 * 
 * @param    string        $setting Requested setting slug
 * 
 * @return   mixed         Requested setting
 */
function wpmoly_o( $search ) {

	return WPMOLY_Settings::get( $search );
}