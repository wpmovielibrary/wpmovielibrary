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
		public function __construct() {

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			if ( $this->has_permalinks_changed() )
				add_action( 'admin_notices', array( $this, 'permalinks_changed_notice' ), 15 );
		}

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
		 * Check for a transient indicating permalinks were changed and
		 * structure not updated.
		 * 
		 * @since    2.0
		 * 
		 * @return   bool|string    False is no change was made or structure updated, changed permalinks option slug else
		 */
		private function has_permalinks_changed() {

			$changed = get_transient( 'wpmoly-permalinks-changed' );
			if ( false === $changed )
				return false;

			return $changed;
		}

		/**
		 * Check for changes on the URL Rewriting of Taxonomies to
		 * update the Rewrite Rules if needed. We need this to avoid
		 * users to get 404 when they try to access their content if they
		 * didn't previously reload the Dashboard Permalink page.
		 * 
		 * @since    2.0
		 * 
		 * @param    array    $field Settings field array
		 * @param    array    $value New setting value
		 * @param    array    $existing_value previous setting value
		 * 
		 * @return   array    Validated setting
		 */
		public static function permalinks_changed( $field, $value, $existing_value ) {

			$rewrites = array(
				'wpmoly-rewrite-movie',
				'wpmoly-rewrite-collection',
				'wpmoly-rewrite-genre',
				'wpmoly-rewrite-actor',
			);

			if ( ! isset( $field['id'] ) || ! in_array( $field['id'], $rewrites ) )
				return $value;

			if ( $existing_value == $value )
				return array( 'error' => false, 'value' => $value );

			$changed = set_transient( 'wpmoly-permalinks-changed', $field['id'] );

			return array( 'value' => $value );

		}

		/**
		 * Show a simple notice for admins to update their permalinks.
		 * 
		 * Hide the notice on Permalinks page, though, to avoid confusion
		 * as it could be interpreted as a failure to update permalinks,
		 * which it is not.
		 * 
		 * @since    2.0
		 */
		public static function permalinks_changed_notice() {

			global $hook_suffix;
			if ( 'options-permalink.php' == $hook_suffix )
				return false;

			echo self::render_template( 'admin/admin-notice.php', array( 'notice' => 'permalinks-changed' ), $require = 'always' );
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

			$settings = self::get_default_settings();

			return $settings['wpmoly-api']['fields']['language']['options'];
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
		 * Return all supported language names for translation
		 *
		 * @since    2.0
		 *
		 * @return   array    Supported languages
		 */
		public static function get_supported_languages() {

			global $wpmoly_languages;

			return $wpmoly_languages;
		}

		/**
		 * Return all available country names for translation
		 *
		 * @since    2.0
		 *
		 * @return   array    Supported languages
		 */
		public static function get_supported_countries() {

			global $wpmoly_countries;

			return $wpmoly_countries;
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
		 * Return Admin Menu config array data
		 *
		 * @since    2.0
		 *
		 * @return   array    WPMOLY Admin Menu array
		 */
		public static function get_admin_menu() {

			global $wpmoly_admin_menu;

			/**
			 * Filter the Admin menu list to edit/add/remove subpages.
			 *
			 * This should be used through Plugins to create additionnal
			 * subpages.
			 *
			 * @since    2.0
			 *
			 * @param    array    $wpmoly_admin_menu Admin menu
			 */
			$wpmoly_admin_menu = apply_filters( 'wpmoly_filter_admin_menu', $wpmoly_admin_menu );

			return $wpmoly_admin_menu;
		}

		/**
		 * Return Admin Bar Menu config array data
		 *
		 * @since    2.0
		 *
		 * @return   array    WPMOLY Admin Bar Menu array
		 */
		public static function get_admin_bar_menu() {

			global $wpmoly_admin_bar_menu;

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
			$wpmoly_admin_bar_menu = apply_filters( 'wpmoly_filter_admin_bar_menu', $wpmoly_admin_bar_menu );

			return $wpmoly_admin_bar_menu;
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