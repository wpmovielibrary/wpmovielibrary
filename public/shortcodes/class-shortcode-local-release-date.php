<?php
/**
 * Define the Local Release Date Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
 */

namespace wpmoly\Shortcodes;

use wpmoly\Core\PublicTemplate as Template;

/**
 * Local Release Date Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
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
	 * Shortcode aliases
	 * 
	 * @var    array
	 */
	protected static $aliases = array();

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
