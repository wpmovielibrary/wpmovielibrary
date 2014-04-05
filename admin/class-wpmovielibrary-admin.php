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
		 * Plugin Settings.
		 *
		 * @since    1.0.0
		 * @var      string
		 */
		protected $settings;
		protected static $default_settings;

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
// 				'WPML_TMDb'        => WPML_TMDb::get_instance(),
				'WPML_Edit_Movies' => WPML_Edit_Movies::get_instance(),
// 				'WPML_Movies'      => WPML_Movies::get_instance(),
				'WPML_Media'       => WPML_Media::get_instance(),
				'WPML_Import'      => WPML_Import::get_instance(),
// 				'WPML_Collections' => WPML_Collections::get_instance(),
// 				'WPML_Genres'      => WPML_Genres::get_instance(),
// 				'WPML_Actors'      => WPML_Actors::get_instance()
			);
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			// Add the options page and menu item.
			add_action( 'admin_menu', array( $this, 'wpml_admin_menu' ) );

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

			wp_enqueue_style( WPML_SLUG .'-admin-common', WPML_URL . '/assets/css/admin-common.css', array(), WPML_VERSION );

			$screen = get_current_screen();
			if ( in_array( $screen->id, $this->plugin_screen_hook_suffix ) )
				wp_enqueue_style( WPML_SLUG .'-admin-styles', WPML_URL . '/assets/css/admin.css', array(), WPML_VERSION );

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

				wp_enqueue_script( WPML_SLUG . '-admin-script', WPML_URL . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-progressbar', 'jquery-ui-tabs' ), WPML_VERSION, true );
				wp_localize_script(
					WPML_SLUG . '-admin-script', 'ajax_object',
					array(
						'ajax_url'           => admin_url( 'admin-ajax.php' ),
						'wpml_check'         => wp_create_nonce( 'wpml-callbacks-nonce' ),
						'images_added'       => __( 'Images uploaded!', 'wpml' ),
						/*'base_url_xxsmall'   => WPML_TMDb::wpml_tmdb_get_base_url( 'poster', 'xx-small' ),
						'base_url_xsmall'    => WPML_TMDb::wpml_tmdb_get_base_url( 'poster', 'x-small' ),
						'base_url_small'     => WPML_TMDb::wpml_tmdb_get_base_url( 'image', 'small' ),
						'base_url_medium'    => WPML_TMDb::wpml_tmdb_get_base_url( 'image', 'medium' ),
						'base_url_full'      => WPML_TMDb::wpml_tmdb_get_base_url( 'image', 'full' ),
						'base_url_original'  => WPML_TMDb::wpml_tmdb_get_base_url( 'image', 'original' ),
						*/'empty_key'          => __( 'I can\'t test an empty key, you know.', 'wpml' ),
						'length_key'         => __( 'Invalid key: it should be 32 characters long.', 'wpml' ),
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
		 *                              Settings
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Establishes initial values for all settings
		 *
		 * @mvc Model
		 *
		 * @return array
		 */
		protected static function get_default_settings() {

			global $wpml_settings;

			$default_settings = apply_filters( 'wpml_summarize_settings', $wpml_settings );

			//if ( '' == get_option( 'wpml_settings' ) )
			//	add_option( 'wpml_settings', $default_settings, $deprecated = false, $autoload = 'no' );

			return $default_settings;
		}



		/**
		 * Retrieves all of the settings from the database
		 *
		 * @mvc Model
		 *
		 * @return array
		 */
		protected static function get_settings() {

			$settings = shortcode_atts(
				self::$default_settings,
				get_option( 'wpml_settings', array() )
			);

			return $settings;
		}

		/**
		 * Register the administration menu for this plugin into the WordPress
		 * Dashboard menu.
		 * 
		 * TODO: export support
		 *
		 * @since    1.0.0
		 */
		public function wpml_admin_menu() {

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
				'wpml_edit_settings',
				__CLASS__ . '::wpml_admin_page'
			);
			/*add_options_page(
				__( 'Options', 'wpml' ),
				__( 'Options', 'wpml' ),
				'manage_options',
				'wpml_settings',
				__CLASS__ . '::wpml_admin_page'
			);*/
		}

		/**
		 * Registers settings sections, fields and settings
		 *
		 * @since    1.0.0
		 */
		public function register_settings() {

			global $wpml_settings;

			foreach ( $wpml_settings as $section ) {

				$section_id = $section['section']['id'];
				$section_title = $section['section']['title'];

				add_settings_section(
					// ID
					"wpml_settings-$section_id",
					// Title
					$section_title,
					// Callback
					'WPMovieLibrary_Admin::markup_section_headers',
					// Page
					'wpml_settings'
				);

				foreach ( $section['settings'] as $id => $field ) {

					$callback = isset( $field['callback'] ) ? $field['callback'] : 'markup_fields';

					add_settings_field(
						// ID
						$id,
						// Title
						$field['title'],
						// Callback
						array( $this, $callback ),
						// Page
						'wpml_settings',
						// Section
						"wpml_settings-$section_id",
						// Arguments
						array( 'id' => $id, 'section' => $section_id ) + $field
					);
				}
			}

			// The settings container
			register_setting(
				'wpml_edit_settings',
				'wpml_settings'
			);
		}

		public function validate_settings( $new_settings ) {

			print_r( $new_settings );
			return $new_settings;
		}

		/**
		 * Render options page.
		 *
		 * @since    1.0.0
		 */
		public static function wpml_admin_page() {

			if ( current_user_can( 'manage_options' ) )
				include_once( plugin_dir_path( __FILE__ ) . 'settings/views/page-settings.php' );
			else
				wp_die( __( 'Access denied.', 'wpml' ) );
		}

		/**
		 * Adds the section introduction text to the Settings page
		 *
		 * @mvc Controller
		 *
		 * @param array $section
		 */
		public static function markup_section_headers( $section ) {
			include( plugin_dir_path( __FILE__ ) . 'settings/views/page-settings-section-headers.php' );
		}

		/**
		 * Delivers the markup for settings fields
		 *
		 * @mvc Controller
		 *
		 * @param array $field
		 */
		public function markup_fields( $field ) {

			$settings = $this->get_settings();

			$_type  = esc_attr( $field['type'] );
			$_title = esc_attr( $field['title'] );
			$_id    = "wpml_settings[{$field['section']}][{$field['id']}]";
			$_value = $settings[ $field['section'] ][ $field['id'] ];

			include( plugin_dir_path( __FILE__ ) . 'settings/views/page-settings-fields.php' );
		}

		/**
		 * Delivers the markup for default_movie_meta settings fields
		 *
		 * @param array $field
		 */
		public function sorted_markup_fields( $field ) {

			$settings = $this->get_settings();

			$_type  = 'sorted';
			$_title = esc_attr( $field['title'] );
			$_id    = "wpml_settings-{$field['section']}-{$field['id']}";
			$_name  = "wpml_settings[{$field['section']}][{$field['id']}]";
			$_value = $settings[ $field['section'] ][ $field['id'] ];

			$items      = WPML_Settings::wpml_get_supported_movie_meta();
			$selected   = $_value;
			$selectable = array_diff( array_keys( $items ), $selected );

			$draggable = ''; $droppable = ''; $options = '';

			foreach ( $selected as $meta ) :
				if ( isset( $items[ $meta ] ) )
					$draggable .= '<li data-movie-meta="' . $meta . '" class="default_movie_meta_selected">' . __( $items[ $meta ]['title'], 'wpml' ) . '</li>';
			endforeach;
			foreach ( $selectable as $meta ) :
				$droppable .= '<li data-movie-meta="' . $meta . '" class="default_movie_meta_droppable">' . __( $items[ $meta ]['title'], 'wpml' ) . '</li>';
			endforeach;

			foreach ( $items as $slug => $meta ) :
				$check = in_array( $slug, $_value );
				$options .= '<option value="' . $slug . '"' . selected( $check, true, false ) . '>' . __( $meta['title'], 'wpml' ) . '</option>';
			endforeach;

			include( plugin_dir_path( __FILE__ ) . 'settings/views/page-settings-fields.php' );
		}

		/**
		 * Render options page.
		 *
		 * @since    1.0.0
		 */
		public static function __wpml_admin_page() {

			/*$errors = array();
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

			include_once( plugin_dir_path( __FILE__ ) . 'settings/views/admin.php' );*/
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
		public function init() {

			$this->plugin_screen_hook_suffix = array(
				'movie_page_import', 'movie_page_wpml_edit_settings', 'edit-movie', 'movie', 'plugins'
			);

			self::$default_settings = self::get_default_settings();
			$this->settings         = self::get_settings();

		}

	}
endif;
