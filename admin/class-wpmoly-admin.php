<?php
/**
 * WPMovieLibrary
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 Charlie MERLAND
 */

if ( ! class_exists( 'WPMovieLibrary_Admin' ) ) :

	/**
	* Plugin Admin class.
	*
	* @package WPMovieLibrary_Admin
	* @author  Charlie MERLAND <charlie@caercam.org>
	*/
	class WPMovieLibrary_Admin extends WPMOLY_Module {

		/**
		 * Slug of the plugin screen.
		 *
		 * @since    1.0
		 * @var      array
		 */
		protected $screen_hooks = null;
		protected $hidden_pages = null;

		/**
		 * Plugin Settings.
		 *
		 * @since    1.0
		 * @var      array
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
				'WPMOLY_Dashboard'   => WPMOLY_Dashboard::get_instance(),
				'WPMOLY_Dashboard'   => WPMOLY_Diagnose::get_instance(),
				'WPMOLY_Settings'    => WPMOLY_Settings::get_instance(),
				'WPMOLY_TMDb'        => WPMOLY_TMDb::get_instance(),
				'WPMOLY_Utils'       => WPMOLY_Utils::get_instance(),
				'WPMOLY_Metaboxes'   => WPMOLY_Metaboxes::get_instance(),
				'WPMOLY_Edit_Movies' => WPMOLY_Edit_Movies::get_instance(),
				'WPMOLY_Media'       => WPMOLY_Media::get_instance(),
				'WPMOLY_Import'      => WPMOLY_Import::get_instance(),
				'WPMOLY_Queue'       => WPMOLY_Queue::get_instance()
			);

			$this->screen_hooks = array(
				'edit'     => 'post.php',
				'new'      => 'post-new.php',
				'movie'    => 'movie',
				'movies'   => 'edit.php',
				'widgets'  => 'widgets.php',
				'diagnose' => 'dashboard_page_wpmovielibrary-diagnose',
				'settings' => sprintf( '%s_page_wpmovielibrary-settings', strtolower( __( 'Movies', 'wpmovielibrary' ) ) )
			);

			$this->hidden_pages = array();

		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'about_redirect' ) );

			if ( wpmoly_modern_wp() )
				add_action( 'admin_head', array( $this, 'custom_admin_colors' ) );

			// Add the options page and menu item.
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
			add_action( 'admin_head', array( $this, 'admin_head' ) );

			// highlight the proper top level menu
			add_action( 'parent_file', array( $this, 'admin_menu_highlight' ) );

			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'pre_enqueue_admin_scripts' ), 8 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			add_action( 'in_admin_footer', array( $this, 'legal_mentions' ) );

			add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ), 10, 1 );

			add_filter( 'plugin_action_links_' . WPMOLY_PLUGIN, array( $this, 'plugin_action_links' ), 10, 1 );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		}

		/**
		 * Redirect to About page after install/update
		 *
		 * @since    1.0
		 */
		public function about_redirect() {

			if ( 'no' == get_option( '_wpmoly_fresh_install', 'yes' ) )
				return false;

			update_option( '_wpmoly_fresh_install', 'no' );
			wp_redirect( admin_url( 'index.php?page=wpmovielibrary-about' ) );
			exit;
		}

		/**
		 * Add TMDb legal mention to the plugin pages' admin footer
		 *
		 * @since    1.0
		 */
		public function legal_mentions() {

			$screen = get_current_screen();
			if ( ! in_array( $screen->id, $this->screen_hooks ) )
				return false;

			echo self::render_admin_template( 'admin-footer.php' );
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
		 * Add new links to the Plugins Page
		 *
		 * @since    2.1.1
		 * 
		 * @param    array    $links Current links list
		 * 
		 * @return   array    $links Updated links list
		 */
		public function plugin_action_links( $links ) {

			$new_links = array(
				sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=wpmovielibrary-settings' ), __( 'Settings', 'wpmovielibrary' ) )
			);

			$links = array_merge( $new_links, $links );

			return $links;
		}

		/**
		 * Add new links to the Plugin's row meta list
		 *
		 * @since    2.1.1
		 * 
		 * @param    mixed    $links Plugin Row Meta
		 * @param    mixed    $file  Plugin Base file
		 * 
		 * @return   array    $links Updated links list
		 */
		public function plugin_row_meta( $links, $file ) {

			if ( $file != WPMOLY_PLUGIN )
				return $links;

			$row_meta = array(
				'docs'    => '<a href="' . esc_url( 'http://wpmovielibrary.com/documentation/' ) . '" title="' . esc_attr( __( 'View WPMovieLibrary Documentation', 'wpmovielibrary' ) ) . '">' . __( 'Documentation', 'wpmovielibrary' ) . '</a>',
				'apidocs' => '<a href="' . esc_url( 'https://wordpress.org/support/plugin/wpmovielibrary/' ) . '" title="' . esc_attr( __( 'Visit WPMovieLibrary Support Forum', 'wpmovielibrary' ) ) . '">' . __( 'Support', 'wpmovielibrary' ) . '</a>',
			);

			$links = array_merge( $links, $row_meta );

			return $links;
		}

		/**
		 * Register and enqueue admin-specific style sheet.
		 *
		 * @since    1.0
		 * 
		 * @param    string    $hook_suffix The current admin page.
		 */
		public function enqueue_admin_styles( $hook_suffix ) {

			wp_enqueue_style( WPMOLY_SLUG . '-common', WPMOLY_URL . '/assets/css/admin/wpmoly-common.css', array(), WPMOLY_VERSION );
			wp_enqueue_style( WPMOLY_SLUG . '-font', WPMOLY_URL . '/assets/fonts/wpmovielibrary/style.css', array(), WPMOLY_VERSION );

			$screen = get_current_screen();
			if ( ! in_array( $hook_suffix, $this->screen_hooks ) && ! in_array( $screen->id, $this->screen_hooks ) )
				return;

			$styles = $this->admin_styles( $hook_suffix );
			foreach ( $styles as $slug => $style )
				wp_enqueue_style( WPMOLY_SLUG . '-' . $slug, WPMOLY_URL . $style, array(), WPMOLY_VERSION );

		}

		/**
		 * Register and enqueue global admin JavaScript.
		 * 
		 * @since    2.0
		 */
		public function pre_enqueue_admin_scripts() {

			wp_enqueue_script( WPMOLY_SLUG . '-admin' , WPMOLY_URL . '/assets/js/admin/wpmoly.js', array( 'jquery' ), WPMOLY_VERSION, true );
		}

		/**
		 * Register and enqueue admin-specific JavaScript.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $hook_suffix The current admin page.
		 */
		public function enqueue_admin_scripts( $hook_suffix ) {

			$screen = get_current_screen();
			if ( ! in_array( $hook_suffix, $this->screen_hooks ) && ! in_array( $screen->id, $this->screen_hooks ) )
				return;

			$scripts = $this->admin_scripts( $hook_suffix );
			foreach ( $scripts as $slug => $script )
				wp_enqueue_script( WPMOLY_SLUG . '-' . $slug, WPMOLY_URL . $script[ 0 ], $script[ 1 ], WPMOLY_VERSION, $script[ 2 ] );

			wp_localize_script(
				WPMOLY_SLUG . '-admin', 'wpmoly_lang',
				WPMOLY_L10n::localize_script()
			);
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

			$pages = array( $this->screen_hooks['settings'], $this->screen_hooks['importer'] );
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
		#progress, #queue_progress, #wpmoly-sort-meta_used li, #wpmoly-sort-details_used li, #wpmoly-movie-archives-movies-meta_used li { background: <?php echo $_eq_light_blue ?> !important; }
		#tmdb_images_preview #tmdb_load_images:hover { border-color: <?php echo $_eq_light_blue ?> !important; }
		#wpmoly-sort-meta_used li, #wpmoly-sort-details_available li, #wpmoly-movie-archives-movies-meta_available li { color: <?php echo $_eq_text_color ?> !important; }
		#wpmoly-sort-meta_used li:hover, #wpmoly-sort-details_available li:hover, #wpmoly-movie-archives-movies-meta_available li:hover { color: <?php echo $_eq_hover_color ?> !important; }
		#wpmoly-sort-meta_available li, #wpmoly-sort-details_available li, #wpmoly-movie-archives-movies-meta_available li { background: <?php echo $_eq_dark_grey ?> !important; }
	</style>
<?php
		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                              Settings
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Register the plugin's custom admin menu.
		 * 
		 * Page and subpages are defined in the related config file and
		 * can be extended by filters.
		 * 
		 * @since    1.0
		 */
		public function admin_menu() {

			$admin_menu = WPMOLY_Settings::get_admin_menu();

			add_dashboard_page(
				$page_title = __( 'About WPMovieLibrary', 'wpmovielibrary' ),
				$menu_title = __( 'About WPMovieLibrary', 'wpmovielibrary' ),
				$capability = 'manage_options',
				$menu_slug  = 'wpmovielibrary-about',
				$function   = 'WPMovieLibrary_Admin::about_page'
			);
			remove_submenu_page( 'index.php', 'wpmovielibrary-about' );

			add_dashboard_page(
				$page_title = __( 'WPMovieLibrary Diagnose', 'wpmovielibrary' ),
				$menu_title = __( 'WPMovieLibrary Diagnose', 'wpmovielibrary' ),
				$capability = 'manage_options',
				$menu_slug  = 'wpmovielibrary-diagnose',
				$function   = 'WPMovieLibrary_Admin::diagnose_page'
			);
			remove_submenu_page( 'index.php', 'wpmovielibrary-diagnose' );

			extract( $admin_menu['page'] );

			add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );

			foreach ( $admin_menu['subpages'] as $id => $subpage ) {

				extract( $subpage, EXTR_PREFIX_ALL | EXTR_OVERWRITE, 'subpage' );
				if ( is_null( $subpage_condition ) || ( ! is_null( $subpage_condition ) && false != call_user_func( $subpage_condition ) ) ) {

					$screen_hook = add_submenu_page( $menu_slug, $subpage_page_title, __( $subpage_menu_title, 'wpmovielibrary' ), $subpage_capability, $subpage_menu_slug, $subpage_function );
					$this->screen_hooks[ $id ] = $screen_hook;

					if ( true === $subpage_hide )
						$this->hidden_pages[] = array( 'menu_slug' => $menu_slug, 'subpage_menu_slug' => $subpage_menu_slug );

					if ( ! empty( $subpage_actions ) ) {
						foreach ( $subpage_actions as $hook => $callback ) {
							if ( is_callable( $callback ) ) {
								$hook = str_replace( '{screen_hook}', $screen_hook, $hook );
								add_action( $hook, $callback );
							}
						}
					}
				}
			}
		}

		/**
		 * Remove Admin menu hidden page links.
		 *
		 * @since    2.0
		 */
		public function admin_head() {

			if ( ! is_array( $this->hidden_pages ) || empty( $this->hidden_pages ) )
				return false;

			foreach ( $this->hidden_pages as $page )
				remove_submenu_page( $page['menu_slug'], $page['subpage_menu_slug'] );
		}

		/**
		 * Highlight Admin submenu for related admin pages
		 * 
		 * @link     http://wordpress.org/support/topic/moving-taxonomy-ui-to-another-main-menu#post-2432769
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

			if ( in_array( $current_screen->id, $this->screen_hooks ) )
				return $parent_file = 'wpmovielibrary';

			return $parent_file;
		}

		/**
		 * Simple "About" page based on WordPress about.php page.
		 * 
		 * Used to show some information about latest versions and features
		 * after installation/update.
		 * 
		 * @since    2.0
		 */
		public static function about_page() {

			echo self::render_admin_template( 'about.php' );
		}

		/**
		 * Diagnose page.
		 * 
		 * @since    2.1.4.4
		 */
		public static function diagnose_page() {

			$diagnose = WPMOLY_Diagnose::get_instance();
			$diagnose->run();

			echo self::render_admin_template( 'diagnose.php', array( 'diagnose' => $diagnose ) );
		}

		/**
		 * Define all admin scripts but use only those needed by the
		 * current page.
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $hook_suffix Current page hook name
		 * 
		 * @return   array     Current page's scripts
		 */
		private function admin_scripts( $hook_suffix ) {

			extract( $this->screen_hooks );

			$wpmoly_slug = WPMOLY_SLUG . '-admin';

			$scripts = array();
			$scripts['utils'] = array( '/assets/js/admin/wpmoly-utils.js', array( 'jquery', 'jquery-color' ), true );

			if ( $hook_suffix == $settings )
				$scripts['settings'] = array( '/assets/js/admin/wpmoly-settings.js', array( $wpmoly_slug, 'jquery', 'jquery-ui-sortable' ), true );

			if ( $hook_suffix == $diagnose )
				$scripts['diagnose'] = array( '/assets/js/admin/wpmoly-diagnose.js', array( $wpmoly_slug, 'jquery', 'underscore', 'wp-backbone' ), true );

			if ( $hook_suffix == $importer ) {
				$scripts['jquery-ajax-queue'] = array( '/assets/js/vendor/jquery-ajaxQueue.js', array( 'jquery' ), true );
				$scripts['importer']          = array( '/assets/js/admin/wpmoly-importer-meta.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['importer-movies']   = array( '/assets/js/admin/wpmoly-importer-movies.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['importer-view']     = array( '/assets/js/admin/wpmoly-importer-view.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['queue']             = array( '/assets/js/admin/wpmoly-queue.js', array( $wpmoly_slug, 'jquery' ), true );
			}

			if ( $hook_suffix == $dashboard ) {
				$scripts['dashboard'] = array( '/assets/js/admin/wpmoly-dashboard.js', array( $wpmoly_slug, 'jquery', 'jquery-ui-sortable' ), true );
				$scripts['editor-details'] = array( '/assets/js/admin/wpmoly-editor-details.js', array( $wpmoly_slug, 'jquery' ), true );
			}

			if ( $hook_suffix == $widgets ) {
// 				$scripts['select2-sortable-js'] = array( '/includes/framework/redux/ReduxCore/assets/js/vendor/select2.sortable.min.js', array( 'jquery' ), false );
// 				$scripts['select2-js']          = array( '/includes/framework/redux/ReduxCore/assets/js/vendor/select2/select2.min.js', array( 'jquery', ), false );
// 				$scripts['field-select-js']     = array( '/includes/framework/redux/ReduxCore/inc/fields/select/field_select.min.js', array( 'jquery' ), false );
				$scripts['widget']              = array( '/assets/js/admin/wpmoly-widget.js', array( $wpmoly_slug, 'jquery', 'underscore', 'wp-backbone' ), true );
			}

			if ( $hook_suffix == $edit || $hook_suffix == $new ) {
				$scripts['jquery-ajax-queue'] = array( '/assets/js/vendor/jquery-ajaxQueue.js', array( 'jquery' ), true );
				$scripts['media']             = array( '/assets/js/admin/wpmoly-media.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['editor-details']    = array( '/assets/js/admin/wpmoly-editor-details.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['editor-meta']       = array( '/assets/js/admin/wpmoly-editor-meta.js', array( $wpmoly_slug, 'jquery' ), true );
			}

			if ( $hook_suffix == $movies ) {
				$scripts['movies']            = array( '/assets/js/admin/wpmoly-movies.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['editor-details']    = array( '/assets/js/admin/wpmoly-editor-details.js', array( $wpmoly_slug, 'jquery' ), true );
			}

			if ( $hook_suffix == $update_movies ) {
				$scripts['jquery-ajax-queue'] = array( '/assets/js/vendor/jquery-ajaxQueue.js', array( 'jquery' ), true );
				$scripts['updates'] = array( '/assets/js/admin/wpmoly-updates.js', array( $wpmoly_slug, 'jquery' ), false );
			}

			return $scripts;
		}

		/**
		 * Define all admin styles but use only those needed by the
		 * current page.
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $hook_suffix Current page hook name
		 * 
		 * @return   array     Current page's styles
		 */
		private function admin_styles( $hook_suffix ) {

			extract( $this->screen_hooks );

			$styles = array();
			$styles['admin']  = '/assets/css/admin/wpmoly.css';

			if ( $hook_suffix == $settings ) {
				$styles['flags']    = '/assets/css/public/wpmoly-flags.css';
				$styles['settings'] = '/assets/css/admin/wpmoly-settings.css';
			}

			if ( $hook_suffix == $diagnose )
				$styles['diagnose'] = '/assets/css/admin/wpmoly-diagnose.css';

			if ( $hook_suffix == $importer )
				$styles['importer'] = '/assets/css/admin/wpmoly-importer.css';

			if ( $hook_suffix == $dashboard )
				$styles['dashboard'] = '/assets/css/admin/wpmoly-dashboard.css';

			if ( $hook_suffix == $widgets ) {
				$styles['select2-css'] = '/includes/framework/redux/ReduxCore/assets/js/vendor/select2/select2.css';
				$styles['redux-field-select-css'] = '/includes/framework/redux/ReduxCore/inc/fields/select/field_select.css';
			}

			if ( $hook_suffix == $edit || $hook_suffix == $new ) {
				$styles['movies']  = '/assets/css/admin/wpmoly-edit-movies.css';
				$styles['media']   = '/assets/css/admin/wpmoly-media.css';
			}

			if ( $hook_suffix == $movies )
				$styles['movies'] = '/assets/css/admin/wpmoly-movies.css';

			if ( $hook_suffix == $update_movies )
				$styles['legacy'] = '/assets/css/admin/wpmoly-legacy.css';

			if ( $hook_suffix == $add_custom_pages )
				$styles['legacy'] = '/assets/css/admin/wpmoly-legacy.css';

			return $styles;
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

	}
endif;
