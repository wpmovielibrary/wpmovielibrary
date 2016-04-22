<?php
/**
 * Define the backdrop class.
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
class Backdrop extends Image {}

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class DefaultBackdrop extends DefaultImage {

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
				'file'   => 'backdrop-thumbnail.jpg',
				'url'    => WPMOLY_URL . 'public/img/backdrop-thumbnail.jpg',
				'width'  => 150,
				'height' => 150,
			),
			'medium' => (object) array(
				'file'   => 'backdrop-medium.jpg',
				'url'    => WPMOLY_URL . 'public/img/backdrop-medium.jpg',
				'width'  => 300,
				'height' => 200
			),
			'large' => (object) array(
				'file'   => 'backdrop-large.jpg',
				'url'    => WPMOLY_URL . 'public/img/backdrop-large.jpg',
				'width'  => 780,
				'height' => 520
			),
			'full' => (object) array(
				'file'   => 'backdrop-full.jpg',
				'url'    => WPMOLY_URL . 'public/img/backdrop-full.jpg',
				'width'  => 1280,
				'height' => 853
			),
			'original' => (object) array(
				'file'   => 'backdrop.jpg',
				'url'    => WPMOLY_URL . 'public/img/backdrop.jpg',
				'width'  => 1920,
				'height' => 1280
			)
		);

		/**
		 * Filter default backdrop sizes
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_sizes
		 */
		return $this->sizes = apply_filters( 'wpmoly/filter/default_backdrop/sizes', (object) $sizes );
	}

}
