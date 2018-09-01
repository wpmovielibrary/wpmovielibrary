<?php
/**
 * Define the Backdrop class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\images;

/**
 * Node class to handle Backdrop instances.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Backdrop extends Image {

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

		$default = Default_Backdrop::get_instance();
		foreach ( $this->sizes as $size => $img ) {
			if ( empty( $img ) && ! empty( $default->sizes->$size ) ) {
				$this->sizes->$size = $default->sizes->$size;
			}
		}

		return $this->sizes;
	}

}
