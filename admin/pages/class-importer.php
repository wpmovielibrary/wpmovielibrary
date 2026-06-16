<?php
/**
 * Define the importer admin page class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary\Admin;

/**
 * Render the plugin importer page.
 *
 * Single-action class: instantiated on demand by Backstage and invoked
 * directly. Minimal stub for v6.0.0 — the importer React app mounts itself
 * client-side, so the page only needs to output its container template.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Importer {

	use Renderable;

	/**
	 * Render the importer page.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function __invoke() {

		if ( ! file_exists( WPMOLY_PATH . 'admin/templates/importer.php' ) ) {
			return;
		}

		// Only `plugin_page` is needed: the shared layout uses it to flag the
		// active nav item. `hook_suffix`/`post_type` only drive the Movies tab
		// highlight and must stay at their empty defaults here.
		$this->render( 'importer', [
			'plugin_page' => 'wpmovielibrary-importer',
		] );
	}
}
