<?php
/**
 * Define the Local Release Date Shortcode class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\shortcodes;

use wpmoly\templates\Front as Template;

/**
 * Local Release Date Shortcode class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Local_Release_Date extends Release_Date {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 *
	 * @var string
	 */
	public static $name = 'movie_local_release_date';

	/**
	 * Shortcode aliases
	 *
	 * @var array
	 */
	protected static $aliases = array();

	/**
	 * Build the Shortcode.
	 *
	 * @since 3.0.0
	 */
	protected function make() {

		parent::make();

		// Hard set key
		$this->attributes['key'] = 'local_release_date';
	}
}
