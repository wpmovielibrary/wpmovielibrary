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
	 * Get the metadata value.
	 * 
	 * This method overrides Metadata::get_meta_value() to apply custom date
	 * formats if needed.
	 * 
	 * @since    3.0
	 * 
	 * @return   mixed
	 */
	protected function get_meta_value() {

		// Hard set key
		$this->attributes['key'] = 'local_release_date';

		// Get Movie ID
		$post_id = $this->get_movie_id();

		// Get value
		$value = get_movie_meta( $post_id, $this->attributes['key'], $single = true );
		if ( empty( $value ) ) {
			return $value;
		}

		if ( ! empty( $this->attributes['format'] ) ) {
			$value = date_i18n( $this->attributes['format'], strtotime( $value ) );
		}

		return $value;
	}
}
