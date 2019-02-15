<?php
/**
 * Define the Default Poster class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\images;

/**
 * Default class for empty Poster instances.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Default_Poster extends Image {

	/**
	 * Default Poster instance.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Default_Poster
	 */
	private static $instance;

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
				'file'   => 'poster-thumbnail.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/poster-thumbnail.jpg',
				'width'  => 150,
				'height' => 150,
			),
			'xxsmall' => (object) array(
				'file'   => 'poster-xxsmall.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/poster-xxsmall.jpg',
				'width'  => 92,
				'height' => 138,
			),
			'xsmall' => (object) array(
				'file'   => 'poster-xsmall.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/poster-xsmall.jpg',
				'width'  => 154,
				'height' => 231,
			),
			'small' => (object) array(
				'file'   => 'poster-small.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/poster-small.jpg',
				'width'  => 185,
				'height' => 278,
			),
			'medium' => (object) array(
				'file'   => 'poster-medium.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/poster-medium.jpg',
				'width'  => 342,
				'height' => 513,
			),
			'large' => (object) array(
				'file'   => 'poster-large.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/poster-large.jpg',
				'width'  => 500,
				'height' => 750,
			),
			'full' => (object) array(
				'file'   => 'poster-full.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/poster-full.jpg',
				'width'  => 780,
				'height' => 1170,
			),
			'original' => (object) array(
				'file'   => 'poster.jpg',
				'url'    => WPMOLY_URL . 'public/assets/img/poster.jpg',
				'width'  => 2000,
				'height' => 3000,
			),
		);

		/**
		 * Filter default poster sizes
		 *
		 * @since 3.0.0
		 *
		 * @param array $default_sizes
		 */
		$this->sizes = apply_filters( 'wpmoly/filter/default_poster/sizes', (object) $sizes );

		return $this->sizes;
	}

}
