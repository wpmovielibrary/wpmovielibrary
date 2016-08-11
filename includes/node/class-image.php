<?php
/**
 * Define the Image class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 */

namespace wpmoly\Node;

/**
 * Generic Node class to handle Backdrop and Poster instances.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 * 
 * @property    string    $title Attachment title.
 * @property    string    $description Attachment description.
 * @property    string    $excerpt Attachment excerpt.
 * @property    string    $image_alt Attachment alternative description.
 */
class Image {

	/**
	 * Image ID.
	 * 
	 * @var    int
	 */
	public $id;

	/**
	 * Image Attachment Post object
	 * 
	 * @var    WP_Post
	 */
	public $attachment;

	/**
	 * Image defaults sizes
	 * 
	 * @var    object
	 */
	protected $sizes;

	/**
	 * Make the Image.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
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
	 * Get available sizes.
	 * 
	 * @since    3.0
	 * 
	 * @return   object
	 */
	public function get_sizes() {

		return $this->sizes;
	}

	/**
	 * Set a handful of useful values for different sizes of the image.
	 * 
	 * @since    3.0
	 * 
	 * @return   object
	 */
	public function set_defaults() {

		$meta = wp_get_attachment_metadata( $this->id, $unfiltered = true );

		$sizes = array(
			'thumbnail' => array(),
			'medium'    => array(),
			'large'     => array()
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
			'height' => ! empty( $meta['height'] ) ? intval( $meta['height'] ) : ''
		);

		$src = wp_get_attachment_image_src( $this->id, 'full' );
		$original['url'] = ! empty( $src[0] ) ? $src[0] : '';

		$sizes['original'] = (object) $original;

		/**
		 * Filter default image sizes
		 * 
		 * @since    3.0
		 * 
		 * @param    object    $sizes Image sizes
		 * @param    object    $attachment Image Attachment Post
		 */
		return $this->sizes = apply_filters( 'wpmoly/filter/images/sizes', (object) $sizes, $this->attachment );
	}

	/*private function filter_size( $size ) {

		if ( is_array( $size ) ) {
			return $this->filter_exact_size( $size );
		}

		$size = (string) $size;
		if ( isset( $this->sizes[ $size ] ) ) {
			return $size;
		}

		return 'original';
	}

	private function filter_exact_size( $size ) {

		$size = (array) $size;
		if ( empty( $size ) ) {
			return false;
		}

		// Only focus on width
		$size = $size[0];

		foreach ( $this->sizes as $slug => $width ) {
			if ( $width >= $size ) {
				return $slug;
			}
		}

		return 'original';
	}*/

	/**
	 * Render the image.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $size Image size to render
	 * @param    string     $format Raw image URL or HTML?
	 * @param    boolean    $echo Echo or return?
	 * 
	 * @return   string|null
	 */
	public function render( $size = 'original', $format = 'raw', $echo = true ) {

		if ( ! isset( $this->sizes->$size ) ) {
			return '';
		}

		$output = $this->sizes->$size->url;
		if ( 'html' == $format ) {
			$output = '<img src="' . esc_url( $output ) . '" alt="" />';
		}

		if ( false === $echo ) {
			return $output;
		}

		echo $output;
	}
}
