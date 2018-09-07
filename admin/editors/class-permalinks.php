<?php
/**
 * Define the Permalink Settings class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\admin\editors;

/**
 * Handle the plugin's URL rewriting settings.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Permalinks {

	/**
	 * Default permalinks slugs.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $slugs;

	/**
	 * Default permalink settings.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $defaults;

	/**
	 * Existing permalinks settings.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $permalinks;

	/**
	 * Add a new block to the Permalink settings option page.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register() {

		$this->register_permalinks();
		$this->register_settings();

		add_settings_section( 'wpmoly-permalink', __( 'Movie Library Permalinks', 'wpmovielibrary' ), array( $this, 'register_sections' ), 'permalink' );
	}

	/**
	 * Display a custom metabox-ish permalink settings block.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_sections() {

		$rules = get_option( 'rewrite_rules' );
		$enabled = ! empty( $rules );

		$metabox = wpmoly_get_template( 'permalink-settings.php' );
		$metabox->set_data( array(
			'settings'   => $this->settings,
			'permalinks' => $this->permalinks,
			'enabled'    => $enabled,
		) );

		$metabox->render();
	}

	/**
	 * Retrieve permalink settings.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function get_permalinks() {

		$permalinks = array();
		if ( is_null( $this->permalinks ) ) {
			$permalinks = get_option( 'wpmoly_permalinks' );
		}

		if ( empty( $permalinks ) ) {
			$permalinks = array();
		}

		$this->permalinks = wp_parse_args( $permalinks, $this->defaults );

		return $this->permalinks;
	}

	/**
	 * Save permalink settings.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function set_permalinks() {

		/**
		 * Filter the permalink settings before saving.
		 *
		 * @since 3.0.0
		 *
		 * @param array $permalinks
		 */
		$permalinks = apply_filters( 'wpmoly/filter/permalinks', $this->permalinks );

		update_option( 'wpmoly_permalinks', $permalinks );
	}

	/**
	 * Register permalinks.
	 *
	 * Add a set of custom permalinks for movies and taxonomies.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_permalinks() {

		$slugs = array(
			'movie'       => _x( 'movie', 'slug', 'wpmovielibrary' ),
			'actor'       => _x( 'actor', 'slug', 'wpmovielibrary' ),
			'collection'  => _x( 'collection', 'slug', 'wpmovielibrary' ),
			'genre'       => _x( 'genre', 'slug', 'wpmovielibrary' ),
			'movies'      => _x( 'movies', 'slug', 'wpmovielibrary' ),
			'actors'      => _x( 'actors', 'slug', 'wpmovielibrary' ),
			'collections' => _x( 'collections', 'slug', 'wpmovielibrary' ),
			'genres'      => _x( 'genres', 'slug', 'wpmovielibrary' ),
		);

		/**
		 * Default permalink slugs.
		 *
		 * @since 3.0.0
		 *
		 * @param array $slugs
		 */
		$this->slugs = apply_filters( 'wpmoly/filter/permalinks/slugs/defaults', $slugs );

		$defaults = array(
			'movie'       => '/' . $this->slugs['movie'] . '/%postname%/',
			'actor'       => '/' . $this->slugs['actor'] . '/%actor%/',
			'collection'  => '/' . $this->slugs['collection'] . '/%collection%/',
			'genre'       => '/' . $this->slugs['genre'] . '/%genre%/',
			'movies'      => '/' . $this->slugs['movies'] . '/',
			'actors'      => '/' . $this->slugs['actors'] . '/',
			'collections' => '/' . $this->slugs['collections'] . '/',
			'genres'      => '/' . $this->slugs['genres'] . '/',
		);

		/**
		 * Default permalink structures.
		 *
		 * @since 3.0.0
		 *
		 * @param array $defaults
		 */
		$this->defaults = apply_filters( 'wpmoly/filter/permalinks/defaults', $defaults );

		$this->permalinks = $this->get_permalinks();
	}

	/**
	 * Register settings.
	 *
	 * Use a ButterBean-like set of settings to create a settings metabox
	 * for the Permalinks setting page.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_settings() {

		$settings = array(
			'movie-permalinks' => array(
				'title'  => __( 'Movies', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-movie',
				'fields' => array(
					'movie' => array(
						'type' => 'radio',
						'title' => __( 'Movie Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single movie pages. <a href="https://codex.wordpress.org/Using_Permalinks">Standard tags</a> are supported along with specific movie tags.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/interstellar/',
							),
							'title_year' => array(
								'label'  => __( 'Title and Year', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%year%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/2016/interstellar/',
							),
							'title_month' => array(
								'label'  => __( 'Title and Month', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%year%/%monthnum%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/2016/08/interstellar/',
							),
							'title_release_year' => array(
								'label'  => __( 'Title and Release Year', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%release_year%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/2014/interstellar/',
							),
							'title_release_month' => array(
								'label'  => __( 'Title and Release Month', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%release_year%/%release_monthnum%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/2014/10/interstellar/',
							),
							'imdb_id' => array(
								'label'  => __( 'Title and IMDb ID', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%imdb_id%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/tt0816692/interstellar/',
							),
							'tmdb_id' => array(
								'label'  => __( 'Title and TMDb ID', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%tmdb_id%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/157336/interstellar/',
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/' . trim( $this->permalinks['movies'], '/' ) . '/',
								'description' => home_url() . '/' . trim( $this->permalinks['movies'], '/' ) . '/interstellar/',
							),
						),
						'default' => 'archive',
						'custom'  => true,
					),
					'movies' => array(
						'type' => 'radio',
						'title' => __( 'Movie Archives Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for movies archives pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movies'] . '/',
								'description' => home_url() . '/' . $this->slugs['movies'] . '/',
							),
						),
						'default' => 'simple',
						'custom'  => true,
					),
				),
			),
			'actor-permalinks' => array(
				'title'  => __( 'Actors', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-actor',
				'fields' => array(
					'actor' => array(
						'type' => 'radio',
						'title' => __( 'Actor Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single actor pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['actor'] . '/',
								'description' => home_url() . '/' . $this->slugs['actor'] . '/matthew-mcconaughey/',
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/' . trim( $this->permalinks['actors'], '/' ) . '/',
								'description' => home_url() . '/' . trim( $this->permalinks['actors'], '/' ) . '/matthew-mcconaughey/',
							),
						),
						'default' => 'simple',
						'custom'  => false,
					),
					'actors' => array(
						'type' => 'radio',
						'title' => __( 'Actors Archives Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for actors archives pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['actors'] . '/',
								'description' => home_url() . '/' . $this->slugs['actors'] . '/',
							),
						),
						'default' => 'simple',
						'custom'  => true,
					),
				),
			),
			'genre-permalinks' => array(
				'title'  => __( 'Genres', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-tags',
				'fields' => array(
					'genre' => array(
						'type' => 'radio',
						'title' => __( 'Genre Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single genre pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['genre'] . '/',
								'description' => home_url() . '/' . $this->slugs['genre'] . '/science-fiction/',
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/' . trim( $this->permalinks['genres'], '/' ) . '/',
								'description' => home_url() . '/' . trim( $this->permalinks['genres'], '/' ) . '/science-fiction/',
							),
						),
						'default' => 'simple',
						'custom'  => false,
					),
					'genres' => array(
						'type' => 'radio',
						'title' => __( 'Genres Archives Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for genres archives pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['genres'] . '/',
								'description' => home_url() . '/' . $this->slugs['genres'] . '/',
							),
						),
						'default' => 'simple',
						'custom'  => true,
					),
				),
			),
			'collection-permalinks' => array(
				'title'  => __( 'Collections', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-folder',
				'fields' => array(
					'collection' => array(
						'type' => 'radio',
						'title' => __( 'Collection Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single collection pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['collection'] . '/',
								'description' => home_url() . '/' . $this->slugs['collection'] . '/christopher-nolan/',
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/' . trim( $this->permalinks['collections'], '/' ) . '/',
								'description' => home_url() . '/' . trim( $this->permalinks['collections'], '/' ) . '/christopher-nolan/',
							),
						),
						'default' => 'simple',
						'custom'  => false,
					),
					'collections' => array(
						'type' => 'radio',
						'title' => __( 'Collections Archives Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for collections archives pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['collections'] . '/',
								'description' => home_url() . '/' . $this->slugs['collections'] . '/',
							),
						),
						'default' => 'simple',
						'custom'  => true,
					),
				),
			),
		);

		if ( has_movie_archives_page() ) {
			$settings['movie-permalinks']['fields']['movies']['disabled'] = true;
			$settings['movie-permalinks']['fields']['movies']['description'] .= '<p><em>' . sprintf( __( 'An archive page has already been set for movies archives. This page will replace the default WordPress archive display and therefore use the page permalink. You can modify this by <a href="%s" target="_blank">editing the page</a> post name.', 'wpmovielibrary' ), esc_url( admin_url( 'post.php?post=' . get_movie_archives_page_id() . '&amp;action=edit' ) ) ) . '</em></p>';
		}

		if ( ! has_actor_archives_page() ) {
			$settings['actor-permalinks']['fields']['actors']['disabled'] = true;
			$settings['actor-permalinks']['fields']['actors']['description'] .= '<p><em>' . sprintf( __( 'You don’t have any archive page set for actors yet, which makes this setting meaningless. Define an archive page by <a href="%1$s">creating a standard WordPress page</a> and set it as archive in the relevant Metabox. <a href="%2$s">Learn more about archive pages</a>.', 'wpmovielibrary' ), esc_url( admin_url( 'post-new.php?post_type=page' ) ), esc_url( '#' ) ) . '</em></p>';
		}

		if ( ! has_genre_archives_page() ) {
			$settings['genre-permalinks']['fields']['genres']['disabled'] = true;
			$settings['genre-permalinks']['fields']['genres']['description'] .= '<p><em>' . sprintf( __( 'You don’t have any archive page set for genres yet, which makes this setting meaningless. Define an archive page by <a href="%1$s">creating a standard WordPress page</a> and set it as archive in the relevant Metabox. <a href="%2$s">Learn more about archive pages</a>.', 'wpmovielibrary' ), esc_url( admin_url( 'post-new.php?post_type=page' ) ), esc_url( '#' ) ) . '</em></p>';
		}

		if ( ! has_collection_archives_page() ) {
			$settings['collection-permalinks']['fields']['collections']['disabled'] = true;
			$settings['collection-permalinks']['fields']['collections']['description'] .= '<p><em>' . sprintf( __( 'You don’t have any archive page set for collections yet, which makes this setting meaningless. Define an archive page by <a href="%1$s">creating a standard WordPress page</a> and set it as archive in the relevant Metabox. <a href="%2$s">Learn more about archive pages</a>.', 'wpmovielibrary' ), esc_url( admin_url( 'post-new.php?post_type=page' ) ), esc_url( '#' ) ) . '</em></p>';
		}

		/**
		 * Filter default permalinks settings.
		 *
		 * @since 3.0.0
		 *
		 * @param array $settings
		 */
		$this->settings = apply_filters( 'wpmoly/filter/permalinks/settings', $settings );
	}

	/**
	 * Save custom permalinks.
	 *
	 * @since 3.0.0
	 *
	 * @access public
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
				if ( 'custom' == $new_permalinks[ $name ] && ! empty( $new_permalinks[ "custom_{$name}" ] ) ) {
					$permalink = $new_permalinks[ "custom_{$name}" ];
					$permalink = str_replace( array(
						'%postname%',
						'%movie%',
						'%actor%',
						'%collection%',
						'%genre%',
					), '', $permalink );
					$permalink = preg_replace( '/([^:])(\/{2,})/', '$1/', $permalink );
				} else {
					$permalink = $new_permalinks[ $name ];
				}
			}

			$permalinks[ $name ] = trailingslashit( $permalink );
		}

		$this->permalinks = $permalinks;
		$this->set_permalinks();
	}

	/**
	 * Register admin notice.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_notice() {

		$check = get_transient( '_wpmoly_reset_permalinks_notice' );
		if ( ! $check ) {
			return false;
		}

		/**
		 * Filter the update permalinks notice message.
		 *
		 * @since 3.0
		 *
		 * @param string $message Update permalinks notice message content.
		 */
		$message = apply_filters( 'wpmoly/filter/permalinks/notice/message', __( 'Changes have been made to the archive pages, permalinks settings need to be update. Simply reload the %s, no saving required.', 'wpmovielibrary' ) );

		/**
		 * Filter the update permalinks notice CSS class names.
		 *
		 * @since 3.0
		 *
		 * @param string $message Update permalinks notice message content.
		 */
		$class = apply_filters( 'wpmoly/filter/permalinks/notice/style', array(
			'notice',
			'notice-wpmoly',
			'notice-info',
			'is-dismissible',
		) );

		$class = implode( ' ', $class );

		echo '<div class="' . esc_attr( $class ) . '"><p>' . sprintf( esc_html( $message ), '<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '" target="_blank">' . __( 'Permalinks page', 'wpmovielibrary' ) . '</a>' ) . '</p></div>';
	}

	/**
	 * Create admin notice transient.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function set_notice() {

		return set_transient( '_wpmoly_reset_permalinks_notice', 1 );
	}

	/**
	 * Remove admin notice transient.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function delete_notice() {

		$notice = get_transient( '_wpmoly_reset_permalinks_notice' );
		if ( 1 !== $notice ) {
			delete_transient( '_wpmoly_reset_permalinks_notice' );
		}
	}

	/**
	 * Add custom rewrite rules for movies.
	 *
	 * Define a list of variants for movies archive to match meta/detail
	 * permalinks.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $rules Existing rewrite rules.
	 *
	 * @return array
	 */
	public function set_movie_archives_rewrite_rules( $rules = array() ) {

		global $wp_rewrite;

		$new_rules = array();

		/**
		 * Filter default movie archives rewrite variants.
		 *
		 * Each variant must define a rule and a matching array of vars.
		 * Defaults variants support meta/detail name translation, used
		 * to set the grid preset to 'custom'.
		 *
		 * @since 3.0.0
		 *
		 * @param array $variants Default variants.
		 */
		$variants = apply_filters( 'wpmoly/filter/movie_archives/rewrite/variants', array(
			array(
				'rule' => '([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})',
				'vars' => array(
					'year'     => '',
					'monthnum' => '',
					'day'      => '',
				),
			),
			array(
				'rule' => '([0-9]{4})/([0-9]{1,2})',
				'vars' => array(
					'year'     => '',
					'monthnum' => '',
				),
			),
			array(
				'rule' => '([0-9]{4})',
				'vars' => array(
					'year' => '',
				),
			),
			array(
				'rule' => '(adult|' . _x( 'adult', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'adult',
					'adult'  => '',
				),
			),
			array(
				'rule' => '(author|' . _x( 'author', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'author',
					'author' => '',
				),
			),
			array(
				'rule' => '(budget|' . _x( 'budget', 'permalink', 'wpmovielibrary' ) . ')/([0-9]+|[0-9]+-|[0-9]+-[0-9]+)',
				'vars' => array(
					'preset' => 'budget',
					'budget' => '',
				),
			),
			array(
				'rule' => '(certification|' . _x( 'certification', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'        => 'certification',
					'certification' => '',
				),
			),
			array(
				'rule' => '(company|production-company|production-companies|' . _x( 'company', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'  => 'company',
					'company' => '',
				),
			),
			array(
				'rule' => '(composer|' . _x( 'composer', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'   => 'composer',
					'composer' => '',
				),
			),
			array(
				'rule' => '(country|production-country|production-countries|' . _x( 'country', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'  => 'country',
					'country' => '',
				),
			),
			array(
				'rule' => '(director|' . _x( 'director', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'   => 'director',
					'director' => '',
				),
			),
			array(
				'rule' => '(format|' . _x( 'format', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'my_format',
					'format' => '',
				),
			),
			array(
				'rule' => '(language|' . _x( 'language', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'   => 'my_language',
					'language' => '',
				),
			),
			array(
				'rule' => '(languages|spoken-languages|' . _x( 'spoken-languages', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'    => 'languages',
					'languages' => '',
				),
			),
			array(
				'rule' => '(local-release|local-release-date|' . _x( 'local-release-date', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'        => 'local_release',
					'local_release' => '',
				),
			),
			array(
				'rule' => '(media|' . _x( 'media', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'my_media',
					'media'  => '',
				),
			),
			array(
				'rule' => '(photography|' . _x( 'photography', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'      => 'photography',
					'photography' => '',
				),
			),
			array(
				'rule' => '(producer|' . _x( 'producer', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'   => 'producer',
					'producer' => '',
				),
			),
			array(
				'rule' => '(rating|' . _x( 'rating', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'my_rating',
					'my_rating' => '',
				),
			),
			array(
				'rule' => '(release|release-date|' . _x( 'release-date', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'  => 'release',
					'release' => '',
				),
			),
			array(
				'rule' => '(revenue|' . _x( 'revenue', 'permalink', 'wpmovielibrary' ) . ')/([0-9]+|[0-9]+-|[0-9]+-[0-9]+)',
				'vars' => array(
					'preset'  => 'revenue',
					'revenue' => '',
				),
			),
			array(
				'rule' => '(runtime|' . _x( 'runtime', 'permalink', 'wpmovielibrary' ) . ')/([0-9]+|[0-9]+-|[0-9]+-[0-9]+)',
				'vars' => array(
					'preset'  => 'runtime',
					'runtime' => '',
				),
			),
			array(
				'rule' => '(status|' . _x( 'status', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'my_status',
					'status' => '',
				),
			),
			array(
				'rule' => '(subtitles|' . _x( 'subtitles', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'    => 'my_subtitles',
					'subtitles' => '',
				),
			),
			array(
				'rule' => '(writer|' . _x( 'writer', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'writer',
					'writer' => '',
				),
			),
		) );

		$movies = isset( $this->permalinks['movies'] ) ? $this->permalinks['movies'] : 'movies';

		if ( ! has_movie_archives_page() ) {
			// Default: no archive page set
			$query = 'index.php?post_type=movie';
			$rule  = trim( $movies, '/' );
			$index = 1;
		} else {
			// Existing archive page
			$archive_page = get_movie_archives_page_id();

			$index = 2;
			$query = sprintf( 'index.php?page_id=%d', $archive_page );

			$rule1 = trim( str_replace( home_url(), '', get_permalink( $archive_page ) ), '/' );
			$rule2 = trim( $movies, '/' );
			$rule  = "(movies|$rule2|$rule1)";

			// Remove default archive rules.
			foreach ( $rules as $r => $v ) {
				if ( false !== strpos( $r, $rule1 ) ) {
					unset( $rules[ $r ] );
				}
			}

			// Set new default archive rules.
			$new_rules[ $rule . '/?$' ]                               = $query;
			$new_rules[ $rule . '/feed/(feed|rdf|rss|rss2|atom)/?$' ] = $query . '&feed=' . $wp_rewrite->preg_index( $index );
			$new_rules[ $rule . '/(feed|rdf|rss|rss2|atom)/?$' ]      = $query . '&feed=' . $wp_rewrite->preg_index( $index );
			$new_rules[ $rule . '/page/([0-9]{1,})/?$' ]              = $query . '&paged=' . $wp_rewrite->preg_index( $index );
		}

		// Loop through allowed variants
		foreach ( $variants as $variant ) {

			$_query = $query;
			$i = $index;

			// Use all vars to increment counter, but don't actually
			// put empty vars in the regex.
			foreach ( $variant['vars'] as $var => $value ) {
				if ( empty( $value ) ) {
					$_query = $_query . '&' . $var . '=' . $wp_rewrite->preg_index( $i );
				} else {
					$_query = $_query . '&' . $var . '=' . $value;
				}
				$i++;
			}

			$_rule = $rule . '/' . $variant['rule'];

			$new_rules[ $_rule . '/?$' ]                               = $_query;
			$new_rules[ $_rule . '/embed/?$' ]                         = $_query . '&embed=true';
			$new_rules[ $_rule . '/trackback/?$' ]                     = $_query . '&tb=1';
			$new_rules[ $_rule . '/feed/(feed|rdf|rss|rss2|atom)/?$' ] = $_query . '&feed=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '/(feed|rdf|rss|rss2|atom)/?$' ]      = $_query . '&feed=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '/page/([0-9]{1,})/?$' ]              = $_query . '&paged=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '/comment-page-([0-9]{1,})/?$' ]      = $_query . '&cpage=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '(?:/([0-9]+))?/?$' ]                 = $_query . '&page=' . $wp_rewrite->preg_index( $i + 1 );
		}

		//print_r( $new_rules ); die();

		$rules = array_merge( $new_rules, $rules );

		return $rules;
	}

	/**
	 * Add custom rewrite rules for movies.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $rules Existing rewrite rules.
	 *
	 * @return array
	 */
	public function set_taxonomy_archives_rewrite_rules( $rules = array() ) {

		global $wp_rewrite;

		$new_rules = array();

		$taxonomies = array( 'actor', 'collection', 'genre' );
		foreach ( $taxonomies as $taxonomy ) {

			$taxonomy = get_taxonomy( $taxonomy );
			if ( ! $taxonomy ) {
				continue;
			}

			if ( ! has_archives_page( $taxonomy->name ) ) {
				continue;
			}

			$archive_page = get_archives_page_id( $taxonomy->name );

			$index = 2;
			$query = sprintf( 'index.php?page_id=%d', $archive_page );

			$rule1 = $taxonomy->rewrite['slug'];
			$rule2 = trim( get_taxonomy_archive_link( $taxonomy->name, 'relative' ), '/' );
			$rule  = "($rule2|$rule1)";

			foreach ( $rules as $r => $v ) {
				if ( false !== strpos( $r, $rule1 ) ) {
					unset( $rules[ $r ] );
				}
			}

			$new_rules[ $rule . '/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' ] = $query . '&' . $taxonomy->name . '=' . $wp_rewrite->preg_index( $index ) . '&feed=' . $wp_rewrite->preg_index( $index + 1 );
			$new_rules[ $rule . '/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' ]      = $query . '&' . $taxonomy->name . '=' . $wp_rewrite->preg_index( $index ) . '&feed=' . $wp_rewrite->preg_index( $index + 1 );
			$new_rules[ $rule . '/([^/]+)/page/([0-9]{1,})/?$' ]              = $query . '&' . $taxonomy->name . '=' . $wp_rewrite->preg_index( $index ) . '&paged=' . $wp_rewrite->preg_index( $index + 1 );
			$new_rules[ $rule . '/([^/]+)/embed/?$' ]                         = $query . '&' . $taxonomy->name . '=' . $wp_rewrite->preg_index( $index ) . '&embed=true';
			$new_rules[ $rule . '/([^/]+)/?$' ]                               = $query . '&' . $taxonomy->name . '=' . $wp_rewrite->preg_index( $index );
		}

		$rules = array_merge( $new_rules, $rules );

		return $rules;
	}

}
