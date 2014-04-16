<?php
/**
 * WPMovieLibrary Queue Class extension.
 * 
 * Queued Movies
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Queue' ) ) :

	class WPML_Queue extends WPML_Module {

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

			add_action( 'admin_init', array( $this, 'init' ) );

			add_action( 'wp_ajax_wpml_enqueue_movies', __CLASS__ . '::enqueue_movies_callback' );
			add_action( 'wp_ajax_wpml_fetch_queued_movies', __CLASS__ . '::fetch_queued_movies_callback' );
		}

		/**
		 * Callback for Queued Movies WPML_Queue_Table AJAX navigation.
		 * 
		 * Checks the AJAX nonce, create a new instance of WPML_Queue_Table
		 * and calls the AJAX handling method to echo the requested rows.
		 *
		 * @since     1.0.0
		 */
		public static function fetch_queued_movies_callback() {

			check_ajax_referer( 'wpml-fetch-queued-movies-nonce', 'wpml_fetch_queued_movies_nonce' );

			$wp_list_table = new WPML_Queue_Table();
			$wp_list_table->ajax_response();
		}

		/**
		 * Callback for WPML_Import movie enqueue method.
		 * 
		 * Checks the AJAX nonce and calls enqueue_movies() to
		 * create import queue of all movies passed through the list.
		 *
		 * @since     1.0.0
		 */
		public static function enqueue_movies_callback() {

			check_ajax_referer( 'wpml-movie-enqueue', 'wpml_ajax_movie_enqueue' );
			self::enqueue_movies();
		}

		/**
		 * Display a custom WP_List_Table of queued movies
		 *
		 * @since     1.0.0
		 */
		public static function display_queued_movie_list() {

			$list = new WPML_Queue_Table();
			$list->prepare_items();
	?>
				<form method="post">
					<input type="hidden" name="page" value="import" />

<?php
			$list->display();

?>
				</form>
<?php
		}

		/**
		 * Process the submitted queued movie list
		 * 
		 * 
		 *
		 * @since     1.0.0
		 * 
		 * @return    void|boolean|string
		 */
		private static function enqueue_movies() {

			$errors = array();
			$_notice = '';

			$post_id = ( isset( $_POST['post_id'] ) && '' != $_POST['post_id'] ? $_POST['post_id'] : null );
			$title = ( isset( $_POST['title'] ) && '' != $_POST['title'] ? $_POST['title'] : null );
			$metadata = ( isset( $_POST['metadata'] ) && '' != $_POST['metadata'] ? $_POST['metadata'] : null );

			if ( is_null( $post_id ) || is_null( $title ) || is_null( $metadata ) )
				return false;

			self::enqueue_movie( $post_id, $title, $metadata );
			wp_die();
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
		 * @since     1.0.0
		 * 
		 * @param     string     $title Movie title.
		 * @param     array      $metadata Movie metadata.
		 * 
		 * @return    int        ID of the updated movie if everything worked, 0 else.
		 */
		private static function enqueue_movie( $post_id, $title, $metadata ) {

			$post_date     = current_time('mysql');
			$post_date     = wp_checkdate( substr( $post_date, 5, 2 ), substr( $post_date, 8, 2 ), substr( $post_date, 0, 4 ), $post_date );
			$post_date_gmt = get_gmt_from_date( $post_date );
			$post_title    = apply_filters( 'the_title', $title );

			$_post = array(
				'ID'             => $post_id,
				'post_date'      => $post_date,
				'post_date_gmt'  => $post_date_gmt,
				'post_name'      => sanitize_title( $post_title ),
				'post_title'     => $post_title,
				'post_status'    => 'import-queued'
			);

			$id = wp_update_post( $_post );

			if ( false === $id )
				printf( __( 'An error occured when adding "%s" to the queue.', WPML_SLUG ), $post_title );

			WPML_Edit_Movies::save_movie_meta( $id, $post = null, $queue = true, $metadata );
		}

		/**
		 * Get queued imported movies.
		 * 
		 * Fetch all posts with 'import-queued' status and 'movie' post type
		 *
		 * @since     1.0.0
		 * 
		 * @return    array    Default movie values
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
						//echo '<!-- '.print_r( $metadata, true ).' -->';
						$metadata = WPML_Utils::get_movie_data( get_the_ID() );
						$columns[ get_the_ID() ] = array(
							'ID'         => get_the_ID(),
							//'poster'     => '<img src="' . WPML_TMDb::get_base_url( 'poster', 'xxx-small' ) . $metadata['poster'] . '" alt="' . get_the_title() . '" />',
							'movietitle' => get_the_title(),
							'director'   => $metadata['crew']['director'],
							'tmdb_id'    => get_post_meta( get_the_ID(), '_wpml_tmdb_id', true )
						);
					}
				}
			}

			return $columns;
		}

		/**
		 * Get queued imported movies count.
		 * 
		 * @since     1.0.0
		 * 
		 * @return    int    Total number of imported movies
		 */
		public static function get_queued_movies_count() {

			$args = array(
				'posts_per_page' => -1,
				'post_type'   => 'movie',
				'post_status' => 'import-queued'
			);

			$query = query_posts( $args );

			return count( $query );
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