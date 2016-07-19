<?php
/**
 * Define the Movies Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcode
 */

namespace wpmoly\Shortcodes;

use WP_Query;
use wpmoly\Grid;
//use wpmoly\Collection;
use wpmoly\Core\PublicTemplate as Template;

/**
 * General Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcode
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Movies extends Shortcode {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 * 
	 * @var    string
	 */
	public static $name = 'movies';

	/**
	 * Shortcode attributes sanitizers
	 * 
	 * @var    array
	 */
	protected $validates = array(
		'mode' => array(
			'default' => 'grid',
			'values'  => array( 'grid', 'list', 'archive' ),
			'filter'  => 'esc_attr'
		)
	);

	/**
	 * Shortcode aliases
	 * 
	 * @var    array
	 */
	protected static $aliases = array(
		'movie_grid'     => 'grid',
		'movies_grid'    => 'grid',
		'movie_list'     => 'list',
		'movies_list'    => 'list',
		'movie_archive'  => 'archive',
		'movies_archive' => 'archive',
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

		if ( ! is_null( $this->tag ) && isset( self::$aliases[ $this->tag ] ) ) {
			$this->set( 'mode', self::$aliases[ $this->tag ] );
		}

		$template = 'shortcodes/movies-' . $this->attributes['mode'] . '.php';

		// Set Template
		$this->template = new Template( $template );
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

		/*$grid = new Grid();
		$grid->set( $this->attributes );

		$data = array(
			'movies'  => $grid->movies
		);

		$this->template->set_data( $data );

		return $this;*/

		/*global $post;

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

		wp_reset_postdata();

		$data = array(
			'movies'  => $movies,//Collection\Movies::find( $args )
			'poster'  => (string) $this->attributes['poster'],
			'details' => (array) $this->attributes['details'],
			'meta'    => (array) $this->attributes['meta'],
		);

		$this->template->set_data( $data );

		return $this;*/
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
