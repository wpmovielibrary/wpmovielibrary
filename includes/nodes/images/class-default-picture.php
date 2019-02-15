<?php
/**
 * Define the Default Picture class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\images;

/**
 * Default class for empty Picture instances.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Default_Picture extends Image {

	/**
	 * Default Picture instance.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Default_Picture
	 */
	private static $instance;

	/**
	 * Get a Default Picture instance.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Default_Picture
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
				'file'   => 'person-neutral-thumbnail.png',
				'url'    => WPMOLY_URL . 'public/assets/img/person-neutral-thumbnail.png',
				'width'  => 150,
				'height' => 150,
			),
			'xxsmall' => (object) array(
				'file'   => 'person-neutral-xxsmall.png',
				'url'    => WPMOLY_URL . 'public/assets/img/person-neutral-xxsmall.png',
				'width'  => 92,
				'height' => 138,
			),
			'xsmall' => (object) array(
				'file'   => 'person-neutral-xsmall.png',
				'url'    => WPMOLY_URL . 'public/assets/img/person-neutral-xsmall.png',
				'width'  => 154,
				'height' => 231,
			),
			'small' => (object) array(
				'file'   => 'person-neutral-small.png',
				'url'    => WPMOLY_URL . 'public/assets/img/person-neutral-small.png',
				'width'  => 185,
				'height' => 278,
			),
			'medium' => (object) array(
				'file'   => 'person-neutral-medium.png',
				'url'    => WPMOLY_URL . 'public/assets/img/person-neutral-medium.png',
				'width'  => 342,
				'height' => 513,
			),
			'large' => (object) array(
				'file'   => 'person-neutral-large.png',
				'url'    => WPMOLY_URL . 'public/assets/img/person-neutral-large.png',
				'width'  => 500,
				'height' => 750,
			),
			'full' => (object) array(
				'file'   => 'person-neutral-full.png',
				'url'    => WPMOLY_URL . 'public/assets/img/person-neutral-full.png',
				'width'  => 780,
				'height' => 1170,
			),
			'original' => (object) array(
				'file'   => 'person-neutral.png',
				'url'    => WPMOLY_URL . 'public/assets/img/person-neutral.png',
				'width'  => 2000,
				'height' => 3000,
			),
		);

		/**
		 * Filter default picture sizes
		 *
		 * @since 3.0.0
		 *
		 * @param array $default_sizes
		 */
		$this->sizes = apply_filters( 'wpmoly/filter/default_picture/sizes', (object) $sizes );

		return $this->sizes;
	}

}
