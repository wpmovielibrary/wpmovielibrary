<?php
/**
 * Define the image class.
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
class Image extends Node {

	public $sizes;

	/**
	 * Node properties sanitizers
	 * 
	 * @var    array
	 */
	protected $validates = array(
		'title'       => 'sanitize_text_field',
		'description' => 'wp_kses_post',
		'excerpt'     => 'sanitize_text_field',
		'image_alt'   => 'sanitize_text_field'
	);

	/**
	 * Node properties escapers
	 * 
	 * @var    array
	 */
	protected $escapes = array(
		'title'       => 'esc_attr',
		'description' => 'wp_kses',
		'excerpt'     => 'esc_attr',
		'image_alt'   => 'esc_attr'
	);

	/**
	 * Node default properties values
	 * 
	 * @var    array
	 */
	protected $defaults = array(
		'title'       => '',
		'description' => '',
		'excerpt'     => '',
		'image_alt'   => ''
	);

	/**
	 * Set a handful of useful values for different sizes of the image.
	 * 
	 * TODO transfer to Backdrop and Poster classes
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $meta Image Attachment metadata
	 * 
	 * @return   null
	 */
	public function set_sizes( $meta ) {

		$default_sizes = (object) array(
			'thumbnail' => array(),
			'medium'    => array(),
			'large'     => array()
		);
		$this->sizes = apply_filters( 'wpmoly/filter/images/default_sizes', $default_sizes );

		foreach ( $this->sizes as $slug => $size ) {
			if ( ! empty( $meta['sizes'][ $slug ] ) ) {
				$m = $meta['sizes'][ $slug ];
				$size = array(
					'file'   => ! empty( $m['file'] )   ? esc_attr( $m['file'] ) : '',
					'width'  => ! empty( $m['width'] )  ? intval( $m['width'] )  : '',
					'height' => ! empty( $m['height'] ) ? intval( $m['height'] ) : '',
				);

				$src = wp_get_attachment_image_src( $this->id, $slug );
				$size['path'] = ! empty( $src[0] ) ? $src[0] : '';

				$this->sizes->$slug = (object) $size;
			}
		}

		$this->sizes->original = (object) array(
			'file'   => ! empty( $meta['file'] )   ? esc_attr( $meta['file'] )  : '',
			'width'  => ! empty( $meta['width'] )  ? intval( $meta['width'] )  : '',
			'height' => ! empty( $meta['height'] ) ? intval( $meta['height'] ) : ''
		);

		$src = wp_get_attachment_image_src( $this->id, 'full' );
		$this->sizes->original->path = ! empty( $src[0] ) ? $src[0] : '';
	}

	/**
	 * Render the image.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $size Image size to render
	 * @param    string     $output Raw image URL or HTML?
	 * @param    boolean    $echo Echo or return?
	 * 
	 * @return   string|null
	 */
	public function render( $size = 'original', $output = 'raw', $echo = true ) {

		if ( ! isset( $this->sizes->$size ) ) {
			return '';
		}

		$output = $this->sizes->$size->path;
		if ( 'html' == $output ) {
			$output = '<img src="' . $output . '" alt="" />';
		}

		if ( false === $echo ) {
			return $output;
		}

		echo $output;
	}

	
	public function remove() {

		
	}

	
	public function save() {

		
	}

	
	public function update() {

		
	}
}
