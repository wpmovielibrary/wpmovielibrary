<?php
/**
 * Define the Local Release Date Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcode
 */

namespace wpmoly\Shortcodes;

use wpmoly\Core\PublicTemplate as Template;

/**
 * Local Release Date Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcode
 * @author     Charlie Merland <charlie@caercam.org>
 */
class LocalReleaseDate extends ReleaseDate {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 * 
	 * @var    string
	 */
	public static $name = 'movie_local_release_date';

	/**
	 * Build the Shortcode.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function make() {

		parent::make();

		// Hard set key
		$this->attributes['key'] = 'local_release_date';
	}
}
