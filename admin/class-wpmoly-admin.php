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
				'WPMOLY_Settings'    => WPMOLY_Settings::get_instance(),
				'WPMOLY_TMDb'        => WPMOLY_TMDb::get_instance(),
				'WPMOLY_Utils'       => WPMOLY_Utils::get_instance(),
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

			if ( wpmoly_modern_wp() )
				add_action( 'admin_head', array( $this, 'custom_admin_colors' ) );

			// Add the options page and menu item.
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
			add_action( 'admin_head', array( $this, 'admin_head' ) );

			// highlight the proper top level menu
			add_action( 'parent_file', array( $this, 'admin_menu_highlight' ) );

			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			add_action( 'in_admin_footer', array( $this, 'legal_mentions' ) );

			add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ), 10, 1 );
		}

		/**
		 * Add TMDb legal mention to the plugin pages' admin footer
		 *
		 * @since     1.0.0
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
		 * Register and enqueue admin-specific style sheet.
		 *
		 * @since    1.0
		 */
		public function enqueue_admin_styles( $hook ) {

			wp_enqueue_style( WPMOLY_SLUG . '-common', WPMOLY_URL . '/assets/css/admin/wpmoly-common.css', array(), WPMOLY_VERSION );
			wp_enqueue_style( WPMOLY_SLUG . '-font', WPMOLY_URL . '/assets/fonts/wpmovielibrary/style.css', array(), WPMOLY_VERSION );

			$screen = get_current_screen();
			if ( ! in_array( $hook, $this->screen_hooks ) && ! in_array( $screen->id, $this->screen_hooks ) )
				return;

			$styles = $this->admin_styles( $hook );
			foreach ( $styles as $slug => $style )
				wp_enqueue_style( WPMOLY_SLUG . '-' . $slug, WPMOLY_URL . $style, array(), WPMOLY_VERSION );

		}

		/**
		 * Register and enqueue admin-specific JavaScript.
		 * 
		 * @since    1.0
		 */
		public function enqueue_admin_scripts( $hook ) {

			$screen = get_current_screen();
			if ( ! in_array( $hook, $this->screen_hooks ) && ! in_array( $screen->id, $this->screen_hooks ) )
				return;

			$scripts = $this->admin_scripts( $hook );
			foreach ( $scripts as $slug => $script )
				wp_enqueue_script( WPMOLY_SLUG . '-' . $slug, WPMOLY_URL . $script[ 0 ], $script[ 1 ], WPMOLY_VERSION, $script[ 2 ] );

			wp_localize_script(
				WPMOLY_SLUG . '-admin', 'wpmoly_ajax',
				$this->localize_script()
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

			$pages = array( $this->screen_hooks['settings'], $this->screen_hooks['import'] );
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
		#progress, #queue_progress, #wpmoly-sort-meta_used li, #wpmoly-sort-details_used li { background: <?php echo $_eq_light_blue ?> !important; }
		#tmdb_images_preview #tmdb_load_images:hover { border-color: <?php echo $_eq_light_blue ?> !important; }
		#wpmoly-sort-meta_used li, #wpmoly-sort-details_available li { color: <?php echo $_eq_text_color ?> !important; }
		#wpmoly-sort-meta_used li:hover, #wpmoly-sort-details_available li:hover { color: <?php echo $_eq_hover_color ?> !important; }
		#wpmoly-sort-meta_available li, #wpmoly-sort-details_available li { background: <?php echo $_eq_dark_grey ?> !important; }
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

			if ( in_array( $current_screen->id, $this->screen_hooks ) )
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

			/*if ( ! current_user_can( 'manage_options' ) )
				wp_die( __( 'Access denied.', 'wpmovielibrary' ) );

			// Restore default settings?
			if ( isset( $_GET['wpmoly_restore_default'] ) && 'true' == $_GET['wpmoly_restore_default'] ) {

				// Check Nonce URL
				if ( ! isset( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], 'wpmoly-restore-default-settings' ) ) {
					add_settings_error( null, 'restore_default', __( 'You don\'t have the permission do perform this action.', 'wpmovielibrary' ), 'error' );
				}
				else {
					$action = WPMOLY_Settings::update_settings( $force = true );
					if ( ! $action )
						add_settings_error( null, 'restore_default', __( 'Unknown error: failed to restore default settings.', 'wpmovielibrary' ), 'error' );
					else
						add_settings_error( null, 'restore_default', __( 'Default settings restored!', 'wpmovielibrary' ), 'updated' );
				}
			}

			// Empty Cache?
			if ( isset( $_GET['wpmoly_empty_cache'] ) && 'true' == $_GET['wpmoly_empty_cache'] ) {

				// Check Nonce URL
				if ( ! isset( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], 'wpmoly-empty-cache' ) ) {
					add_settings_error( null, 'empty_cache', __( 'You don\'t have the permission do perform this action.', 'wpmovielibrary' ), 'error' );
				}
				else {
					$action = WPMOLY_Cache::empty_cache();
					if ( is_wp_error( $action ) )
						add_settings_error( null, 'empty_cache', $action->get_error_message(), 'error' );
					else
						add_settings_error( null, 'empty_cache', $action, 'updated' );
				}
			}

			$_allowed = array( 'api', 'wpmoly', 'images', 'taxonomies', 'deactivate', 'uninstall', 'cache', 'legacy', 'maintenance' );
			$_section = ( isset( $_GET['wpmoly_section'] ) && in_array( $_GET['wpmoly_section'], $_allowed ) ) ? esc_attr( $_GET['wpmoly_section'] ) : 'api' ;

			$attributes = array(
				'_section' => $_section
			);

			echo self::render_admin_template( 'settings/settings.php', $attributes );*/
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
			$scripts['admin'] = array( '/assets/js/admin/wpmoly.js', array( 'jquery' ), true );
			$scripts['utils'] = array( '/assets/js/admin/wpmoly-utils.js', array( 'jquery',  ), true );

			if ( $hook_suffix == $settings )
				$scripts['settings'] = array( '/assets/js/admin/wpmoly-settings.js', array( $wpmoly_slug, 'jquery', 'jquery-ui-sortable' ), true );

			if ( $hook_suffix == $importer ) {
				$scripts['jquery-ajax-queue'] = array( '/assets/js/vendor/jquery-ajaxQueue.js', array( 'jquery' ), true );
				$scripts['importer']          = array( '/assets/js/admin/wpmoly-importer-meta.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['importer-movies']   = array( '/assets/js/admin/wpmoly-importer-movies.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['importer-view']     = array( '/assets/js/admin/wpmoly-importer-view.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['queue']             = array( '/assets/js/admin/wpmoly-queue.js', array( $wpmoly_slug, 'jquery' ), true );
			}

			if ( $hook_suffix == $dashboard )
				$scripts['dashboard'] = array( '/assets/js/admin/wpmoly-dashboard.js', array( $wpmoly_slug, 'jquery', 'jquery-ui-sortable' ), true );

			if ( $hook_suffix == $widgets )
				$scripts['widget']    = array( '/assets/js/admin/wpmoly-widget.js', array( $wpmoly_slug, 'jquery' ), false );

			if ( $hook_suffix == $edit || $hook_suffix == $new ) {
				$scripts['jquery-ajax-queue'] = array( '/assets/js/vendor/jquery-ajaxQueue.js', array( 'jquery' ), true );
				$scripts['media']             = array( '/assets/js/admin/wpmoly-media.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['editor-details']    = array( '/assets/js/admin/wpmoly-editor-details.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['editor-meta']       = array( '/assets/js/admin/wpmoly-editor-meta.js', array( $wpmoly_slug, 'jquery' ), true );
			}

			if ( $hook_suffix == $movies ) {
				$scripts['movies']            = array( '/assets/js/admin/wpmoly-movies.js', array( $wpmoly_slug, 'jquery' ), true );
				$scripts['editor-details']    = array( '/assets/js/admin/wpmoly-editor-details.js', array( $wpmoly_slug, 'jquery' ), true );
				//$scripts['editor-meta']       = array( '/assets/js/admin/wpmoly-editor-meta.js', array( $wpmoly_slug, 'jquery' ), true );
			}

			if ( $hook_suffix == $update_movies ) {
				$scripts['jquery-ajax-queue'] = array( '/assets/js/vendor/jquery-ajaxQueue.js', array( 'jquery' ), true );
				$scripts['updates'] = array( '/assets/js/admin/wpmoly-updates.js', array( $wpmoly_slug, 'jquery' ), false );
			}

			//$scripts[''] = array( '', array(), true );

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
				$styles['flags']    = '/assets/css/public/flags.css';
				$styles['settings'] = '/assets/css/admin/wpmoly-settings.css';
			}

			if ( $hook_suffix == $importer )
				$styles['importer'] = '/assets/css/admin/wpmoly-importer.css';

			if ( $hook_suffix == $dashboard )
				$styles['dashboard'] = '/assets/css/admin/wpmoly-dashboard.css';

			if ( $hook_suffix == $edit || $hook_suffix == $new ) {
				$styles['movies']  = '/assets/css/admin/wpmoly-edit-movies.css';
				$styles['media']   = '/assets/css/admin/wpmoly-media.css';
			}

			if ( $hook_suffix == $movies )
				$styles['movies'] = '/assets/css/admin/wpmoly-movies.css';

			if ( $hook_suffix == $update_movies )
				$styles['legacy'] = '/assets/css/admin/wpmoly-legacy.css';

			return $styles;
		}

		/**
		 * i18n method for script
		 * 
		 * Adds a translation object to the plugin's JavaScript object
		 * containing localized texts.
		 * 
		 * TODO: move to dedicated lang class?
		 * 
		 * @since    1.0
		 */
		private function localize_script() {

			$localize = array(
				'utils' => array(
					'wpmoly_check' => wp_create_nonce( 'wpmoly-callbacks-nonce' ),
					'language' => wpmoly_o( 'api-language' )
				),
				'lang' => array(
					'available'		=> __( 'Available', 'wpmovielibrary' ),
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
					'media_no_movie'	=> __( 'No movie could be found. You need to select a movie before importing images or posters.', 'wpmovielibrary' ),
					'movie'			=> __( 'Movie', 'wpmovielibrary' ),
					'movie_updated'		=> _n( 'movie updated', 'movies updated', 0, 'wpmovielibrary' ),
					'movies_updated'	=> _n( 'movie updated', 'movies updated', 2, 'wpmovielibrary' ),
					'not_updated'		=> __( 'not updated', 'wpmovielibrary' ),
					'oops'			=> __( 'Oops… Did something went wrong?', 'wpmovielibrary' ),
					'poster'		=> __( 'Poster', 'wpmovielibrary' ),
					'save_image'		=> __( 'Saving Images…', 'wpmovielibrary' ),
					'search_movie_title'	=> __( 'Searching movie', 'wpmovielibrary' ),
					'search_movie'		=> __( 'Fetching movie data', 'wpmovielibrary' ),
					'see_less'		=> __( 'see no more', 'wpmovielibrary' ),
					'see_more'		=> __( 'see more', 'wpmovielibrary' ),
					'selected'		=> _n( 'selected', 'selected', 0, 'wpmovielibrary' ),
					'set_featured'		=> __( 'Setting featured image…', 'wpmovielibrary' ),
					'updated'		=> __( 'updated successfully', 'wpmovielibrary' ),
					'used'			=> __( 'Used', 'wpmovielibrary' ),
					'updating'		=> __( 'updating movies...', 'wpmovielibrary' ),
					'x_selected'		=> _n( 'selected', 'selected', 2, 'wpmovielibrary' ),

				)
			);

			$base_urls = WPMOLY_TMDb::get_image_url();
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
