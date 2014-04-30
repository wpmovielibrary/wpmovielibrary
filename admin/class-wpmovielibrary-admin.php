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

			if ( ! is_admin() )
				return false;

			$this->register_hook_callbacks();

			$this->modules = array(
				'WPML_Settings'    => WPML_Settings::get_instance(),
				'WPML_TMDb'        => WPML_TMDb::get_instance(),
				'WPML_Utils'       => WPML_Utils::get_instance(),
				'WPML_Edit_Movies' => WPML_Edit_Movies::get_instance(),
				'WPML_Media'       => WPML_Media::get_instance(),
				'WPML_Import'      => WPML_Import::get_instance(),
				'WPML_Queue'       => WPML_Queue::get_instance()
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
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

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
				}

				$base_urls = WPML_TMDb::get_image_url();

				$localize = array(
					'utils' => array(
						'wpml_check' => wp_create_nonce( 'wpml-callbacks-nonce' ),
						'base_url' => array(
							'xxsmall'	=> $base_urls['poster']['xx-small'],
							'xsmall'	=> $base_urls['poster']['x-small'],
							'small'		=> $base_urls['image']['small'],
							'medium'	=> $base_urls['image']['medium'],
							'full'		=> $base_urls['image']['full'],
							'original'	=> $base_urls['image']['original'],
						)
					),
					'lang' => array(
						'done'			=> __( 'Done!', WPML_SLUG ),
						'empty_key'		=> __( 'I can\'t test an empty key, you know.', WPML_SLUG ),
						'images_added'		=> __( 'Images added!', WPML_SLUG ),
						'image_from'		=> __( 'Image from', WPML_SLUG ),
						'images_uploaded'	=> __( 'Images uploaded!', WPML_SLUG ),
						'import_images'		=> __( 'Import Images', WPML_SLUG ),
						'import_images_title'	=> __( 'Import Images for "%s"', WPML_SLUG ),
						'import_images_wait'	=> __( 'Please wait while the images are uploaded...', WPML_SLUG ),
						'import_posters'	=> __( 'Import Poster', WPML_SLUG ),
						'import_posters_title'	=> __( 'Select a poster for "%s"', WPML_SLUG ),
						'import_posters_wait'	=> __( 'Please wait while the poster is uploaded...', WPML_SLUG ),
						'imported'		=> __( 'Imported', WPML_SLUG ),
						'in_progress'		=> __( 'Progressing', WPML_SLUG ),
						'length_key'		=> __( 'Invalid key: it should be 32 characters long.', WPML_SLUG ),
						'load_images'		=> __( 'Load Images', WPML_SLUG ),
						'load_more'		=> __( 'Load More', WPML_SLUG ),
						'loading_images'	=> __( 'Loading Images…', WPML_SLUG ),
						'oops'			=> __( 'Oops… Did something went wrong?', WPML_SLUG ),
						'poster'		=> __( 'Poster', WPML_SLUG ),
						'save_image'		=> __( 'Saving Images…', WPML_SLUG ),
						'search_movie_title'	=> __( 'Searching movie', WPML_SLUG ),
						'search_movie'		=> __( 'Fetching movie data', WPML_SLUG ),
						'see_less'		=> __( 'see no more', WPML_SLUG ),
						'see_more'		=> __( 'see more', WPML_SLUG ),
						'set_featured'		=> __( 'Setting featured image…', WPML_SLUG ),
					)
				);

				if ( 'movie' == $screen->id )
					wp_enqueue_script( 'jquery-effects-shake' );

				wp_enqueue_script( WPML_SLUG . '-admin-script', WPML_URL . '/assets/js/admin.js', array( 'jquery' ), WPML_VERSION, true );
				wp_enqueue_script( WPML_SLUG . '-settings', WPML_URL . '/assets/js/wpml.settings.js', array( 'jquery', 'jquery-ui-sortable' ), WPML_VERSION, true );

				wp_localize_script(
					WPML_SLUG . '-admin-script', 'wpml_ajax',
					$localize
				);
			}

		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                              Settings
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
		public function admin_menu() {

			add_submenu_page(
				'edit.php?post_type=movie',
				__( 'Import Movies', WPML_SLUG ),
				__( 'Import Movies', WPML_SLUG ),
				'manage_options',
				'import',
				'WPML_Import::import_page'
			);
			/*add_submenu_page(
				'edit.php?post_type=movie',
				__( 'Export Movies', WPML_SLUG ),
				__( 'Export Movies', WPML_SLUG ),
				'manage_options',
				'export',
				__CLASS__ . '::export_page'
			);*/
			add_submenu_page(
				'edit.php?post_type=movie',
				__( 'Settings', WPML_SLUG ),
				__( 'Settings', WPML_SLUG ),
				'manage_options',
				'wpml_edit_settings',
				__CLASS__ . '::admin_page'
			);
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

				add_settings_section( "wpml_settings-$section_id", $section_title, __CLASS__ . '::markup_section_headers', 'wpml_settings' );

				foreach ( $section['settings'] as $id => $field ) {

					$callback = isset( $field['callback'] ) ? $field['callback'] : 'markup_fields';

					add_settings_field( $id, __( $field['title'], WPML_SLUG ), array( $this, $callback ), 'wpml_settings', "wpml_settings-$section_id", array( 'id' => $id, 'section' => $section_id ) + $field );
				}
			}

			// The settings container
			register_setting(
				'wpml_edit_settings',
				'wpml_settings',
				array( $this, 'validate_settings' )
			);
		}

		public function validate_settings( $new_settings ) {

			if ( isset( $new_settings['wpml']['default_movie_meta_sorted'] ) && '' != $new_settings['wpml']['default_movie_meta_sorted'] )

			$meta_sorted = explode( ',', $new_settings['wpml']['default_movie_meta_sorted'] );
			$meta = WPML_Settings::get_supported_movie_meta();

			foreach ( $meta_sorted as $i => $_meta )
				if ( ! in_array( $_meta, array_keys( $meta ) ) )
					unset( $meta_sorted[ $i ] );

			$new_settings['wpml']['default_movie_meta_sorted'] = $meta_sorted;
			$new_settings['wpml']['default_movie_meta'] = $meta_sorted;

			return $new_settings;
		}

		/**
		 * Render options page.
		 *
		 * @since    1.0.0
		 */
		public static function admin_page() {

			if ( ! current_user_can( 'manage_options' ) )
				wp_die( __( 'Access denied.', WPML_SLUG ) );

			$_section = ( isset( $_REQUEST['wpml_section'] ) && in_array( $_REQUEST['wpml_section'], array( 'tmdb', 'wpml', 'uninstall', 'restore' ) ) ) ? esc_attr( $_REQUEST['wpml_section'] ) : 'tmdb' ;

			include_once( plugin_dir_path( __FILE__ ) . 'settings/views/page-settings.php' );
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

			$settings = WPML_Settings::get_settings();

			$_type  = esc_attr( $field['type'] );
			$_title = esc_attr( $field['title'] );
			$_id    = "wpml_settings-{$field['section']}-{$field['id']}";
			$_name  = "wpml_settings[{$field['section']}][{$field['id']}]";
			$_value = $settings[ $field['section'] ][ $field['id'] ];

			include( plugin_dir_path( __FILE__ ) . 'settings/views/page-settings-fields.php' );
		}

		/**
		 * Delivers the markup for default_movie_meta settings fields
		 *
		 * @param array $field
		 */
		public function sorted_markup_fields( $field ) {

			$settings = WPML_Settings::get_settings();

			$_type  = 'sorted';
			$_title = esc_attr( $field['title'] );
			$_id    = "wpml_settings-{$field['section']}-{$field['id']}";
			$_name  = "wpml_settings[{$field['section']}][{$field['id']}]";

			if ( 'default_movie_meta' == $field['id'] && isset( $settings['wpml']['default_movie_meta_sorted'] ) )
				$_value = $settings[ $field['section'] ]['default_movie_meta_sorted'];
			else
				$_value = $settings[ $field['section'] ][ $field['id'] ];

			$items      = WPML_Settings::get_supported_movie_meta();
			$selected   = $_value;
			$selectable = array_diff( array_keys( $items ), $selected );
			$selectable = empty( $selectable ) ? array_keys( $items ) : $selectable;

			$draggable = ''; $droppable = ''; $options = '';

			foreach ( $selected as $meta ) :
				if ( isset( $items[ $meta ] ) )
					$draggable .= '<li data-movie-meta="' . $meta . '" class="default_movie_meta_selected">' . __( $items[ $meta ]['title'], WPML_SLUG ) . '</li>';
			endforeach;
			foreach ( $selectable as $meta ) :
				$droppable .= '<li data-movie-meta="' . $meta . '" class="default_movie_meta_droppable">' . __( $items[ $meta ]['title'], WPML_SLUG ) . '</li>';
			endforeach;

			foreach ( $items as $slug => $meta ) :
				$check = in_array( $slug, $_value );
				$options .= '<option value="' . $slug . '"' . selected( $check, true, false ) . '>' . __( $meta['title'], WPML_SLUG ) . '</option>';
			endforeach;

			include( plugin_dir_path( __FILE__ ) . 'settings/views/page-settings-fields.php' );
		}

		/**
		 * Render movie export page
		 *
		 * @since    1.0.0
		 */
		public function export_page() {
			// TODO: implement export
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
		public function init() {

			$this->plugin_screen_hook_suffix = array(
				'movie_page_import', 'movie_page_wpml_edit_settings', 'edit-movie', 'movie', 'plugins'
			);

			self::$default_settings = WPML_Settings::get_default_settings();
			$this->settings         = WPML_Settings::get_settings();

		}

	}
endif;
