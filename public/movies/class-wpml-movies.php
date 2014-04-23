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
			$args['menu_icon'] = ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ? 'dashicons-format-video' : WPML_URL . '/admin/assets/img/icon-movie.png' );

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

			$html = '<div class="wpml_movie_detail">';

			foreach ( $fields as $field ) {

				switch ( $field ) {
					case 'movie_media':
					case 'movie_status':
						$meta = call_user_func_array( "WPML_Utils::get_{$field}", array( get_the_ID() ) );
						if ( '' != $meta ) {
							if ( 1 ==  WPML_Settings::wpml__details_as_icons() ) {
								$html .= '<div class="wpml_' . $field . ' ' . $meta . ' wpml_detail_icon"></div>';
							}
							else {
								$default_fields = call_user_func( "WPML_Settings::get_available_{$field}" );
								$html .= '<div class="wpml_' . $field . ' ' . $meta . ' wpml_detail_label"><span class="wpml_movie_detail_item">' . $default_fields[ $meta ] . '</span></div>';
							}
						}
						break;
					case 'rating':
						$html .= sprintf( $default_format, $field, __( 'Movie rating', WPML_SLUG ), $field, sprintf( '<div class="movie_rating_display stars_%s"></div>', ( '' == $movie_rating ? '0_0' : str_replace( '.', '_', $movie_rating ) ) ) );
						break;
					default:
						
						break;
				}
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

			$html = '<dl class="wpml_movie">';

			foreach ( $fields as $field ) {

				switch ( $field ) {
					case 'genres':
						$genres = WPML_Settings::wpml__enable_genre() ? get_the_term_list( get_the_ID(), 'genre', '', ', ', '' ) : $tmdb_data[ $field ];
						$html .= sprintf( $default_format, $field, $default_fields[ $field ]['title'], $field, $genres );
						break;
					case 'cast':
						$actors = WPML_Settings::wpml__enable_actor() ? get_the_term_list( get_the_ID(), 'actor', '', ', ', '' ) : $tmdb_data[ $field ];
						$html .= sprintf( $default_format, $field, __( 'Staring', WPML_SLUG ), $field, $actors );
						break;
					case 'release_date':
						$html .= sprintf( $default_format, $field, $default_fields[ $field ]['title'], $field, date_i18n( get_option( 'date_format' ), strtotime( $tmdb_data[ $field ] ) ) );
						break;
					case 'runtime':
						$html .= sprintf( $default_format, $field, $default_fields[ $field ]['title'], $field, date_i18n( get_option( 'time_format' ), mktime( 0, $tmdb_data[ $field ] ) ) );
						break;
					case 'director':
						$term = WPML_Settings::wpml__enable_collection() ? get_term_by( 'name', $tmdb_data[ $field ], 'collection' ) : $tmdb_data[ $field ];
						$collection = ( $term && ! is_wp_error( $link = get_term_link( $term, 'collection' ) ) ) ? '<a href="' . $link . '">' . $tmdb_data[ $field ] . '</a>' : $tmdb_data[ $field ];
						$html .= sprintf( $default_format, $field, __( 'Directed by', WPML_SLUG ), $field, $collection );
						break;
					default:
						if ( in_array( $field, $fields ) && isset( $tmdb_data[ $field ] ) && '' != $tmdb_data[ $field ] )
							$html .= sprintf( $default_format, $field, $default_fields[ $field ]['title'], $field, $tmdb_data[ $field ] );
						break;
				}
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

			$metas = array( 'wpml_movie_media', 'wpml_movie_status', 'wpml_movie_rating' );
			$key_vars = array_keys( $wp_query->query_vars );

			foreach ( $metas as $meta ) {

				if ( in_array( $meta, $key_vars ) ) {
					$wp_query->set( 'meta_key', "_{$meta}" );
					$wp_query->set( 'meta_value', $wp_query->get( $meta ) );
				}
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

			$action = WPML_Settings::deactivate__movies();

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

			flush_rewrite_rules();

		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;