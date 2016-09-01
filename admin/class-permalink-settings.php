<?php
/**
 * Define the Permalink Settings class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Admin;

use wpmoly\Core\AdminTemplate as Template;

/**
 * Handle the plugin's URL rewriting settings.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class PermalinkSettings {

	/**
	 * Custom permalink settings.
	 * 
	 * @var    array
	 */
	private $settings = array();

	/**
	 * Singleton.
	 *
	 * @var    Rewrite
	 */
	private static $instance = null;

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 */
	private function __construct() {

		$settings = array(
			'movie-permalinks' => array(
				'title'  => __( 'Movies', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-movie',
				'fields' => array(
					'movie_permalink' => array(
						'type' => 'radio',
						'title' => __( 'Movie Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single movie pages. <a href="https://codex.wordpress.org/Using_Permalinks">Standard tags</a> are supported along with specific movie tags.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/%postname%/',
								'description' => home_url() . '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/interstellar/'
							),
							'title_year' => array(
								'label'  => __( 'Title and Year', 'wpmovielibrary' ),
								'value'  => '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/%year%/%postname%/',
								'description' => home_url() . '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/2016/interstellar/'
							),
							'title_month' => array(
								'label'  => __( 'Title and Month', 'wpmovielibrary' ),
								'value'  => '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/%year%/%monthnum%/%postname%/',
								'description' => home_url() . '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/2016/08/interstellar/'
							),
							'title_release_year' => array(
								'label'  => __( 'Title and Release Year', 'wpmovielibrary' ),
								'value'  => '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/%release_year%/%postname%/',
								'description' => home_url() . '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/2014/interstellar/'
							),
							'title_release_month' => array(
								'label'  => __( 'Title and Release Month', 'wpmovielibrary' ),
								'value'  => '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/%release_year%/%release_monthnum%/%postname%/',
								'description' => home_url() . '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/2014/10/interstellar/'
							),
							'imdb_id' => array(
								'label'  => __( 'IMDb ID', 'wpmovielibrary' ),
								'value'  => '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/%imdb_id%/',
								'description' => home_url() . '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/tt0816692/'
							),
							'tmdb_id' => array(
								'label'  => __( 'TMDb ID', 'wpmovielibrary' ),
								'value'  => '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/%tmdb_id%/',
								'description' => home_url() . '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/157336/'
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/' . _x( 'movies', 'slug', 'wpmovielibrary' ) . '/%postname%/',
								'description' => home_url() . '/' . _x( 'movies', 'slug', 'wpmovielibrary' ) . '/interstellar/'
							),
							'custom' => array(
								'label'  => __( 'Custom', 'wpmovielibrary' ),
								'value'  => '',
								'description' => ''
							)
						),
						'default' => 'archive'
					),
					'movie_base' => array(
						'type' => 'text',
						'title' => __( 'Movie archives', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for movie archive pages. If a dedicated archive page has already been set this will override the page’s permalink.', 'wpmovielibrary' ),
						'default' => _x( 'movies', 'slug', 'wpmovielibrary' )
					)
				)
			),
			'actor-permalinks' => array(
				'title'  => __( 'Actors', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-actor',
				'fields' => array(
					'actor_permalink' => array(
						'type' => 'radio',
						'title' => __( 'Actor Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single actor pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . _x( 'actor', 'slug', 'wpmovielibrary' ) . '/',
								'description' => home_url() . '/' . _x( 'actor', 'slug', 'wpmovielibrary' ) . '/matthew-mcconaughey/'
							),
							'custom' => array(
								'label'  => __( 'Custom', 'wpmovielibrary' ),
								'value'  => '',
								'description' => ''
							)
						),
						'default' => 'simple'
					),
					'actor_base' => array(
						'type' => 'text',
						'title' => __( 'Actor archives', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for actor archive pages. If a dedicated archive page has already been set this will override the page’s permalink.', 'wpmovielibrary' ),
						'default' => _x( 'actors', 'slug', 'wpmovielibrary' )
					)
				)
			),
			'genre-permalinks' => array(
				'title'  => __( 'Genres', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-tags',
				'fields' => array(
					'genre_permalink' => array(
						'type' => 'radio',
						'title' => __( 'Genre Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single genre pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . _x( 'genre', 'slug', 'wpmovielibrary' ) . '/',
								'description' => home_url() . '/' . _x( 'genre', 'slug', 'wpmovielibrary' ) . '/science-fiction/'
							),
							'custom' => array(
								'label'  => __( 'Custom', 'wpmovielibrary' ),
								'value'  => '',
								'description' => ''
							)
						),
						'default' => 'simple'
					),
					'genre_base' => array(
						'type' => 'text',
						'title' => __( 'Genre archives', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for genre archive pages. If a dedicated archive page has already been set this will override the page’s permalink.', 'wpmovielibrary' ),
						'default' => _x( 'genres', 'slug', 'wpmovielibrary' )
					)
				)
			),
			'collection-permalinks' => array(
				'title'  => __( 'Collections', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-folder',
				'fields' => array(
					'collection_permalink' => array(
						'type' => 'radio',
						'title' => __( 'Collection Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single collection pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . _x( 'collection', 'slug', 'wpmovielibrary' ) . '/',
								'description' => home_url() . '/' . _x( 'collection', 'slug', 'wpmovielibrary' ) . '/christopher-nolan/'
							),
							'custom' => array(
								'label'  => __( 'Custom', 'wpmovielibrary' ),
								'value'  => '',
								'description' => ''
							)
						),
						'default' => 'simple'
					),
					'collection_base' => array(
						'type' => 'text',
						'title' => __( 'Collection archives', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for collection archive pages. If a dedicated archive page has already been set this will override the page’s permalink.', 'wpmovielibrary' ),
						'default' => _x( 'collections', 'slug', 'wpmovielibrary' )
					)
				)
			)
		);

		/**
		 * Filter default permalinks settings.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $settings
		 */
		$this->settings = apply_filters( 'wpmoly/filter/permalink/settings', $settings );
	}

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
	 * Add a new block to the Permalink settings option page.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register() {

		add_settings_section( 'wpmoly-permalink', __( 'Movie Library Permalinks', 'wpmovielibrary' ), array( $this, 'register_sections' ), 'permalink' );
	}

	/**
	 * Display a custom metabox-ish permalink settings block.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register_sections() {

		$metabox = new Template( 'permalink-settings.php' );
		$metabox->set_data( array(
			'settings' => $this->settings
		) );

		$metabox->render();
	}

	/**
	 * Save custom permalinks.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function update() {

		/*if (  ) {
			
		}*/
	}

}
