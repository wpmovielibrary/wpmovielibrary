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
	 * Default permalinks.
	 * 
	 * @var    array
	 */
	private $defaults = array();

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

		$defaults = array(
			'movie_permalink'      => '/' . _x( 'movie', 'slug', 'wpmovielibrary' ) . '/%postname%/',
			'actor_permalink'      => '/' . _x( 'actor', 'slug', 'wpmovielibrary' ) . '/%actor%/',
			'collection_permalink' => '/' . _x( 'collection', 'slug', 'wpmovielibrary' ) . '/%collection%/',
			'genre_permalink'      => '/' . _x( 'genre', 'slug', 'wpmovielibrary' ) . '/%genre%/',
			'movie_archives'       => '/' . _x( 'movies', 'slug', 'wpmovielibrary' ) . '/',
			'actor_archives'       => '',
			'collection_archives'  => '',
			'genre_archives'       => '',
		);

		/**
		 * Default permalink structures settings.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $defaults
		 */
		$this->defaults = apply_filters( 'wpmoly/filter/permalinks/structure/defaults', $defaults );

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
							)
						),
						'default' => 'archive'
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
								'value'  => '/' . _x( 'actor', 'slug', 'wpmovielibrary' ) . '/%actor%/',
								'description' => home_url() . '/' . _x( 'actor', 'slug', 'wpmovielibrary' ) . '/matthew-mcconaughey/'
							)
						),
						'default' => 'simple'
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
								'value'  => '/' . _x( 'genre', 'slug', 'wpmovielibrary' ) . '/%genre%/',
								'description' => home_url() . '/' . _x( 'genre', 'slug', 'wpmovielibrary' ) . '/science-fiction/'
							)
						),
						'default' => 'simple'
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
								'value'  => '/' . _x( 'collection', 'slug', 'wpmovielibrary' ) . '/%collection%/',
								'description' => home_url() . '/' . _x( 'collection', 'slug', 'wpmovielibrary' ) . '/christopher-nolan/'
							)
						),
						'default' => 'simple'
					)
				)
			),
			'archives-permalinks' => array(
				'title'  => __( 'Archives', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-archive',
				'fields' => array(
					'movie_archives' => array(
						'type' => 'text',
						'title' => __( 'Movie archives', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for movie archive pages. If a dedicated archive page has already been set this will override the page’s permalink.', 'wpmovielibrary' ),
						'default' => _x( 'movies', 'slug', 'wpmovielibrary' )
					),
					'actor_archives' => array(
						'type' => 'text',
						'title' => __( 'Actor archives', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for actor archive pages. If a dedicated archive page has already been set this will override the page’s permalink.', 'wpmovielibrary' ),
						'default' => _x( 'actors', 'slug', 'wpmovielibrary' )
					),
					'genre_archives' => array(
						'type' => 'text',
						'title' => __( 'Genre archives', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for genre archive pages. If a dedicated archive page has already been set this will override the page’s permalink.', 'wpmovielibrary' ),
						'default' => _x( 'genres', 'slug', 'wpmovielibrary' )
					),
					'collection_archives' => array(
						'type' => 'text',
						'title' => __( 'Collection archives', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for collection archive pages. If a dedicated archive page has already been set this will override the page’s permalink.', 'wpmovielibrary' ),
						'default' => _x( 'collections', 'slug', 'wpmovielibrary' )
					)
				)
			)
		);

		if ( ! has_actor_archives_page() ) {
			$settings['archives-permalinks']['fields']['actor_archives']['disabled'] = true;
			$settings['archives-permalinks']['fields']['actor_archives']['description'] .= '<p><em>' . sprintf( __( 'You don’t have any archive page set for actors yet, which makes this setting meaningless. Define an archive page by <a href="%s">creating a standard WordPress page</a> and set it as archive in the relevant Metabox. <a href="%s">Learn more about archive pages</a>.', 'wpmovielibrary' ), esc_url( admin_url( 'post-new.php?post_type=page' ) ), esc_url( '#' ) ) . '</em></p>';
		}

		if ( ! has_genre_archives_page() ) {
			$settings['archives-permalinks']['fields']['genre_archives']['disabled'] = true;
			$settings['archives-permalinks']['fields']['genre_archives']['description'] .= '<p><em>' . sprintf( __( 'You don’t have any archive page set for genres yet, which makes this setting meaningless. Define an archive page by <a href="%s">creating a standard WordPress page</a> and set it as archive in the relevant Metabox. <a href="%s">Learn more about archive pages</a>.', 'wpmovielibrary' ), esc_url( admin_url( 'post-new.php?post_type=page' ) ), esc_url( '#' ) ) . '</em></p>';
		}

		if ( ! has_collection_archives_page() ) {
			$settings['archives-permalinks']['fields']['collection_archives']['disabled'] = true;
			$settings['archives-permalinks']['fields']['collection_archives']['description'] .= '<p><em>' . sprintf( __( 'You don’t have any archive page set for collections yet, which makes this setting meaningless. Define an archive page by <a href="%s">creating a standard WordPress page</a> and set it as archive in the relevant Metabox. <a href="%s">Learn more about archive pages</a>.', 'wpmovielibrary' ), esc_url( admin_url( 'post-new.php?post_type=page' ) ), esc_url( '#' ) ) . '</em></p>';
		}

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

		$permalinks = wp_parse_args( (array) get_option( 'wpmoly_permalinks' ), $this->defaults );

		$metabox = new Template( 'permalink-settings.php' );
		$metabox->set_data( array(
			'settings'   => $this->settings,
			'permalinks' => $permalinks
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

		global $pagenow;

		if ( 'options-permalink.php' !== $pagenow ) {
			return false;
		}

		if ( empty( $_POST['wpmoly_permalinks'] ) ) {
			return false;
		}

		$permalinks = get_option( 'wpmoly_permalinks' );
		if ( ! $permalinks ) {
			$permalinks = array();
		}

		$_permalinks = wp_parse_args( $_POST['wpmoly_permalinks'], $this->defaults );
		foreach ( $_permalinks as $_name => $_permalink ) {
			if ( 'custom' == $_permalinks[ $_name ] ) {
				$_permalink = ! empty( $_permalinks["custom_{$_name}"] ) ? $this->slashit( $_permalinks["custom_{$_name}"] ) : '';
			}

			$permalinks[ $_name ] = $_permalink;
		}

		update_option( 'wpmoly_permalinks', $permalinks );
	}

	/**
	 * Make sure we have slashes before and after a permalink structure.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $permalink
	 * 
	 * @return   string
	 */
	private function slashit( $permalink ) {

		return $permalink = '/' . ltrim( untrailingslashit( (string) $permalink ), '/' );
	}

}
