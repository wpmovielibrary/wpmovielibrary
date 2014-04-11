<?php
/**
 * WPMovieLibrary Edit_Movies Class extension.
 * 
 * Edit Movies
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Edit_Movies' ) ) :

	class WPML_Edit_Movies extends WPML_Module {

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

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			add_filter( 'manage_movie_posts_columns', __CLASS__ . '::movies_columns_head' );
			add_action( 'manage_movie_posts_custom_column', __CLASS__ . '::movies_columns_content', 10, 2 );
			add_action( 'quick_edit_custom_box', __CLASS__ . '::quick_edit_movies', 10, 2 );
			add_action( 'bulk_edit_custom_box', __CLASS__ . '::bulk_edit_movies', 10, 2 );
			add_filter( 'post_row_actions', __CLASS__ . '::expand_quick_edit_link', 10, 2 );

			add_action( 'the_posts', __CLASS__ . '::the_posts_hijack', 10, 2 );
			add_action( 'ajax_query_attachments_args', __CLASS__ . '::load_images_dummy_query_args', 10, 1 );
			add_action( 'admin_post_thumbnail_html', __CLASS__ . '::load_posters_link', 10, 2 );

			add_action( 'add_meta_boxes', __CLASS__ . '::add_meta_boxes' );
			add_action( 'save_post_movie', __CLASS__ . '::save_tmdb_data' );

			add_action( 'wp_ajax_wpml_save_details', __CLASS__ . '::save_details_callback' );
		}

		/**
		 * Register and enqueue admin-specific JavaScript.
		 * 
		 * wpml.importer extends wpml with specific import functions.
		 *
		 * @since    1.0.0
		 * 
		 * @param    string    $hook Current screen hook
		 */
		public function admin_enqueue_scripts( $hook ) {

			if ( ! in_array( $hook, array( 'edit.php', 'post.php', 'post-new.php' ) ) || 'movie' != get_post_type() )
				return;

			wp_enqueue_script( WPML_SLUG . '-media' , WPML_URL . '/assets/js/wpml.media.js' , array( 'jquery' ), WPML_VERSION, true );
			wp_enqueue_script( WPML_SLUG . '-editor' , WPML_URL . '/assets/js/wpml.editor.js' , array( 'jquery' ), WPML_VERSION, true );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                      "All Movies" WP List Table
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Add a custom column to Movies WP_List_Table list.
		 * Insert a simple 'Poster' column to Movies list table to display
		 * movies' poster set as featured image if available.
		 * 
		 * @since     1.0.0
		 * 
		 * @param     array    Default WP_List_Table header columns
		 * 
		 * @return    array    Default columns with new poster column
		 */
		public static function movies_columns_head( $defaults ) {

			$title = array_search( 'title', array_keys( $defaults ) );
			$comments = array_search( 'comments', array_keys( $defaults ) ) - 1;

			$defaults = array_merge(
				array_slice( $defaults, 0, $title, true ),
				array( 'poster' => __( 'Poster', 'wpml' ) ),
				array_slice( $defaults, $title, $comments, true ),
				array( 'movie_status' => __( 'Status', 'wpml' ) ),
				array( 'movie_media' => __( 'Media', 'wpml' ) ),
				array( 'movie_rating' => __( 'Rating', 'wpml' ) ),
				array_slice( $defaults, $comments, count( $defaults ), true )
			);

			unset( $defaults['author'] );
			return $defaults;
		}

		/**
		 * Add a custom column to Movies WP_List_Table list.
		 * Insert movies' poster set as featured image if available.
		 * 
		 * @since     1.0.0
		 * 
		 * @param     string   $column_name The column name
		 * @param     int      $post_id current movie's post ID
		 */
		public static function movies_columns_content( $column_name, $post_id ) {

			switch ( $column_name ) {
				case 'poster':
					$html = '<img src="'.WPML_Media::get_featured_image( $post_id ).'" alt="" />';
					break;
				case 'movie_status':
				case 'movie_media':
					$meta = get_post_meta( $post_id, '_wpml_' . $column_name, true );
					$_details = WPML_Settings::get_supported_movie_details();
					if ( isset( $_details[ $column_name ]['options'][ $meta ] ) )
						$html = $_details[ $column_name ]['options'][ $meta ];
					else
						$html = '&mdash;';
					break;
				case 'movie_rating':
					$meta = get_post_meta( $post_id, '_wpml_movie_rating', true );
					if ( '' != $meta )
						$html = '<div id="movie-rating-display" class="stars-' . str_replace( '.', '-', $meta ) . '"></div>';
					else
						$html = '<div id="movie-rating-display" class="stars-0-0"></div>';
					break;
				default:
					$html = '';
					break;
			}

			echo $html;
		}

		/**
		 * Add new fields to Movies' Quick Edit form in Movies Lists to edit
		 * Movie Details directly from the list.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    $column_name WP List Table Column name
		 * @param    string    $post_type Post type
		 */
		public static function quick_edit_movies( $column_name, $post_type ) {

			if ( 'movie' != $post_type || 'poster' != $column_name || 1 !== did_action( 'quick_edit_custom_box' ) )
				return false;

			self::quickbulk_edit( 'quick' );
		}

		/**
		 * Add new fields to Movies' Bulk Edit form in Movies Lists.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    $column_name WP List Table Column name
		 * @param    string    $post_type Post type
		 */
		public static function bulk_edit_movies( $column_name, $post_type ) {

			if ( 'movie' != $post_type || 'poster' != $column_name || 1 !== did_action( 'bulk_edit_custom_box' ) )
				return false;

			self::quickbulk_edit( 'bulk' );
		}

		/**
		 * Generic function to show WPML Quick/Bulk Edit form.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    $type Form type, 'quick' or 'bulk'.
		 */
		private static function quickbulk_edit( $type ) {

			if ( ! in_array( $type, array( 'quick', 'bulk' ) ) )
				return false;

			$default_movie_media = WPML_Settings::get_available_movie_media();
			$default_movie_status = WPML_Settings::get_available_movie_status();

			$check = 'is_' . $type . 'edit';

			$nonce_name = 'wpml_' . $type . 'edit_movie_details_nonce';
			$nonce = wp_create_nonce( '_wpml_' . $type . 'edit_movie_details' );

			include( plugin_dir_path( __FILE__ ) . '/views/quick-edit.php' );
		}

		/**
		 * Alter the Quick Edit link in Movies Lists to update the Movie Details
		 * current values.
		 * 
		 * TODO: group Details in a single, cached query.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array     $actions List of current actions
		 * @param    object    $post Current Post object
		 * 
		 * @return   string    Edited Post Actions
		 */
		public static function expand_quick_edit_link( $actions, $post ) {

			global $current_screen;

			if ( isset( $current_screen ) && ( ( $current_screen->id != 'edit-movie' ) || ( $current_screen->post_type != 'movie' ) ) )
				return $actions;

			$nonce = wp_create_nonce( '_wpml_movie_details' );

			$details = '{';
			$details .= 'movie_id: ' . $post->ID . ',';
			$details .= 'movie_media: \'' . get_post_meta( $post->ID, '_wpml_movie_media', TRUE ) . '\',';
			$details .= 'movie_status: \'' . get_post_meta( $post->ID, '_wpml_movie_status', TRUE ) . '\',';
			$details .= 'movie_rating: \'' . get_post_meta( $post->ID, '_wpml_movie_rating', TRUE ) . '\'';
			$details .= '}';

			$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
			$actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
			$actions['inline hide-if-no-js'] .= " onclick=\"wpml.movie.populate_quick_edit({$details}, '{$nonce}')\">"; 
			$actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
			$actions['inline hide-if-no-js'] .= '</a>';

			return $actions;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                      Movie images Media Modal
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Handle dummy arguments for AJAX Query. Movie Edit page adds
		 * an extended WP Media Modal to allow user to pick up which
		 * images he wants to download from the current movie. Media Modals
		 * are filled with the latest uploaded media, in order to avoid
		 * this with restrict the selection in the Javascript part by 
		 * querying only the post's attachment. We also need the movie
		 * TMDb ID, so we pass it as a search query.
		 * 
		 * This method cleans up the options and add the TMDb ID to the
		 * WP Query to bypass wp_ajax_query_attachments() filtering of
		 * the query options.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $query List of options for the query
		 * 
		 * @return   array    Filtered list of query options
		 */
		public static function load_images_dummy_query_args( $query ) {

			if ( isset( $query['s'] ) && 1 == preg_match_all( '/^TMDb_ID=([0-9]+),type=(image|poster)$/i', $query['s'], $m ) ) {

				unset( $query['post__not_in'] );
				unset( $query['post_mime_type'] );
				unset( $query['post_status'] );
				unset( $query['s'] );

				$query['post_type'] = 'movie';
				$query['tmdb_id'] = $m[1][0];
				$query['tmdb_type'] = $m[2][0];

			}

			return $query;
		}

		/**
		 * This is the hijack trick. Use the 'the_posts' filter hook to
		 * determine whether we're looking for movie images or regular
		 * posts. If the query has a 'tmdb_id' field, images wanted, we
		 * load them. If not, it's just a regular Post query, return the
		 * Posts.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $posts Posts concerned by the hijack, should be only one
		 * @param    array    $_query concerned WP_Query instance
		 * 
		 * @return   array    Posts return by the query if we're not looking for movie images
		 */
		public static function the_posts_hijack( $posts, $_query ) {

			if ( ! is_null( $_query ) && isset( $_query->query['tmdb_id'] ) && isset( $_query->query['tmdb_type'] ) ) {

				$tmdb_id = esc_attr( $_query->query['tmdb_id'] );
				$tmdb_type = esc_attr( $_query->query['tmdb_type'] );
				$paged = intval( $_query->query['paged'] );
				$per_page = intval( $_query->query['posts_per_page'] );

				if ( 'image' == $tmdb_type )
					$images = self::load_movie_images( $tmdb_id, $posts[0] );
				else if ( 'poster' == $tmdb_type )
					$images = self::load_movie_posters( $tmdb_id, $posts[0] );

				$images = array_slice( $images, ( ( $paged - 1 ) * $per_page ), $per_page );

				wp_send_json_success( $images );
			}

			return $posts;
		}

		/**
		 * Load the Movie Images and display a jsonified result.s
		 * 
		 * @since    1.0.0
		 * 
		 * @param    int      $tmdb_id Movie TMDb ID to fetch images
		 * @param    array    $post Related Movie Post
		 */
		public static function load_movie_images( $tmdb_id, $post ) {

			$images = WPML_TMDb::get_movie_images( $tmdb_id );
			$images = apply_filters( 'wpml_jsonify_movie_images', $images, $post, 'image' );

			return $images;
		}

		/**
		 * Load the Movie Images and display a jsonified result.s
		 * 
		 * @since    1.0.0
		 * 
		 * @param    int      $tmdb_id Movie TMDb ID to fetch images
		 * @param    array    $post Related Movie Post
		 */
		public static function load_movie_posters( $tmdb_id, $post ) {

			$posters = WPML_TMDb::get_movie_posters( $tmdb_id );
			$posters = apply_filters( 'wpml_jsonify_movie_images', $posters, $post, 'poster' );

			return $posters;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Callbacks
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Save movie details: media, status, rating.
		 * 
		 * Although values are submitted as array each value is stored in a
		 * dedicated post meta.
		 *
		 * @since    1.0.0
		 */
		public static function save_details_callback() {

			check_ajax_referer( 'wpml-callbacks-nonce', 'wpml_check' );

			$post_id      = ( isset( $_POST['post_id'] )      && '' != $_POST['post_id']      ? $_POST['post_id']      : '' );
			$wpml_details = ( isset( $_POST['wpml_details'] ) && '' != $_POST['wpml_details'] ? $_POST['wpml_details'] : '' );

			if ( '' == $post_id || '' == $wpml_details )
				return false;

			$post = get_post( $post_id );
			if ( 'movie' != get_post_type( $post ) )
				return false;

			update_post_meta( $post_id, '_wpml_movie_media', $wpml_details['media'] );
			update_post_meta( $post_id, '_wpml_movie_status', $wpml_details['status'] );
			update_post_meta( $post_id, '_wpml_movie_rating', $wpml_details['rating'] );
		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Meta Boxes
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Register WPML Metaboxes
		 * 
		 * @since    1.0.0
		 */
		public static function add_meta_boxes() {

			// Movie Metadata
			add_meta_box( 'wpml_meta', __( 'WPMovieLibrary − Movie Meta', 'wpml' ), __CLASS__ . '::metabox_meta', 'movie', 'normal', 'high', null );

			// Movie Details
			add_meta_box( 'wpml_images', __( 'WPMovieLibrary − Movie Images', 'wpml' ), __CLASS__ . '::metabox_images', 'movie', 'normal', 'high', null );

			// Movie Details
			add_meta_box( 'wpml_details', __( 'WPMovieLibrary − Movie Details', 'wpml' ), __CLASS__ . '::metabox_details', 'movie', 'side', 'default', null );
		}

		/**
		 * Main Metabox: TMDb API results.
		 * 
		 * Display a large Metabox below post editor to fetch and edit movie
		 * informations using the TMDb API.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    object    Current Post object
		 * @param    null      $metabox null
		 */
		public static function metabox_meta( $post, $metabox ) {

			$value = get_post_meta( $post->ID, '_wpml_movie_data', true );
			$value = apply_filters( 'wpml_filter_empty_array', $value );

			if ( isset( $_REQUEST['wpml_auto_fetch'] ) && ( empty( $value ) || isset( $value['_empty'] ) ) )
				$value = WPML_TMDb::_get_movie_by_title( $post->post_title, WPML_Settings::tmdb__lang() );

			include_once( plugin_dir_path( __FILE__ ) . '/views/metabox-movie-meta.php' );
		}

		/**
		 * Movie Images Metabox.
		 * 
		 * Display a large Metabox below post editor to fetch and edit movie
		 * informations using the TMDb API.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    object    Current Post object
		 * @param    null      $metabox null
		 */
		public static function metabox_images( $post, $metabox ) {

			$value = get_post_meta( $post->ID, '_wpml_movie_data', true );
			$value = apply_filters( 'wpml_filter_empty_array', $value );

			if ( isset( $_REQUEST['wpml_auto_fetch'] ) && ( empty( $value ) || isset( $value['_empty'] ) ) )
				$value = WPML_TMDb::_get_movie_by_title( $post->post_title, WPML_Settings::tmdb__lang() );

			include_once( plugin_dir_path( __FILE__ ) . '/views/metabox-movie-images.php' );
		}

		/**
		 * Left side Metabox: Movie details. Used to handle Movie
		 * related details.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    object    Current Post object
		 * @param    null      $metabox null
		 */
		public static function metabox_details( $post, $metabox ) {

			$v = get_post_meta( $post->ID, '_wpml_movie_status', true );
			$movie_status = ( isset( $v ) && '' != $v ? $v : key( WPML_Settings::get_default_movie_status() ) );

			$v = get_post_meta( $post->ID, '_wpml_movie_media', true );
			$movie_media  = ( isset( $v ) && '' != $v ? $v : key( WPML_Settings::get_default_movie_media() ) );

			$v = get_post_meta( $post->ID, '_wpml_movie_rating', true );
			$movie_rating = ( isset( $v ) && '' != $v ? number_format( $v, 1 ) : 0.0 );
			$movie_rating_str = str_replace( '.', '-', $movie_rating );

			include_once( plugin_dir_path( __FILE__ ) . 'views/metabox-movie-details.php' );
		}

		/**
		 * Add a link to the current Post's Featured Image Metabox to trigger
		 * a Modal window. This will be used by the future Movie Posters
		 * selection Modal, yet to be implemented.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    $content Current Post's Featured Image Metabox
		 *                              content, ready to be edited.
		 * @param    string    $post_id Current Post's ID (unused at that point)
		 * 
		 * @return   string    Updated $content
		 */
		public static function load_posters_link( $content, $post_id ) {
			return $content . '<a id="tmdb_load_posters" class="hide-if-no-js" href="#">' . __( 'See available Movie Posters', 'wpml' ) . '</a>';
		}

		/**
		 * Save TMDb fetched data.
		 *
		 * @since     1.0.0
		 */
		public static function save_tmdb_data( $post_id, $tmdb_data = null ) {

			if ( ! $post = get_post( $post_id ) || ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || 'movie' != get_post_type( $post ) || ! current_user_can( 'edit_post', $post_id ) )
				return false;

			if ( ! is_null( $tmdb_data ) && count( $tmdb_data ) ) {

				$tmdb_data = apply_filters( 'wpml_filter_empty_array', $tmdb_data );

				// Save TMDb data
				update_post_meta( $post_id, '_wpml_movie_data', $tmdb_data );

				// Set poster as featured image
				$id = WPML_Media::set_image_as_featured( $tmdb_data['poster'], $post_id, $tmdb_data['tmdb_id'], $tmdb_data['meta']['title'] );
				update_post_meta( $post_id, '_thumbnail_id', $id );

				// Switch status from import draft to published
				if ( 'import-draft' == get_post_status( $post_id ) ) {
					$update = wp_update_post( array(
						'ID' => $post_id,
						'post_name'   => sanitize_title_with_dashes( $tmdb_data['meta']['title'] ),
						'post_status' => 'publish',
						'post_title'  => $tmdb_data['meta']['title'],
						'post_date'   => current_time( 'mysql' )
					) );
				}

				// Autofilling Taxonomy
				if ( WPML_Settings::wpml__taxonomy_autocomplete() ) {

					if ( WPML_Settings::wpml__enable_actor() ) {
						$actors = explode( ',', $tmdb_data['crew']['cast'] );
						$actors = wp_set_object_terms( $post_id, $actors, 'actor', false );
					}

					if ( WPML_Settings::wpml__enable_genre() ) {
						$genres = explode( ',', $tmdb_data['meta']['genres'] );
						$genres = wp_set_object_terms( $post_id, $genres, 'genre', false );
					}

					if ( WPML_Settings::wpml__enable_collection() ) {
						$collections = explode( ',', $tmdb_data['crew']['director'] );
						$collections = wp_set_object_terms( $post_id, $collections, 'collection', false );
					}
				}
			}
			else if ( isset( $_REQUEST['tmdb_data'] ) && '' != $_REQUEST['tmdb_data'] ) {
				update_post_meta( $post_id, '_wpml_movie_data', $_REQUEST['tmdb_data'] );
			}

			if ( isset( $_REQUEST['wpml_details'] ) && ! is_null( $_REQUEST['wpml_details'] ) ) {

				if ( isset( $_REQUEST['is_quickedit'] ) )
					check_admin_referer( '_wpml_quickedit_movie_details', 'wpml_quickedit_movie_details_nonce' );
				else if ( isset( $_REQUEST['is_bulkedit'] ) )
					check_admin_referer( '_wpml_bulkedit_movie_details', 'wpml_bulkedit_movie_details_nonce' );

				$wpml_d = $_REQUEST['wpml_details'];

				if ( isset( $wpml_d['movie_status'] ) && ! is_null( $wpml_d['movie_status'] ) )
					update_post_meta( $post_id, '_wpml_movie_status', $wpml_d['movie_status'] );

				if ( isset( $wpml_d['movie_media'] ) && ! is_null( $wpml_d['movie_media'] ) )
					update_post_meta( $post_id, '_wpml_movie_media', $wpml_d['movie_media'] );

				if ( isset( $wpml_d['movie_rating'] ) && ! is_null( $wpml_d['movie_rating'] ) )
					update_post_meta( $post_id, '_wpml_movie_rating', number_format( $wpml_d['movie_rating'], 1 ) );
			}
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}
		
	}
	
endif;