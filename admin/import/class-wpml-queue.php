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

			add_action( 'admin_init', array( $this, 'init' ) );

			add_action( 'wp_ajax_wpml_enqueue_movies', __CLASS__ . '::enqueue_movies_callback' );
			add_action( 'wp_ajax_wpml_dequeue_movies', __CLASS__ . '::dequeue_movies_callback' );
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

			ob_start();
			self::display_queued_movie_list();
			$rows = ob_get_clean();

			$total_items = self::get_queued_movies_count();

			$response = array( 'rows' => $rows );
			$response['total_items'] = $total_items;
			$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );
			$response['pagination']['top'] = '';
			$response['pagination']['bottom'] = '';
			$response['column_headers'] = '';

			wp_die( json_encode( $response ) );
		}

		/**
		 * Callback for WPML_Queue movie enqueue method.
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
		 * Callback for WPML_Queue movie dequeue method.
		 * 
		 * Checks the AJAX nonce and calls dequeue_movies() to
		 * pop movies off the import queue.
		 *
		 * @since     1.0.0
		 */
		public static function dequeue_movies_callback() {

			check_ajax_referer( 'wpml-movie-dequeue', 'wpml_ajax_movie_dequeue' );
			self::dequeue_movies();
		}

		/**
		 * Display a custom WP_List_Table of queued movies
		 *
		 * @since     1.0.0
		 */
		public static function display_queued_movie_list() {

			$movies = self::get_queued_movies();
			$_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX );

			if ( ! $_ajax ) :
?>
					<ul id="wpml-queued-list" class="wp-list-table">

<?php
			endif;

			foreach ( $movies as $movie ) : ?>
						<li>
							<div scope="row" class="check-column"><input type="checkbox" id="post_<?php echo $movie['ID'] ?>" name="movie[]" value="<?php echo $movie['ID'] ?>" /></div>
							<div class="movietitle column-movietitle"><span class="movie_title"><?php echo $movie['title'] ?></span></div>
							<div class="director column-director"><span class="movie_director"><?php echo $movie['director'] ?></span></div>
							<div class="actions column-actions">
								<div class="row-actions visible">
									<span class="dequeue"><a class="dequeue_movie" id="dequeue_<?php echo $movie['ID'] ?>" data-post-id="<?php echo $movie['ID'] ?>" href="#" title="<?php _e( 'Dequeue', WPML_SLUG ) ?>" onclick="wpml_queue._dequeue(['<?php echo $movie['ID'] ?>']); return false;"><span class="dashicons dashicons-no"></span></a> | </span>
									<span class="delete"><a class="delete_movie" id="delete_<?php echo $movie['ID'] ?>" data-post-id="<?php echo $movie['ID'] ?>" href="#" title="<?php _e( 'Delete', WPML_SLUG ) ?>" onsubmit="wpml_importer.delete_movie(['<?php echo $movie['ID'] ?>']); return false;"><span class="dashicons dashicons-post-trash"></span></a></span>
								</div>
							</div>
							<div class="status column-status"><span class="movie_status"><?php _e( 'Queued', WPML_SLUG ) ?></span></div>
						</li>
<?php
			endforeach;
			if ( ! $_ajax ) :
?>
					</ul>
<?php
			endif;
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

			$movies = ( isset( $_POST['movies'] ) && '' != $_POST['movies'] ? $_POST['movies'] : null );

			if ( is_null( $movies ) || ! is_array( $movies ) )
				return false;

			foreach ( $movies as $movie )
				self::enqueue_movie( $movie['post_id'], esc_attr( $movie['meta']['title'] ), $movie );

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
		 * Process the submitted dequeue movie list
		 *
		 * @since     1.0.0
		 * 
		 * @return    void|boolean|string
		 */
		private static function dequeue_movies() {

			$errors = array();
			$_notice = '';

			$movies = ( isset( $_POST['movies'] ) && '' != $_POST['movies'] ? $_POST['movies'] : null );

			if ( is_null( $movies ) || ! is_array( $movies ) )
				return false;

			foreach ( $movies as $movie )
				self::dequeue_movie( $movie );

			wp_die();
		}

		/**
		 * Remove a movie from the queue list.
		 * 
		 * Simply change the movie's post_status to 'import-draft' and
		 * update the dates.
		 *
		 * @since     1.0.0
		 * 
		 * @param     string     $title Movie title.
		 * 
		 * @return    int        ID of the updated movie if everything worked, 0 else.
		 */
		private static function dequeue_movie( $post_id ) {

			$post_date     = current_time('mysql');
			$post_date     = wp_checkdate( substr( $post_date, 5, 2 ), substr( $post_date, 8, 2 ), substr( $post_date, 0, 4 ), $post_date );
			$post_date_gmt = get_gmt_from_date( $post_date );

			$_post = array(
				'ID'             => $post_id,
				'post_date'      => $post_date,
				'post_date_gmt'  => $post_date_gmt,
				'post_status'    => 'import-draft'
			);

			$id = wp_update_post( $_post );

			if ( false === $id ) {
				printf( __( 'An error occured when trying to remove "%s" from the queue.', WPML_SLUG ), get_the_title( $post_id ) );
				return false;
			}

			$id = delete_post_meta( $post_id, '_wpml_movie_data' );

			if ( false === $id ) {
				printf( __( 'An error occured when trying to remove "%s" from the queue.', WPML_SLUG ), get_the_title( $post_id ) );
				return false;
			}

			return true;
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
							'title' => get_the_title(),
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