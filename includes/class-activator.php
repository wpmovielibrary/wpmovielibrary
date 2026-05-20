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
	 * @since 6.0.0
	 * 
	 * @static
	 * @access public
	 */
	public static function activate() {

		self::seed_default_terms();
		self::update_version();

		flush_rewrite_rules();
	}

	/**
	 * Seed the default terms.
	 *
	 * Loaded explicitly because the activation hook fires before
	 * `plugins_loaded`, so `rehearsal()` has not yet pulled in
	 * `helpers.php` or registered the taxonomies.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 */
	private static function seed_default_terms() {

		require_once WPMOLY_PATH . 'includes/helpers.php';
		require_once WPMOLY_PATH . 'includes/class-taxonomies.php';

		Taxonomies::get_instance()->register();

		$defaults = config( 'defaults', [] );

		foreach ( $defaults as $taxonomy => $terms ) {
			$taxonomy_slug = "wpmoly_movie_{$taxonomy}";
			foreach ( $terms as $slug => $label ) {
				$term_slug = sanitize_title( $slug );
				if ( ! term_exists( $term_slug, $taxonomy_slug ) ) {
					if ( 'rating' === $taxonomy ) {
						wp_insert_term( $slug, $taxonomy_slug, [ 'slug' => $term_slug, 'description' => $label ] );
					} else {
						wp_insert_term( $label, $taxonomy_slug, [ 'slug' => $term_slug ] );
					}
				}
			}
		}
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