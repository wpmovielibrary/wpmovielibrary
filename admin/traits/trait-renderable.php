<?php
/**
 * Define the Renderable trait.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary\Admin;

use WPMovieLibrary\Support\Template;

/**
 * Shared rendering helper for admin page classes.
 *
 * Provides a single `render()` method that spins up the template engine and
 * outputs a compiled template, so the single-action page classes don't each
 * duplicate the engine wiring.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
trait Renderable {

	/**
	 * Render an admin template and output the result.
	 *
	 * @since 6.0.0
	 *
	 * @access protected
	 *
	 * @param string $template Template name (dot or slash notation, no extension).
	 * @param array  $data     Variables passed to the template.
	 */
	protected function render( string $template, array $data = [] ) : void {

		require_once WPMOLY_PATH . 'includes/support/class-template.php';

		$engine = new Template(
			template_dir: WPMOLY_PATH . 'admin/templates',
			cache_dir: wp_upload_dir()['basedir'] . '/wpmovielibrary/cache',
			cache: false
		);

		echo $engine->render( $template, $data );
	}
}
