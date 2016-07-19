<?php
/**
 * Define the images collection class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Collections;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Images extends Collection {

	/**
	 * Collection items type, either 'backdrops' or 'posters'.
	 * 
	 * @var    string
	 */
	public $type;

	/**
	 * Initialize Collection.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Items type, either 'backdrops' or 'posters'.
	 * 
	 * @return   Images    Return itself to allow chaining
	 */
	public function __construct( $type = '' ) {

		parent::__construct();

		$this->type = $type;
	}
}