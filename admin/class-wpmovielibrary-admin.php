<?php
/**
 * WPMovieLibrary
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

if ( ! class_exists( 'WPMovieLibrary_Admin' ) ) :

	/**
	* Plugin Admin class.
	*
	* @package WPMovieLibrary_Admin
	* @author  Charlie MERLAND <charlie.merland@gmail.com>
	*/
	class WPMovieLibrary_Admin extends WPML_Module {

		/**
		 * Slug of the plugin screen.
		 *
		 * @since    1.0.0
		 * @var      string
		 */
		protected $plugin_screen_hook_suffix = null;

		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		protected function __construct() {

			$this->register_hook_callbacks();

			$this->modules = array(
				'WPML_Settings'    => WPML_Settings::get_instance(),
				'WPML_Utils'       => WPML_Utils::get_instance(),
				'WPML_TMDb'        => WPML_TMDb::get_instance(),
				'WPML_Edit_Movies' => WPML_Edit_Movies::get_instance(),
				'WPML_Movies'      => WPML_Movies::get_instance(),
				'WPML_Media'       => WPML_Media::get_instance(),
				'WPML_Import'      => WPML_Import::get_instance(),
				'WPML_Collections' => WPML_Collections::get_instance(),
				'WPML_Genres'      => WPML_Genres::get_instance(),
				'WPML_Actors'      => WPML_Actors::get_instance()
			);
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'admin_init', array( $this, 'init' ) );

			// Add the options page and menu item.
			add_action( 'admin_menu', __CLASS__ . '::wpml_admin_menu' );

			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		}

		/**
		 * Register and enqueue admin-specific style sheet.
		 *
		 * @since     1.0.0
		 *
		 * @return    null    Return early if no settings page is registered.
		 */
		public function enqueue_admin_styles() {


			if ( ! isset( $this->plugin_screen_hook_suffix ) )
				return;

			wp_enqueue_style( WPML_SLUG .'-admin-common', WPML_URL . '/admin/assets/css/admin-common.css', array(), WPML_VERSION );

			$screen = get_current_screen();
			if ( in_array( $screen->id, $this->plugin_screen_hook_suffix ) )
				wp_enqueue_style( WPML_SLUG .'-admin-styles', WPML_URL . '/admin/assets/css/admin.css', array(), WPML_VERSION );

		}

		/**
		 * Register and enqueue admin-specific JavaScript.
		 *
		 * @since     1.0.0
		 *
		 * @return    null    Return early if no settings page is registered.
		 */
		public function enqueue_admin_scripts() {

			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return;
			}

			$screen = get_current_screen();
			if ( in_array( $screen->id, $this->plugin_screen_hook_suffix ) ) {

				if ( 'edit-movie' == $screen->id )
					wp_enqueue_script( 'jquery-ui-progressbar' );

				if ( 'movie_page_settings' == $screen->id ) {
					wp_enqueue_script( 'jquery-ui-sortable' );
					wp_enqueue_script( 'jquery-ui-draggable' );
					wp_enqueue_script( 'jquery-ui-droppable' );
					wp_enqueue_script( 'jquery-ui-tabs' );
				}

				wp_enqueue_script( WPML_SLUG . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-progressbar', 'jquery-ui-tabs' ), WPML_VERSION );
				wp_localize_script(
					WPML_SLUG . '-admin-script', 'ajax_object',
					array(
						'ajax_url'           => admin_url( 'admin-ajax.php' ),
						'wpml_check'         => wp_create_nonce( 'wpml-callbacks-nonce' ),
						'images_added'       => __( 'Images uploaded!', 'wpml' ),
						'base_url_xxsmall'   => WPML_TMDb::wpml_tmdb_get_base_url( 'poster', 'xx-small' ),
						'base_url_xsmall'    => WPML_TMDb::wpml_tmdb_get_base_url( 'poster', 'x-small' ),
						'base_url_small'     => WPML_TMDb::wpml_tmdb_get_base_url( 'image', 'small' ),
						'base_url_medium'    => WPML_TMDb::wpml_tmdb_get_base_url( 'image', 'medium' ),
						'base_url_full'      => WPML_TMDb::wpml_tmdb_get_base_url( 'image', 'full' ),
						'base_url_original'  => WPML_TMDb::wpml_tmdb_get_base_url( 'image', 'original' ),
						'search_movie_title' => __( 'Searching movie', 'wpml' ),
						'search_movie'       => __( 'Fetching movie data', 'wpml' ),
						'set_featured'       => __( 'Setting featured image…', 'wpml' ),
						'images_added'       => __( 'Images added!', 'wpml' ),
						'image_from'         => __( 'Image from', 'wpml' ),
						'load_images'        => __( 'Load Images', 'wpml' ),
						'load_more'          => __( 'Load More', 'wpml' ),
						'loading_images'     => __( 'Loading Images…', 'wpml' ),
						'save_image'         => __( 'Saving Images…', 'wpml' ),
						'poster'             => __( 'Poster', 'wpml' ),
						'done'               => __( 'Done!', 'wpml' ),
						'see_more'           => __( 'see more', 'wpml' ),
						'see_less'           => __( 'see no more', 'wpml' ),
						'oops'               => __( 'Oops… Did something went wrong?', 'wpml' )
					)
				);
			}

		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                     Settings, Import/Export Pages
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Register the administration menu for this plugin into the WordPress
		 * Dashboard menu.
		 * 
		 * TODO: export support
		 *
		 * @since    1.0.0
		 */
		public static function wpml_admin_menu() {

			add_submenu_page(
				'edit.php?post_type=movie',
				__( 'Import Movies', 'wpml' ),
				__( 'Import Movies', 'wpml' ),
				'manage_options',
				'import',
				'WPML_Import::wpml_import_page'
			);
			/*add_submenu_page(
				'edit.php?post_type=movie',
				__( 'Export Movies', 'wpml' ),
				__( 'Export Movies', 'wpml' ),
				'manage_options',
				'export',
				__CLASS__ . '::wpml_export_page'
			);*/
			add_submenu_page(
				'edit.php?post_type=movie',
				__( 'Options', 'wpml' ),
				__( 'Options', 'wpml' ),
				'manage_options',
				'settings',
				__CLASS__ . '::wpml_admin_page'
			);
		}

		/**
		 * Render options page.
		 *
		 * @since    1.0.0
		 */
		public static function wpml_admin_page() {

			$errors = array();
			$_notice = '';
			$_section = '';

			if ( isset( $_POST['restore_default'] ) && '' != $_POST['restore_default'] ) {

				check_admin_referer('wpml-admin');

				if ( 0 === did_action( 'wpml_restore_default_settings' ) )
					do_action( 'wpml_restore_default_settings' );
				$_notice = __( 'Default Settings have been restored.', 'wpml' );
			}

			if ( isset( $_POST['submit'] ) && '' != $_POST['submit'] ) {

				check_admin_referer('wpml-admin');

				if ( isset( $_POST['tmdb_data'] ) && '' != $_POST['tmdb_data'] )
					$tmdb_data = $_POST['tmdb_data'];

				if ( isset( $tmdb_data['wpml'] ) && '' != $tmdb_data['wpml'] ) {

					$supported = array_keys( WPML_Settings::wpml_o( 'wpml-settings' ) );
					$wpml = $tmdb_data['wpml'];

					if ( isset( $wpml['default_movie_meta_sorted'] ) && '' != $wpml['default_movie_meta_sorted'] ) {
						$wpml['default_movie_meta'] = explode( ',', $wpml['default_movie_meta_sorted'] );
						unset( $wpml['default_movie_meta_sorted'] );
					}

					foreach ( $wpml as $key => $setting ) {
						if ( in_array( $key, $supported ) ) {
							if ( is_array( $setting ) )
								WPML_Settings::wpml_o( 'wpml-settings-'.esc_attr( $key ), $setting );
							else
								WPML_Settings::wpml_o( 'wpml-settings-'.esc_attr( $key ), esc_attr( $setting ) );
						}
					}
				}

				if ( isset( $tmdb_data['tmdb'] ) && '' != $tmdb_data['tmdb'] ) {

					$tmdb = $tmdb_data['tmdb'];
					$supported = array_keys( WPML_Settings::wpml_o( 'tmdb-settings' ) );
					foreach ( $tmdb as $key => $setting ) {
						if ( in_array( $key, $supported ) ) {
							WPML_Settings::wpml_o( 'tmdb-settings-'.esc_attr( $key ), esc_attr( $setting ) );
						}
					}
				}

				if ( empty( $errors ) )
					$_notice = __( 'Settings saved.', 'wpml' );

			}

			if ( isset( $_REQUEST['wpml_section'] ) && in_array( $_REQUEST['wpml_section'], array( 'tmdb', 'wpml', 'uninstall', 'restore' ) ) )
				$_section =  $_REQUEST['wpml_section'];

			include_once( WPML_PATH . '/admin/views/admin.php' );
		}

		/**
		 * Render movie export page
		 *
		 * @since    1.0.0
		 */
		public function wpml_export_page() {
			// TODO: implement export
			// include_once( 'views/export.php' );
		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Methods
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Get number of existing Collections.
		 * 
		 * @since    1.0.0
		 * 
		 * @return   int    Total count of Collections
		 */
		/*public function wpml_get_collection_count() {
			$c = get_terms( array( 'collection' ) );
			return ( isset( $c[0]->count ) && '' != $c[0]->count ? $c[0]->count : 0 );
		}*/

		/**
		 * Get number of existing Movies.
		 * 
		 * @since    1.0.0
		 * 
		 * @return   int    Total count of Movies
		 */
		/*public function wpml_get_movie_count() {
			$c = get_posts( array( 'posts_per_page' => -1, 'post_type' => 'movie' ) );
			return count( $c );
		}*/

		/**
		 * Get all available movies.
		 * 
		 * @since    1.0.0
		 * 
		 * @return   array    Movie list
		 */
		/*public function wpml_get_movies() {

			$movies = array();

			query_posts( array(
				'posts_per_page' => -1,
				'post_type'   => 'movie'
			) );

			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					$movie = array(
						'id'     => get_the_ID(),
						'title'  => get_the_title(),
						'url'    => get_permalink(),
						'poster' => $this->wpml_get_featured_image( get_the_ID(), 'medium' )
					);

					$tmdb_data = get_post_meta( get_the_ID(), '_wpml_movie_data', true );
					if ( '' != $tmdb_data ) {
						$movie['genres']   = $tmdb_data['genres'];
						$movie['runtime']  = $tmdb_data['runtime'];
						$movie['overview'] = $tmdb_data['overview'];
					}

					$movies[] = $movie;
				}
			}

			return $movies;

		}*/

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {

			$this->plugin_screen_hook_suffix = array(
				'movie_page_import', 'movie_page_settings', 'edit-movie', 'movie', 'plugins'
			);

		}

	}
endif;
