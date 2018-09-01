<?php
/**
 * Define the Image class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\images;

/**
 * Generic Node class to handle Backdrop and Poster instances.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Image {

	/**
	 * Image ID.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Image Attachment Post object
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var WP_Post
	 */
	protected $attachment;

	/**
	 * Image defaults sizes
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var object
	 */
	public $sizes;

	/**
	 * Make the Image.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct( $image = null ) {

		if ( is_numeric( $image ) ) {
			$this->id   = absint( $image );
			$this->attachment = get_post( $this->id );
		} elseif ( $image instanceof Image ) {
			$this->id   = absint( $image->id );
			$this->attachment = $image->attachment;
		} elseif ( isset( $image->ID ) ) {
			$this->id   = absint( $image->ID );
			$this->attachment = $image;
		}

		$this->set_defaults();
	}

	/**
	 * Get image's attachment.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return object
	 */
	public function get_attachment() {

		return $this->attachment;
	}

	/**
	 * Get available sizes.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return object
	 */
	public function get_sizes() {

		return $this->sizes;
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

		$meta = wp_get_attachment_metadata( $this->id, true );

		$sizes = array(
			'thumbnail' => array(),
			'medium'    => array(),
			'large'     => array(),
		);

		// Basic WordPress image sizes
		foreach ( $sizes as $slug => $size ) {
			if ( ! empty( $meta['sizes'][ $slug ] ) ) {
				$m = $meta['sizes'][ $slug ];
				$size = array(
					'file'   => ! empty( $m['file'] )   ? esc_attr( $m['file'] ) : '',
					'width'  => ! empty( $m['width'] )  ? intval( $m['width'] )  : '',
					'height' => ! empty( $m['height'] ) ? intval( $m['height'] ) : '',
				);

				$src = wp_get_attachment_image_src( $this->id, $slug );
				$size['url'] = ! empty( $src[0] ) ? $src[0] : '';

				$sizes[ $slug ] = (object) $size;
			}
		}

		// Original size data
		$original = array(
			'file'   => ! empty( $meta['file'] )   ? esc_attr( $meta['file'] )  : '',
			'width'  => ! empty( $meta['width'] )  ? intval( $meta['width'] )  : '',
			'height' => ! empty( $meta['height'] ) ? intval( $meta['height'] ) : '',
		);

		$src = wp_get_attachment_image_src( $this->id, 'full' );
		$original['url'] = ! empty( $src[0] ) ? $src[0] : '';

		$sizes['original'] = (object) $original;

		/**
		 * Filter default image sizes
		 *
		 * @since 3.0.0
		 *
		 * @param object $sizes Image sizes
		 * @param object $attachment Image Attachment Post
		 */
		$this->sizes = apply_filters( 'wpmoly/filter/images/sizes', (object) $sizes, $this->attachment );

		return $this->sizes;
	}

	/**
	 * Render the image.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $size Image size to render
	 * @param string $format Raw image URL or HTML?
	 * @param boolean $echo Echo or return?
	 *
	 * @return string|null
	 */
	public function render( $size = 'original', $format = 'raw', $echo = true ) {

		if ( ! isset( $this->sizes->$size ) ) {
			return '';
		}

		$output = $this->sizes->$size->url;
		if ( 'html' == $format ) {
			$output = '<img src="' . esc_url( $output ) . '" alt="" />';
		} else {
			$output = esc_url( $output );
		}

		if ( false === $echo ) {
			return $output;
		}

		echo $output;
	}

}
