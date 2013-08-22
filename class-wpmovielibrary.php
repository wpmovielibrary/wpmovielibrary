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
	 * TMDb API.
	 *
	 * @since    1.0.0
	 * @var      array
	 */
	public $wpml_crew = null;

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
				'default_fields' => array(
					'director'     => 'Director',
					'producer'     => 'Producer',
					'photgraphy'   => 'Director of Photography',
					'composer'     => 'Original Music Composer',
					'author'       => 'Author',
					'writer'       => 'Writer',
					'actors'       => 'Actors'
				)
			),
		);

		// Load settings or register new ones
		$this->wpml_default_settings();

		// Basic movie default fields
		$this->wpml_meta = array(
			'title'                => array( 'title' => __( 'Title', 'wpml' ), 'type' => 'text' ),
			'overview'             => array( 'title' => __( 'Overview', 'wpml' ), 'type' => 'textarea' ),
			'production_companies' => array( 'title' => __( 'Production', 'wpml' ), 'type' => 'text' ),
			'production_countries' => array( 'title' => __( 'Country', 'wpml' ), 'type' => 'text' ),
			'spoken_languages'     => array( 'title' => __( 'Languages', 'wpml' ), 'type' => 'text' ),
			'runtime'              => array( 'title' => __( 'Runtime', 'wpml' ), 'type' => 'text' ),
			'genres'               => array( 'title' => __( 'Genres', 'wpml' ), 'type' => 'text' ),
			'release_date'         => array( 'title' => __( 'Release Date', 'wpml' ), 'type' => 'text' )
		);

		$this->wpml_movie_details = array(
			'movie_media'   => array(
				'title' => __( 'Title', 'wpml' ),
				'options' => array(
					'dvd'   => __( 'DVD', 'wpml' ),
					'vod'   => __( 'VOD', 'wpml' ),
					'vhs'   => __( 'VHS', 'wpml' ),
					'other' => __( 'Other', 'wpml' ),
				),
				'default' => array(
					'dvd'   => __( 'DVD', 'wpml' ),
				),
			),
			'movie_status'  => array(
				'title' => __( 'Overview', 'wpml' ),
				'options' => array(
					'available' => __( 'Available', 'wpml' ),
					'loaned'    => __( 'Loaned', 'wpml' ),
					'scheduled' => __( 'Scheduled', 'wpml' ),
				),
				'default' => array(
					'available' => __( 'Available', 'wpml' ),
				)
			)
		);

		// Load TMDb API Class
		$this->tmdb = new WPML_TMDb( $this->wpml_o('tmdb-settings') );

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

		// New Movie metaboxes
		add_action( 'add_meta_boxes', array( $this, 'wpml_metaboxes' ) );

		// Movie save
		add_action( 'save_post', array( $this, 'wpml_save_tmdb_data' ) );

		// Movie content
		// add_filter( 'the_content', array( $this->tmdb, 'wpml_content_filter' ) );

		// register widgets
		// add_action( 'widgets_init', array( $this, 'wpml_widgets' ) );

		// Ajax callbacks
		add_action( 'wp_ajax_wpml_save_details', array( $this, 'wpml_save_details_callback' ) );
		add_action( 'wp_ajax_tmdb_save_image', array( $this, 'wpml_save_image_callback' ) );
		add_action( 'wp_ajax_tmdb_set_featured', array( $this, 'wpml_set_featured_image_callback' ) );

		add_action( 'wp_ajax_tmdb_search', array( $this->tmdb, 'wpml_tmdb_search_callback' ) );
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
			'menu_icon'          => $this->plugin_url . '/assets/icon-movie.png',
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
		wp_localize_script(
			$this->plugin_slug . '-admin-script', 'ajax_object',
			array(
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'images_added'       => __( 'Images uploaded!', 'wpml' ),
				'base_url_small'     => $this->tmdb->wpml_tmdb_get_base_url( 'image', 'small' ),
				'base_url_original'  => $this->tmdb->wpml_tmdb_get_base_url( 'image', 'original' ),
				'search_movie_title' => __( 'Searching movie', 'wpml' ),
				'search_movie'       => __( 'Fetching movie data', 'wpml' ),
				'set_featured'       => __( 'Setting featured image…', 'wpml' ),
				'images_added'       => __( 'Images added!', 'wpml' ),
				'save_image'         => __( 'Saving Images…', 'wpml' ),
				'poster'             => __( 'Poster', 'wpml' ),
				'done'               => __( 'Done!', 'wpml' ),
				'oops'               => __( 'Oops… Did something went wrong?', 'wpml' )
			)
		);
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
	 *                             Methods
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * Set the image as featured image.
	 * 
	 * @param int $image The ID of the image to set as featured
	 * @param int $post_id The post ID the image is to be associated with
	 * 
	 * @return string|WP_Error Populated HTML img tag on success
	 * 
	 * @since   1.0.0
	 */
	private function wpml_set_image_as_featured( $image, $post_id, $title ) {

		$size = $this->wpml_o('tmdb-settings-poster_size');
		$file = $this->tmdb->config['poster_url'][ $size ] . $image;

		$image = $this->wpml_image_upload( $file, $post_id, $title );

		if ( is_object( $image ) )
			return false;
		else
			return $image;
	}

	/**
	 * Media Sideload Image revisited
	 * This is basically an override function for WP media_sideload_image
	 * modified to return the uploaded attachment ID instead of HTML img
	 * tag.
	 * 
	 * @see http://codex.wordpress.org/Function_Reference/media_sideload_image
	 * 
	 * @param string $file The URL of the image to download
	 * @param int $post_id The post ID the media is to be associated with
	 * @param string $title Optional. Title of the image
	 * 
	 * @return string|WP_Error Populated HTML img tag on success
	 * 
	 * @since   1.0.0
	 */
	private function wpml_image_upload( $file, $post_id, $title = null ) {

	        if ( empty( $file ) )
			return false;

		$tmp   = download_url( $file );

		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
		$file_array['name'] = basename( $matches[0] );
		$file_array['tmp_name'] = $tmp;

		if ( is_wp_error( $tmp ) ) {
			@unlink( $file_array['tmp_name'] );
			$file_array['tmp_name'] = '';
		}

		$id = media_handle_sideload( $file_array, $post_id, $title );
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
			return print_r( $id, true );
		}

		return $id;
	}

	public function wpml_import_movie_list() {

		$this->wpml_import_movies();

		$movies = $this->wpml_get_imported_movies();
		$meta   = array_merge( $this->wpml_meta, $this->wpml_o('tmdb-default_fields') );

		$list = new WPML_List_Table( $movies, $meta );
		$list->prepare_items(); 
		$list->display();
	}

	public function wpml_import_movies() {

		if ( ! isset( $_POST['wpml_import_list'] ) || '' == $_POST['wpml_import_list'] )
			return false;

		$movies = explode( ',', $_POST['wpml_import_list'] );
		$movies = array_map( array( $this, 'wpml_prepare_movie_import_title' ), $movies );

		foreach ( $movies as $i => $movie )
			$this->wpml_import_movie( $movie['movietitle'] );

		return true;
	}

	/**
	 * Save a temporary 'movie' post type for submitted title.
	 * 
	 * This is used to save movies submitted from a list before any
	 * alteration is made by user. Posts will be kept as 'import-draft'
	 * for 24 hours and then destroyed on the next plugin init.
	 * 
	 * @param string $title Movie title.
	 * 
	 * @return int Newly created post ID if everything worked, 0 if no post created.
	 *
	 * @since     1.0.0
	 */
	private function wpml_import_movie( $title ) {

		$post_date     = current_time('mysql');
		$post_date     = wp_checkdate( substr( $post_date, 5, 2 ), substr( $post_date, 8, 2 ), substr( $post_date, 0, 4 ), $post_date );
		$post_date_gmt = get_gmt_from_date( $post_date );
		$post_author   = get_current_user_id();
		$post_content  = null;
		$post_title    = apply_filters( 'the_title', $title );

		$pages = get_page_by_title( $post_title, OBJECT, 'movie' );

		if ( ! is_null( $pages ) )
			$_ID = $pages->ID;
		else
			$_ID = '';

		$_post = array(
			'ID'             => $_ID,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_author'    => $post_author,
			'post_content'   => $post_content,
			'post_date'      => $post_date,
			'post_date_gmt'  => $post_date_gmt,
			'post_name'      => sanitize_title( $post_title ),
			'post_status'    => 'import-draft',
			'post_title'     => $post_title,
			'post_type'      => 'movie'
		);

		$id = wp_insert_post( $_post, true );

		if ( is_wp_error( $id ) )
			return $id->get_error_message();
		else
			return $id;
	}

	public function wpml_prepare_movie_import_title( $title ) {
		return array(
			'ID'         => 0,
			'poster'     => '--',
			'movietitle' => $title,
			'director'   => '--',
			'tmdb_id'    => '--'
		);
	}

	public function wpml_get_imported_movies() {

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

		array_unique( $columns );

		return $columns;
	}

	public function wpml_clean_search_title( $query ) {
		$s = trim( $query );
		$s = preg_replace( '/[^\p{L}\p{N}\s]/u', '', $s );
		return $s;
	}

	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                             Callbacks
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * Save movie details: media, status, rating.
	 * 
	 * Although values are submitted as array each value is stored in a
	 * dedicated post meta.
	 *
	 * @since     1.0.0
	 */
	public function wpml_save_details_callback() {

		$post_id      = ( isset( $_POST['post_id'] )      && '' != $_POST['post_id']      ? $_POST['post_id']      : '' );
		$wpml_details = ( isset( $_POST['wpml_details'] ) && '' != $_POST['wpml_details'] ? $_POST['wpml_details'] : '' );

		if ( '' == $post_id || '' == $wpml_details )
			return false;

		$post = get_post( $post_id );
		if ( 'movie' != get_post_type( $post ) )
			return false;

		update_post_meta( $post_id, '_wpml_movie_media', $wpml_details['media'] );
		update_post_meta( $post_id, '_wpml_movie_status', $wpml_details['status'] );
		update_post_meta( $post_id, '_wpml_movie_rating', $wpml_details['rating'] );
	}

	/**
	 * Upload a movie image.
	 * 
	 * Extract params from $_POST values. Image URL and post ID are
	 * required, title is optional. If no title is submitted file's
	 * basename will be used as image name.
	 * 
	 * @param string $image Image url
	 * @param int $post_id ID of the post the image will be attached to
	 * @param string $title Post title to use as image title to avoir crappy TMDb images names.
	 *
	 * @since     1.0.0
	 *
	 * @return    string    Uploaded image ID
	 */
	public function wpml_save_image_callback() {

		$image   = ( isset( $_GET['image'] )   && '' != $_GET['image']   ? $_GET['image']   : '' );
		$post_id = ( isset( $_GET['post_id'] ) && '' != $_GET['post_id'] ? $_GET['post_id'] : '' );
		$title   = ( isset( $_GET['title'] )   && '' != $_GET['title']   ? $_GET['title']   : '' );

		if ( '' == $image || '' == $post_id )
			return false;

		echo $this->wpml_image_upload( $image, $post_id, $title );
		die();
	}

	/**
	 * Upload an image and set it as featured image of the submitted post.
	 * 
	 * Extract params from $_POST values. Image URL and post ID are
	 * required, title is optional. If no title is submitted file's
	 * basename will be used as image name.
	 * 
	 * Return the uploaded image ID to updated featured image preview in
	 * editor.
	 * 
	 * @param string $image Image url
	 * @param int $post_id ID of the post the image will be attached to
	 * @param string $title Post title to use as image title to avoir crappy TMDb images names.
	 *
	 * @since     1.0.0
	 *
	 * @return    string    Uploaded image ID
	 */
	public function wpml_set_featured_image_callback() {

		$image   = ( isset( $_GET['image'] )   && '' != $_GET['image']   ? $_GET['image']   : '' );
		$post_id = ( isset( $_GET['post_id'] ) && '' != $_GET['post_id'] ? $_GET['post_id'] : '' );
		$title   = ( isset( $_GET['title'] )   && '' != $_GET['title']   ? $_GET['title']   : '' );

		if ( '' == $image || '' == $post_id || 1 != $this->wpml_o('tmdb-settings-poster_featured') )
			return false;

		echo $this->wpml_set_image_as_featured( $image, $post_id, $title );
		die();
	}

	/**
	 * Save TMDb fetched data.
	 *
	 * @since     1.0.0
	 */
	public function wpml_save_tmdb_data( $post_id ) {

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return false;

		if ( ! isset( $_POST['tmdb_data'] ) || '' == $_POST['tmdb_data'] )
			return false;

		$post = get_post( $post_id );
		if ( 'movie' != get_post_type( $post ) )
			return false;

		update_post_meta( $post_id, '_wpml_movie_data', $_POST['tmdb_data'] );
	}


	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                             Meta Boxes
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * Register WPML Metaboxes
	 * 
	 * @since    1.0.0
	 */
	public function wpml_metaboxes() {
		add_meta_box( 'tmdbstuff', __( 'TMDb − The Movie Database', 'wpml' ), array( $this, 'wpml_metabox_tmdb' ), 'movie', 'normal', 'high', null );
		add_meta_box( 'wpml', __( 'Movie Library − Details', 'wpml' ), array( $this, 'wpml_metabox_details' ), 'movie', 'side', 'default', null );
	}

	/**
	 * Main Metabox: TMDb API results.
	 * Display a large Metabox below post editor to fetch and edit movie
	 * informations using the TMDb API.
	 * 
	 * @since    1.0.0
	 */
	public function wpml_metabox_tmdb( $post, $metabox ) {

		$meta  = get_post_meta( $post->ID, '_wpml_movie_data' );
		$value = ( isset( $meta[0] ) && '' != $meta[0] ? $meta[0] : array() );

		

		include_once( 'views/metabox-tmdb.php' );
	}

	/**
	 * Left side Metabox: Movie details.
	 * Used to handle Movies-related details.
	 * 
	 * @since    1.0.0
	 */
	public function wpml_metabox_details( $post, $metabox ) {

		$v = get_post_meta( $post->ID, '_wpml_movie_status', true );
		$movie_status = ( isset( $v ) && '' != $v ? $v : key( $this->wpml_movie_details['movie_status']['default'] ) );

		$v = get_post_meta( $post->ID, '_wpml_movie_media', true );
		$movie_media  = ( isset( $v ) && '' != $v ? $v : key( $this->wpml_movie_details['movie_media']['default'] ) );

		$v = get_post_meta( $post->ID, '_wpml_movie_rating', true );
		$movie_rating = ( isset( $v ) && '' != $v ? $v : 0 );

		include_once( 'views/metabox-details.php' );
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
			__( 'Import Movies', 'wpml' ),
			__( 'Import Movies', 'wpml' ),
			'manage_options',
			'import',
			array( $this, 'wpml_import_page' )
		);
		add_submenu_page(
			'edit.php?post_type=movie',
			__( 'Export Movies', 'wpml' ),
			__( 'Export Movies', 'wpml' ),
			'manage_options',
			'export',
			array( $this, 'wpml_export_page' )
		);
		add_submenu_page(
			'edit.php?post_type=movie',
			__( 'Options', 'wpml' ),
			__( 'Options', 'wpml' ),
			'manage_options',
			'settings',
			array( $this, 'wpml_admin_page' )
		);
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
		include_once( 'views/import.php' );
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



