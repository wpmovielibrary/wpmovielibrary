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
	private $defaults;

	/**
	 * Custom permalink settings.
	 * 
	 * @var    array
	 */
	private $settings;

	/**
	 * Existing permalink structures
	 * 
	 * @var    array
	 */
	private $permalinks;

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
			'movie_base'           => _x( 'movie', 'slug', 'wpmovielibrary' ),
			'actor_base'           => _x( 'actor', 'slug', 'wpmovielibrary' ),
			'collection_base'      => _x( 'collection', 'slug', 'wpmovielibrary' ),
			'genre_base'           => _x( 'genre', 'slug', 'wpmovielibrary' ),

			'movies_base'          => _x( 'movies', 'slug', 'wpmovielibrary' ),
			'actors_base'          => _x( 'actors', 'slug', 'wpmovielibrary' ),
			'collections_base'     => _x( 'collections', 'slug', 'wpmovielibrary' ),
			'genres_base'          => _x( 'genres', 'slug', 'wpmovielibrary' ),

			'movie_permalink'      => '/%movie_base%/%postname%/',
			'actor_permalink'      => '/%actor_base%/%actor%/',
			'collection_permalink' => '/%collection_base%/%collection%/',
			'genre_permalink'      => '/%genre_base%/%genre%/',

			'movie_archives'       => '/%movies_base%/',
			'actor_archives'       => '/%actors_base%/',
			'collection_archives'  => '/%collections_base%/',
			'genre_archives'       => '/%genres_base%/',
		);

		/**
		 * Default permalink structures settings.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $defaults
		 */
		$this->defaults = apply_filters( 'wpmoly/filter/permalinks/structure/defaults', $defaults );

		$this->permalinks = $this->get_permalinks();

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
								'value'  => '/%movie_base%/%postname%/',
								'description' => home_url() . '/' . $this->permalinks['movie_base'] . '/interstellar/'
							),
							'title_year' => array(
								'label'  => __( 'Title and Year', 'wpmovielibrary' ),
								'value'  => '/%movie_base%/%year%/%postname%/',
								'description' => home_url() . '/' . $this->permalinks['movie_base'] . '/2016/interstellar/'
							),
							'title_month' => array(
								'label'  => __( 'Title and Month', 'wpmovielibrary' ),
								'value'  => '/%movie_base%/%year%/%monthnum%/%postname%/',
								'description' => home_url() . '/' . $this->permalinks['movie_base'] . '/2016/08/interstellar/'
							),
							'title_release_year' => array(
								'label'  => __( 'Title and Release Year', 'wpmovielibrary' ),
								'value'  => '/%movie_base%/%release_year%/%postname%/',
								'description' => home_url() . '/' . $this->permalinks['movie_base'] . '/2014/interstellar/'
							),
							'title_release_month' => array(
								'label'  => __( 'Title and Release Month', 'wpmovielibrary' ),
								'value'  => '/%movie_base%/%release_year%/%release_monthnum%/%postname%/',
								'description' => home_url() . '/' . $this->permalinks['movie_base'] . '/2014/10/interstellar/'
							),
							'imdb_id' => array(
								'label'  => __( 'IMDb ID', 'wpmovielibrary' ),
								'value'  => '/%movie_base%/%imdb_id%/',
								'description' => home_url() . '/' . $this->permalinks['movie_base'] . '/tt0816692/'
							),
							'tmdb_id' => array(
								'label'  => __( 'TMDb ID', 'wpmovielibrary' ),
								'value'  => '/%movie_base%/%tmdb_id%/',
								'description' => home_url() . '/' . $this->permalinks['movie_base'] . '/157336/'
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/%movies_base%/%postname%/',
								'description' => home_url() . '/' . $this->permalinks['movies_base'] . '/interstellar/'
							)
						),
						'default' => 'archive'
					),
					'movie_base' => array(
						'type' => 'text',
						'title' => __( 'Movie base', 'wpmovielibrary' ),
						'description' => __( 'Base name for movie permalinks. Default is "movie".', 'wpmovielibrary' ),
						'default' => _x( 'movie', 'slug', 'wpmovielibrary' )
					),
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
								'value'  => '/%actor_base%/%actor%/',
								'description' => home_url() . '/' . $this->permalinks['actor_base'] . '/matthew-mcconaughey/'
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/%actors_base%/%actor%/',
								'description' => home_url() . '/' . $this->permalinks['actors_base'] . '/interstellar/'
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
								'value'  => '/%genre_base%/%genre%/',
								'description' => home_url() . '/' . $this->permalinks['genre_base'] . '/science-fiction/'
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/%genres_base%/%genre%/',
								'description' => home_url() . '/' . $this->permalinks['genres_base'] . '/interstellar/'
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
								'value'  => '/%collection_base%/%collection%/',
								'description' => home_url() . '/' . $this->permalinks['collection_base'] . '/christopher-nolan/'
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/%collections_base%/%collection%/',
								'description' => home_url() . '/' . $this->permalinks['collections_base'] . '/interstellar/'
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
		$this->settings = apply_filters( 'wpmoly/filter/permalinks/settings', $settings );
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

		$enabled = ! empty( get_option( 'rewrite_rules' ) );

		$metabox = new Template( 'permalink-settings.php' );
		$metabox->set_data( array(
			'settings'   => $this->settings,
			'permalinks' => $this->permalinks,
			'enabled'    => $enabled
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

		$permalinks = array();
		$new_permalinks = $_POST['wpmoly_permalinks'];

		foreach ( $this->defaults as $name => $permalink ) {
			if ( ! empty( $new_permalinks[ $name ] ) ) {
				if ( 'custom' == $new_permalinks[ $name ] && ! empty( $new_permalinks["custom_{$name}"] ) ) {
					$permalink = $new_permalinks["custom_{$name}"];
				} else {
					$permalink = $new_permalinks[ $name ];
				}
			}

			if ( false === strpos( $name, 'base' ) ) {
				$permalink = $this->slashit( $permalink );
			}

			$permalinks[ $name ] = $permalink;
		}

		$this->permalinks = $permalinks;
		$this->set_permalinks();
	}

	/**
	 * Retrieve permalink settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function get_permalinks() {

		$permalinks = array();
		if ( is_null( $this->permalinks ) ) {
			$permalinks = get_option( 'wpmoly_permalinks' );
		}

		if ( empty( $permalinks ) ) {
			$permalinks = array();
		}

		return $this->permalinks = wp_parse_args( $permalinks, $this->defaults );
	}

	/**
	 * Save permalink settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	private function set_permalinks() {

		/**
		 * Filter the permalink settings before saving.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $permalinks
		 */
		$permalinks = apply_filters( 'wpmoly/filter/permalinks', $this->permalinks );

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
