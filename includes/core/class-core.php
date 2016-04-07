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
 * Simple Singleton Class.
 * 
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Core {

	/**
	 * Current instance.
	 * 
	 * @since    3.0
	 * 
	 * @var      Library
	 */
	protected static $instances = array();

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   self
	 */
	final public static function get_instance() {

		$class = get_called_class();
		if ( ! isset( self::$instances[ $class ] ) ) {
			self::$instances[ $class ] = new $class;
		}

		return self::$instances[ $class ];
	}

	protected function __construct() {}

	final private function __clone() {}
}