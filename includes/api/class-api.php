<?php
/**
 * Define the API class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly\API;

use WP_Error;

/**
 * Handle the interactions with the TMDb API.
 *
 * @package    WPMovieLibrary
 * @author     Charlie Merland <charlie@caercam.org>
 */
class API {

	/**
	 * Current instance.
	 *
	 * @since    3.0
	 *
	 * @var      Library
	 */
	public static $instance;

	/**
	 * Define the API class.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function __construct() {

		self::$instance = $this;

		$this->movie  = new Movie;
		//$this->tv     = new TV;
		//$this->person = new Person;
	}

	/**
	 * Singleton.
	 *
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}
}