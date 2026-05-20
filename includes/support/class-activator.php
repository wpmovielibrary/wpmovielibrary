<?php
/**
 * Define the plugin activator class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary\Support;

use WPMovieLibrary\Registrars;

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

		require_once WPMOLY_PATH . 'includes/support/helpers.php';
		require_once WPMOLY_PATH . 'includes/registrars/class-post-types.php';
		require_once WPMOLY_PATH . 'includes/registrars/class-taxonomies.php';

		Registrars\Post_Types::get_instance()->register();
		Registrars\Taxonomies::get_instance()->register();

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