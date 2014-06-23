<?php
/**
 * WPMovieLibrary Edit_Movies Class extension.
 * 
 * Edit Movies related methods. Handles Post List Tables, Quick and Bulk Edit
 * Forms, Meta and Details Metaboxes, Images and Posters WP Media Modals.
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

			if ( ! is_admin() )
				return false;

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			add_action( 'admin_footer', array( $this, 'edit_details_inline' ) );

			add_filter( 'manage_movie_posts_columns', __CLASS__ . '::movies_columns_head' );
			add_action( 'manage_movie_posts_custom_column', __CLASS__ . '::movies_columns_content', 10, 2 );
			add_action( 'quick_edit_custom_box', __CLASS__ . '::quick_edit_movies', 10, 2 );
			add_action( 'bulk_edit_custom_box', __CLASS__ . '::bulk_edit_movies', 10, 2 );
			add_filter( 'post_row_actions', __CLASS__ . '::expand_quick_edit_link', 10, 2 );

			add_action( 'the_posts', __CLASS__ . '::the_posts_hijack', 10, 2 );
			add_action( 'ajax_query_attachments_args', __CLASS__ . '::load_images_dummy_query_args', 10, 1 );

			add_action( 'add_meta_boxes', __CLASS__ . '::add_meta_boxes', 10 );
			add_action( 'save_post_movie', __CLASS__ . '::save_movie_meta', 10, 4 );

			add_action( 'wp_ajax_wpml_set_detail', __CLASS__ . '::set_detail_callback' );
			add_action( 'wp_ajax_wpml_save_details', __CLASS__ . '::save_details_callback' );
		}

		/**
		 * Register and enqueue admin-specific JavaScript.
		 *
		 * @since    1.0.0
		 * 
		 * @param    string    $hook Current screen hook
		 */
		public function admin_enqueue_scripts( $hook ) {

			if ( ! in_array( $hook, array( 'edit.php', 'post.php', 'post-new.php' ) ) || 'movie' != get_post_type() )
				return;

			wp_enqueue_script( WPML_SLUG . '-media' , WPML_URL . '/assets/js/wpml.media.js' , array( WPML_SLUG, 'jquery' ), WPML_VERSION, true );
			wp_enqueue_script( WPML_SLUG . '-movies' , WPML_URL . '/assets/js/wpml.movies.js' , array( WPML_SLUG, 'jquery' ), WPML_VERSION, true );
			wp_enqueue_script( WPML_SLUG . '-editor-details' , WPML_URL . '/assets/js/wpml.editor.details.js' , array( WPML_SLUG, 'jquery' ), WPML_VERSION, true );
			wp_enqueue_script( WPML_SLUG . '-editor-meta' , WPML_URL . '/assets/js/wpml.editor.meta.js' , array( WPML_SLUG, 'jquery' ), WPML_VERSION, true );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                      "All Movies" WP List Table
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		public function edit_details_inline() {

			if ( 'edit-movie' != get_current_screen()->id )
				return false;

			$default_movie_media = WPML_Settings::get_available_movie_media();
			$default_movie_status = WPML_Settings::get_available_movie_status();
			$default_movie_rating = WPML_Settings::get_available_movie_rating();

			include( plugin_dir_path( __FILE__ ) . '/views/edit-details-inline.php' );
		}

		/**
		 * Add a custom column to Movies WP_List_Table list.
		 * Insert a simple 'Poster' column to Movies list table to display
		 * movies' poster set as featured image if available.
		 * 
		 * @since     1.0.0
		 * 
		 * @param     array    $defaults Default WP_List_Table header columns
		 * 
		 * @return    array    Default columns with new poster column
		 */
		public static function movies_columns_head( $defaults ) {

			$title = array_search( 'title', array_keys( $defaults ) );
			$comments = array_search( 'comments', array_keys( $defaults ) ) - 1;

			$defaults = array_merge(
				array_slice( $defaults, 0, $title, true ),
				array( 'poster' => __( 'Poster', WPML_SLUG ) ),
				array_slice( $defaults, $title, $comments, true ),
				array( 'movie_status' => __( 'Status', WPML_SLUG ) ),
				array( 'movie_media' => __( 'Media', WPML_SLUG ) ),
				array( 'movie_rating' => __( 'Rating', WPML_SLUG ) ),
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
					if ( isset( $_details[ $column_name ]['options'][ $meta ] ) ) {
						$html = $_details[ $column_name ]['options'][ $meta ];
						$html = '<span class="' . $column_name . '_title">' . __( $html, WPML_SLUG ) . '</span>';
					}
					else
						$html = '<span class="' . $column_name . '_title"><em>' . __( 'None', WPML_SLUG ) . '</em></span>';
					$html .= '<a href="#" class="wpml-inline-edit-toggle hide-if-no-js" onclick="wpml_edit_details.inline_editor( \'' . str_replace( 'movie_', '', $column_name ) . '\', this ); return false;"><span class="dashicons dashicons-admin-generic"></span></a>';
					break;
				case 'movie_rating':
					$meta = get_post_meta( $post_id, '_wpml_movie_rating', true );
					if ( '' != $meta )
						$html = '<div id="movie-rating-display" class="movie_rating_title stars stars-' . str_replace( '.', '-', $meta ) . '"></div>';
					else
						$html = '<div id="movie-rating-display" class="movie_rating_title stars stars-0-0"></div>';
					$html .= '<a href="#" class="wpml-inline-edit-toggle hide-if-no-js" onclick="wpml_edit_details.inline_editor( \'rating\', this ); return false;"><span class="dashicons dashicons-admin-generic"></span></a>';
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

			$nonce = WPML_Utils::create_nonce( 'set-quickedit-movie-details' );

			$details = '{';
			$details .= 'movie_id: ' . $post->ID . ',';
			$details .= 'movie_media: \'' . get_post_meta( $post->ID, '_wpml_movie_media', TRUE ) . '\',';
			$details .= 'movie_status: \'' . get_post_meta( $post->ID, '_wpml_movie_status', TRUE ) . '\',';
			$details .= 'movie_rating: \'' . get_post_meta( $post->ID, '_wpml_movie_rating', TRUE ) . '\'';
			$details .= '}';

			$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
			$actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
			$actions['inline hide-if-no-js'] .= " onclick=\"wpml_edit_movies.quick_edit({$details}, '{$nonce}')\">"; 
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
		 * "All Movies" WP List Table inline edit shortcut for details.
		 *
		 * @since    1.0.0
		 */
		public static function set_detail_callback() {

			$detail = ( isset( $_POST['type'] ) && in_array( $_POST['type'], array( 'status', 'media', 'rating' ) ) ? esc_attr( $_POST['type'] ) : null );
			if ( is_null( $detail ) )
				return new WP_Error( 'invalid', __( 'Invalid detail: should be status, media or rating.', WPML_SLUG ) );

			WPML_Utils::check_ajax_referer( $detail . '-inline-edit' );

			$post_id = ( isset( $_POST['post_id'] ) && '' != $_POST['post_id'] ? intval( $_POST['post_id'] ) : null );
			$value = ( isset( $_POST['data'] ) && '' != $_POST['data'] ? esc_attr( $_POST['data'] ) : null );

			if ( is_null( $post_id ) || is_null( $detail ) || is_null( $value ) )
				return new WP_Error( 'invalid', __( 'Empty or invalid Post ID or Movie Details', WPML_SLUG ) );

			$response = self::set_movie_detail( $post_id, $detail, $value );

			WPML_Utils::ajax_response( $response, array(), WPML_Utils::create_nonce( $detail . '-inline-edit' ) );
		}

		/**
		 * Save movie details: media, status, rating.
		 * 
		 * Although values are submitted as array each value is stored in a
		 * dedicated post meta.
		 *
		 * @since    1.0.0
		 */
		public static function save_details_callback() {

			$post_id = ( isset( $_POST['post_id'] )      && '' != $_POST['post_id']      ? intval( $_POST['post_id'] ) : null );
			$details = ( isset( $_POST['wpml_details'] ) && '' != $_POST['wpml_details'] ? $_POST['wpml_details'] : null );

			if ( is_null( $post_id ) || is_null( $details ) )
				return new WP_Error( 'invalid', __( 'Empty or invalid Post ID or Movie Details', WPML_SLUG ) );

			WPML_Utils::check_ajax_referer( 'save-movie-details' );

			$response = self::save_movie_details( $post_id, $details );

			WPML_Utils::ajax_response( $response, array(), WPML_Utils::create_nonce( $detail . '-inline-edit' ) );
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
			add_meta_box( 'wpml_meta', __( 'WPMovieLibrary − Movie Meta', WPML_SLUG ), __CLASS__ . '::metabox_meta', 'movie', 'normal', 'high', null );

			// Movie Details
			add_meta_box( 'wpml_images', __( 'WPMovieLibrary − Movie Images', WPML_SLUG ), __CLASS__ . '::metabox_images', 'movie', 'normal', 'high', null );

			// Movie Details
			add_meta_box( 'wpml_details', __( 'WPMovieLibrary − Movie Details', WPML_SLUG ), __CLASS__ . '::metabox_details', 'movie', 'side', 'default', null );
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
			$select = null;
			$status = '';

			if ( isset( $_GET['wpml_search_movie'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'search-movies' ) && ( empty( $value ) || isset( $value['_empty'] ) ) ) {

				$search_by = ( isset( $_GET['search_by'] ) && in_array( $_GET['search_by'], array( 'title', 'id' ) ) ? $_GET['search_by'] : null );
				$search_query = ( isset( $_GET['search_query'] ) && '' != $_GET['search_query'] ? $_GET['search_query'] : null );

				if ( ! is_null( $search_by ) && ! is_null( $search_query ) )
					$value = call_user_func_array( array( 'WPML_TMDb', "_get_movie_by_$search_by" ), array( $search_query, WPML_Settings::tmdb__lang() ) );

				if ( isset( $value['result'] ) ) {

					if ( 'movie' == $value['result'] )
						$value = $value['movies'][ 0 ];
					else if ( 'movies' == $value['result'] )
						$select = $value['movies'];
				}
			}

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
			$movie_status = ( isset( $v ) && '' != $v ? $v : '' );

			$v = get_post_meta( $post->ID, '_wpml_movie_media', true );
			$movie_media  = ( isset( $v ) && '' != $v ? $v : '' );

			$v = get_post_meta( $post->ID, '_wpml_movie_rating', true );
			$movie_rating = ( isset( $v ) && '' != $v ? number_format( $v, 1 ) : 0.0 );
			$movie_rating_str = str_replace( '.', '-', $movie_rating );

			include_once( plugin_dir_path( __FILE__ ) . 'views/metabox-movie-details.php' );
		}

		/**
		 * Set specific movie detail.
		 * 
		 * @since     1.0.0
		 * 
		 * @param    int       $post_id ID of the current Post
		 * @param    string    $detail Movie detail: media, status or rating
		 * @param    string    $value Detail value
		 * 
		 * @return   int|object    WP_Error object is anything went
		 *                                  wrong, true else
		 */
		public static function set_movie_detail( $post_id, $detail, $value ) {

			$post = get_post( $post_id );
			if ( ! $post || 'movie' != get_post_type( $post ) )
				return new WP_Error( 'invalid_post', sprintf( __( 'Error: invalid post, post #%s is not a movie.', WPML_SLUG ), $post_id ) );

			if ( 'status' == $detail || 'media' == $detail ) {

				$allowed = call_user_func( "WPML_Settings::get_available_movie_{$detail}" );
				if ( ! in_array( $value, array_keys( $allowed ) ) )
					return new WP_Error( 'invalid_value', sprintf( __( 'Error: invalid value, allowed values for \'%s\' are %s.', WPML_SLUG ), $detail, implode( ', ', array_keys( $allowed ) ) ) );

				update_post_meta( $post_id, '_wpml_movie_' . $detail, $value );
				$updated = get_post_meta( $post_id, '_wpml_movie_' . $detail, true );
				if ( '' == $updated || $value != $updated )
					return new WP_Error( 'update_error', __( 'Error: couldn\'t update movie detail.', WPML_SLUG ) );
			}
			else if ( 'rating' == $detail ) {
				$value = number_format( $value, 1 );
				if ( 0 > floor( $value ) || 5 < ceil( $value ) )
					return new WP_Error( 'invalid_value', sprintf( __( 'Error: invalid value, allowed values for \'rating\' are floats between 0.0 and 5.0.', WPML_SLUG ) ) );

				update_post_meta( $post_id, '_wpml_movie_' . $detail, $value );
				$updated = get_post_meta( $post_id, '_wpml_movie_' . $detail, true );
				if ( '' == $updated || 0 != abs( $value - number_format( $updated, 1 ) ) )
					return new WP_Error( 'update_error', __( 'Error: couldn\'t update movie detail.', WPML_SLUG ) );
			}

			return $post_id;
		}

		/**
		 * Save movie details.
		 * 
		 * @since     1.0.0
		 * 
		 * @param    int      $post_id ID of the current Post
		 * @param    array    $details Movie details: media, status, rating
		 * 
		 * @return   int|object    WP_Error object is anything went
		 *                                  wrong, true else
		 */
		public static function save_movie_details( $post_id, $details ) {

			$post = get_post( $post_id );
			if ( ! $post || 'movie' != get_post_type( $post ) )
				return new WP_Error( 'invalid_post', __( 'Error: submitted post is not a movie.', WPML_SLUG ) );

			if ( ! is_array( $details ) || ! isset( $details['movie_media'] ) || ! isset( $details['movie_status'] ) || ! isset( $details['movie_rating'] ) )
				return new WP_Error( 'invalid_details', __( 'Error: the submitted movie details are invalid.', WPML_SLUG ) );

			update_post_meta( $post_id, '_wpml_movie_media', $details['movie_media'] );
			update_post_meta( $post_id, '_wpml_movie_status', $details['movie_status'] );
			update_post_meta( $post_id, '_wpml_movie_rating', number_format( $details['movie_rating'], 1 ) );

			return $post_id;
		}

		/**
		 * Save TMDb fetched data.
		 * 
		 * Uses the 'save_post_movie' action hook to save the movie metadata
		 * as a postmeta. This method is used in regular post creation as
		 * well as in movie import. If no $movie_meta is passed, we're 
		 * most likely creating a new movie, use $_REQUEST to get the data.
		 * 
		 * Saves the movie details as well.
		 * 
		 * TODO: add some security to harden the $_REQUEST use.
		 * TODO: use WP_Error
		 *
		 * @since     1.0.0
		 * 
		 * @param    int        $post_ID ID of the current Post
		 * @param    object     $post Post Object of the current Post
		 * @param    boolean    $queue Queued movie?
		 * @param    array      $movie_meta Movie Metadata to save with the post
		 * 
		 * @return   int|WP_Error
		 */
		public static function save_movie_meta( $post_ID, $post, $queue = false, $movie_meta = null ) {

			if ( ! current_user_can( 'edit_post', $post_ID ) )
				return new WP_Error( __( 'You are not allowed to edit posts.', WPML_SLUG ) );

			if ( ! $post = get_post( $post_ID ) || 'movie' != get_post_type( $post ) )
				return new WP_Error( sprintf( __( 'Posts with #%s is invalid or is not a movie.', WPML_SLUG ), $post_ID ) );

			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
				return $post_ID;

			$errors = new WP_Error();

			if ( ! is_null( $movie_meta ) && count( $movie_meta ) ) {

				$movie_meta = apply_filters( 'wpml_filter_empty_array', $movie_meta );
				$movie_meta = apply_filters( 'wpml_validate_meta_data', $movie_meta );

				// Save TMDb data
				update_post_meta( $post_ID, '_wpml_movie_data', $movie_meta );

				// Set poster as featured image
				if ( WPML_Settings::images__poster_featured() && ! $queue ) {
					$upload = WPML_Media::set_image_as_featured( $movie_meta['poster'], $post_ID, $movie_meta['tmdb_id'], $movie_meta['meta']['title'] );
					if ( is_wp_error( $upload ) )
						$errors->add( $upload->get_error_code(), $upload->get_error_message() );
					else
						update_post_meta( $post_ID, '_thumbnail_id', $upload );
				}

				// Switch status from import draft to published
				if ( 'import-draft' == get_post_status( $post_ID ) && ! $queue ) {
					$update = wp_update_post(
						array(
							'ID' => $post_ID,
							'post_name'   => sanitize_title_with_dashes( $movie_meta['meta']['title'] ),
							'post_status' => 'publish',
							'post_title'  => $movie_meta['meta']['title'],
							'post_date'   => current_time( 'mysql' )
						),
						$wp_error = true
					);
					if ( is_wp_error( $update ) )
						$errors->add( $update->get_error_code(), $update->get_error_message() );
				}

				// Autofilling Actors
				if ( WPML_Settings::taxonomies__enable_actor() && WPML_Settings::taxonomies__actor_autocomplete() ) {
					$limit = intval( WPML_Settings::taxonomies__actor_limit() );
					$actors = explode( ',', $movie_meta['crew']['cast'] );
					if ( $limit )
						$actors = array_slice( $actors, 0, $limit );
					$actors = wp_set_object_terms( $post_ID, $actors, 'actor', false );
				}

				// Autofilling Genres
				if ( WPML_Settings::taxonomies__enable_genre() && WPML_Settings::taxonomies__genre_autocomplete() ) {
					$genres = explode( ',', $movie_meta['meta']['genres'] );
					$genres = wp_set_object_terms( $post_ID, $genres, 'genre', false );
				}

				// Autofilling Collections
				if ( WPML_Settings::taxonomies__enable_collection() && WPML_Settings::taxonomies__collection_autocomplete() ) {
					$collections = explode( ',', $movie_meta['crew']['director'] );
					$collections = wp_set_object_terms( $post_ID, $collections, 'collection', false );
				}
			}
			else if ( isset( $_REQUEST['tmdb_data'] ) && '' != $_REQUEST['tmdb_data'] ) {

				$movie_meta = apply_filters( 'wpml_filter_empty_array', $_REQUEST['tmdb_data'] );
				$movie_meta = apply_filters( 'wpml_validate_meta_data', $movie_meta );

				update_post_meta( $post_ID, '_wpml_movie_data', $movie_meta );
			}

			if ( isset( $_REQUEST['wpml_details'] ) && ! is_null( $_REQUEST['wpml_details'] ) ) {

				if ( isset( $_REQUEST['is_quickedit'] ) || isset( $_REQUEST['is_bulkedit'] ) )
					WPML_Utils::check_admin_referer( 'quickedit-movie-details' );

				$wpml_details = $_REQUEST['wpml_details'];
				self::save_movie_details( $post_ID, $wpml_details );
			}

			return ( ! empty( $errors->errors ) ? $errors : $post_ID );
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param    bool    $network_wide
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