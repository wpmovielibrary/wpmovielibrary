<?php
/**
 * WPMovieLibrary Cache Class extension.
 * 
 * Manage WPMovieLibrary settings
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Cache' ) ) :

	/**
	 * WPML Cache class
	 *
	 * @package WPMovieLibrary
	 * @author  Charlie MERLAND <charlie@caercam.org>
	 */
	class WPML_Cache extends WPML_Module {

		/**
		 * Constructor
		 *
		 * @since    1.2
		 */
		public function __construct() {

			$this->register_hook_callbacks();

			if ( is_admin() || ( ! WPML_Settings::cache__db_caching() && ! WPML_Settings::cache__html_caching() ) || ! WPML_Settings::cache__caching_time() )
				return false;
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.2
		 */
		public function register_hook_callbacks() {

			add_filter( 'wpml_cache_name', __CLASS__ . '::wpml_cache_name', 10, 2 );
		}

		/**
		 * Cache data using Transient API
		 * 
		 * @param    string    $transient Transient name
		 * @param    mixed     $value Transient value
		 * 
		 * @return   boolean   True if transient stored, false else
		 */
		public static function set( $transient, $value ) {

			if ( empty( $value ) || is_null( $value ) || '' == $value )
				return false;

			$set = set_transient( 'wpml_cache_' . $transient, $value );

			return $set;
		}

		/**
		 * Get a cached data using Transient API
		 * 
		 * @param    string    $transient Transient name
		 * 
		 * @return   mixed     Cached value
		 */
		public static function get( $transient ) {

			return get_transient( 'wpml_cache_' . $transient );
		}

		/**
		 * Delete a cached data using Transient API
		 * 
		 * @param    string    $transient Transient name
		 * 
		 * @return   boolean   true if successful, false otherwise
		 */
		public static function delete( $transient ) {

			return delete_transient( 'wpml_cache_' . $transient );
		}

		/**
		 * Cache outputs.
		 * 
		 * Uses an anonymous function to execute and get the result of the
		 * code to cache; if $echo is set to true, just display the result.
		 * If $echo is set to false, return the result.
		 * 
		 * @since    1.2
		 * 
		 * @param    string      the key to indicate the value
		 * @param    function    anonymous function executing the code to cache
		 * @param    boolean     should we echo the result of just return it?
		 * 
		 * @return   mixed
		 */
		public static function output( $name, $function, $echo = false ) {

			// Cache is disabled, or user is logged in but user cache is disabled
			if ( ! WPML_Settings::cache__caching() || ( is_user_logged_in() && ! WPML_Settings::cache__user_caching() ) )
				return self::output_callback( $function, $echo );

			$expire = WPML_Settings::cache__caching_time();
			if ( ! $expire )
				return false;

			$name = 'wpml_cache_' . $name;
			$output = get_transient( $name );

			if ( ! empty( $output ) )
				return $output;

			$output = self::output_callback( $function, $echo );

			set_transient( $name, $output, $expire );

			return $output;
		}

		/**
		 * Cache outputs callback.
		 * 
		 * Used to handle the cache callback function depending on the
		 * wanted output, echo or return.
		 * 
		 * @since    1.2
		 * 
		 * @param    function    anonymous function executing the code to cache
		 * @param    boolean     should we echo the result of just return it?
		 * 
		 * @return   mixed       return a string if $echo set to true and callback work, false if callback is bogus, void if valid callback
		 */
		private static function output_callback( $function, $echo = false ) {

			if ( ! is_callable( $function ) )
				return false;

			if ( true === $echo ) {
				ob_start();
				call_user_func( $function );
				$output = ob_get_clean();
				echo $output;
				return;
			}
			else
				$output = call_user_func( $function );

			return $output;
		}

		/**
		 * General method for cache cleaning.
		 * 
		 * @since    1.2
		 * 
		 * @return   string|WP_Error    Result notification or WP_Error
		 */
		public static function empty_cache() {

			global $wpdb;

			$transient = self::clean_transient( null, $force = true );

			if ( false === $transient )
				return new WP_Error( 'transient_error', sprintf( __( 'An error occured when trying to delete transients: %s', 'wpmovielibrary' ), $wpdb->last_error ) );
			else if ( ! $transient )
				return __( 'No transient found.', 'wpmovielibrary' );
			else if ( $transient )
				return sprintf( _n( '1 transient deleted', '%s transients deleted.', $transient, 'wpmovielibrary' ), $transient );
		}

		/**
		 * Handle Transients cleaning. Mainly used for deactivation and
		 * uninstallation actions, and occasionally manual cache cleaning.
		 * 
		 * When deactivating/uninstalling, delete all Plugin's related
		 * movie transient, depending on the Plugin settings.
		 * 
		 * @since    1.2
		 * 
		 * @param    string     $action Are we deactivating or uninstalling the plugin?
		 * @param    boolean    $force Force cleaning
		 * 
		 * @return   int        $result Number of deleted rows
		 */
		public static function clean_transient( $action, $force = false ) {

			global $wpdb, $_wp_using_ext_object_cache;

			$force = ( true === $force );
			$result = 0;

			if ( ! $force ) {
				$_action = get_option( 'wpml_settings' );
				if ( ! $_action || ! isset( $_action[ $action ] ) || ! isset( $_action[ $action ]['cache'] ) )
					return false;

				$action = $_action[ $action ]['cache'];
				if ( is_array( $action ) )
					$action = $action[0];
			}

			if ( $force || ( ! $_wp_using_ext_object_cache && 'empty' == $action ) ) {
				if ( 'clean' == $action )
					$where = 'option_name LIKE "_transient_%wpml_cache_%"';
				else
					$where = 'option_name LIKE "_transient_%wpml%"';

				$result = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE {$where}" );
				$wpdb->query( "OPTIMIZE TABLE {$wpdb->options}" );
			}

			return $result;
		}

		/**
		 * Apply a generic formatting to the cached data names.
		 * 
		 * If $extra is available and an Post ID is set it will be used
		 * to generate a unique name for the cached data; if no ID is
		 * passed an hash will be generated from the $extra data and added
		 * to the name.
		 * 
		 * @since    1.2
		 * 
		 * @param    string     $name Cached data name
		 * @param    boolean    $extra Cached data extra information
		 * 
		 * @return   int        $result Number of deleted rows
		 */
		public static function wpml_cache_name( $name, $extra = null ) {

			if ( is_null( $extra ) )
				return $name;

			if ( is_array( $extra ) || is_object( $extra ) )
				$extra = serialize( $extra );

			$name .= '_' . substr( md5( $extra ), 0, 8 );

			return $name;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.2
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.2
		 */
		public function deactivate() {}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.2
		 */
		public static function uninstall() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.2
		 */
		public function init() {}

	}

endif;