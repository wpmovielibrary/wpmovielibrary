<?php
/**
 * WPMovieLibrary Movie Class extension.
 * 
 * Add and manage a Movie Custom Post Type
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Movies' ) ) :

	class WPMOLY_Movies extends WPMOLY_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {
			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', __CLASS__ . '::register_post_type', 10 );

			// Load Movies as well as Posts in the Loop
			add_action( 'pre_get_posts', __CLASS__ . '::show_movies_in_home_page', 10, 1 );
			add_filter( 'pre_get_posts', __CLASS__ . '::filter_search_query', 11, 1 );

			// Movie content
			add_filter( 'the_content', __CLASS__ . '::movie_content' );
			add_filter( 'get_the_excerpt', __CLASS__ . '::movie_excerpt' );

			// Pass meta through URLs
			add_action( 'pre_get_posts', __CLASS__ . '::movies_query_meta', 10, 1 );
			add_filter( 'query_vars', __CLASS__ . '::movies_query_vars', 10, 1 );

			// TODO: is that useful anyway?
			add_filter( 'wpmoly_get_movies_from_media', __CLASS__ . '::get_movies_from_media', 10, 1 );
			add_filter( 'wpmoly_get_movies_from_status', __CLASS__ . '::get_movies_from_status', 10, 1 );
			//add_filter( 'posts_request', __CLASS__ . '::posts_request', 10, 2 );
		}

		/**
		 * Debug
		 *
		 * @since    2.0
		 */
		public static function posts_request( $request, $wp_query ) {
			var_dump( $request );
			return $request;
		}

		/**
		 * Register a 'movie' custom post type and 'import-draft' post status
		 *
		 * @since    1.0
		 */
		public static function register_post_type() {

			$labels = array(
				'name'               => __( 'Movies', 'wpmovielibrary' ),
				'singular_name'      => __( 'Movie', 'wpmovielibrary' ),
				'add_new'            => __( 'Add New', 'wpmovielibrary' ),
				'add_new_item'       => __( 'Add New Movie', 'wpmovielibrary' ),
				'edit_item'          => __( 'Edit Movie', 'wpmovielibrary' ),
				'new_item'           => __( 'New Movie', 'wpmovielibrary' ),
				'all_items'          => __( 'All Movies', 'wpmovielibrary' ),
				'view_item'          => __( 'View Movie', 'wpmovielibrary' ),
				'search_items'       => __( 'Search Movies', 'wpmovielibrary' ),
				'not_found'          => __( 'No movies found', 'wpmovielibrary' ),
				'not_found_in_trash' => __( 'No movies found in Trash', 'wpmovielibrary' ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Movies', 'wpmovielibrary' )
			);

			$slug = wpmoly_o( 'rewrite-movie' );
			$slug = ( '' != $slug ? $slug : 'movies' );

			$args = array(
				'labels'             => $labels,
				'rewrite'            => array(
					'slug'       => $slug
				),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'has_archive'        => true,
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
				'menu_position'      => 5
			);

			register_post_type( 'movie', $args );

			register_post_status( 'import-draft', array(
				'label'                     => _x( 'Imported Draft', 'wpmovielibrary' ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => false,
				'label_count'               => _n_noop( 'Imported Draft <span class="count">(%s)</span>', 'Imported Draft <span class="count">(%s)</span>' ),
			) );

			register_post_status( 'import-queued', array(
				'label'                     => _x( 'Queued Movie', 'wpmovielibrary' ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => false,
				'label_count'               => _n_noop( 'Queued Movie <span class="count">(%s)</span>', 'Queued Movies <span class="count">(%s)</span>' ),
			) );

		}

		/**
		 * Return various Movie's Post Meta. Possible meta: status, media, rating
		 * and data.
		 *
		 * @since    1.0
		 * 
		 * @param    string    Meta type to return: data, status, media or rating
		 * @param    int       Movie Post ID
		 *
		 * @return   array|string    WPMOLY Movie Meta if available, empty string else.
		 */
		public static function get_movie_meta( $post_id = null, $meta = null ) {

			if ( is_null( $post_id ) )
				$post_id =  get_the_ID();

			if ( ! $post = get_post( $post_id ) || 'movie' != get_post_type( $post_id ) )
				return false;

			if ( is_admin() && 'data' == $meta && wpmoly_has_deprecated_meta( $post_id ) && wpmoly_o( 'legacy-mode' ) )
				WPMOLY_Legacy::update_movie( $post_id );

			if ( 'data' == $meta ) {
				$_meta = WPMOLY_Settings::get_supported_movie_meta();
				$value = array();

				$value['tmdb_id'] = get_post_meta( $post_id, "_wpmoly_movie_tmdb_id", true );
				$value['poster'] = get_post_meta( $post_id, "_wpmoly_movie_poster", true );

				foreach ( array_keys( $_meta ) as $slug )
					$value[ $slug ] = get_post_meta( $post_id, "_wpmoly_movie_{$slug}", true );

				return $value;
			}

			$value = get_post_meta( $post_id, "_wpmoly_movie_{$meta}", true );
			if ( 'rating' == $meta )
				$value = number_format( floatval( $value ), 1 );

			return $value;
		}
 
		/**
		 * Show movies in default home page post list.
		 * 
		 * Add action on pre_get_posts hook to add movie to the list of
		 * queryable post_types.
		 *
		 * @since    1.0
		 * 
		 * @param    int       $query the WP_Query Object object to alter
		 *
		 * @return   WP_Query    Query Object
		 */
		public static function show_movies_in_home_page( $query ) {

			if ( ! is_home() && ! is_search() && ! is_archive() )
				return $query;

			$post_type = array( 'post', 'movie' );
			$post_status = array( 'publish', 'private' );

			if ( 1 == wpmoly_o( 'frontpage' ) && is_home() && $query->is_main_query() ) {

				if ( '' != $query->get( 'post_type' ) )
					$post_type = array_unique( array_merge( $query->get( 'post_type' ), $post_type ) );

				if ( '' != $query->get( 'post_status' ) )
					$post_status = array_unique( array_merge( $query->get( 'post_status' ), $post_status ) );

				$query->set( 'post_type', $post_type );
				$query->set( 'post_status', $post_status );
			}

			return $query;
		}

		/**
		 * Show some info about movies in post view.
		 * 
		 * Add a filter on the_content hook to display infos selected in options
		 * about the movie: note, director, overview, actors…
		 *
		 * @since    1.0
		 * 
		 * @param    string      $content The original post content
		 *
		 * @return   string      The filtered content containing original content plus movie infos if available, the untouched original content else.
		 */
		public static function movie_content( $content ) {

			if ( 'movie' != get_post_type() )
				return $content;

			// Caching
			$name = apply_filters( 'wpmoly_cache_name', 'movie_content_' . get_the_ID() );
			$html = WPMOLY_Cache::output( $name, function() use ( $content ) {

				// Naughty PHP 5.3 fix
				$details  = WPMOLY_Movies::movie_details();
				$metadata = WPMOLY_Movies::movie_metadata();

				$html = $details . $metadata;

				return $html;

			}, $echo = false );

			// Original content should not be cached
			$content = $html . $content;

			return $content;
		}

		/**
		 * Replace movies mxcerpt by movies overview if available.
		 *
		 * @since    2.0
		 * 
		 * @param    string      $excerpt The original post excerpt
		 *
		 * @return   string      The filtered excerpt containing the movie's overview if any, original excerpt else.
		 */
		public static function movie_excerpt( $excerpt ) {

			if ( 'movie' != get_post_type() )
				return $excerpt;

			if ( ! wpmoly_o( 'excerpt' ) )
				return $excerpt;

			$overview = wpmoly_get_movie_meta( get_the_ID(), 'overview' );
			if ( '' == $overview )
				return $excerpt;

			$excerpt_length = wpmoly_o( 'excerpt-length' );
			if ( ! $excerpt_length )
				$excerpt_length = apply_filters( 'excerpt_length', 55 );
			$excerpt_more   = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
			$overview       = wp_trim_words( $overview, $excerpt_length, $excerpt_more );

			return $overview;
		}

		/**
		 * Generate current movie's details list.
		 *
		 * @since    1.0
		 *
		 * @return   null|string    The current movie's metadata list
		 */
		public static function movie_details() {

			if ( 'nowhere' == wpmoly_o( 'show-details' ) || ( 'posts_only' == wpmoly_o( 'show-details' ) && ! is_singular() ) )
				return null;

			$fields = wpmoly_o( 'sort-details' );
			if ( empty( $fields ) || ! isset( $fields['used'] ) )
				return null;

			$fields = $fields['used'];
			if ( isset( $fields['placebo'] ) )
				unset( $fields['placebo'] );
			$post_id = get_the_ID();

			$items = array();

			foreach ( $fields as $slug => $field ) {
				$detail = call_user_func_array( 'wpmoly_get_movie_meta', array( 'post_id' => $post_id, 'meta' => $slug ) );
				$items[] = apply_filters( "wpmoly_format_movie_{$slug}", $detail );
			}

			$html = WPMovieLibrary::render_template( 'movies/movie-details.php', array( 'items' => $items ), $require = 'always' );

			return $html;
		}

		/**
		 * Generate current movie's metadata list.
		 *
		 * @since    1.0
		 *
		 * @return   null|string    The current movie's metadata list
		 */
		public static function movie_metadata() {

			if ( 'nowhere' == wpmoly_o( 'show-meta' ) || ( 'posts_only' == wpmoly_o( 'show-meta' ) && ! is_singular() ) )
				return null;

			$metadata = wpmoly_get_movie_meta();
			$metadata = wpmoly_filter_undimension_array( $metadata );

			$fields = wpmoly_o( 'sort-meta' );
			$default_fields = WPMOLY_Settings::get_supported_movie_meta();

			if ( '' == $metadata || empty( $fields ) || ! isset( $fields['used'] ) )
				return null;

			$fields = $fields['used'];
			if ( isset( $fields['placebo'] ) )
				unset( $fields['placebo'] );
			$items = array();

			foreach ( $fields as $slug => $field ) {

				$_field = $metadata[ $slug ];

				// Custom filter if available
				if ( has_filter( "wpmoly_format_movie_{$slug}" ) )
					$_field = apply_filters( "wpmoly_format_movie_{$slug}", $_field );

				// Filter empty field
				$_field = apply_filters( "wpmoly_format_movie_field", $_field );

				$fields[ $slug ] = $_field;
				$items[] = array( 'slug' => $slug, 'title' => __( $default_fields[ $slug ]['title'], 'wpmovielibrary' ), 'value' => $_field );
			}

			$html = WPMovieLibrary::render_template( 'movies/movie-metadata.php', array( 'items' => $items ), $require = 'always' );

			return $html;
		}

		/**
		 * Add support for Movie Details to the current WP_Query.
		 * 
		 * If current WP_Query has a WPMOLY meta var set, edit the query to
		 * return the movies matching the wanted detail.
		 *
		 * @since    1.0
		 * 
		 * @param    object      $wp_query Current WP_Query instance
		 */
		public static function movies_query_meta( $wp_query ) {

			if ( is_admin() )
				return false;

			if ( isset( $wp_query->query_vars['meta'] ) ) {
				$meta = 'meta';
				$meta_key = $wp_query->query_vars['meta'];
			}
			else if ( isset( $wp_query->query_vars['detail'] ) ) {
				$meta = 'detail';
				$meta_key = $wp_query->query_vars['detail'];
			}
			else
				return false;

			$l10n_rewrite = WPMOLY_L10n::get_l10n_rewrite();
			$meta_value   = strtolower( $wp_query->query_vars['value'] );
			$meta_key     = apply_filters( 'wpmoly_filter_rewrites',$meta_key );
			$meta_key     = array_search( $meta_key, $l10n_rewrite[ $meta ] );

			// If meta_key does not exist, trigger a 404 error
			if ( ! $meta_key ) {
				$wp_query->set( 'post__in', array( -1 ) );
				return false;
			}

			// Languages and countries meta are special
			if ( 'spoken_languages' == $meta_key && 'meta' == $meta )
				$meta = 'languages';
			else if ( 'production_countries' == $meta_key && 'meta' == $meta )
				$meta = 'countries';

			$value = array_search( $meta_value, $l10n_rewrite[ $meta ] );
			if ( ! $value )
				$value = array_search( remove_accents( rawurldecode( $meta_value ) ), $l10n_rewrite[ $meta ] );

			if ( false != $value )
				$meta_value = $value;

			// Year is just a part of release date but can be useful
			if ( 'year' == $meta_key ) {
				$wp_query->set( 'meta_query', array(
					array(
						'key'     => '_wpmoly_movie_release_date',
						'value'   => $meta_value,
						'compare' => 'LIKE'
					)
				) );
			}
			else if ( 'rating' == $meta_key ) {
				$wp_query->set( 'meta_query', array(
					array(
						'key'     => '_wpmoly_movie_rating',
						'value'   => number_format( $meta_value, 1, '.', ''),
						'compare' => 'LIKE'
					)
				) );
			}
			else {
				$wp_query->set( 'meta_query', array(
					array(
						'key'     => "_wpmoly_movie_{$meta_key}",
						'value'   => $meta_value,
						'compare' => 'LIKE'
					)
				) );
			}
		}

		/**
		 * Add Movie Details slugs to queryable vars
		 * 
		 * @since    1.0
		 * 
		 * @param    array     Current WP_Query instance's queryable vars
		 * 
		 * @return   array     Updated WP_Query instance
		 */
		public static function movies_query_vars( $q_var ) {
			$q_var[] = 'detail';
			$q_var[] = 'meta';
			$q_var[] = 'value';
			return $q_var;
		}

		/**
		 * Filter search query to add support for movies
		 * 
		 * If query is a search, find the IDs of all posts having meta
		 * containing the search as well as posts having it in content
		 * or title. 
		 * 
		 * @since    2.0
		 * 
		 * @param    object    WP_Query object
		 * 
		 * @return   object    WP_Query object
		 */
		public static function filter_search_query( $wp_query ) {

			if ( is_admin() || ! is_search() || ! isset( $wp_query->query['s'] ) )
				return $wp_query;

			global $wpdb;

			$like = $wp_query->query['s'];
			$like = ( method_exists( 'wpdb', 'esc_like' ) ? $wpdb->esc_like( $like ) : like_escape( $like ) );
			$like = '%' . str_replace( ' ', '%', $like ) . '%';
			$query = $wpdb->prepare(
				"SELECT DISTINCT post_id FROM {$wpdb->postmeta}
				  WHERE meta_key LIKE '%s'
				    AND meta_value LIKE '%s'
				 UNION
				 SELECT DISTINCT ID FROM {$wpdb->posts}
				  WHERE ( post_title LIKE '%s' OR post_content LIKE '%s' )
				    AND post_status='publish'
				    AND post_type IN ('post', 'page', 'movie')",
				'%_wpmoly_%', $like, $like, $like
			);

			$results = $wpdb->get_results( $query );
			if ( ! $wpdb->num_rows )
				return $wp_query;

			$ids = array();
			foreach ( $results as $result )
				$ids[] = $result->post_id;

			unset( $wp_query->query );
			$wp_query->set( 's', null );
			$wp_query->set( 'post_type', array( 'post', 'movie' ) );
			$wp_query->set( 'post__in', $ids );

			return $wp_query;
		}

		/**
		 * Filter Hook
		 * 
		 * Used to get a list of Movies depending on their Media
		 * 
		 * @since    1.0
		 * 
		 * @param    string    Media slug
		 * 
		 * @return   array     Array of Post objects
		 */
		public static function get_movies_from_media( $media = null ) {

			$media = esc_attr( $media );

			// Caching
			$name = apply_filters( 'wpmoly_cache_name', 'movie_from_media', $media );
			$movies = WPMOLY_Cache::output( $name, function() use ( $media ) {
				$allowed = WPMOLY_Settings::get_available_movie_media();
				$allowed = array_keys( $allowed );

				if ( is_null( $media ) || ! in_array( $media, $allowed ) )
					$media = WPMOLY_Settings::get_default_movie_media();

				$args = array(
					'post_type' => 'movie',
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'meta_query' => array(
						array(
							'key'   => '_wpmoly_movie_media',
							'value' => $media
						)
					)
				);
				
				$query = new WP_Query( $args );
				$movies = $query->posts;

				return $movies;

			}, $echo = false );

			return $movies;
		}

		/**
		 * Filter Hook
		 * 
		 * Used to get a list of Movies depending on their Status
		 * 
		 * @since    1.0
		 * 
		 * @param    string    Status slug
		 * 
		 * @return   array     Array of Post objects
		 */
		public static function get_movies_from_status( $status = null ) {

			$status = esc_attr( $status );

			// Caching
			$name = apply_filters( 'wpmoly_cache_name', 'movie_from_status', $status );
			$movies = WPMOLY_Cache::output( $name, function() use ( $status ) {

				$allowed = WPMOLY_Settings::get_available_movie_status();
				$allowed = array_keys( $allowed );

				if ( is_null( $status ) || ! in_array( $status, $allowed ) )
					$status = WPMOLY_Settings::get_default_movie_status();

				$args = array(
					'post_type' => 'movie',
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'meta_query' => array(
						array(
							'key'   => '_wpmoly_movie_status',
							'value' => $status
						)
					)
				);
				
				$query = new WP_Query( $args );
				$movies = $query->posts;

				return $movies;

			}, $echo = false );

			return $movies;
		}

		/**
		 * Handle Deactivation/Uninstallation actions.
		 * 
		 * Depending on the Plugin settings, conserve, convert, remove
		 * or delete completly all movies created while using the plugin.
		 * 
		 * @param    string    $action Are we deactivating or uninstalling
		 *                             the plugin?
		 * 
		 * @return   boolean   Did everything go smooth or not?
		 */
		public static function clean_movies( $action ) {

			if ( ! in_array( $action, array( 'deactivate', 'uninstall' ) ) )
				return false;

			$_action = get_option( 'wpmoly_settings' );
			if ( ! $_action || ! isset( $_action[ "wpmoly-{$action}-movies" ] ) )
				return false;

			$action = $_action[ "wpmoly-{$action}-movies" ];
			if ( is_array( $action ) )
				$action = $action[0];

			$contents = new WP_Query(
				array(
					'post_type'      => 'movie',
					'posts_per_page' => -1
				)
			);

			switch ( $action ) {
				case 'convert':
					foreach ( $contents->posts as $post ) {
						set_post_type( $post->ID, 'post' );
						add_post_meta( $post->ID, '_wpmoly_content_type', 'movie', true );
					}
					break;
				case 'remove':
					foreach ( $contents->posts as $post ) {
						wp_delete_post( $post->ID, true );
					}
					break;
				case 'delete':
					foreach ( $contents->posts as $post ) {
						wp_delete_post( $post->ID, true );
						$attachments = get_children( array( 'post_parent' => $post->ID ) );
						foreach ( $attachments as $a ) {
							wp_delete_post( $a->ID, true );
						}
					}
					break;
				default:
					break;
			}
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {

			global $wpdb;

			$contents = new WP_Query(
				array(
					'post_type'      => 'post',
					'posts_per_page' => -1,
					'meta_key'       => '_wpmoly_content_type',
					'meta_value'     => 'movie'
				)
			);

			foreach ( $contents->posts as $post ) {
				set_post_type( $post->ID, 'movie' );
				delete_post_meta( $post->ID, '_wpmoly_content_type', 'movie' );
			}

			self::register_post_type();

		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {

			self::clean_movies( 'deactivate' );
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.0
		 */
		public static function uninstall() {

			self::clean_movies( 'uninstall' );
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}

endif;
