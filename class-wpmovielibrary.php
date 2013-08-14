<?php
/**
 * WPMovieLibrary
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <contact@caercam.org>
 * @license   GPL-3.0+
 * @link      http://www.caercam.org/
 * @copyright 2013 CaerCam.org
 */

/**
 * Plugin class.
 *
 * @package WPMovieLibrary
 * @author  Charlie MERLAND <contact@caercam.org>
 */
class WPMovieLibrary {

	/**
	 * Plugin name
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $plugin_name = 'WPMovieLibrary';

	/**
	 * Plugin version
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $version = '1.0.0';

	/**
	 * Plugin slug
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_slug = 'wpml';

	/**
	 * Plugin URL
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_url = '';

	/**
	 * Plugin URL
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_path = '';

	/**
	 * Plugin Settings var
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_settings = 'wpml_settings';

	/**
	 * Plugin Settings
	 * 
	 * @since    1.0.0
	 * @var      array
	 */
	protected $wpml_settings = null;

	/**
	 * Self.
	 * 
	 * @since    1.0.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * TMDb API.
	 *
	 * @since    1.0.0
	 * @var      object
	 */
	protected $tmdb = null;

	/**
	 * Initialize WPMovieLibrary.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		$this->plugin_url  = plugins_url( $this->plugin_name );
		$this->plugin_path = plugin_dir_path( __FILE__ );

		$this->wpml_settings = array(
			'wpml' => array(
				'name' => $this->plugin_name,
				'url'  => $this->plugin_url,
				'path' => $this->plugin_path,
			),
			'tmdb' => array(
				'settings' => array(
					'APIKey'          => '',
					'lang'            => 'en',
					'scheme'          => 'https',
					'poster_size'     => 'original',
					'poster_featured' => 1,
					'images_size'     => 'original',
					'images_max'      => 12,
				),
				'fields' => array(
					'id'                    => array( 'title' => __( 'TMDb ID', 'wp_movie_library' ) ),
					'imdb_id'               => array( 'title' => __( 'IMDb ID', 'wp_movie_library' ) ),
					'title'                 => array( 'title' => __( 'Title', 'wp_movie_library' ) ),
					'tagline'               => array( 'title' => __( 'Tagline', 'wp_movie_library' ) ),
					'overview'              => array( 'title' => __( 'Overview', 'wp_movie_library' ) ),
					'production_companies'  => array( 'title' => __( 'Production Companies', 'wp_movie_library' ) ),
					'production_countries'  => array( 'title' => __( 'Production Countries', 'wp_movie_library' ) ),
					'spoken_languages'      => array( 'title' => __( 'Language', 'wp_movie_library' ) ),
					'runtime'               => array( 'title' => __( 'Runtime', 'wp_movie_library' ) ),
					'genres'                => array( 'title' => __( 'Genres', 'wp_movie_library' ) ),
					'casts'                 => array( 'title' => __( 'Cast &amp; Crew', 'wp_movie_library' ) ),
					'release_date'          => array( 'title' => __( 'Release Date', 'wp_movie_library' ) ),
					'images'                => array( 'title' => __( 'Movie Images', 'wp_movie_library' ) )
				)
			),
			'meta_data' => array(
				'title'                 => array( 'title' => __( 'Title', 'wp_movie_library' ) ),
				'tagline'               => array( 'title' => __( 'Tagline', 'wp_movie_library' ) ),
				'overview'              => array( 'title' => __( 'Overview', 'wp_movie_library' ) ),
				'director'              => array( 'title' => __( 'Director', 'wp_movie_library' ) ),
				'production'            => array( 'title' => __( 'Production', 'wp_movie_library' ) ),
				'country'               => array( 'title' => __( 'Country', 'wp_movie_library' ) ),
				'language'              => array( 'title' => __( 'Language', 'wp_movie_library' ) ),
				'runtime'               => array( 'title' => __( 'Runtime', 'wp_movie_library' ) ),
				'genres'                => array( 'title' => __( 'Genres', 'wp_movie_library' ) ),
				'cast'                  => array( 'title' => __( 'Cast', 'wp_movie_library' ) ),
				'crew'                  => array( 'title' => __( 'Crew', 'wp_movie_library' ) ),
				'release_date'          => array( 'title' => __( 'Release Date', 'wp_movie_library' ) ),
				'images'                => array( 'title' => __( 'Images', 'wp_movie_library' ), 'type' => 'hidden' )
			),
		);

		$this->wpml_default_settings();

		// Load plugin text domain, movie post type, default config
		add_action( 'init', array( $this, 'wpml_load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'wpml_register_post_type' ) );
		add_action( 'init', array( $this, 'wpml_default_settings' ) );

		// Notice missing API key
		add_action( 'admin_notices', array( $this, 'wpml_activate_notice' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'wpml_admin_menu' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// New Movie metabox
		add_action( 'add_meta_boxes', array( $this, 'wpml_meta_box' ) );

		$this->tmdb = new WPML_TMDb( $this->wpml_o('tmdb-settings') );

		// Movie save
		add_action( 'save_post', array( $this->tmdb, 'wpml_save_tmdb_data' ) );

		// Movie content
		add_filter( 'the_content', array( $this->tmdb, 'wpml_content_filter' ) );

		// register widgets
		// add_action( 'widgets_init', array( $this, 'wpml_widgets' ) );

		// Ajax callbacks
		add_action( 'wp_ajax_tmdb_search', array( $this->tmdb, 'wpml_tmdb_search_callback' ) );
		add_action( 'wp_ajax_nopriv_tmdb_search', array( $this->tmdb, 'wpml_tmdb_search_callback' ) );
		add_action( 'wp_ajax_ajax_tmdb_search', array( $this->tmdb, 'wpml_tmdb_search_callback' ) );

		add_action( 'wp_ajax_tmdb_save_image', array( $this->tmdb, 'wpml_tmdb_save_image_callback' ) );
		add_action( 'wp_ajax_nopriv_tmdb_save_image', array( $this->tmdb, 'wpml_tmdb_save_image_callback' ) );
		add_action( 'wp_ajax_ajax_tmdb_save_image', array( $this->tmdb, 'wpml_tmdb_save_image_callback' ) );

		add_action( 'wp_ajax_tmdb_api_key_check', array( $this->tmdb, 'wpml_tmdb_api_key_check_callback' ) );

		// Define custom functionality. Read more about actions and filters: http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		//add_action( 'TODO', array( $this, 'action_method_name' ) );
		//add_filter( 'TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                      Activation / Desactivation
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Missing API Key notification. Display a message on plugins and
	 * Movie Settings pages reminding to save a valid API Key.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public function wpml_activate_notice( $network_wide ) {
		global $hook_suffix;

		$hooks = array( 'plugins.php', 'movie_page_settings' );

		if ( ! in_array( $hook_suffix, $hooks ) || false !== $this->wpml_get_api_key() )
			return false;

		echo '<div class="updated wpml">';
		echo '<p>';
		_e( 'Congratulation, you successfully installed WPMovieLibrary. You need a valid <acronym title="TheMovieDB">TMDb</acronym> API key to start adding your movies. Go to the <a href="">WPMovieLibrary Settings page</a> to add your API key.', 'wpml' );
		echo '</p>';
		echo '</div>';
	}


	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                  Styles, Scripts, Custom Post Types
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function wpml_load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Load WPML default settings if unexisting.
	 *
	 * @since    1.0.0
	 */
	public function wpml_default_settings( $force = false ) {

		$options = get_option( $this->plugin_settings );
		if ( ( false === $options || ! is_array( $options ) ) || true == $force ) {
			delete_option( $this->plugin_settings );
			add_option( $this->plugin_settings, $this->wpml_settings );
			
		}
	}

	/**
	 * Register a 'movie' custom post type
	 *
	 * @since    1.0.0
	 */
	public function wpml_register_post_type() {

		$labels = array(
			'name'               => __( 'Movies', 'wp_movie_library' ),
			'singular_name'      => __( 'Movie', 'wp_movie_library' ),
			'add_new'            => __( 'Add New', 'wp_movie_library' ),
			'add_new_item'       => __( 'Add New Movie', 'wp_movie_library' ),
			'edit_item'          => __( 'Edit Movie', 'wp_movie_library' ),
			'new_item'           => __( 'New Movie', 'wp_movie_library' ),
			'all_items'          => __( 'All Movies', 'wp_movie_library' ),
			'view_item'          => __( 'View Movie', 'wp_movie_library' ),
			'search_items'       => __( 'Search Movies', 'wp_movie_library' ),
			'not_found'          => __( 'No movies found', 'wp_movie_library' ),
			'not_found_in_trash' => __( 'No movies found in Trash', 'wp_movie_library' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Movies', 'wp_movie_library' )
		);

		$args = array(
			'labels'             => $labels,
			'rewrite'            => array(
				'with_front' => false,
				'slug'       => 'movies'
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'has_archive'        => true,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
			'menu_icon'          => $this->plugin_url . '/assets/movie-icon.png',
			'menu_position'      => 5
		);

		register_post_type( 'movie', $args );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		wp_enqueue_style( 'jquery-ui-progressbar', plugins_url( 'css/jquery-ui-progressbar.min.css', __FILE__ ), array(), $this->version );
		wp_enqueue_style( 'jquery-ui-tabs', plugins_url( 'css/jquery-ui-tabs.min.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-progressbar' );
		wp_enqueue_script( 'jquery-ui-tabs' );

		wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		wp_localize_script( $this->plugin_slug . '-admin-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
	}


	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                             Meta Boxes
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * 
	 * 
	 * @since    1.0.0
	 */
	public function wpml_meta_box() {
		add_meta_box( 'tmdbstuff', __( 'TMDb − The Movie Database', 'wp_movie_library' ), array( $this, 'wpml_meta_box_html' ), 'movie', 'normal', 'high', null );
	}

	/**
	 * 
	 * 
	 * @since    1.0.0
	 */
	public function wpml_meta_box_html( $post, $metabox ) {
		$meta  = get_post_meta( $post->ID, 'wpml_tmdb_data' );
		$value = ( isset( $meta[0] ) && '' != $meta[0] ? $meta[0] : array() );
		include_once( 'views/metabox.php' );
	}


	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                             Admin Pages
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function wpml_admin_menu() {

		add_submenu_page(
			'edit.php?post_type=movie',
			__( 'Options', 'wp_movie_library' ),
			__( 'Options', 'wp_movie_library' ),
			'manage_options',
			'settings',
			array( $this, 'wpml_admin_page' )
		);
		// TODO: implement import/export features
		// add_submenu_page(
		// 	'edit.php?post_type=movie',
		// 	__( 'Import Movies', 'wp_movie_library' ),
		// 	__( 'Import Movies', 'wp_movie_library' ),
		// 	'manage_options',
		// 	'import',
		// 	array( $this, 'wpml_import_page' )
		// );
		// add_submenu_page(
		// 	'edit.php?post_type=movie',
		// 	__( 'Export Movies', 'wp_movie_library' ),
		// 	__( 'Export Movies', 'wp_movie_library' ),
		// 	'manage_options',
		// 	'export',
		// 	array( $this, 'wpml_export_page' )
		// );
	}

	/**
	 * Render options page.
	 *
	 * @since    1.0.0
	 */
	public function wpml_admin_page() {

		if ( isset( $_POST['restore_default'] ) && '' != $_POST['restore_default'] ) {
			$this->wpml_default_settings( true );
			$this->msg_settings = __( 'Default Settings have been restored.', 'wpml' );
		}

		if ( isset( $_POST['submit'] ) && '' != $_POST['submit'] ) {

			$supported = array_keys( $this->wpml_o( 'tmdb-settings' ) );
			foreach ( $_POST as $key => $setting ) {
				if ( in_array( $key, $supported ) ) {
					$this->wpml_o( 'tmdb-settings-'.esc_attr( $key ), esc_attr( $setting ) );
				}
			}

			$this->msg_settings = __( 'Settings saved.', 'wpml' );
		}

		include_once( 'views/admin.php' );
	}

	/**
	 * Render movie import page
	 *
	 * @since    1.0.0
	 */
	public function wpml_import_page() {
		// TODO: implement import
		// include_once( 'views/import.php' );
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

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
	 *        Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}


	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                              Options
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * Get TMDb API if available
	 *
	 * @since    1.0.0
	 */
	public function wpml_get_api_key() {
		$api_key = $this->wpml_o('tmdb-settings-APIKey');
		return ( '' != $api_key ? $api_key : false );
	}

	/**
	 * Built-in option finder/modifier
	 * Default behavior with no empty search and value params results in
	 * returning the complete WPML options' list.
	 * 
	 * If a search query is specified, navigate through the options'
	 * array and return the asked option if existing, empty string if it
	 * doesn't exist.
	 * 
	 * If a replacement value is specified and the search query is valid,
	 * update WPML options with new value.
	 * 
	 * Return can be string, boolean or array. If search, return array or
	 * string depending on search result. If value, return boolean true on
	 *  success, false on failure.
	 * 
	 * @param    string        Search query for the option: 'aaa-bb-c'. Default none.
	 * @param    string        Replacement value for the option. Default none.
	 * 
	 * @return   string/boolean/array        option array of string, boolean on update.
	 *
	 * @since    1.0.0
	 */
	public function wpml_o( $search = '', $value = null ) {

		$options = get_option( $this->plugin_settings, $this->wpml_settings );

		if ( '' != $search && is_null( $value ) ) {
			$s = explode( '-', $search );
			$o = $options;
			while ( count( $s ) ) {
				$k = array_shift( $s );
				if ( isset( $o[ $k ] ) )
					$o = $o[ $k ];
				else
					$o = '';
			}
		}
		else if ( '' != $search && ! is_null( $value ) ) {
			$s = explode( '-', $search );
			$this->wpml_o_( $options, $s, $value );
			$o = update_option( $this->plugin_settings, $options );
		}
		else {
			$o = $options;
		}

		return $o;
	}

	/**
	 * Built-in option modifier
	 * Navigate through WPML options to find a matching option and update
	 * its value.
	 * 
	 * @param    array         Options array passed by reference
	 * @param    string        key list to match the specified option
	 * @param    string        Replacement value for the option. Default none
	 *
	 * @since    1.0.0
	 */
	private function wpml_o_( &$array, $key, $value = '' ) {
		$a = &$array;
		foreach ( $key as $k )
			$a = &$a[ $k ];
		$a = $value;
	}

}



