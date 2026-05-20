<?php
/**
 * Define the plugin activator class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

/**
 * Run the plugin activation tasks.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Activator {

	/**
	 * Run the activation tasks.
	 *
	 * Explicitly register post types and taxonomies so that
	 * flush_rewrite_rules() can catch permalinks properly.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access public
	 */
	public static function activate() {

		require_once WPMOLY_PATH . 'includes/helpers.php';
		require_once WPMOLY_PATH . 'includes/class-post-types.php';
		require_once WPMOLY_PATH . 'includes/class-taxonomies.php';

		Post_Types::get_instance()->register();
		Taxonomies::get_instance()->register();

		self::update_version();

		flush_rewrite_rules();
	}

	/**
	 * Update the plugin version.
	 * 
	 * @since 6.0.0
	 * 
	 * @static
	 * @access private
	 */
	private static function update_version() {

		$db_version = get_option( 'wpmoly_version' );
		if ( version_compare( $db_version, WPMOLY_VERSION, '<' ) ) {
			// Save new version.
			update_option( 'wpmoly_version', WPMOLY_VERSION );
		}
	}
}