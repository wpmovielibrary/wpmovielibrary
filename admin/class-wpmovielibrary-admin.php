<?php
/**
 * WPMovieLibrary
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

if ( ! class_exists( 'WPMovieLibrary_Admin' ) ) :

	/**
	* Plugin Admin class.
	*
	* @package WPMovieLibrary_Admin
	* @author  Charlie MERLAND <charlie@caercam.org>
	*/
	class WPMovieLibrary_Admin extends WPML_Module {

		/**
		 * Slug of the plugin screen.
		 *
		 * @since    1.0
		 * @var      string
		 */
		protected $plugin_screen_hook_suffix = null;

		/**
		 * Plugin Settings.
		 *
		 * @since    1.0
		 * @var      string
		 */
		protected $settings;
		protected static $default_settings;

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		protected function __construct() {

			if ( ! is_admin() )
				return false;

			$this->init();
			$this->register_hook_callbacks();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {

			$this->modules = array(
				'WPML_Dashboard'   => WPML_Dashboard::get_instance(),
				'WPML_Settings'    => WPML_Settings::get_instance(),
				'WPML_TMDb'        => WPML_TMDb::get_instance(),
				'WPML_Utils'       => WPML_Utils::get_instance(),
				'WPML_Edit_Movies' => WPML_Edit_Movies::get_instance(),
				'WPML_Media'       => WPML_Media::get_instance(),
				'WPML_Import'      => WPML_Import::get_instance(),
				'WPML_Queue'       => WPML_Queue::get_instance()
			);

			$this->plugin_screen_hook_suffix = array(
				'edit_movie' => 'edit-movie',
				'movie' => 'movie',
				'plugins' => 'plugins',
				'widgets' => 'widgets.php'
			);

			self::$default_settings = WPML_Settings::get_default_settings();
			$this->settings = WPML_Settings::get_settings();

		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			if ( WPML_Utils::is_modern_wp() )
				add_action( 'admin_head', array( $this, 'custom_admin_colors' ) );

			add_filter( 'pre_update_option_wpml_settings', array( $this, 'filter_settings' ), 10, 2 );

			// Add the options page and menu item.
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );

			// highlight the proper top level menu
			add_action( 'parent_file', array( $this, 'admin_menu_highlight' ) );

			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			add_action( 'admin_notices', array( $this, 'api_key_notice' ) );
			add_action( 'admin_notices', array( $this, 'missing_archive_page' ) );

			add_action( 'in_admin_footer', array( $this, 'legal_mentions' ) );

			add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ), 10, 1 );
		}

		/**
		 * Notify the absence of set API key. If no API key is set and
		 * the internal API isn't enabled, show an admin notice on all
		 * the plugin's pages.
		 *
		 * @since     1.0.0
		 */
		public function api_key_notice() {

			$screen = get_current_screen();
			if ( ! in_array( $screen->id, $this->plugin_screen_hook_suffix ) || ( isset( $_GET['hide_wpml_api_key_notice'] ) && '1' == $_GET['hide_wpml_api_key_notice'] ) )
				return false;

			$hide_notice = get_option( 'wpml_api_key_notice_hide', '0' );
			$hide_notice = ( '1' == $hide_notice ? true : false );

			if ( false === $hide_notice && '' == WPML_Settings::tmdb__apikey() && false === WPML_Settings::tmdb__internal_api() )
				echo self::render_template( 'admin-notice.php', array( 'notice' => 'api-key-error' ) );

			return true;
		}

		/**
		 * Update API key notice visibility option
		 *
		 * @since     1.0.0
		 */
		public static function show_api_key_notice() {

			$hide_notice = ( '1' == $_GET['hide_wpml_api_key_notice'] ? 1 : 0 );
			update_option( 'wpml_api_key_notice_hide', $hide_notice );
		}

		/**
		 * Notify the absence of set API key. If no API key is set and
		 * the internal API isn't enabled, show an admin notice on all
		 * the plugin's pages.
		 *
		 * @since     1.0.0
		 */
		public function missing_archive_page() {

			$screen = get_current_screen();
			if ( ! in_array( $screen->id, $this->plugin_screen_hook_suffix ) || ( isset( $_GET['wpml_set_archive_page'] ) && '1' == $_GET['wpml_set_archive_page'] ) )
				return false;

			$page = get_page_by_title( 'WPMovieLibrary Archives', OBJECT, 'wpml_page' );

			if ( ! is_null( $page ) )
				return false;

			echo self::render_template( 'admin-notice.php', array( 'notice' => 'missing-archive' ) );
		}

		/**
		 * Add TMDb legal mention to the plugin pages' admin footer
		 *
		 * @since     1.0.0
		 */
		public function legal_mentions() {

			$screen = get_current_screen();
			if ( ! in_array( $screen->id, $this->plugin_screen_hook_suffix ) )
				return false;

			echo self::render_template( 'admin-footer.php' );
		}

		/**
		 * Add a new item to the Right Now Dashboard Widget
		 *
		 * @since    1.0.1
		 * 
		 * @param    array    Additional items
		 * 
		 * @return   array    Additional items
		 */
		public function dashboard_glance_items( $items = array() ) {

			$movies = wp_count_posts( 'movie' );
			$items[] = sprintf( '<a class="movie-count" href="%s">%s</a>', admin_url( '/edit.php?post_type=movie' ), sprintf( _n( '%d movie', '%d movies', $movies->publish, 'wpmovielibrary' ), $movies->publish ) );

			return $items;
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

			if ( ! WPML_Utils::is_modern_wp() )
				wp_enqueue_style( WPML_SLUG . '-legacy', WPML_URL . '/assets/css/legacy.css', array(), WPML_VERSION );

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
		public function enqueue_admin_scripts( $hook ) {

			$screen = get_current_screen();
			if ( ! in_array( $hook, $this->plugin_screen_hook_suffix ) && ! in_array( $screen->id, $this->plugin_screen_hook_suffix ) )
				return;

			// Main admin script, containing basic functions
			wp_enqueue_script( WPML_SLUG, WPML_URL . '/assets/js/wpml.js', array( 'jquery' ), WPML_VERSION, true );
			wp_enqueue_script( WPML_SLUG . '-utils', WPML_URL . '/assets/js/wpml.utils.js', array( 'jquery', WPML_SLUG ), WPML_VERSION, true );
			wp_localize_script(
				WPML_SLUG, 'wpml_ajax',
				$this->localize_script()
			);

			// Settings script
			if ( $hook == $this->plugin_screen_hook_suffix['settings'] || $hook == $this->plugin_screen_hook_suffix['import'] )
				wp_enqueue_script( WPML_SLUG . '-settings', WPML_URL . '/assets/js/wpml.settings.js', array( WPML_SLUG, 'jquery', 'jquery-ui-sortable' ), WPML_VERSION, true );

			if ( $hook == $this->plugin_screen_hook_suffix['dashboard'] )
				wp_enqueue_script( WPML_SLUG . '-dashboard', WPML_URL . '/assets/js/wpml.dashboard.js', array( WPML_SLUG, 'jquery', 'jquery-ui-sortable' ), WPML_VERSION, true );

			if ( 'widgets.php' == $hook )
				wp_enqueue_script( WPML_SLUG . '-widget', WPML_URL . '/assets/js/wpml.widget.js', array( WPML_SLUG, 'jquery' ), WPML_VERSION, false );

		}

		/**
		 * i18n method for script
		 * 
		 * Adds a translation object to the plugin's JavaScript object
		 * containing localized texts.
		 * 
		 * @since    1.0
		 */
		private function localize_script() {

			$localize = array(
				'utils' => array(
					'wpml_check' => wp_create_nonce( 'wpml-callbacks-nonce' ),
					'language' => WPML_Settings::tmdb__lang()
				),
				'lang' => array(
					'deleted_movie'		=> __( 'One movie successfully deleted.', 'wpmovielibrary' ),
					'deleted_movies'	=> __( '%s movies successfully deleted.', 'wpmovielibrary' ),
					'dequeued_movie'	=> __( 'One movie removed from the queue.', 'wpmovielibrary' ),
					'dequeued_movies'	=> __( '%s movies removed from the queue.', 'wpmovielibrary' ),
					'done'			=> __( 'Done!', 'wpmovielibrary' ),
					'empty_key'		=> __( 'I can\'t test an empty key, you know.', 'wpmovielibrary' ),
					'enqueued_movie'	=> __( 'One movie added to the queue.', 'wpmovielibrary' ),
					'enqueued_movies'	=> __( '%s movies added to the queue.', 'wpmovielibrary' ),
					'images_added'		=> __( 'Images added!', 'wpmovielibrary' ),
					'image_from'		=> __( 'Image from', 'wpmovielibrary' ),
					'images_uploaded'	=> __( 'Images uploaded!', 'wpmovielibrary' ),
					'import_images'		=> __( 'Import Images', 'wpmovielibrary' ),
					'import_images_title'	=> __( 'Import Images for "%s"', 'wpmovielibrary' ),
					'import_images_wait'	=> __( 'Please wait while the images are uploaded...', 'wpmovielibrary' ),
					'import_poster'		=> __( 'Import Poster', 'wpmovielibrary' ),
					'import_poster_title'	=> __( 'Select a poster for "%s"', 'wpmovielibrary' ),
					'import_poster_wait'	=> __( 'Please wait while the poster is uploaded...', 'wpmovielibrary' ),
					'imported'		=> __( 'Imported', 'wpmovielibrary' ),
					'imported_movie'	=> __( 'One movie successfully imported!', 'wpmovielibrary' ),
					'imported_movies'	=> __( '%s movies successfully imported!', 'wpmovielibrary' ),
					'in_progress'		=> __( 'Progressing', 'wpmovielibrary' ),
					'length_key'		=> __( 'Invalid key: it should be 32 characters long.', 'wpmovielibrary' ),
					'load_images'		=> __( 'Load Images', 'wpmovielibrary' ),
					'load_more'		=> __( 'Load More', 'wpmovielibrary' ),
					'loading_images'	=> __( 'Loading Images…', 'wpmovielibrary' ),
					'oops'			=> __( 'Oops… Did something went wrong?', 'wpmovielibrary' ),
					'poster'		=> __( 'Poster', 'wpmovielibrary' ),
					'save_image'		=> __( 'Saving Images…', 'wpmovielibrary' ),
					'search_movie_title'	=> __( 'Searching movie', 'wpmovielibrary' ),
					'search_movie'		=> __( 'Fetching movie data', 'wpmovielibrary' ),
					'see_less'		=> __( 'see no more', 'wpmovielibrary' ),
					'see_more'		=> __( 'see more', 'wpmovielibrary' ),
					'set_featured'		=> __( 'Setting featured image…', 'wpmovielibrary' ),
				)
			);

			$base_urls = WPML_TMDb::get_image_url();
			if ( is_wp_error( $base_urls ) ) {
				$localize['base_urls'] = array(
					'xxsmall' => null, 'xsmall' => null, 'small' => null, 'medium' => null, 'full' => null, 'original' => null
				);
			}
			else {
				$localize['base_urls'] = array(
					'xxsmall'	=> $base_urls['poster']['xx-small'],
					'xsmall'	=> $base_urls['poster']['x-small'],
					'small'		=> $base_urls['backdrop']['small'],
					'medium'	=> $base_urls['backdrop']['medium'],
					'full'		=> $base_urls['backdrop']['full'],
					'original'	=> $base_urls['backdrop']['original'],
				);
			}

			return $localize;
		}

		/**
		 * Adapt Plugin Import and Settings pages to match the current
		 * user's dashboard color set. This is only available in the
		 * new WP3.8 Dashboard.
		 * 
		 * @since    1.0
		 */
		public function custom_admin_colors() {

			global $current_screen, $_wp_admin_css_colors;

			$pages = array( $this->plugin_screen_hook_suffix['settings'], $this->plugin_screen_hook_suffix['import'] );
			$color = get_user_meta( get_current_user_id(), 'admin_color', true );

			if ( ! in_array( $current_screen->id, $pages ) || '' == $color || ! isset( $_wp_admin_css_colors[ $color ] ) || empty( $_wp_admin_css_colors[ $color ] ) )
				return false;

			$colors = $_wp_admin_css_colors[ $color ];
			$_eq_dark_grey  = $colors->colors[ 0 ];
			$_eq_light_grey = $colors->colors[ 1 ];
			$_eq_dark_blue  = $colors->colors[ 2 ];
			$_eq_light_blue = $colors->colors[ 3 ];
			$_eq_text_color = $colors->icon_colors['base'];
			$_eq_hover_color = $colors->icon_colors['focus'];
?>
	<style>
		.label_off.active, .label_on.active, .wpml-tabs-nav > li:hover h4 span, #progress, #wpml-tabs .default_movie_details .default_movie_detail.selected, #wpml-tabs .default_movie_meta_sortable .default_movie_meta_selected, #queue_progress { background-color: <?php echo $_eq_light_blue ?> !important; }
		#tmdb_images_preview #tmdb_load_images:hover { border-color: <?php echo $_eq_light_blue ?>; }
		.wpml-tabs-nav > li > a { color: <?php echo $_eq_text_color ?>; }
		.wpml-tabs-nav > li:hover a { color: <?php echo $_eq_hover_color ?>; }
		.label_off, .label_on, .wpml-tabs-nav > li:hover { background-color: <?php echo $_eq_dark_grey ?>; }
		.wpml-tabs-nav { background-color: <?php echo $_eq_light_grey ?>; }
		.wpml-tabs-nav > li.active a, .wpml-tabs-nav > li.active:hover a { background-color: <?php echo $_eq_dark_blue ?>; }
	</style>
<?php
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
		 * @since    1.0
		 */
		public function admin_menu() {

			add_menu_page(
				$page_title = WPML_NAME,
				$menu_title = __( 'Movies', 'wpmovielibrary' ),
				$capability = 'manage_options',
				$menu_slug = 'wpmovielibrary',
				$function = null,
				$icon_url = ( WPML_Utils::is_modern_wp() ? 'dashicons-format-video' : WPML_URL . '/assets/img/legacy/icon-movie.png' ),
				$position = 6
			);

			$this->plugin_screen_hook_suffix['dashboard'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Your library', 'wpmovielibrary' ),
				__( 'Your library', 'wpmovielibrary' ),
				'manage_options',
				'wpmovielibrary',
				'WPML_Dashboard::dashboard'
			);

			$this->plugin_screen_hook_suffix['all_movies'] = add_submenu_page(
				'wpmovielibrary',
				__( 'All Movies', 'wpmovielibrary' ),
				__( 'All Movies', 'wpmovielibrary' ),
				'manage_options',
				'edit.php?post_type=movie',
				null
			);

			$this->plugin_screen_hook_suffix['new_movie'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Add New', 'wpmovielibrary' ),
				__( 'Add New', 'wpmovielibrary' ),
				'manage_options',
				'post-new.php?post_type=movie',
				null
			);

			if ( WPML_Settings::taxonomies__enable_collection() ) :
			$this->plugin_screen_hook_suffix['collections'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Collections', 'wpmovielibrary' ),
				__( 'Collections', 'wpmovielibrary' ),
				'manage_options',
				'edit-tags.php?taxonomy=collection&post_type=movie',
				null
			);
			endif;

			if ( WPML_Settings::taxonomies__enable_genre() ) :
			$this->plugin_screen_hook_suffix['genres'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Genres', 'wpmovielibrary' ),
				__( 'Genres', 'wpmovielibrary' ),
				'manage_options',
				'edit-tags.php?taxonomy=genre&post_type=movie',
				null
			);
			endif;

			if ( WPML_Settings::taxonomies__enable_actor() ) :
			$this->plugin_screen_hook_suffix['actors'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Actors', 'wpmovielibrary' ),
				__( 'Actors', 'wpmovielibrary' ),
				'manage_options',
				'edit-tags.php?taxonomy=actor&post_type=movie',
				null
			);
			endif;

			$this->plugin_screen_hook_suffix['import'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Import Movies', 'wpmovielibrary' ),
				__( 'Import Movies', 'wpmovielibrary' ),
				'manage_options',
				'wpml_import',
				'WPML_Import::import_page'
			);
			$this->plugin_screen_hook_suffix['settings'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Settings', 'wpmovielibrary' ),
				__( 'Settings', 'wpmovielibrary' ),
				'manage_options',
				'wpml_edit_settings',
				__CLASS__ . '::admin_page'
			);

			add_action( 'load-' . $this->plugin_screen_hook_suffix['import'], 'WPML_Import::import_movie_list_add_options' );
		}

		/**
		 * Highlight Admin submenu for related admin pages
		 * 
		 * @link    http://wordpress.org/support/topic/moving-taxonomy-ui-to-another-main-menu#post-2432769
		 * 
		 * @since    1.0
		 * 
		 * @return   string    Updated parent if needed, current else
		 */
		public function admin_menu_highlight() {

			global $current_screen, $submenu_file, $submenu, $parent_file;

			if ( isset( $submenu['wpmovielibrary'] ) )
				foreach ( $submenu['wpmovielibrary'] as $item )
					if ( htmlspecialchars_decode( $submenu_file ) == $item[ 2 ] )
						$submenu_file = htmlspecialchars_decode( $submenu_file );

			if ( 'movie' != $current_screen->post_type )
				return $parent_file;

			if ( in_array( $current_screen->taxonomy, array( 'collection', 'genre', 'actor' ) ) )
				return $parent_file = 'wpmovielibrary';

			if ( in_array( $current_screen->id, $this->plugin_screen_hook_suffix ) )
				return $parent_file = 'wpmovielibrary';

			return $parent_file;
		}

		/**
		 * Render the Settings Page.
		 * 
		 * Is either one of the maintenance tools is at use, handle it
		 * before doing anything. As of now maintenance tools are 
		 * restricted to default settings restoration and cache cleaning.
		 *
		 * @since    1.0
		 */
		public static function admin_page() {

			if ( ! current_user_can( 'manage_options' ) )
				wp_die( __( 'Access denied.', 'wpmovielibrary' ) );

			// Restore default settings?
			if ( isset( $_GET['wpml_restore_default'] ) && 'true' == $_GET['wpml_restore_default'] ) {

				// Check Nonce URL
				if ( ! isset( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], 'wpml-restore-default-settings' ) ) {
					add_settings_error( null, 'restore_default', __( 'You don\'t have the permission do perform this action.', 'wpmovielibrary' ), 'error' );
				}
				else {
					$action = WPML_Settings::update_settings( $force = true );
					if ( ! $action )
						add_settings_error( null, 'restore_default', __( 'Unknown error: failed to restore default settings.', 'wpmovielibrary' ), 'error' );
					else
						add_settings_error( null, 'restore_default', __( 'Default settings restored!', 'wpmovielibrary' ), 'updated' );
				}
			}

			// Empty Cache?
			if ( isset( $_GET['wpml_empty_cache'] ) && 'true' == $_GET['wpml_empty_cache'] ) {

				// Check Nonce URL
				if ( ! isset( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], 'wpml-empty-cache' ) ) {
					add_settings_error( null, 'empty_cache', __( 'You don\'t have the permission do perform this action.', 'wpmovielibrary' ), 'error' );
				}
				else {
					$action = WPML_Cache::empty_cache();
					if ( is_wp_error( $action ) )
						add_settings_error( null, 'empty_cache', $action->get_error_message(), 'error' );
					else
						add_settings_error( null, 'empty_cache', $action, 'updated' );
				}
			}

			$_allowed = array( 'api', 'wpml', 'images', 'taxonomies', 'deactivate', 'uninstall', 'cache', 'maintenance' );
			$_section = ( isset( $_GET['wpml_section'] ) && in_array( $_GET['wpml_section'], $_allowed ) ) ? esc_attr( $_GET['wpml_section'] ) : 'api' ;

			$attributes = array(
				'_section' => $_section
			);

			echo self::render_template( 'settings/settings.php', $attributes );
		}

		/**
		 * Registers settings sections, fields and settings
		 *
		 * @since    1.0
		 */
		public function register_settings() {

			global $wpml_settings;

			foreach ( $wpml_settings as $section ) {

				if ( isset( $section['section'] ) && isset( $section['settings'] ) ) {

					$section_id = $section['section']['id'];
					$section_title = $section['section']['title'];

					add_settings_section( "wpml_settings-$section_id", $section_title, __CLASS__ . '::markup_section_headers', 'wpml_settings' );

					foreach ( $section['settings'] as $id => $field ) {

						$callback = isset( $field['callback'] ) ? $field['callback'] : 'markup_fields';

						add_settings_field( $id, __( $field['title'], 'wpmovielibrary' ), array( $this, $callback ), 'wpml_settings', "wpml_settings-$section_id", array( 'id' => $id, 'section' => $section_id ) + $field );
					}
				}
			}

			// The settings container
			register_setting(
				'wpml_edit_settings',
				'wpml_settings'
			);
		}

		/**
		 * Adds the section introduction text to the Settings page
		 *
		 * @mvc Controller
		 *
		 * @param array $section
		 */
		public static function markup_section_headers( $section ) {
			echo self::render_template( 'settings/section-headers.php', array( 'section' => $section ) );
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
			$attributes = array();

			$attributes['_type']  = esc_attr( $field['type'] );
			$attributes['_title'] = esc_attr( $field['title'] );
			$attributes['_id']    = "wpml_settings-{$field['section']}-{$field['id']}";
			$attributes['_name']  = "wpml_settings[{$field['section']}][{$field['id']}]";
			$attributes['_value'] = $settings[ $field['section'] ][ $field['id'] ];
			$attributes['settings'] = $settings;
			$attributes['field'] = $field;

			echo self::render_template( 'settings/fields.php', $attributes, $require = 'always' );
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
					$draggable .= '<li data-movie-meta="' . $meta . '" class="default_movie_meta_selected">' . __( $items[ $meta ]['title'], 'wpmovielibrary' ) . '</li>';
			endforeach;
			foreach ( $selectable as $meta ) :
				$droppable .= '<li data-movie-meta="' . $meta . '" class="default_movie_meta_droppable">' . __( $items[ $meta ]['title'], 'wpmovielibrary' ) . '</li>';
			endforeach;

			foreach ( $items as $slug => $meta ) :
				$check = in_array( $slug, $_value );
				$options .= '<option value="' . $slug . '"' . selected( $check, true, false ) . '>' . __( $meta['title'], 'wpmovielibrary' ) . '</option>';
			endforeach;

			$attributes = array(
				'_type' => $_type,
				'_id' => $_id,
				'_name' => $_name,
				'_title' => $_title,
				'_value' => $_value,
				'field' => $field,
				'draggable' => $draggable,
				'droppable' => $droppable,
				'selected' => $selected,
				'options' => $options,
				'items' => $items
			);

			echo self::render_template( 'settings/fields.php', $attributes, $require = 'always' );
		}

		/**
		 * Validate the submitted Settings
		 * 
		 * This essentially checks for sorted movie meta, as this option
		 * is more a visual stuff an as such, not stored in a regular
		 * setting field.
		 * 
		 * Also check for changes on the URL Rewriting of Taxonomies to
		 * update the Rewrite Rules if needed. We need to do so to avoid
		 * users to get 404 when they try to access their content if they
		 * didn't previously reload the Dashboard Permalink page.
		 * 
		 * @since    1.0
		 * 
		 * @param    array    $new_settings Array containing the new settings
		 * @param    array    $old_settings Array containing the old settings
		 * 
		 * @return   array    Validated settings
		 */
		public static function filter_settings( $new_settings, $old_settings ) {

			$settings = WPML_Settings::validate_settings( $new_settings );
			$settings[ WPML_SETTINGS_REVISION_NAME ] = WPML_SETTINGS_REVISION;

			if ( isset( $new_settings['wpml']['default_movie_meta_sorted'] ) && '' != $new_settings['wpml']['default_movie_meta_sorted'] ) {

				$meta_sorted = explode( ',', $new_settings['wpml']['default_movie_meta_sorted'] );
				$meta = WPML_Settings::get_supported_movie_meta();

				foreach ( $meta_sorted as $i => $_meta )
					if ( ! in_array( $_meta, array_keys( $meta ) ) )
						unset( $meta_sorted[ $i ] );

				$settings['wpml']['default_movie_meta_sorted'] = $meta_sorted;
				$settings['wpml']['default_movie_meta'] = $meta_sorted;
			}

			// Check for changes in URL Rewrite
			$updated_movie_rewrite = ( isset( $old_settings['wpml']['movie_rewrite'] ) &&
						   isset( $settings['wpml']['movie_rewrite'] ) &&
						   $old_settings['wpml']['movie_rewrite'] != $settings['wpml']['movie_rewrite'] );

			$updated_details_rewrite = ( isset( $old_settings['wpml']['details_rewrite'] ) &&
						   isset( $settings['wpml']['details_rewrite'] ) &&
						   $old_settings['wpml']['details_rewrite'] != $settings['wpml']['details_rewrite'] );

			$updated_collection_rewrite = ( isset( $old_settings['taxonomies']['collection_rewrite'] ) &&
							isset( $settings['taxonomies']['collection_rewrite'] ) &&
							$old_settings['taxonomies']['collection_rewrite'] != $settings['taxonomies']['collection_rewrite'] );

			$updated_genre_rewrite = ( isset( $old_settings['taxonomies']['genre_rewrite'] ) &&
						   isset( $settings['taxonomies']['genre_rewrite'] ) &&
						   $old_settings['taxonomies']['genre_rewrite'] != $settings['taxonomies']['genre_rewrite'] );

			$updated_actor_rewrite = ( isset( $old_settings['taxonomies']['actor_rewrite'] ) &&
						   isset( $settings['taxonomies']['actor_rewrite'] ) &&
						   $old_settings['taxonomies']['actor_rewrite'] != $settings['taxonomies']['actor_rewrite'] );

			// Update Rewrite Rules if needed
			if ( $updated_movie_rewrite || $updated_details_rewrite || $updated_collection_rewrite || $updated_genre_rewrite || $updated_actor_rewrite )
				add_settings_error( null, 'url_rewrite', sprintf( __( 'You update the taxonomies URL rewrite. You should visit <a href="%s">WordPress Permalink</a> page to update the Rewrite rules; you may experience errors when trying to load pages using the new URL if the structures are not update correctly. Tip: you don\'t need to change anything in the Permalink page: simply loading it will update the rules.', 'wpmovielibrary' ), admin_url( '/options-permalink.php' ) ), 'updated' );

			return $settings;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {}

	}
endif;
