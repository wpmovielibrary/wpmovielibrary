<?php
/**
 * Define the Core class.
 * 
 * @link       http://wpmovielibrary.com
 * @since      3.0
 * 
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

/**
 * 
 * 
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Core {

	/**
	 * Current instance.
	 * 
	 * @since    3.0
	 * 
	 * @var      Library
	 */
	public static $instance;

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}
}