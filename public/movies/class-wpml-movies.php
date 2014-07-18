<?php
/**
 * WPMovieLibrary Movie Class extension.
 * 
 * Add and manage a Movie Custom Post Type
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Movies' ) ) :

	class WPML_Movies extends WPML_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', __CLASS__ . '::register_post_type', 10 );

			// Load Movies as well as Posts in the Loop
			add_action( 'pre_get_posts', __CLASS__ . '::show_movies_in_home_page' );

			// Movie content
			add_filter( 'the_content', __CLASS__ . '::movie_content' );

			add_action( 'pre_get_posts', __CLASS__ . '::movies_query_meta', 10, 1 );
			add_filter( 'query_vars', __CLASS__ . '::movies_query_vars', 10, 1 );

			add_filter( 'wpml_get_movies_from_media', __CLASS__ . '::get_movies_from_media', 10, 1 );
			add_filter( 'wpml_get_movies_from_status', __CLASS__ . '::get_movies_from_status', 10, 1 );
		}

		/**
		 * Register a 'movie' custom post type and 'import-draft' post status
		 *
		 * @since    1.0.0
		 */
		public static function register_post_type() {

			$labels = array(
				'name'               => __( 'Movies', WPML_SLUG ),
				'singular_name'      => __( 'Movie', WPML_SLUG ),
				'add_new'            => __( 'Add New', WPML_SLUG ),
				'add_new_item'       => __( 'Add New Movie', WPML_SLUG ),
				'edit_item'          => __( 'Edit Movie', WPML_SLUG ),
				'new_item'           => __( 'New Movie', WPML_SLUG ),
				'all_items'          => __( 'All Movies', WPML_SLUG ),
				'view_item'          => __( 'View Movie', WPML_SLUG ),
				'search_items'       => __( 'Search Movies', WPML_SLUG ),
				'not_found'          => __( 'No movies found', WPML_SLUG ),
				'not_found_in_trash' => __( 'No movies found in Trash', WPML_SLUG ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Movies', WPML_SLUG )
			);

			$slug = WPML_Settings::wpml__movie_rewrite();
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

			// Dashicons or PNG
			$args['menu_icon'] = ( WPML_Utils::is_modern_wp() ? 'dashicons-format-video' : WPML_URL . '/assets/img/icon-movie.png' );

			register_post_type( 'movie', $args );

			register_post_status( 'import-draft', array(
				'label'                     => _x( 'Imported Draft', WPML_SLUG ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => false,
				'label_count'               => _n_noop( 'Imported Draft <span class="count">(%s)</span>', 'Imported Draft <span class="count">(%s)</span>' ),
			) );

			register_post_status( 'import-queued', array(
				'label'                     => _x( 'Queued Movie', WPML_SLUG ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => false,
				'label_count'               => _n_noop( 'Queued Movie <span class="count">(%s)</span>', 'Queued Movies <span class="count">(%s)</span>' ),
			) );

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
		public static function show_movies_in_home_page( $query ) {

			if ( ! is_home() && ! is_search() && ! is_archive() )
				return $query;

			$post_type = array( 'post', 'movie' );
			$post_status = array( 'publish', 'private' );

			if ( 1 == WPML_Settings::wpml__show_in_home() && is_home() && $query->is_main_query() ) {

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
		 * @since     1.0.0
		 * 
		 * @param     string      $content The original post content
		 *
		 * @return    string      The filtered content containing original
		 *                        content plus movie infos if available, the
		 *                        untouched original content else.
		 */
		public static function movie_content( $content ) {

			if ( 'movie' != get_post_type() )
				return $content;

			$details  = WPML_Movies::movie_details();
			$metadata = WPML_Movies::movie_metadata();

			$content = $details . $metadata . $content;

			return $content;
		}

		/**
		 * Generate current movie's details list.
		 *
		 * @since     1.0.0
		 *
		 * @return    null|string    The current movie's metadata list
		 */
		private static function movie_details() {

			if ( 'nowhere' == WPML_Settings::wpml__details_in_posts() || ( 'posts_only' == WPML_Settings::wpml__details_in_posts() && ! is_singular() ) )
				return null;

			$fields = WPML_Settings::wpml__default_movie_details();

			if ( empty( $fields ) )
				return null;

			$post_id = get_the_ID();

			if ( is_string( $fields ) )
				$fields = array( $fields );

			$html = '<div class="wpml_movie_detail">';

			foreach ( $fields as $field ) {
				$detail = call_user_func( "WPML_Utils::get_{$field}", $post_id );
				$html .= apply_filters( "wpml_format_{$field}", $detail );
			}

			$html .= '</div>';

			return $html;
		}

		/**
		 * Generate current movie's metadata list.
		 *
		 * @since     1.0.0
		 *
		 * @return    null|string    The current movie's metadata list
		 */
		private static function movie_metadata() {

			if ( 'nowhere' == WPML_Settings::wpml__meta_in_posts() || ( 'posts_only' == WPML_Settings::wpml__meta_in_posts() && ! is_singular() ) )
				return null;

			$tmdb_data = WPML_Utils::get_movie_data();
			$tmdb_data = WPML_Utils::filter_undimension_array( $tmdb_data );

			$fields = WPML_Settings::wpml__default_movie_meta();
			$default_format = '<dt class="wpml_%s_field_title">%s</dt><dd class="wpml_%s_field_value">%s</dd>';
			$default_fields = WPML_Settings::get_supported_movie_meta();

			if ( '' == $tmdb_data || empty( $fields ) )
				return null;

			if ( is_string( $fields ) )
				$fields = array( $fields );

			$html = '<dl class="wpml_movie">';

			foreach ( $fields as $key => $field ) {

				$_field = $tmdb_data[ $field ];

				// Custom filter if available
				if ( has_filter( "wpml_format_movie_{$field}" ) )
					$_field = apply_filters( "wpml_format_movie_{$field}", $_field );

				// Filter empty field
				$_field = apply_filters( "wpml_format_movie_field", $_field );

				$fields[ $key ] = $_field;
				$html .= sprintf( $default_format, $field, __( $default_fields[ $field ]['title'], WPML_SLUG ), $field, $_field );
			}

			$html .= '</dl>';

			return $html;
		}

		/**
		 * Add support for Movie Details to the current WP_Query.
		 * 
		 * If current WP_Query has a WPML meta var set, edit the query to
		 * return the movies matching the wanted detail.
		 *
		 * @since     1.0.0
		 * 
		 * @param     object      $wp_query Current WP_Query instance
		 *
		 * @return    string      The WP_Query instance, updated or not.
		 */
		public static function movies_query_meta( $wp_query ) {

			$key_vars = array_keys( $wp_query->query_vars );

			if ( in_array( 'wpml_movie_media', $key_vars ) ) {
				$value = $wp_query->get( 'wpml_movie_media' );
				if ( $value == __( 'bluray', WPML_SLUG ) )
					$value = 'bluray';
				else if ( $value == __( 'cinema', WPML_SLUG ) )
					$value = 'cinema';
				else if ( $value == __( 'other', WPML_SLUG ) )
					$value = 'other';

				$wp_query->set( 'meta_key', '_wpml_movie_media' );
				$wp_query->set( 'meta_value', $value );
			}

			if ( in_array( 'wpml_movie_status', $key_vars ) ) {
				$value = $wp_query->get( 'wpml_movie_status' );
				if ( $value == __( 'unavailable', WPML_SLUG ) )
					$value = 'unavailable';
				else if ( $value == __( 'available', WPML_SLUG ) )
					$value = 'available';
				else if ( $value == __( 'loaned', WPML_SLUG ) )
					$value = 'loaned';
				else if ( $value == __( 'scheduled', WPML_SLUG ) )
					$value = 'scheduled';

				$wp_query->set( 'meta_key', '_wpml_movie_status' );
				$wp_query->set( 'meta_value', $value );
			}

			if ( in_array( 'wpml_movie_rating', $key_vars ) ) {
				$wp_query->set( 'meta_key', '_wpml_movie_rating' );
				$wp_query->set( 'meta_value', $wp_query->get( 'wpml_movie_rating' ) );
			}

			return $wp_query;
		}

		/**
		 * Add Movie Details slugs to queryable vars
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array     Current WP_Query instance's queryable vars
		 * 
		 * @return   array     Updated WP_Query instance
		 */
		public static function movies_query_vars( $q_var ) {
			$q_var[] = 'wpml_movie_media';
			$q_var[] = 'wpml_movie_status';
			$q_var[] = 'wpml_movie_rating';
			return $q_var;
		}

		/**
		 * Filter Hook
		 * 
		 * Used to get a list of Movies depending on their Media
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    Media slug
		 * 
		 * @return   array     Array of Post objects
		 */
		public static function get_movies_from_media( $media = null ) {

			$media = esc_attr( $media );

			$default = WPML_Settings::get_available_movie_media();
			$allowed = array_keys( $default );

			if ( is_null( $media ) || ! in_array( $media, $allowed ) )
				$media = WPML_Settings::get_default_movie_media();

			$args = array(
				'post_type' => 'movie',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key'   => '_wpml_movie_media',
						'value' => $media
					)
				)
			);
			
			$query = new WP_Query( $args );

			return $query->posts;
		}

		/**
		 * Filter Hook
		 * 
		 * Used to get a list of Movies depending on their Status
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    Status slug
		 * 
		 * @return   array     Array of Post objects
		 */
		public static function get_movies_from_status( $status = null ) {

			$status = esc_attr( $status );

			$default = WPML_Settings::get_available_movie_status();
			$allowed = array_keys( $default );

			if ( is_null( $status ) || ! in_array( $status, $allowed ) )
				$status = WPML_Settings::get_default_movie_status();

			$args = array(
				'post_type' => 'movie',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key'   => '_wpml_movie_status',
						'value' => $status
					)
				)
			);
			
			$query = new WP_Query( $args );

			return $query->posts;
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

			$_action = get_option( 'wpml_settings' );
			if ( ! $_action || ! isset( $_action[ $action ] ) || ! isset( $_action[ $action ]['movies'] ) )
				return false;

			$action = $_action[ $action ]['movies'];
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
						add_post_meta( $post->ID, '_wpml_content_type', 'movie', true );
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
		 * @since    1.0.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {

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

			self::register_post_type();

		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {

			self::clean_movies( 'deactivate' );
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.0.0
		 */
		public static function uninstall() {

			self::clean_movies( 'uninstall' );
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;