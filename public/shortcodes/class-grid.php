<?php
/**
 * Define the Grid Shortcode class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\shortcodes;

/**
 * General Shortcode class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Grid extends Shortcode {

	/**
	 * Shortcode name, used for declaring the Shortcode.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @var string
	 */
	public static $name = 'grid';

	/**
	 * Shortcode attributes sanitizers.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $validates = array(
		'id' => array(
			'default' => '',
			'values'  => null,
			'filter'  => 'intval',
		),
		'mode' => array(
			'default' => 'grid',
			'values'  => array( 'grid', 'list', 'archive' ),
			'filter'  => 'esc_attr',
		),
	);

	/**
	 * Shortcode aliases.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access protected
	 *
	 * @var array
	 */
	protected static $aliases = array(
		'movie'               => 'grid',
		'movies'              => 'grid',
		'actor'               => 'grid',
		'actors'              => 'grid',
		'collection'          => 'grid',
		'collections'         => 'grid',
		'genre'               => 'grid',
		'genres'              => 'grid',
		'movie_grid'          => 'grid',
		'movies_grid'         => 'grid',
		'actor_grid'          => 'grid',
		'actors_grid'         => 'grid',
		'collection_grid'     => 'grid',
		'collections_grid'    => 'grid',
		'genre_grid'          => 'grid',
		'genres_grid'         => 'grid',
		'movie_list'          => 'list',
		'movies_list'         => 'list',
		'actor_list'          => 'list',
		'actors_list'         => 'list',
		'collection_list'     => 'list',
		'collections_list'    => 'list',
		'genre_list'          => 'list',
		'genres_list'         => 'list',
		'movie_archive'       => 'archive',
		'movies_archive'      => 'archive',
		'actor_archive'       => 'archive',
		'actors_archive'      => 'archive',
		'collection_archive'  => 'archive',
		'collections_archive' => 'archive',
		'genre_archive'       => 'archive',
		'genres_archive'      => 'archive',
	);

	/**
	 * Build the Shortcode.
	 *
	 * Prepare Shortcode parameters.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function make() {

		if ( ! is_null( $this->tag ) && isset( self::$aliases[ $this->tag ] ) ) {
			$this->set( 'mode', self::$aliases[ $this->tag ] );
		}
	}

	/**
	 * Run the Shortcode.
	 *
	 * Perform all needed Shortcode stuff.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return Shortcode
	 */
	public function run() {

		$grid = get_grid( $this->attributes['id'] );
		$grid->set_mode( $this->attributes['mode'] );

		$this->template = get_grid_template( $grid );

		return $this;
	}

	/**
	 * Initialize the Shortcode.
	 *
	 * Run things before doing anything.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function init() {}
}
