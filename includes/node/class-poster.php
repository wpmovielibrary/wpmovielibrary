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
class Poster extends Image {

	/**
	 * Default poster thumbnail (150x150)
	 * 
	 * @var    string
	 */
	protected $thumbnail;

	/**
	 * Default smallest poster (92x138)
	 * 
	 * @var    string
	 */
	protected $xxsmall;
	
	/**
	 * Default smaller poster (154x231)
	 * 
	 * @var    string
	 */
	protected $xsmall;
	
	/**
	 * Default small poster (185x278)
	 * 
	 * @var    string
	 */
	protected $small;
	
	/**
	 * Default medium poster (342x513)
	 * 
	 * @var    string
	 */
	protected $medium;
	
	/**
	 * Default large poster (500x750)
	 * 
	 * @var    string
	 */
	protected $large;

	/**
	 * Default full poster (780x1170)
	 * 
	 * @var    string
	 */
	protected $full;

	/**
	 * Default original poster (2000x3000)
	 * 
	 * @var    string
	 */
	protected $original;

	/**
	 * Setup default posters sizes.
	 * 
	 * TODO this is a Node; use ::set()
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function init() {

		$this->thumbnail = 'poster-thumbnail.jpg';
		$this->xxsmall   = 'poster-xxsmall.jpg';
		$this->xsmall    = 'poster-xsmall.jpg';
		$this->small     = 'poster-small.jpg';
		$this->medium    = 'poster-medium.jpg';
		$this->large     = 'poster-large.jpg';
		$this->full      = 'poster-full.jpg';
		$this->original  = 'poster.jpg';
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
	 * 
	 * @return   string
	 */
	public static function get_default( $size, $type = 'url' ) {

		return 'path' === $type ? self::get_default_path( $size ) : self::get_default_url( $size );
	}
}
