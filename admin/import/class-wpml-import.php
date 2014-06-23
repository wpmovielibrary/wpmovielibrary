<?php
/**
 * WPMovieLibrary Import Class extension.
 * 
 * Import Movies
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Import' ) ) :

	class WPML_Import extends WPML_Module {

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

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			add_filter( 'set-screen-option', __CLASS__ . '::import_movie_list_set_option', 10, 3 );

			add_action( 'wp_ajax_wpml_delete_movies', __CLASS__ . '::delete_movies_callback' );
			add_action( 'wp_ajax_wpml_import_movies', __CLASS__ . '::import_movies_callback' );
			add_action( 'wp_ajax_wpml_imported_movies', __CLASS__ . '::imported_movies_callback' );
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

			global $admin_page_hooks;
			if ( $admin_page_hooks['wpmovielibrary'] . '_page_wpml_import' != $hook )
				return;

			wp_enqueue_script( WPML_SLUG . '-jquery-ajax-queue', WPML_URL . '/assets/js/jquery.ajaxQueue.js', array( 'jquery' ), WPML_VERSION, true );
			wp_enqueue_script( WPML_SLUG . '-importer-meta' , WPML_URL . '/assets/js/wpml.importer.meta.js' , array( WPML_SLUG, 'jquery' ), WPML_VERSION, true );
			wp_enqueue_script( WPML_SLUG . '-importer-movies', WPML_URL . '/assets/js/wpml.importer.movies.js', array( WPML_SLUG, 'jquery' ), WPML_VERSION, true );
			wp_enqueue_script( WPML_SLUG . '-importer-view', WPML_URL . '/assets/js/wpml.importer.view.js', array( WPML_SLUG, 'jquery' ), WPML_VERSION, true );
			wp_enqueue_script( WPML_SLUG . '-queue', WPML_URL . '/assets/js/wpml.queue.js', array( WPML_SLUG, 'jquery' ), WPML_VERSION, true );
		}

		/**
		 * Delete movie
		 * 
		 * Remove imported movies draft and attachment from database
		 *
		 * @since     1.0.0
		 * 
		 * @return     boolean     deletion status
		 */
		public static function delete_movies_callback() {

			WPML_Utils::check_ajax_referer( 'delete-movies' );

			$movies = ( isset( $_GET['movies'] ) && '' != $_GET['movies'] ? $_GET['movies'] : null );

			$response = self::delete_movies( $movies );
			WPML_Utils::ajax_response( $response, array(), WPML_Utils::create_nonce( 'delete-movies' ) );
		}

		/**
		 * Callback for Imported Movies WPML_Import_Table AJAX navigation.
		 * 
		 * Checks the AJAX nonce, create a new instance of WPML_Import_Table
		 * and calls the AJAX handling method to echo the requested rows.
		 *
		 * @since     1.0.0
		 */
		public static function imported_movies_callback() {

			WPML_Utils::check_ajax_referer( 'imported-movies' );

			$wp_list_table = new WPML_Import_Table();
			$wp_list_table->ajax_response();
		}

		/**
		 * Callback for WPML_Import movie import method.
		 * 
		 * Checks the AJAX nonce and calls import_movies() to
		 * create import drafts of all movies passed through the list.
		 *
		 * @since     1.0.0
		 */
		public static function import_movies_callback() {

			WPML_Utils::check_ajax_referer( 'import-movies-list' );

			$movies = ( isset( $_POST['movies'] ) && '' != $_POST['movies'] ? esc_textarea( $_POST['movies'] ) : null );

			$response = self::import_movies( $movies );
			WPML_Utils::ajax_response( $response, array(), WPML_Utils::create_nonce( 'import-movies-list' ) );
		}

		/**
		 * Display a custom WP_List_Table of imported movies
		 *
		 * @since     1.0.0
		 */
		public static function display_import_movie_list() {

			$list = new WPML_Import_Table();
			$list->prepare_items();
	?>
				<form method="post">
					<input type="hidden" name="page" value="import" />

<?php
			$list->search_box('search', 'search_id'); 
			$list->display();

?>
				</form>
<?php
		}

		/**
		 * Delete movies
		 * 
		 * 
		 *
		 * @since     1.0.0
		 * 
		 * @param     array    $movies Array of movies Post IDs to delete
		 * 
		 * @return    array|WP_Error    Array of delete movie IDs if successfull,
		 *                              WP_Error instance if anything failed
		 */
		public static function delete_movies( $movies ) {

			$errors = new WP_Error();
			$response = array();

			if ( is_null( $movies ) || ! is_array( $movies ) ) {
				$errors->add( 'invalid', __( 'Invalid movie list submitted.', WPML_SLUG ) );
				return $errors;
			}

			$response = WPML_Utils::ajax_filter( array( __CLASS__, 'delete_movie' ), array( $movies ), $loop = true );
			return $response;
		}

		/**
		 * Delete imported movie
		 * 
		 * Delete the specified movie from the list of movie set for further
		 * import. Automatically delete attached images such as featured
		 * image if any is found.
		 *
		 * @since     1.0.0
		 * 
		 * @param     int    $post_id    Movie's post ID.
		 * 
		 * @return    int|WP_Error    Movie Post ID if deleted, WP_Error
		 *                            if Post or Attachment delete failed
		 */
		public static function delete_movie( $post_id ) {

			if ( false === wp_delete_post( $post_id, $force_delete = true ) )
				return new WP_Error( 'error', sprintf( __( 'An error occured trying to delete Post #%s', WPML_SLUG ), $post_id ) );

			$thumb_id = get_post_thumbnail_id( $post_id );
			if ( '' != $thumb_id )
				if ( false === wp_delete_attachment( $thumb_id ) )
					return new WP_Error( 'error', sprintf( __( 'An error occured trying to delete Attachment #%s', WPML_SLUG ), $thumb_id ) );

			return $post_id;
		}

		/**
		 * Process the submitted movie list
		 * 
		 * This method can be used through an AJAX callback; in this case
		 * the nonce check is already done in callback so we only check
		 * for nonce we're not doing AJAX. List is exploded by comma and
		 * fed to import_movie() to create import drafts.
		 * 
		 * If AJAX, the function echo a status message and simply dies.
		 * If no AJAX, ir returns false on failure, and a status message
		 * on success.
		 *
		 * @since     1.0.0
		 * 
		 * @return    void|boolean|string
		 */
		private static function import_movies( $movies ) {

			$errors = new WP_Error();
			$response = array();

			$movies = explode( ',', $movies );
			$movies = array_map( __CLASS__ . '::prepare_movie_import', $movies );

			if ( is_null( $movies ) || ! is_array( $movies ) ) {
				$errors->add( 'invalid', __( 'Invalid movie list submitted.', WPML_SLUG ) );
				return $errors;
			}

			$response = WPML_Utils::ajax_filter( array( __CLASS__, 'import_movie' ), array( $movies ), $loop = true );
			return $response;
		}

		/**
		 * Save a temporary movie for submitted title.
		 * 
		 * This is used to save movies submitted from a list before any
		 * alteration is made by user. Posts will be kept as 'import-draft'
		 * for 24 hours and then destroyed on the next plugin init.
		 *
		 * @since     1.0.0
		 * 
		 * @param     string     $title Movie title.
		 * 
		 * @return    int        Newly created post ID if everything worked, 0 if no post created.
		 */
		public static function import_movie( $movie ) {

			$post_date     = current_time('mysql');
			$post_date     = wp_checkdate( substr( $post_date, 5, 2 ), substr( $post_date, 8, 2 ), substr( $post_date, 0, 4 ), $post_date );
			$post_date_gmt = get_gmt_from_date( $post_date );
			$post_author   = get_current_user_id();
			$post_content  = '';
			$post_excerpt  = '';
			$post_title    = $movie['movietitle'];

			$page = get_page_by_title( $post_title, OBJECT, 'movie' );

			if ( ! is_null( $page ) ) {
				$message = sprintf( '%s − <span class="edit"><a href="%s">%s</a> |</span> <span class="view"><a href="%s">%s</a></span>',
					sprintf( __( 'Movie "%s" already imported.', WPML_SLUG ), "<em>" . get_the_title( $page->ID ) . "</em>" ),
					get_edit_post_link( $page->ID ),
					__( 'Edit', WPML_SLUG ),
					get_permalink( $page->ID ),
					__( 'View', WPML_SLUG )
				);
				return new WP_Error( 'existing_movie', $message );
			}

			$posts = array(
				'ID'             => '',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_author'    => $post_author,
				'post_content'   => $post_content,
				'post_excerpt'   => $post_excerpt,
				'post_date'      => $post_date,
				'post_date_gmt'  => $post_date_gmt,
				'post_name'      => sanitize_title( $post_title ),
				'post_status'    => 'import-draft',
				'post_title'     => $post_title,
				'post_type'      => 'movie'
			);

			$import = wp_insert_post( $posts );

			return $import;
		}

		/**
		 * Set the default values for imported movies list
		 *
		 * @since     1.0.0
		 * 
		 * @param     string    $title    Movie title
		 * 
		 * @return    array    Default movie values
		 */
		private static function prepare_movie_import( $title ) {
			$title = str_replace( "\&#039;", "'", $title );
			return array(
				'ID'         => 0,
				'poster'     => '--',
				'movietitle' => trim( $title ),
				'director'   => '--',
				'tmdb_id'    => '--'
			);
		}

		/**
		 * Get previously imported movies.
		 * 
		 * Fetch all posts with 'import-draft' status and 'movie' post type
		 *
		 * @since     1.0.0
		 * 
		 * @return    array    Default movie values
		 */
		public static function get_imported_movies() {

			$columns = array();

			$args = array(
				'posts_per_page' => -1,
				'post_type'   => 'movie',
				'post_status' => 'import-draft'
			);

			query_posts( $args );

			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					if ( 'import-draft' == get_post_status() ) {
						$columns[ get_the_ID() ] = array(
							'ID'         => get_the_ID(),
							'poster'     => get_post_meta( get_the_ID(), '_wp_attached_file', true ),
							'movietitle' => get_the_title(),
							'director'   => get_post_meta( get_the_ID(), '_wpml_tmdb_director', true ),
							'tmdb_id'    => get_post_meta( get_the_ID(), '_wpml_tmdb_id', true )
						);
					}
				}
			}

			return $columns;
		}

		/**
		 * Add a Screen Option panel on Movie Import Page.
		 *
		 * @since     1.0.0
		 */
		public static function import_movie_list_add_options() {

			$option = 'per_page';
			$args = array(
				'label'   => __( 'Import Drafts', WPML_SLUG ),
				'default' => 30,
				'option'  => 'drafts_per_page'
			);

			add_screen_option( $option, $args );
		}

		/**
		 * Save newly set Movie Drafts number in Movie Import Page.
		 *
		 * @since     1.0.0
		 */
		public static function import_movie_list_set_option( $status, $option, $value ) {
			return $value;
		}

		/**
		 * Render movie import page
		 * 
		 * TODO: use WP_Error
		 *
		 * @since    1.0.0
		 */
		public static function import_page() {

			$errors = new WP_Error();
			$imported = array();

			$_section = '';
			$_imported = WPML_Stats::get_imported_movies_count();
			$_queued = WPML_Stats::get_queued_movies_count();

			if ( isset( $_POST['wpml_save_imported'] ) && '' != $_POST['wpml_save_imported'] && isset( $_POST['tmdb'] ) && count( $_POST['tmdb'] ) ) {

				WPML_Utils::check_admin_referer( 'save-imported-movies' );

				foreach ( $_POST['tmdb'] as $tmdb_data ) {
					if ( 0 != $tmdb_data['tmdb_id'] ) {
						$update = WPML_Edit_Movies::save_movie_meta( $tmdb_data['post_id'], $post = null, $queue = false, $tmdb_data );
						if ( is_wp_error( $update ) )
							$errors->add( $update->get_error_code(), $update->get_error_message() );
						else
							$imported[] = $update;
					}
				}

				if ( ! empty( $errors->errors ) ) {
					$_errors = array();
					foreach ( $errors->errors as $error ) {
						if ( is_array( $error ) ) {
							foreach ( $error as $e ) {
								$_errors[] = '<li>' . $e . '</li>';
							}
						}
						else {
							$_errors[] = '<li>' . $error . '</li>';
						}
					}
					WPML_Utils::admin_notice( sprintf( __( 'The following errors occured: <ul>%s</ul>', WPML_SLUG ), implode( '', $_errors ) ), 'error' );
				}

				if ( ! empty( $imported ) )
					WPML_Utils::admin_notice( sprintf( _n( 'One movie imported successfully!', '%d movies imported successfully!', count( $imported ), WPML_SLUG ), count( $imported ) ), 'updated' );
			}
			else if ( isset( $_POST['wpml_importer'] ) && '' != $_POST['wpml_importer'] ) {


			}

			if ( isset( $_GET['wpml_section'] ) && in_array( $_GET['wpml_section'], array( 'wpml_import', 'wpml_import_queue', 'wpml_imported' ) ) )
				$_section =  $_GET['wpml_section'];

			include_once( plugin_dir_path( __FILE__ ) . '/views/import.php' );
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