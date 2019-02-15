<?php
/**
 * Define the Default Backdrop class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\images;

/**
 * Default class for empty Backdrop instances.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Default_Backdrop extends Image {

	/**
	 * Default Backdrop instance.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Default_Backdrop
	 */
	private static $instance;

	/**
	 * Get a Default Backdrop instance.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Default_Backdrop
	 */
	final public static function get_instance( $unused = null ) {

		if ( isset( self::$instance ) ) {
			return self::$instance;
		}

		self::$instance = new static;

		return self::$instance;
	}

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

		$sizes = array(
			'thumbnail' => (object) array(
				'file'   => 'backdrop-thumbnail.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/backdrop-thumbnail.jpg',
				'width'  => 150,
				'height' => 150,
			),
			'medium' => (object) array(
				'file'   => 'backdrop-medium.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/backdrop-medium.jpg',
				'width'  => 300,
				'height' => 200,
			),
			'large' => (object) array(
				'file'   => 'backdrop-large.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/backdrop-large.jpg',
				'width'  => 780,
				'height' => 520,
			),
			'full' => (object) array(
				'file'   => 'backdrop-full.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/backdrop-full.jpg',
				'width'  => 1280,
				'height' => 853,
			),
			'original' => (object) array(
				'file'   => 'backdrop.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/backdrop.jpg',
				'width'  => 1920,
				'height' => 1280,
			),
		);

		/**
		 * Filter default backdrop sizes
		 *
		 * @since 3.0.0
		 *
		 * @param array $default_sizes
		 */
		$this->sizes = apply_filters( 'wpmoly/filter/default_backdrop/sizes', (object) $sizes );

		return $this->sizes;
	}
}
