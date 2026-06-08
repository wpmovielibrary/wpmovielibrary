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

		$this->render( 'importer', [] );
	}
}
