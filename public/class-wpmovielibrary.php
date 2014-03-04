<?php
/**
 * WPMovieLibrary
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

/**
 * Plugin class
 *
 * @package WPMovieLibrary
 * @author  Charlie MERLAND <charlie.merland@gmail.com>
 */
class WPMovieLibrary {

	/**
	 * Plugin name
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	const NAME = 'WPMovieLibrary';

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Plugin Settings var
	 * 
	 * @since    1.0.0
	 * 
	 * @var      string
	 */
	protected $plugin_settings = 'wpml_settings';

	/**
	 * Plugin Settings
	 * 
	 * @since    1.0.0
	 * @var      array
	 */
	protected $wpml_settings = null;

	/**
	 * Plugin slug
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_slug = 'wpml';

	/**
	 * Plugin URL
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_url = '';

	/**
	 * Plugin URL
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_path = '';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$this->plugin_url  = plugins_url( WPMovieLibrary::NAME );
		$this->plugin_path = plugin_dir_path( __FILE__ );

		$this->wpml_settings = array(
			'wpml' => array(
				'name' => WPMovieLibrary::NAME,
				'url'  => $this->plugin_url,
				'path' => $this->plugin_path,
				'settings' => array(
					'tmdb_in_posts'    => 'posts_only',
					'default_post_tmdb' => array(
						'director' => __( 'Director', 'wpml' ),
						'genres'   => __( 'Genres', 'wpml' ),
						'runtime'  => __( 'Runtime', 'wpml' ),
						'overview' => __( 'Overview', 'wpml' ),
						'rating'   => __( 'Rating', 'wpml' )
					),
					'show_in_home'          => 1,
					'enable_collection'     => 1,
					'enable_actor'          => 1,
					'enable_genre'          => 1,
					'taxonomy_autocomplete' => 1,
					'deactivate' => array(
						'movies'      => 'conserve',
						'collections' => 'conserve',
						'genres'      => 'conserve',
						'actors'      => 'conserve',
						'cache'       => 'empty'
					),
					'uninstall' => array(
						'movies'      => 'convert',
						'collections' => 'convert',
						'genres'      => 'convert',
						'actors'      => 'convert',
						'cache'       => 'empty'
					)
				)
			),
			'tmdb' => array(
				'settings' => array(
					'APIKey'          => '',
					'dummy'           => 1,
					'lang'            => 'en',
					'scheme'          => 'https',
					'caching'         => 1,
					'caching_time'    => 15,
					'poster_size'     => 'original',
					'poster_featured' => 1,
					'images_size'     => 'original',
					'images_max'      => 12,
				),
				'default_fields' => array(
					'director'     => __( 'Director', 'wpml' ),
					'producer'     => __( 'Producer', 'wpml' ),
					'photography'  => __( 'Director of Photography', 'wpml' ),
					'composer'     => __( 'Original Music Composer', 'wpml' ),
					'author'       => __( 'Author', 'wpml' ),
					'writer'       => __( 'Writer', 'wpml' ),
					'cast'         => __( 'Actors', 'wpml' )
				)
			),
		);

		$this->default_post_tmdb = array(
			'title'                => __( 'Title', 'wpml' ),
			'original_title'       => __( 'Original Title', 'wpml' ),
			'release_date'         => __( 'Release Date', 'wpml' ),
			'genres'               => __( 'Genres', 'wpml' ),
			'director'             => __( 'Director', 'wpml' ),
			'cast'                 => __( 'Actors', 'wpml' ),
			'production_countries' => __( 'Country', 'wpml' ),
			'runtime'              => __( 'Runtime', 'wpml' ),
			'overview'             => __( 'Overview', 'wpml' ),
			'rating'               => __( 'Rating', 'wpml' )
		);

		// Load settings or register new ones
		$this->wpml_default_settings();
		add_action( 'init', array( $this, 'wpml_default_settings' ) );

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Load movie post type, taxonomies
		add_action( 'init', array( $this, 'wpml_register_post_type' ) );
		add_action( 'init', array( $this, 'wpml_register_taxonomy' ) );

		// Enqueue scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Add link to WP Admin Bar
		add_action( 'wp_before_admin_bar_render', array( $this, 'wpml_admin_bar_menu' ), 999 );

		// Load Movies as well as Posts in the Loop
		add_action( 'pre_get_posts', array( $this, 'wpml_show_movies_in_home_page' ) );

		// Movie content
		add_filter( 'the_content', array( $this, 'wpml_movie_content' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 * 
	 * Restore previously converted contents. If WPML was previously
	 * deactivated or uninstalled using the 'convert' option, Movies and
	 * Custom Taxonomies should still be in the database. If they are, we
	 * convert them back to WPML contents.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		global $wpdb;

		$contents = new WP_Query(
			array(
				'post_type'      => 'post',
				'posts_per_page' => -1,
				'meta_key'       => '_wpml_content_type',
				'meta_value'     => 'movie'
			)
		);

		foreach ( $contents->posts as $post ) {
			set_post_type( $post->ID, 'movie' );
			delete_post_meta( $post->ID, '_wpml_content_type', 'movie' );
		}

		$contents = $wpdb->get_results( 'SELECT term_id, slug FROM ' . $wpdb->terms . ' WHERE slug LIKE "wpml_%"' );

		$collections = array();
		$genres      = array();
		$actors      = array();

		foreach ( $contents as $term ) {
			if ( false !== strpos( $term->slug, 'wpml_collection' ) ) {
				$collections[] = $term->term_id;
			}
			else if ( false !== strpos( $term->slug, 'wpml_genre' ) ) {
				$genres[] = $term->term_id;
			}
			else if ( false !== strpos( $term->slug, 'wpml_actor' ) ) {
				$actors[] = $term->term_id;
			}
		}

		$wpdb->query( 'UPDATE ' . $wpdb->term_taxonomy . ' SET taxonomy = "collection" WHERE term_id IN (' . implode( ',', $collections ) . ')' );
		$wpdb->query( 'UPDATE ' . $wpdb->term_taxonomy . ' SET taxonomy = "genre" WHERE term_id IN (' . implode( ',', $genres ) . ')' );
		$wpdb->query( 'UPDATE ' . $wpdb->term_taxonomy . ' SET taxonomy = "actor" WHERE term_id IN (' . implode( ',', $actors ) . ')' );

		$wpdb->query(
			'UPDATE ' . $wpdb->terms . '
			 SET slug = REPLACE(slug, "wpml_collection-", ""),
			     slug = REPLACE(slug, "wpml_genre-", ""),
			     slug = REPLACE(slug, "wpml_actor-", "")'
		);

	}

	/**
	 * Fired when the plugin is deactivated.
	 * 
	 * When deactivatin/uninstalling WPML, adopt different behaviors depending
	 * on user options. Movies and Taxonomies can be kept as they are,
	 * converted to WordPress standars or removed. Default is conserve on
	 * deactivation, convert on uninstall.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		global $wpdb;

		$o           = get_option( 'wpml_settings' );
		$movies      = $o['wpml']['settings']['deactivate']['movies'];
		$collections = $o['wpml']['settings']['deactivate']['collections'];
		$genres      = $o['wpml']['settings']['deactivate']['genres'];
		$actors      = $o['wpml']['settings']['deactivate']['actors'];
		$cache       = $o['wpml']['settings']['deactivate']['cache'];

		// Handling Movie Custom Post Type on WPML deactivation

		$contents = new WP_Query(
			array(
				'post_type'      => 'movie',
				'posts_per_page' => -1
			)
		);

		if ( 'convert' == $movies ) {
			foreach ( $contents->posts as $post ) {
				set_post_type( $post->ID, 'post' );
				add_post_meta( $post->ID, '_wpml_content_type', 'movie', true );
			}
		}
		else if ( 'remove' == $movies ) {
			foreach ( $contents->posts as $post ) {
				wp_delete_post( $post->ID, true );
			}
		}
		else if ( 'delete' == $movies ) {
			foreach ( $contents->posts as $post ) {
				wp_delete_post( $post->ID, true );
				$attachments = get_children( array( 'post_parent' => $post->ID ) );
				foreach ( $attachments as $a ) {
					wp_delete_post( $a->ID, true );
				}
			}
		}

		// Handling Custom Category-like Taxonomies on WPML deactivation

		$contents = get_terms( array( 'collection' ), array() );

		if ( 'convert' == $collections ) {
			foreach ( $contents as $term ) {
				wp_update_term( $term->term_id, 'collection', array( 'slug' => 'wpml_collection-' . $term->slug ) );
				$wpdb->update(
					$wpdb->term_taxonomy,
					array( 'taxonomy' => 'category' ),
					array( 'taxonomy' => 'collection' ),
					array( '%s' )
				);
			}
		}
		else if ( 'remove' == $collections || 'delete' == $collections ) {
			foreach ( $contents as $term ) {
				wp_delete_term( $term->term_id, 'collection' );
			}
		}

		// Handling Genres Taxonomies on WPML deactivation

		$contents = get_terms( array( 'genre' ), array() );

		if ( 'convert' == $genres ) {
			foreach ( $contents as $term ) {
				wp_update_term( $term->term_id, 'genre', array( 'slug' => 'wpml_genre-' . $term->slug ) );
				$wpdb->update(
					$wpdb->term_taxonomy,
					array( 'taxonomy' => 'post_tag' ),
					array( 'taxonomy' => 'genre' ),
					array( '%s' )
				);
			}
		}
		else if ( 'remove' == $genres || 'delete' == $genres ) {
			foreach ( $contents as $term ) {
				wp_delete_term( $term->term_id, 'genre' );
			}
		}

		// Handling Actors Taxonomies on WPML deactivation

		$contents = get_terms( array( 'actor' ), array() );

		if ( 'convert' == $actors ) {
			foreach ( $contents as $term ) {
				wp_update_term( $term->term_id, 'actor', array( 'slug' => 'wpml_actor-' . $term->slug ) );
				$wpdb->update(
					$wpdb->term_taxonomy,
					array( 'taxonomy' => 'post_tag' ),
					array( 'taxonomy' => 'actor' ),
					array( '%s' )
				);
			}
		}
		else if ( 'remove' == $actors || 'delete' == $actors ) {
			foreach ( $contents as $term ) {
				wp_delete_term( $term->term_id, 'actor' );
			}
		}

		// Handling Cache cleanup on WPML deactivation
		// Adapted from Sébastien Corne's "purge-transient" snippet

		global $_wp_using_ext_object_cache;

		if ( ! $_wp_using_ext_object_cache && 'empty' == $cache ) {

			global $wpdb;

			$sql = "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE \"_transient_%_movies_%\"";
			$transients = $wpdb->get_col( $sql );

			foreach ( $transients as $transient )
				$result = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE \"{$transient}\"" );

			$wpdb->query( 'OPTIMIZE TABLE ' . $wpdb->options );
		}

	}

	/**
	 * Missing API Key notification. Display a message on plugins and
	 * Movie Settings pages reminding to save a valid API Key.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                      "Network Deactivate" action,
	 *                                       false if WPMU is disabled or
	 *                                       plugin is deactivated on an
	 *                                       individual blog.
	 */
	public function wpml_activate_notice( $network_wide ) {

		global $hook_suffix;

		$hooks = array( 'plugins.php', 'movie_page_settings' );

		if ( ! in_array( $hook_suffix, $hooks ) || false !== $this->wpml_get_api_key() )
			return false;

		echo '<div class="updated wpml">';
		echo '<p>';
		printf( __( 'Congratulation, you successfully installed WPMovieLibrary. You need a valid <acronym title="TheMovieDB">TMDb</acronym> API key to start adding your movies. Go to the <a href="%s">WPMovieLibrary Settings page</a> to add your API key.', 'wpml' ), admin_url( 'edit.php?post_type=movie&page=settings' ) );
		echo '</p>';
		echo '</div>';
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_slug, plugins_url( 'assets/css/public.css', __FILE__ ), array(), WPMovieLibrary::VERSION );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_slug, plugins_url( 'assets/js/public.js', __FILE__ ), array(), WPMovieLibrary::VERSION, true );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Add a New Movie link to WP Admin Bar.
	 * 
	 * WordPress 3.8 introduces Dashicons, for older versions we use a PNG
	 * icon instead.
	 *
	 * @since    1.0.0
	 */
	public function wpml_admin_bar_menu() {

		global $wp_admin_bar;

		$args = array(
			'id'    => 'wpmovielibrary',
			'title' => __( 'New Movie', 'wpml' ),
			'href'  => admin_url( 'post-new.php?post_type=movie' ),
			'meta'  => array(
				'title' => __( 'New Movie', 'wpml' )
			)
		);

		// Dashicons or PNG
		if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
			$args['title'] = '<img src="' . $this->plugin_url . '/admin/assets/img/icon-movie.png" alt="" />' . $args['title'];
		}
		else {
			$args['meta']['class'] = 'haz-dashicon';
		}

		$wp_admin_bar->add_menu( $args );
	}
 
	/**
	 * Show movies in default home page post list.
	 * 
	 * Add action on pre_get_posts hook to add movie to the list of
	 * queryable post_types.
	 *
	 * @since     1.0.0
	 * 
	 * @param     int       $query the WP_Query Object object to alter
	 *
	 * @return    WP_Query    Query Object
	 */
	public function wpml_show_movies_in_home_page( $query ) {
		if ( 1 == $this->wpml_o( 'wpml-settings-show_in_home' ) && is_home() && $query->is_main_query() )
			$query->set( 'post_type', array( 'post', 'movie', 'page' ) );
		return $query;
	}

	/**
	 * Show some info about movies in post view.
	 * 
	 * Add a filter on the_content hook to display infos selected in options
	 * about the movie: note, director, overview, actors…
	 *
	 * @since     1.0.0
	 * 
	 * @param     string      $content The original post content
	 *
	 * @return    string      The filtered content containing original
	 *                        content plus movie infos if available, the
	 *                        untouched original content else.
	 */
	public function wpml_movie_content( $content ) {

		if ( 'movie' != get_post_type() || 'nowhere' == $this->wpml_o( 'wpml-settings-tmdb_in_posts' ) || ( 'posts_only' == $this->wpml_o( 'wpml-settings-tmdb_in_posts' ) && ! is_singular() ) )
			return $content;

		$tmdb_data = get_post_meta( get_the_ID(), '_wpml_movie_data', true );
		$movie_rating = get_post_meta( get_the_ID(), '_wpml_movie_rating', true );
		$fields = $this->wpml_o( 'wpml-settings-default_post_tmdb' );
		$default_format = '<dt class="wpml_%s_field_title">%s</dt><dd class="wpml_%s_field_value">%s</dd>';

		if ( '' == $tmdb_data || empty( $fields ) )
			return $content;

		$html  = '<dl class="wpml_movie">';

		foreach ( $fields as $field ) {

			switch ( $field ) {
				case 'genres':
					$html .= sprintf( $default_format, $field, __( ucfirst( $field ), 'wpml' ), $field, $tmdb_data[ $field ] );
					break;
				case 'production_countries':
					$html .= sprintf( $default_format, $field, __( 'Country', 'wpml' ), $field, $tmdb_data[ $field ] );
					break;
				case 'release_date':
					$html .= sprintf( $default_format, $field, __( 'Release Date', 'wpml' ), $field, date_i18n( get_option( 'date_format' ), strtotime( $tmdb_data[ $field ] ) ) );
					break;
				case 'original_title':
					$html .= sprintf( $default_format, $field, __( 'Original Title', 'wpml' ), $field, $tmdb_data[ $field ] );
					break;
				case 'director':
					$html .= sprintf( $default_format, $field, __( 'Directed by', 'wpml' ), $field, $tmdb_data[ $field ] );
					break;
				case 'cast':
					$html .= sprintf( $default_format, $field, __( 'Staring', 'wpml' ), $field, $tmdb_data[ $field ] );
					break;
				case 'rating':
					$html .= sprintf( $default_format, $field, __( 'Movie rating', 'wpml' ), $field, sprintf( '<div class="movie_rating_display stars_%s"></div>', ( '' == $movie_rating ? '0_0' : str_replace( '.', '_', $movie_rating ) ) ) );
					break;
				default:
					if ( in_array( $field, array_keys( $this->default_post_tmdb ) ) && isset( $tmdb_data[ $field ] ) )
						$html .= sprintf( $default_format, $field, __( ucfirst( $field ), 'wpml' ), $field, $tmdb_data[ $field ] );
					break;
			}
		}

		$html .= '</dl>';

		$content = $html . $content;

		return $content;
	}

	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *               Custom Post Types, Status & Taxonomy
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * Register a 'movie' custom post type and 'import-draft' post status
	 *
	 * @since    1.0.0
	 */
	public function wpml_register_post_type() {

		$labels = array(
			'name'               => __( 'Movies', 'wpml' ),
			'singular_name'      => __( 'Movie', 'wpml' ),
			'add_new'            => __( 'Add New', 'wpml' ),
			'add_new_item'       => __( 'Add New Movie', 'wpml' ),
			'edit_item'          => __( 'Edit Movie', 'wpml' ),
			'new_item'           => __( 'New Movie', 'wpml' ),
			'all_items'          => __( 'All Movies', 'wpml' ),
			'view_item'          => __( 'View Movie', 'wpml' ),
			'search_items'       => __( 'Search Movies', 'wpml' ),
			'not_found'          => __( 'No movies found', 'wpml' ),
			'not_found_in_trash' => __( 'No movies found in Trash', 'wpml' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Movies', 'wpml' )
		);

		$args = array(
			'labels'             => $labels,
			'rewrite'            => array(
				'slug'       => 'movies'
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'has_archive'        => true,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
			'menu_position'      => 5
		);

		// Dashicons or PNG
		$args['menu_icon'] = ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ? 'dashicons-format-video' : $this->plugin_url . '/admin/assets/img/icon-movie.png' );

		register_post_type( 'movie', $args );

		register_post_status( 'import-draft', array(
			'label'                     => _x( 'Imported Draft', 'wpml' ),
			'public'                    => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => false,
			'label_count'               => _n_noop( 'Imported Draft <span class="count">(%s)</span>', 'Imported Draft <span class="count">(%s)</span>' ),
		) );
	}

	/**
	 * Register a 'Collections' custom taxonomy to aggregate movies
	 *
	 * @since    1.0.0
	 */
	public function wpml_register_taxonomy() {

		if ( 1 == $this->wpml_o( 'wpml-settings-enable_collection' ) ) {
			register_taxonomy(
				'collection',
				'movie',
				array(
					'labels'   => array(
						'name'          => __( 'Collections', 'wpml' ),
						'add_new_item'  => __( 'New Movie Collection', 'wpml' )
					),
					'show_tagcloud' => false,
					'hierarchical'  => true,
					'rewrite' => array( 'slug' => 'collection' )
				)
			);
		}

		if ( 1 == $this->wpml_o( 'wpml-settings-enable_actor' ) ) {
			register_taxonomy(
				'actor',
				'movie',
				array(
					'labels'   => array(
						'name'          => __( 'Actors', 'wpml' ),
						'add_new_item'  => __( 'New Actor', 'wpml' )
					),
					'show_tagcloud' => true,
					'hierarchical'  => false,
					'rewrite' => array( 'slug' => 'actor' )
				)
			);
		}

		if ( 1 == $this->wpml_o( 'wpml-settings-enable_genre' ) ) {
			register_taxonomy(
				'genre',
				'movie',
				array(
					'labels'   => array(
						'name'          => __( 'Genres', 'wpml' ),
						'add_new_item'  => __( 'New Genre', 'wpml' )
					),
					'show_tagcloud' => true,
					'hierarchical'  => false,
					'rewrite' => array( 'slug' => 'genre' )
				)
			);
		}

	}


	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                              Options
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * Load WPML default settings if unexisting.
	 *
	 * @since    1.0.0
	 */
	public function wpml_default_settings( $force = false ) {

		$options = get_option( $this->plugin_settings );
		if ( ( false === $options || ! is_array( $options ) ) || true == $force ) {
			delete_option( $this->plugin_settings );
			add_option( $this->plugin_settings, $this->wpml_settings );

		}
	}

	/**
	 * Get TMDb API if available
	 *
	 * @since    1.0.0
	 */
	public function wpml_get_api_key() {
		$api_key = $this->wpml_o('tmdb-settings-APIKey');
		return ( '' != $api_key ? $api_key : false );
	}

	/**
	 * Are we on TMDb dummy mode?
	 *
	 * @since    1.0.0
	 */
	public function wpml_is_dummy() {
		$dummy = ( 1 == $this->wpml_o('tmdb-settings-dummy') ? true : false );
		return $dummy;
	}

	/**
	 * Built-in option finder/modifier
	 * Default behavior with no empty search and value params results in
	 * returning the complete WPML options' list.
	 * 
	 * If a search query is specified, navigate through the options'
	 * array and return the asked option if existing, empty string if it
	 * doesn't exist.
	 * 
	 * If a replacement value is specified and the search query is valid,
	 * update WPML options with new value.
	 * 
	 * Return can be string, boolean or array. If search, return array or
	 * string depending on search result. If value, return boolean true on
	 *  success, false on failure.
	 * 
	 * @param    string        Search query for the option: 'aaa-bb-c'. Default none.
	 * @param    string        Replacement value for the option. Default none.
	 * 
	 * @return   string|boolean|array        option array of string, boolean on update.
	 *
	 * @since    1.0.0
	 */
	public function wpml_o( $search = '', $value = null ) {

		$options = get_option( $this->plugin_settings, $this->wpml_settings );

		if ( '' != $search && is_null( $value ) ) {
			$s = explode( '-', $search );
			$o = $options;
			while ( count( $s ) ) {
				$k = array_shift( $s );
				if ( isset( $o[ $k ] ) )
					$o = $o[ $k ];
				else
					$o = '';
			}
		}
		else if ( '' != $search && ! is_null( $value ) ) {
			$s = explode( '-', $search );
			$this->wpml_o_( $options, $s, $value );
			$o = update_option( $this->plugin_settings, $options );
		}
		else {
			$o = $options;
		}

		return $o;
	}

	/**
	 * Built-in option modifier
	 * Navigate through WPML options to find a matching option and update
	 * its value.
	 * 
	 * @param    array         Options array passed by reference
	 * @param    string        key list to match the specified option
	 * @param    string        Replacement value for the option. Default none
	 *
	 * @since    1.0.0
	 */
	private function wpml_o_( &$array, $key, $value = '' ) {
		$a = &$array;
		foreach ( $key as $k )
			$a = &$a[ $k ];
		$a = $value;
	}

}
