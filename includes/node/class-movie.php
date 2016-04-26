<?php
/**
 * Define the .
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */

namespace wpmoly\Node;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Movie extends Node {

	/**
	 * Movie ID.
	 * 
	 * @since    3.0
	 * 
	 * @var      int
	 */
	public $id;

	/**
	 * Movie Post object
	 * 
	 * @var    WP_Post
	 */
	public $post;

	/**
	 * Movie Metadata
	 * 
	 * @var    Meta
	 */
	public $meta;

	/**
	 * Movie Details
	 * 
	 * @var    Details
	 */
	public $details;

	/**
	 * Movie Media
	 * 
	 * @var    Media
	 */
	public $media;

	/**
	 * Object instances.
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	public static $instances;

	/**
	 * Initialize the Node.
	 * 
	 * Nothing to do for movies at this stage.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function init() {}

	/**
	 * Make the Node.
	 * 
	 * Meta, Details and Media are set at this stage and not in Node::init()
	 * be cause Node::make() is run after constructor and we need $this->id
	 * to be available to link data Nodes to the movie Node.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function make() {

		$this->post = get_post( $this->id );

		$this->meta    = new Meta( $this->id );
		$this->details = new Details( $this->id );
		$this->media   = new Media( $this->id );
	}

	/**
	 * Simple accessor for Backdrops collection.
	 * 
	 * @since    3.0
	 * 
	 * @param    boolean    $load Try to load images if empty
	 * 
	 * @return   Posters
	 */
	public function get_backdrops( $load = false ) {

		return $this->media->get_backdrops( $load );
	}

	/**
	 * Simple accessor for Posters collection.
	 * 
	 * @since    3.0
	 * 
	 * @param    boolean    $load Try to load images if empty
	 * 
	 * @return   Posters
	 */
	public function get_posters( $load = false ) {

		return $this->media->get_posters( $load );
	}

	/**
	 * Simple accessor for Movie's Backdrop.
	 * 
	 * Different variant can be used. 'featured' will use the featured image
	 * if available, default backdrop if no featured image is defined. 'first',
	 * 'last' and 'random' are self-explanatory and will fall back to the
	 * default backdrop if no backdrop is available.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $variant Backdrop variant.
	 * 
	 * @return   Backdrop|DefaultBackdrop
	 */
	public function get_backdrop( $variant = 'featured' ) {

		return $this->media->get_backdrop( $variant );
	}

	/**
	 * Simple accessor for Movie's Poster.
	 * 
	 * Different variant can be used. 'featured' will use the featured image
	 * if available, default poster if no featured image is defined. 'first',
	 * 'last' and 'random' are self-explanatory and will fall back to the
	 * default poster if no poster is available.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $variant Poster variant.
	 * 
	 * @return   Poster|DefaultPoster
	 */
	public function get_poster( $variant = 'featured' ) {

		return $this->media->get_poster( $variant );
	}

	/**
	 * Find a movie using a specific criteria.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Type of criteria
	 * @param    string    $value Value to search
	 * 
	 * @return   Movie
	 */
	public static function find_by( $type, $value ) {

		return new static;
	}

	/**
	 * Fetch the Movie.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function fetch() {}

	/**
	 * Save the Movie.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function save() {

		$this->meta->save();
		$this->details->save();

		$this->media->save();
	}

	/**
	 * Update the Movie.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function update() {

		$this->meta->update();
		$this->details->update();

		$this->media->update();
	}

	/**
	 * Remove the Movie.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function remove() {

		$this->meta->remove();
		$this->details->remove();

		$this->media->remove();
	}
}