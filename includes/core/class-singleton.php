<?php
/**
 * Define the Singleton class.
 * 
 * @link       http://wpmovielibrary.com
 * @since      3.0
 * 
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

/**
 * Simple Singleton Class.
 * 
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Singleton {

	/**
	 * Current instance.
	 * 
	 * @since    3.0
	 * 
	 * @var      Singleton
	 */
	protected static $instances = array();

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   Singleton
	 */
	final public static function get_instance() {

		$class = get_called_class();
		if ( ! isset( self::$instances[ $class ] ) ) {
			self::$instances[ $class ] = new $class;
		}

		return self::$instances[ $class ];
	}

	/**
	 * Protected class constructor.
	 * 
	 * @since    3.0
	 */
	protected function __construct() {}

	/**
	 * Final clone magic method.
	 * 
	 * @since    3.0
	 */
	final private function __clone() {}
}