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
class Backdrop extends Image {

	/**
	 * Default backdrop thumbnail (150x150)
	 * 
	 * @var    string
	 */
	protected $thumbnail;

	/**
	 * Default medium backdrop (300x200)
	 * 
	 * @var    string
	 */
	protected $medium;
	
	/**
	 * Default large backdrop (780x520)
	 * 
	 * @var    string
	 */
	protected $large;

	/**
	 * Default full backdrop (1280x853)
	 * 
	 * @var    string
	 */
	protected $full;

	/**
	 * Default original backdrop (1920x1280)
	 * 
	 * @var    string
	 */
	protected $original;

	/**
	 * Setup default backdrops sizes.
	 * 
	 * TODO this is a Node; use ::set()
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function init() {

		$this->thumbnail = 'backdrop-thumbnail.jpg';
		$this->medium    = 'backdrop-medium.jpg';
		$this->large     = 'backdrop-large.jpg';
		$this->full      = 'backdrop-full.jpg';
		$this->original  = 'backdrop.jpg';
	}

	/**
	 * Get default backdrop path for a specific size.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $size Backdrop size
	 * 
	 * @return   string
	 */
	public static function get_default_path( $size = 'original' ) {

		$backdrop = new static;

		return isset( $backdrop->$size ) ? WPMOLY_PATH . 'public/img/' . $backdrop->$size : '';
	}

	/**
	 * Get default backdrop URL for a specific size.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $size Backdrop size
	 * 
	 * @return   string
	 */
	public static function get_default_url( $size = 'original' ) {

		$backdrop = new static;

		return isset( $backdrop->$size ) ? WPMOLY_URL . 'public/img/' . $backdrop->$size : '';
	}

	/**
	 * Get default backdrop path or URL for a specific size.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $size Backdrop size
	 * 
	 * @return   string
	 */
	public static function get_default( $size = 'original', $type = 'url' ) {

		return 'path' === $type ? self::get_default_path( $size ) : self::get_default_url( $size );
	}
}
