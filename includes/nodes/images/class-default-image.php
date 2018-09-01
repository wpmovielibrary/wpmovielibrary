<?php
/**
 * Define the DefaultImage class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\images;

/**
 * Generic Singleton Node class to handle empty Backdrop and Poster instances.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Default_Image extends Image {

	/**
	 * Default Poster instance.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var array
	 */
	private static $instance;

	/**
	 * Make the Image.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function make() {

		$this->attachment = false;

		$this->set_defaults();
	}

	/**
	 * Get a Default Poster instance.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Default_Poster
	 */
	final public static function get_instance( $unused = null ) {

		if ( isset( self::$instance ) ) {
			return self::$instance;
		}

		self::$instance = new static;

		return self::$instance;
	}
}
