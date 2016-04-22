<?php
/**
 * Define the poster class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Node;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Poster extends Image {}

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class DefaultPoster extends DefaultImage {

	/**
	 * Set a handful of useful values for different sizes of the image.
	 * 
	 * @since    3.0
	 * 
	 * @return   object
	 */
	public function set_defaults() {

		$sizes = array(
			'thumbnail' => (object) array(
				'file'   => 'poster-thumbnail.jpg',
				'url'    => WPMOLY_URL . 'public/img/poster-thumbnail.jpg',
				'width'  => 150,
				'height' => 150,
			),
			'xxsmall' => (object) array(
				'file'   => 'poster-xxsmall.jpg',
				'url'    => WPMOLY_URL . 'public/img/poster-xxsmall.jpg',
				'width'  => 92,
				'height' => 138
			),
			'xsmall' => (object) array(
				'file'   => 'poster-xsmall.jpg',
				'url'    => WPMOLY_URL . 'public/img/poster-xsmall.jpg',
				'width'  => 154,
				'height' => 231
			),
			'small' => (object) array(
				'file'   => 'poster-small.jpg',
				'url'    => WPMOLY_URL . 'public/img/poster-small.jpg',
				'width'  => 185,
				'height' => 278
			),
			'medium' => (object) array(
				'file'   => 'poster-medium.jpg',
				'url'    => WPMOLY_URL . 'public/img/poster-medium.jpg',
				'width'  => 342,
				'height' => 513
			),
			'large' => (object) array(
				'file'   => 'poster-large.jpg',
				'url'    => WPMOLY_URL . 'public/img/poster-large.jpg',
				'width'  => 500,
				'height' => 750
			),
			'full' => (object) array(
				'file'   => 'poster-full.jpg',
				'url'    => WPMOLY_URL . 'public/img/poster-full.jpg',
				'width'  => 780,
				'height' => 1170
			),
			'original' => (object) array(
				'file'   => 'poster.jpg',
				'url'    => WPMOLY_URL . 'public/img/poster.jpg',
				'width'  => 2000,
				'height' => 3000
			)
		);

		/**
		 * Filter default poster sizes
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_sizes
		 */
		return $this->sizes = apply_filters( 'wpmoly/filter/default_poster/sizes', (object) $sizes );
	}

	/**
	 * Get default poster path for a specific size.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $size Backdrop size
	 * 
	 * @return   string
	 */
	public static function get_default_path( $size ) {

		$poster = new static;

		return isset( $poster->$size ) ? WPMOLY_PATH . 'public/img/' . $poster->$size : null;
	}

	/**
	 * Get default poster URL for a specific size.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $size Backdrop size
	 * 
	 * @return   string
	 */
	public static function get_default_url( $size ) {

		$poster = new static;

		return isset( $poster->$size ) ? WPMOLY_URL . 'public/img/' . $poster->$size : null;
	}

	/**
	 * Get default poster path or URL for a specific size.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $size Backdrop size
	 * @param    string    $type 'url' or 'path'
	 * 
	 * @return   string
	 */
	public static function get_default( $size, $type = 'url' ) {

		return 'path' === $type ? self::get_default_path( $size ) : self::get_default_url( $size );
	}

}
