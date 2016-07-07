<?php
/**
 * Define the media class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Node;

use wpmoly\Node\Image;
use wpmoly\Collection\Images;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Media extends Node {

	/**
	 * Movie Backdrops collection
	 * 
	 * @since    3.0
	 * 
	 * @var      Backdrops
	 */
	protected $backdrops;

	/**
	 * Movie Posters collection
	 * 
	 * @since    3.0
	 * 
	 * @var      Posters
	 */
	protected $posters;

	/**
	 * Initialize the Node.
	 * 
	 * Set collections and related Movie instance.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function make() {

		$this->backdrops = new Images;
		$this->backdrops->type = 'backdrops';

		$this->posters   = new Images;
		$this->posters->type = 'posters';
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
	public function get_backdrops( $load = false, $language = '', $number = -1 ) {

		if ( ! $this->backdrops->has_items() && true === $load ) {
			$this->load_backdrops( $language, $number );
		}

		if ( -1 == $number ) {
			return $this->backdrops;
		}

		$backdrops = new Images( 'backdrops' );
		while ( $this->backdrops->key() < $number - 1 ) {
			$backdrops->add( $this->backdrops->next() );
		}

		$this->backdrops->rewind();

		return $backdrops;
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
	public function get_posters( $load = false, $language = '', $number = -1 ) {

		if ( ! $this->posters->has_items() && true === $load ) {
			$this->load_posters( $language, $number );
		}

		if ( -1 == $number ) {
			return $this->posters;
		}

		$posters = new Images( 'posters' );
		while ( $this->posters->key() < $number - 1 ) {
			$posters->add( $this->posters->next() );
		}

		$this->posters->rewind();

		return $posters;
	}

	/**
	 * Simple accessor for Movie's Backdrop.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $variant Backdrop variant.
	 * 
	 * @return   Backdrop|DefaultBackdrop
	 */
	public function get_backdrop( $variant = 'featured' ) {

		if ( 'featured' == $variant && ! has_post_thumbnail( $this->id ) ) {
			$variant = 'default';
		}

		switch ( $variant ) {
			case 'featured' :
				$image_id = get_post_thumbnail_id( $this->id );
				$backdrop = Backdrop::get_instance( $image_id );
				break;
			case 'first' :
				$backdrop = $this->backdrops->first();
				break;
			case 'last' :
				$backdrop = $this->backdrops->last();
				break;
			case 'random' :
				$backdrop = $this->backdrops->random();
				break;
			case 'default' :
			default :
				$backdrop = DefaultBackdrop::get_instance();
				break;
		}

		if ( ! $backdrop instanceof Image ) {
			$backdrop = DefaultBackdrop::get_instance();
		}

		return $backdrop;
	}

	/**
	 * Simple accessor for Movie's Poster.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $variant Poster variant.
	 * 
	 * @return   Poster|DefaultPoster
	 */
	public function get_poster( $variant = 'featured' ) {

		if ( 'featured' == $variant && ! has_post_thumbnail( $this->id ) ) {
			$variant = 'default';
		}

		switch ( $variant ) {
			case 'featured' :
				$image_id = get_post_thumbnail_id( $this->id );
				$poster = Poster::get_instance( $image_id );
				break;
			case 'first' :
				$poster = $this->posters->first();
				break;
			case 'last' :
				$poster = $this->posters->last();
				break;
			case 'random' :
				$poster = $this->posters->random();
				break;
			case 'default' :
			default :
				$poster = DefaultPoster::get_instance();
				break;
		}

		if ( ! $poster instanceof Image ) {
			$poster = DefaultPoster::get_instance();
		}

		return $poster;
	}

	/**
	 * Load media: backdrops and posters for the current Movie.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Images type to load: 'backdrops', 'posters' or 'both'
	 * @param    string    $language Language to filter images
	 * @param    int       $number Number of images to fetch
	 * 
	 * @return   array
	 */
	public function load( $type = 'both', $language = '', $number = -1 ) {

		if ( 'both' == $type ) {
			$this->load_backdrops( $language, $number );
			$this->load_posters( $language, $number );
		} elseif ( 'backdrops' == $type ) {
			$this->load_backdrops( $language, $number );
		} elseif ( 'posters' == $type ) {
			$this->load_posters( $language, $number );
		}

		return array( 'backdrops' => $this->backdrops, 'posters' => $this->posters );
	}

	/**
	 * Load backdrops for the current Movie.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $language Language to filter images
	 * @param    int       $number Number of images to fetch
	 * 
	 * @return   Backdrops
	 */
	public function load_backdrops( $language = '', $number = -1 ) {

		global $wpdb;

		$attachments = get_posts( array(
			'post_type'   => 'attachment',
			'numberposts' => -1,
			'post_status' => null,
			'post_parent' => $this->id,
			'meta_key'    => '_wpmoly_image_related_tmdb_id'
		) );

		foreach ( $attachments as $i => $attachment ) {

			$image = array(
				'id'          => $attachment->ID,
				'title'       => $attachment->post_title,
				'description' => $attachment->post_content,
				'excerpt'     => $attachment->post_excerpt,
				'image_alt'   => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', $single = true )
			);

			$this->backdrops->add( new Backdrop( $image ) );
		}

		return $this->backdrops;
	}

	/**
	 * Load posters for the current Movie.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $language Language to filter images
	 * @param    int       $number Number of images to fetch
	 * 
	 * @return   null
	 */
	public function load_posters( $language = '', $number = -1 ) {

		global $wpdb;

		$attachments = get_posts( array(
			'post_type'   => 'attachment',
			'numberposts' => -1,
			'post_status' => null,
			'post_parent' => $this->id,
			'meta_key'    => '_wpmoly_poster_related_tmdb_id'
		) );

		foreach ( $attachments as $i => $attachment ) {

			$image = array(
				'id'          => $attachment->ID,
				'title'       => $attachment->post_title,
				'description' => $attachment->post_content,
				'excerpt'     => $attachment->post_excerpt,
				'image_alt'   => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', $single = true )
			);

			$this->posters->add( new Poster( $image ) );
		}

		return $this->posters;
	}

	/**
	 * Fetch backdrops from TMDb for the current Movie.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $language Language to filter images
	 * @param    int       $number Number of images to fetch
	 * 
	 * @return   null
	 */
	public function fetch_backdrops( $language = '', $number = -1 ) {

		$this->fetch( 'backdrops', $language, $number );
	}

	/**
	 * Fetch posters from TMDb for the current Movie.
	 * 
	 * @since    3.0
	 * @param    string    $language Language to filter images
	 * @param    int       $number Number of images to fetch
	 * 
	 * @return   null
	 */
	public function fetch_posters( $language = '', $number = -1 ) {

		$this->fetch( 'posters', $language, $number );
	}

	/**
	 * Fetch images from TMDb for the current Movie.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Type of media to fetch: 'backdrops' or 'posters'
	 * @param    string    $language Language to filter images
	 * @param    int       $number Number of images to fetch
	 * 
	 * @return   null
	 */
	protected function fetch( $type = 'backdrops', $language = '', $number = -1 ) {

		$tmdb_id = $this->movie->get( 'tmdb_id' );
	}

	/**
	 * Make the Node.
	 * 
	 * Nothing to do for details at this stage.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function init() {}
}