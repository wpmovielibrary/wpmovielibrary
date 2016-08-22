<?php
/**
 * Define the Movie Images Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
 */

namespace wpmoly\Shortcodes;

use wpmoly\Node\Collection;
use wpmoly\Core\PublicTemplate as Template;

/**
 * Movie Images Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Images extends Shortcode {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 * 
	 * @var    string
	 */
	public static $name = 'movie_images';

	/**
	 * Shortcode attributes sanitizers
	 * 
	 * @var    array
	 */
	protected $validates = array(
		'id' => array(
			'default' => false,
			'values'  => null,
			'filter'  => 'intval'
		),
		'title' => array(
			'default' => null,
			'values'  => null,
			'filter'  => 'esc_attr'
		),
		'type' => array(
			'default' => 'backdrops',
			'values'  => array( 'backdrop', 'backdrops', 'poster', 'posters' ),
			'filter'  => 'esc_attr'
		),
		'number' => array(
			'default' => -1,
			'values'  => null,
			'filter'  => 'intval'
		),
		'size' => array(
			'default' => 'thumbnail',
			'values'  => array( 'thumbnail', 'medium', 'large', 'full', 'original' ),
			'filter'  => 'esc_attr'
		),
		'featured' => array(
			'default' => true,
			'values'  => null,
			'filter'  => '_is_bool'
		)
	);

	/**
	 * Shortcode aliases
	 * 
	 * @var    array
	 */
	protected static $aliases = array(
		'movie_backdrop'  => 'backdrop',
		'movie_backdrops' => 'backdrops',
		'movie_image'     => 'backdrop',
		'movie_images'    => 'backdrops',
		'movie_poster'    => 'poster',
		'movie_posters'   => 'posters'
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

		$this->collection = new Collection;

		switch ( $this->tag ) {
			case 'movie_image':
			case 'movie_backdrop':
				$this->attributes['number'] = 1;
				$this->attributes['type'] = self::$aliases[ $this->tag ];
				$this->collection->type = 'backdrops';
				break;
			case 'movie_images':
			case 'movie_backdrops':
				$this->attributes['type'] = self::$aliases[ $this->tag ];
				$this->collection->type = 'backdrops';
				break;
			case 'movie_poster':
				$this->attributes['number'] = 1;
				$this->attributes['type'] = self::$aliases[ $this->tag ];
				$this->collection->type = 'posters';
			case 'movie_posters':
				$this->attributes['type'] = self::$aliases[ $this->tag ];
				$this->collection->type = 'posters';
				break;
			default:
				return false;
				break;
		}

		// Set Template
		$this->template = new Template( 'shortcodes/images.php' );
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

		// Get Movie
		$post_id = $this->get_movie_id();
		$this->movie = get_movie( $post_id );

		$callback = $this->attributes['type'];
		$this->$callback();

		$data = array(
			'type'   => $this->attributes['type'],
			'size'   => $this->attributes['size'],
			'images' => $this->collection
		);

		$this->template->set_data( $data );

		return $this;
	}

	/**
	 * Get Movie ID from title if needed.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	protected function get_movie_id() {

		global $wpdb;

		if ( is_null( $this->attributes['title'] ) ) {
			return $this->attributes['id'];
		}

		$like = $wpdb->esc_like( $this->attributes['title'] );
		$like = '%' . $like . '%';

		$post_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE %s",
				$like
			)
		);

		return $this->attributes['id'] = $post_id;
	}

	/**
	 * Retrieve a poster.
	 * 
	 * Get last uploaded poster by default, post thumbnail if 'featured' is
	 * set to true.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	private function poster() {

		$variant = 'last';
		if ( true === $this->attributes['featured'] ) {
			$variant = 'featured';
		}

		$poster = $this->movie->get_poster( $variant );

		$this->collection->add( $poster );
	}

	/**
	 * Retrieve posters.
	 * 
	 * Get all posters if no 'number' parameter was provided.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	private function posters() {

		$posters = $this->movie->get_posters( $language = '', $number = $this->attributes['number'] );

		$this->collection = $posters;
	}

	/**
	 * Retrieve a poster.
	 * 
	 * TODO implement variants
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	private function backdrop() {

		$backdrop = $this->movie->get_backdrop();

		$this->collection->add( $backdrop );
	}

	/**
	 * Retrieve backdrops.
	 * 
	 * Get all backdrops if no 'number' parameter was provided.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	private function backdrops() {

		$backdrops = $this->movie->get_backdrops( $language = '', $number = $this->attributes['number'] );

		$this->collection = $backdrops;
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
