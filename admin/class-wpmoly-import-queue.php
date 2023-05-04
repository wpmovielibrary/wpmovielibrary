<?php
/**
 * WPMovieLibrary Queue Class extension.
 * 
 * Queued Movies
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Queue' ) ) :

	class WPMOLY_Queue extends WPMOLY_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {

			if ( ! is_admin() )
				return false;

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'admin_init', array( $this, 'init' ) );

			add_action( 'wp_ajax_wpmoly_enqueue_movies', __CLASS__ . '::enqueue_movies_callback' );
			add_action( 'wp_ajax_wpmoly_dequeue_movies', __CLASS__ . '::dequeue_movies_callback' );
			add_action( 'wp_ajax_wpmoly_queued_movies', __CLASS__ . '::queued_movies_callback' );
			add_action( 'wp_ajax_wpmoly_import_queued_movie', __CLASS__ . '::import_queued_movie_callback' );
		}

		/**
		 * Callback for WPMOLY_Queue movie enqueue method.
		 * 
		 * Checks the AJAX nonce and calls enqueue_movies() to
		 * create import queue of all movies passed through the list.
		 *
		 * @since    1.0
		 */
		public static function enqueue_movies_callback() {

			wpmoly_check_ajax_referer( 'enqueue-movies' );

			$movies = ( isset( $_POST['movies'] ) && '' != $_POST['movies'] ? $_POST['movies'] : null );

			$response = self::enqueue_movies( $movies );
			wpmoly_ajax_response( $response, array(), wpmoly_create_nonce( 'enqueue-movies' ) );
		}

		/**
		 * Callback for WPMOLY_Queue movie dequeue method.
		 * 
		 * Checks the AJAX nonce and calls dequeue_movies() to
		 * pop movies off the import queue.
		 *
		 * @since    1.0
		 */
		public static function dequeue_movies_callback() {

			wpmoly_check_ajax_referer( 'dequeue-movies' );

			$movies = ( isset( $_POST['movies'] ) && '' != $_POST['movies'] ? $_POST['movies'] : null );

			$response = self::dequeue_movies( $movies );
			wpmoly_ajax_response( $response, array(), wpmoly_create_nonce( 'dequeue-movies' ) );
		}

		/**
		 * Callback for Queued Movies list AJAX navigation.
		 *
		 * @since    1.0
		 */
		public static function queued_movies_callback() {

			wpmoly_check_ajax_referer( 'queued-movies' );

			ob_start();
			self::display_queued_movie_list();
			$rows = ob_get_clean();

			$total_items = (array) wp_count_posts( 'movie' );
			$total_items = $total_items['import-queued'];

			$response = array( 'rows' => $rows );
			$i18n = array();

			$response['total_items'] = $total_items;
			$response['pagination']['top'] = '';
			$response['pagination']['bottom'] = '';
			$response['column_headers'] = '';
			$i18n['total_items_i18n'] = ( $total_items ? sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) : _( 'No item' ) );

			wpmoly_ajax_response( $response, $i18n, wpmoly_create_nonce( 'queued-movies' ) );
		}

		/**
		 * Callback for Queued Movies Import.
		 *
		 * @since    1.0
		 */
		public static function import_queued_movie_callback() {

			wpmoly_check_ajax_referer( 'import-queued-movies' );

			$post_id = ( isset( $_POST['post_id'] ) && '' != $_POST['post_id'] ? $_POST['post_id'] : null );

			$response = self::import_queued_movie( $post_id );
			wpmoly_ajax_response( $response, array(), wpmoly_create_nonce( 'import-queued-movies' ) );
		}

		/**
		 * Display a custom WP_List_Table of queued movies
		 *
		 * @since    1.0
		 */
		public static function display_queued_movie_list() {

			$movies = self::get_queued_movies();
			$_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX );

			$attributes = array(
				'movies' => $movies,
				'_ajax' => $_ajax
			);

			echo self::render_admin_template( 'import/queued-movies.php', $attributes );
		}

		/**
		 * Process the submitted queued movie list
		 *
		 * @since    1.0
		 * 
		 * @param    array    $movies Array of movies metadata
		 * 
		 * @return   array|WP_Error    Array of update movies Post IDs if no error occured, or WP_Error
		 */
		private static function enqueue_movies( $movies ) {

			$response = array();
			$errors = new WP_Error();

			if ( is_null( $movies ) || ! is_array( $movies ) ) {
				$errors->add( 'invalid', __( 'Invalid movie list submitted.', 'wpmovielibrary' ) );
				return $errors;
			}

			$response = wpmoly_ajax_filter( array( __CLASS__, 'enqueue_movie' ), array( $movies ), $loop = true );
			return $response;
		}

		/**
		 * Save a movie in the queue list.
		 * 
		 * This is used to pre-import the movies submitted from a list
		 * and for which the metadata have been fetched, without not saving
		 * them as movies, meaning we save the metadata but don't import
		 * the posters. This is indicated for (very) large list of movies
		 * that can be a pain to import if anything goes wrong when
		 * downloading the poster.
		 *
		 * @since    1.0
		 * 
		 * @param    array    $metadata Movie metadata.
		 * 
		 * @return   int|WP_Error    Movie Post ID if successfully enqueued, WP_Error if failed.
		 */
		public static function enqueue_movie( $movie ) {

			$post_id = esc_attr( $movie['post_id'] );
			$post_title = esc_attr( $movie['title'] );
			$post_title = str_replace( "&#039;", "'", $post_title );
			$metadata = $movie;

			$post_date     = current_time( 'mysql' );
			$post_date_gmt = current_time( 'mysql', 1 );

			$_post = array(
				'ID'             => $post_id,
				'post_date'      => $post_date,
				'post_date_gmt'  => $post_date_gmt,
				'post_name'      => sanitize_title( $post_title ),
				'post_title'     => $post_title,
				'post_status'    => 'import-queued'
			);

			$update = wp_update_post( $_post, $wp_error = true );
			if ( is_wp_error( $update ) )
				return new WP_Error( 'error', sprintf( __( 'An error occured when adding "%s" to the queue: %s', 'wpmovielibrary' ), $post_title, $update->get_error_message() ) );

			$update = WPMOLY_Edit_Movies::save_movie( $update, $post = null, $queue = true, $metadata );
			if ( is_wp_error( $update ) )
				return new WP_Error( 'error', sprintf( __( 'An error occured when adding "%s" to the queue: %s', 'wpmovielibrary' ), $post_title, $update->get_error_message() ) );

			return $post_id;
		}

		/**
		 * Process the submitted dequeue movie list
		 * 
		 * Post IDs should be valid Movies CPT IDs. The method will return
		 * an array of Post IDs if no error occured, a WP_Error instance
		 * containing all errors if any.
		 *
		 * @since    1.0
		 * 
		 * @param    array    $movies Array of movies Post IDs to dequeue
		 * 
		 * @return   array|WP_Error    Array of update movies Post IDs if no error occured, or WP_Error
		 */
		private static function dequeue_movies( $movies ) {

			$response = array();
			$errors = new WP_Error();

			if ( is_null( $movies ) || ! is_array( $movies ) ) {
				$errors->add( 'invalid', __( 'Invalid movie list submitted.', 'wpmovielibrary' ) );
				return $errors;
			}

			$response = wpmoly_ajax_filter( array( __CLASS__, 'dequeue_movie' ), array( $movies ), $loop = true );
			return $response;
		}

		/**
		 * Remove a movie from the queue list.
		 * 
		 * Simply change the movie's post_status to 'import-draft',
		 * update the dates and delete the movie metadata.
		 *
		 * @since    1.0
		 * 
		 * @param    string     $post_id Movie Post ID.
		 * 
		 * @return   int|WP_Error    Post ID if everything worked, WP_Error instance if update of meta delete failed
		 */
		public static function dequeue_movie( $post_id ) {

			$_post = array(
				'ID'             => $post_id,
				'post_status'    => 'import-draft'
			);

			$update = wp_update_post( $_post, $wp_error = true );
			if ( is_wp_error( $update ) )
				return new WP_Error( 'error', sprintf( __( 'An error occured when trying to remove "%s" from the queue: %s', 'wpmovielibrary' ), get_the_title( $post_id ), $update->get_error_message() ) );

			$update = delete_post_meta( $post_id, '_wpmoly_movie_data' );
			if ( false === $update )
				return new WP_Error( 'error', sprintf( __( 'An error occured when trying to delete "%s" metadata.', 'wpmovielibrary' ), get_the_title( $post_id ) ) );

			return $post_id;
		}

		/**
		 * Convert a queued movie to regular movie.
		 * 
		 * Simply change the movie's post_status from 'import-queued' to
		 * 'publish' and 
		 *
		 * @since    1.0
		 * 
		 * @param    int    $post_id Movie Post ID
		 * 
		 * @return   int|boolean        ID of the updated movie if everything worked, false else.
		 */
		private static function import_queued_movie( $post_id ) {

			if ( is_null( $post_id ) || ! $post = get_post( $post_id ) || 'movie' != get_post_type( $post_id ) )
				return new WP_Error( 'invalid_movie', sprintf( __( 'Error: submitted Post ID doesn\t match any valid movie.', 'wpmovielibrary' ) ) );

			$meta = wpmoly_get_movie_meta( $post_id );
			if ( '' == $meta || ! is_array( $meta ) || ! isset( $meta['poster'] ) || ! isset( $meta['tmdb_id'] ) )
				return new WP_Error( 'invalid_meta', sprintf( __( 'Error: cannot find submitted movie\'s metadata, try enqueuing it again.', 'wpmovielibrary' ) ) );

			$_post = array(
				'ID'          => $post_id,
				'post_status' => 'publish',
				//'post_date'   => 
			);

			if ( wpmoly_o( 'poster-featured' ) ) {
				$id = WPMOLY_Media::set_image_as_featured( $meta['poster'], $post_id, $meta['tmdb_id'], $meta['title'] );
				update_post_meta( $post_id, '_thumbnail_id', $id );
			}

			$update = wp_update_post( $_post, $wp_error = true );
			if ( is_wp_error( $update ) )
				return new WP_Error( 'error', sprintf( __( 'An error occured when trying to imported queued movie "%s": %s', 'wpmovielibrary' ), get_the_title( $post_id ), $update->get_error_message() ) );

			return $update;
		}

		/**
		 * Get queued imported movies.
		 * 
		 * Fetch all posts with 'import-queued' status and 'movie' post type
		 *
		 * @since    1.0
		 * 
		 * @return   array    Default movie values
		 */
		public static function get_queued_movies() {

			$columns = array();

			$args = array(
				'posts_per_page' => -1,
				'post_type'   => 'movie',
				'post_status' => 'import-queued'
			);

			query_posts( $args );

			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					if ( 'import-queued' == get_post_status() ) {
						$columns[ get_the_ID() ] = array(
							'ID'       => get_the_ID(),
							'title'    => get_the_title(),
							'director' => wpmoly_get_movie_meta( get_the_ID(), 'director' ),
							'tmdb_id'  => wpmoly_get_movie_meta( get_the_ID(), 'tmdb_id', true )
						);
					}
				}
			}

			return $columns;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}

endif;