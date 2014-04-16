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
					wp_enqueue_script( 'jquery-ui-tabs' );
				}

				$base_urls = WPML_TMDb::get_base_url();

				$localize = array(
					'utils' => array(
						'wpml_check' => wp_create_nonce( 'wpml-callbacks-nonce' ),
						'base_url' => array(
							'xxsmall'   => $base_urls['poster_url']['xx-small'],
							'xsmall'    => $base_urls['poster_url']['x-small'],
							'small'     => $base_urls['image_url']['small'],
							'medium'    => $base_urls['image_url']['medium'],
							'full'      => $base_urls['image_url']['full'],
							'original'  => $base_urls['image_url']['original'],
						)
					),
					'lang' => array(
						'images_added'       => __( 'Images uploaded!', 'wpml' ),
						'empty_key'          => __( 'I can\'t test an empty key, you know.', 'wpml' ),
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
				__( 'Import Movies', 'wpml' ),
				__( 'Import Movies', 'wpml' ),
				'manage_options',
				'import',
				'WPML_Import::import_page'
			);
			/*add_submenu_page(
				'edit.php?post_type=movie',
				__( 'Export Movies', 'wpml' ),
				__( 'Export Movies', 'wpml' ),
				'manage_options',
				'export',
				__CLASS__ . '::export_page'
			);*/
			add_submenu_page(
				'edit.php?post_type=movie',
				__( 'Options', 'wpml' ),
				__( 'Options', 'wpml' ),
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

					add_settings_field( $id, $field['title'], array( $this, $callback ), 'wpml_settings', "wpml_settings-$section_id", array( 'id' => $id, 'section' => $section_id ) + $field );
				}
			}

			// The settings container
			register_setting(
				'wpml_edit_settings',
				'wpml_settings'
			);
		}

		public function validate_settings( $new_settings ) {

			return $new_settings;
		}

		/**
		 * Render options page.
		 *
		 * @since    1.0.0
		 */
		public static function admin_page() {

			if ( ! current_user_can( 'manage_options' ) )
				wp_die( __( 'Access denied.', 'wpml' ) );

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
			$_value = $settings[ $field['section'] ][ $field['id'] ];

			$items      = WPML_Settings::get_supported_movie_meta();
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
