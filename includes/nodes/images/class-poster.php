<?php
/**
 * Define the Poster class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\images;

/**
 * Node class to handle Poster instances.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Poster extends Image {

	/**
	 * Set a handful of useful values for different sizes of the image.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return object
	 */
	public function set_defaults() {

		$this->sizes = parent::set_defaults();

		$default = Default_Poster::get_instance();
		foreach ( $this->sizes as $size => $img ) {
			if ( empty( $img ) && ! empty( $default->sizes->$size ) ) {
				$this->sizes->$size = $default->sizes->$size;
			}
		}

		return $this->sizes;
	}

}
