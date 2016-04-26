<?php
/**
 * Define the Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Shortcodes;

use WP_Query;
use wpmoly\Collection;
use wpmoly\Core\PublicTemplate as Template;

/**
 * General Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Movies extends Shortcode {

	/**
	 * Shortcode attributes sanitizers
	 * 
	 * @var    array
	 */
	protected $validates = array(
		'collection' => array(
			'default' => false,
			'values'  => null,
			'filter'  => 'esc_attr'
		),
		'genre' => array(
			'default' => false,
			'values'  => null,
			'filter'  => 'esc_attr'
		),
		'actor' => array(
			'default' => false,
			'values'  => null,
			'filter'  => 'esc_attr'
		),
		'order' => array(
			'default' => 'desc',
			'values'  => array( 'asc', 'desc' ),
			'filter'  => 'esc_attr'
		),
		'orderby' => array(
			'default' => 'date',
			'values'  => array( 'date', 'title', 'rating' ),
			'filter'  => 'esc_attr'
		),
		'count' => array(
			'default' => 4,
			'values'  => null,
			'filter'  => 'intval'
		),
		'poster' => array(
			'default' => 'medium',
			'values'  => array( 'none', 'thumb', 'thumbnail', 'medium', 'large', 'full' ),
			'filter'  => 'esc_attr'
		),
		'meta' => array(
			'default' => false,
			'values'  => array( 'director', 'runtime', 'release_date', 'genres', 'actors', 'overview', 'title', 'original_title', 'production', 'country', 'language', 'producer', 'photography', 'composer', 'author', 'writer' ),
			'filter'  => 'esc_attr'
		),
		'details' => array(
			'default' => false,
			'values'  => array( 'media', 'status', 'rating' ),
			'filter'  => 'esc_attr'
		),
		'paginate' => array(
			'default' => false,
			'values'  => 'boolean',
			'filter'  => 'esc_attr'
		)
	);

	/**
	 * Build the Shortcode.
	 * 
	 * Prepare Shortcode parameters.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function make() {

		// Set name, used for declaring the Shortcode
		self::$name = 'movies';

		// Set Template
		$this->template = new Template( 'shortcodes/movies.php' );
	}

	/**
	 * Run the Shortcode.
	 * 
	 * Perform all needed Shortcode stuff.
	 * 
	 * @since    3.0
	 * 
	 * @return   Shortcode
	 */
	public function run() {

		global $post;

		$args = array(
			'post_type'      => 'movie',
			'post_status'    => 'publish',
			'posts_per_page' => $this->attributes['count'],
			'page'           => $this->attributes['paginate'] ? get_query_var( 'page' ) : 1,
			'order'          => $this->attributes['order']
		);

		if ( 'rating' == $this->attributes['orderby'] ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_wpmoly_movie_rating';
		} else {
			$args['orderby'] = $this->attributes['orderby'];
		}

		if ( ! $this->attributes['collection'] ) {
			$args['collection'] = $this->attributes['collection'];
		} elseif ( ! $this->attributes['genre'] ) {
			$args['genre'] = $this->attributes['genre'];
		} elseif ( ! $this->attributes['actor'] ) {
			$args['actor'] = $this->attributes['actor'];
		}

		$movies = array();

		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$movies[] = get_movie( $post );
			}
		}

		$this->template->set_data( array(
			'movies' => $movies//Collection\Movies::find( $args )
		) );

		return $this;
	}

	/**
	 * Initialize the Shortcode.
	 * 
	 * Run things before doing anything.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function init() {}
}
