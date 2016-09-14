<?php
/**
 * Define the Rewrite class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

/**
 * Handle the plugin's URL rewriting.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Rewrite {

	/**
	 * Singleton.
	 *
	 * @var    Rewrite
	 */
	private static $instance = null;

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   Singleton
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * 
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $rules Existing rewrite rules
	 * 
	 * @return   array
	 */
	public function rewrite_rules( $rules ) {

		$permalinks = get_option( 'wpmoly_permalinks' );
		if ( ! $permalinks ) {
			$permalinks = array();
		}

		/*print_r( $permalinks );
		print_r( $rules ); die();*/

		return $rules;
	}
}
