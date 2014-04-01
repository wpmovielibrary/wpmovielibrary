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

			add_action( 'init', __CLASS__ . '::wpml_register_post_type' );

			// Load Movies as well as Posts in the Loop
			add_action( 'pre_get_posts', __CLASS__ . '::wpml_show_movies_in_home_page' );

			// Movie content
			add_filter( 'the_content', __CLASS__ . '::wpml_movie_content' );

			// Movie Detail Query search
			add_filter( 'query_vars', __CLASS__ . '::wpml_movies_query_vars', 10, 1 );
			add_action( 'pre_get_posts', __CLASS__ . '::wpml_movies_query_meta', 10, 1 );
			add_action( 'generate_rewrite_rules', __CLASS__ . '::wpml_register_permalinks', 10, 1 );

			add_filter( 'wpml_get_movies_from_media', __CLASS__ . '::wpml_get_movies_from_media', 10, 1 );
			add_filter( 'wpml_get_movies_from_status', __CLASS__ . '::wpml_get_movies_from_status', 10, 1 );
		}

		/**
		 * Register a 'movie' custom post type and 'import-draft' post status
		 *
		 * @since    1.0.0
		 */
		public static function wpml_register_post_type() {

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
			$args['menu_icon'] = ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ? 'dashicons-format-video' : WPML_URL . '/admin/assets/img/icon-movie.png' );

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
		public static function wpml_show_movies_in_home_page( $query ) {

			if ( ! is_home() && ! is_search() && ! is_archive() )
				return $query;

			$post_type = array( 'post', 'movie' );
			$post_status = array( 'publish', 'private' );

			if ( 1 == WPML_Settings::wpml_o( 'wpml-settings-show_in_home' ) && is_home() && $query->is_main_query() ) {

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
		public static function wpml_movies_query_meta( $wp_query ) {

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
		public static function wpml_movie_content( $content ) {

			if ( 'movie' != get_post_type() )
				return $content;

			$details  = WPML_Movies::wpml_movie_details();
			$metadata = WPML_Movies::wpml_movie_metadata();

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
		private static function wpml_movie_details() {

			if ( 'nowhere' == WPML_Settings::wpml_o( 'wpml-settings-details_in_posts' ) || ( 'posts_only' == WPML_Settings::wpml_o( 'wpml-settings-details_in_posts' ) && ! is_singular() ) )
				return null;

			$fields = WPML_Settings::wpml_o( 'wpml-settings-default_movie_details' );

			if ( empty( $fields ) )
				return null;

			$html = '<div class="wpml_movie_detail">';

			foreach ( $fields as $field ) {

				switch ( $field ) {
					case 'movie_media':
					case 'movie_status':
						$meta = call_user_func_array( "WPML_Utils::wpml_get_{$field}", array( get_the_ID() ) );
						if ( '' != $meta ) {
							if ( 1 ==  WPML_Settings::wpml_o( 'wpml-settings-details_as_icons' ) ) {
								$html .= '<div class="wpml_' . $field . ' ' . $meta . ' wpml_detail_icon"></div>';
							}
							else {
								$default_fields = call_user_func( "WPML_Settings::wpml_get_available_{$field}" );
								$html .= '<div class="wpml_' . $field . ' ' . $meta . ' wpml_detail_label"><span class="wpml_movie_detail_item">' . $default_fields[ $meta ] . '</span></div>';
							}
						}
						break;
					case 'rating':
						$html .= sprintf( $default_format, $field, __( 'Movie rating', 'wpml' ), $field, sprintf( '<div class="movie_rating_display stars_%s"></div>', ( '' == $movie_rating ? '0_0' : str_replace( '.', '_', $movie_rating ) ) ) );
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
		private static function wpml_movie_metadata() {

			if ( 'nowhere' == WPML_Settings::wpml_o( 'wpml-settings-meta_in_posts' ) || ( 'posts_only' == WPML_Settings::wpml_o( 'wpml-settings-meta_in_posts' ) && ! is_singular() ) )
				return null;

			$tmdb_data = WPML_Utils::wpml_get_movie_data();
			$tmdb_data = WPML_Utils::wpml_filter_undimension_array( $tmdb_data );

			$fields = WPML_Settings::wpml_o( 'wpml-settings-default_movie_meta' );
			$default_format = '<dt class="wpml_%s_field_title">%s</dt><dd class="wpml_%s_field_value">%s</dd>';
			$default_fields = WPML_Settings::wpml_get_supported_movie_meta();

			if ( '' == $tmdb_data || empty( $fields ) )
				return null;

			$html = '<dl class="wpml_movie">';

			foreach ( $fields as $field ) {

				switch ( $field ) {
					case 'genres':
						$genres = WPML_Settings::wpml_use_genre() ? get_the_term_list( get_the_ID(), 'genre', '', ', ', '' ) : $tmdb_data[ $field ];
						$html .= sprintf( $default_format, $field, $default_fields[ $field ]['title'], $field, $genres );
						break;
					case 'cast':
						$actors = WPML_Settings::wpml_use_actor() ? get_the_term_list( get_the_ID(), 'actor', '', ', ', '' ) : $tmdb_data[ $field ];
						$html .= sprintf( $default_format, $field, __( 'Staring', 'wpml' ), $field, $actors );
						break;
					case 'release_date':
						$html .= sprintf( $default_format, $field, $default_fields[ $field ]['title'], $field, date_i18n( get_option( 'date_format' ), strtotime( $tmdb_data[ $field ] ) ) );
						break;
					case 'runtime':
						$html .= sprintf( $default_format, $field, $default_fields[ $field ]['title'], $field, date_i18n( get_option( 'time_format' ), mktime( 0, $tmdb_data[ $field ] ) ) );
						break;
					case 'director':
						$term = WPML_Settings::wpml_use_collection() ? get_term_by( 'name', $tmdb_data[ $field ], 'collection' ) : $tmdb_data[ $field ];
						$collection = ( $term && ! is_wp_error( $link = get_term_link( $term, 'collection' ) ) ) ? '<a href="' . $link . '">' . $tmdb_data[ $field ] . '</a>' : $tmdb_data[ $field ];
						$html .= sprintf( $default_format, $field, __( 'Directed by', 'wpml' ), $field, $collection );
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
		 * Add Movie Details slugs to queryable vars
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array     Current WP_Query instance's queryable vars
		 * 
		 * @return   array     Updated WP_Query instance
		 */
		public static function wpml_movies_query_vars( $q_var ) {
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
		public static function wpml_get_movies_from_media( $media = null ) {

			$media = esc_attr( $media );

			$default = WPML_Settings::wpml_get_available_movie_media();
			$allowed = array_keys( $default );

			if ( is_null( $media ) || ! in_array( $media, $allowed ) )
				$media = WPML_Settings::wpml_get_default_movie_media();

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
		public static function wpml_get_movies_from_status( $status = null ) {

			$status = esc_attr( $status );

			$default = WPML_Settings::wpml_get_available_movie_status();
			$allowed = array_keys( $default );

			if ( is_null( $status ) || ! in_array( $status, $allowed ) )
				$status = WPML_Settings::wpml_get_default_movie_status();

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
		 * Create a new set of permalinks for Movie Details
		 * 
		 * TODO: rename and add option to either add or remove permalinks
		 *
		 * @since    1.0.0
		 *
		 * @param    object     $wp_rewrite Instance of WordPress WP_Rewrite Class
		 */
		public static function wpml_register_permalinks( $wp_rewrite ) {

			$new_rules = array(
				'movies/(dvd|vod|bluray|vhs|cinema|other)/?$' => 'index.php?post_type=movie&wpml_movie_media=' . $wp_rewrite->preg_index( 1 ),
				'movies/(dvd|vod|bluray|vhs|cinema|other)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_media=' . $wp_rewrite->preg_index( 1 ),
				'movies/(available|loaned|scheduled)/?$' => 'index.php?post_type=movie&wpml_movie_status=' . $wp_rewrite->preg_index( 1 ),
				'movies/(available|loaned|scheduled)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_status=' . $wp_rewrite->preg_index( 1 ) . '&paged=' . $wp_rewrite->preg_index( 2 ),
				'movies/(0.0|0.5|1.0|1.5|2.0|2.5|3.0|3.5|4.0|4.5|5.0)/?$' => 'index.php?post_type=movie&wpml_movie_rating=' . $wp_rewrite->preg_index( 1 ),
				'movies/(0.0|0.5|1.0|1.5|2.0|2.5|3.0|3.5|4.0|4.5|5.0)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_rating=' . $wp_rewrite->preg_index( 1 ) . '&paged=' . $wp_rewrite->preg_index( 2 ),
			);

			$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;