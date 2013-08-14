<?php

class WPMovieLibrary__ {

	protected $APIKey;

	protected $wpml_options;
	private   $wpml_options_var = 'wpml_options';

	public function __construct() {

		$this->wpml_options = array(
			'default' => array(
				'comments'     => true,
			),
			'general' => array(
				'APIKey'     => '',
			),
			'tmdb_fields' => array(
				'id',
				'imdb_id',
				'title',
				'tagline',
				'overview',
				'production_companies',
				'production_countries',
				'spoken_languages',
				'runtime',
				'genres',
				'casts',
				'release_date',
				'images'
			),
			'meta_data' => array(
				'title'         => array( 'title' => __( 'Title', 'wp_movie_library' ) ),
				'tagline'       => array( 'title' => __( 'Tagline', 'wp_movie_library' ) ),
				'overview'      => array( 'title' => __( 'Overview', 'wp_movie_library' ) ),
				'director'      => array( 'title' => __( 'Director', 'wp_movie_library' ) ),
				'production'    => array( 'title' => __( 'Production', 'wp_movie_library' ) ),
				'country'       => array( 'title' => __( 'Country', 'wp_movie_library' ) ),
				'language'      => array( 'title' => __( 'Language', 'wp_movie_library' ) ),
				'runtime'       => array( 'title' => __( 'Runtime', 'wp_movie_library' ) ),
				'genres'        => array( 'title' => __( 'Genres', 'wp_movie_library' ) ),
				'cast'          => array( 'title' => __( 'Cast', 'wp_movie_library' ) ),
				'crew'          => array( 'title' => __( 'Crew', 'wp_movie_library' ) ),
				'release_date'  => array( 'title' => __( 'Release Date', 'wp_movie_library' ) ),
				'images'        => array( 'title' => __( 'Images', 'wp_movie_library' ), 'type' => 'hidden' )
			),
		);

		# HOOKS AND FILTERS START

		# activation hook
		register_activation_hook( __FILE__, array( $this, 'wpml_plugin' ) );

		# register post type
		add_action( 'init', array( $this, 'wpml_post_type' ) );

		# add menu items
		add_action( 'admin_menu', array( $this, 'wpml_admin_menus' ) );
		
		# meta_boxes
		add_action( 'add_meta_boxes', array( $this, 'wpml_meta_box' ) );

		# save imdb_id on save
		//add_action( 'publish_movie', array( $this, 'wpml_fetch_meta_data' ) );
		add_action( 'save_post', array( $this, 'wpml_save_tmdb_data' ) );
		//add_action( 'transition_post_status', array( $this, 'wpml_autosave_tmdb_data' ), 10, 3 );

		# css
		add_filter( 'wp_head', array( $this, 'wpml_style' ) );

		# admin header
		add_filter( 'admin_head', array( $this, 'wpml_admin_header' ) );

		# the_content filter
		add_filter( 'the_content', array( $this, 'wpml_content_filter' ) );

		# register widgets
		add_action( 'widgets_init', array( $this, 'wpml_widgets' ) );

		# Ajax callbacks
		add_action( 'wp_ajax_tmdb_search', array( $this, 'wpml_tmdb_search_callback' ) );
		add_action( 'wp_ajax_nopriv_tmdb_search', array( $this, 'wpml_tmdb_search_callback' ) );
		add_action( 'wp_ajax_ajax_tmdb_search', array( $this, 'wpml_tmdb_search_callback' ) );
		add_action( 'wp_ajax_tmdb_save_image', array( $this, 'wpml_tmdb_save_image_callback' ) );
		add_action( 'wp_ajax_nopriv_tmdb_save_image', array( $this, 'wpml_tmdb_save_image_callback' ) );
		add_action( 'wp_ajax_ajax_tmdb_save_image', array( $this, 'wpml_tmdb_save_image_callback' ) );
	}

	/**
	 * Activation hook: initialize plugin's default options
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_activate() {
		$this->wpml_set_options( $this->wpml_options['default'] );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	private function wpml_get_options() {
		return get_option( $this->wpml_options_var );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	private function wpml_set_options( $options ) {
		return update_option( $this->wpml_options_var, $options );
	}

	/**
	 * Register WPML custom post type 'Movie'.
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_post_type() {
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
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ), // comments feature depends on plugin options
			'menu_icon'          => WPML_URL . '/resources/images/movie-icon.png',
			'menu_position'      => 5
		);

		$options = $this->wpml_get_options();

		if ( $options['comments'] ) {
			$args['supports'][] = 'comments';
		}

		register_post_type( 'movie', $args );
	}

	/**
	 * Add a submenu pages to Movie menu
	 * Create new entries in Movie menu: Options,
	 * Import Movies.
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_admin_menus() {
		
	}

	/**
	 * WPMovieLibrary Options page.
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_options_page() {

		if ( empty( $_POST ) )
			return false;
		
		$options = array(
			'comments'    => isset( $_POST['comments'] ),
			'api_key'     => $_POST['tmdb_api_key']
		);

		$this->wpml_set_options( $options );
		$this->msg_settings = __( 'Settings saved.', 'wp_movie_library' );

		$options = get_option( $this->wpml_options_var );

		require $this->views_path . '/wpml-options.php';
	}

	/**
	 * WPMovieLibrary Movies Import page.
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_import_movies_page() {
		//require $this->views_path . '/wpml-import-movies.php';
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_meta_box() {
		add_meta_box( 'tmdbstuff', __( 'TMDb − The Movie Database', 'wp_movie_library' ), array( $this, 'wpml_meta_box_html' ), 'movie', 'normal', 'high', null );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_meta_box_html( $post, $metabox ) {

		$meta  = get_post_meta( $post->ID, 'wpml_tmdb_data' );
		$value = ( isset( $meta[0] ) && '' != $meta[0] ? $meta[0] : array() );

		require WPML_VIEW . '/wpml-tmdb-metabox.php';
	}

	public function wpml_save_tmdb_data( $post_id ) {

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return false;

		if ( ! isset( $_POST['tmdb_data'] ) || '' == $_POST['tmdb_data'] )
			return false;

		$post = get_post( $post_id );
		if ( 'movie' != get_post_type( $post ) )
			return false;

		update_post_meta( $post_id, 'wpml_tmdb_data', $_POST['tmdb_data'] );
	}

	public function wpml_save_tmdb_image( $image, $post_id ) {
		//$upload = media_sideload_image( $image, $post_id );
		//return ( is_object( $upload ) ? false : true );
		return 'true';
	}

	public function wpml_tmdb_save_image_callback() {

		$image   = ( isset( $_GET['image'] )   && '' != $_GET['image']   ? $_GET['image']   : '' );
		$post_id = ( isset( $_GET['post_id'] ) && '' != $_GET['post_id'] ? $_GET['post_id'] : '' );

		if ( '' == $image || '' == $post_id )
			return false;

		echo $this->wpml_save_tmdb_image( $image, $post_id );
		die();
	}

	public function wpml_tmdb_search_callback() {

		$type = ( isset( $_GET['type'] ) && '' != $_GET['type'] ? $_GET['type'] : '' );
		$data = ( isset( $_GET['data'] ) && '' != $_GET['data'] ? $_GET['data'] : '' );

		if ( '' == $data || '' == $type )
			return false;

		if ( 'title' == $type )
			$this->wpml_get_movie_by_title( $data );
		else if ( 'id' == $type )
			$this->wpml_get_movie_by_id( $data );

		die();
	}

	private function wpml_get_movie_by_title( $title ) {

		require_once WPML_PATH . 'lib/wpml-tmdb.php';
		$tmdb = new TMDb( $this->APIKey, 'fr', false, 'https://' );

		$data = $tmdb->searchMovie( $title );
		$ret = '';

		if ( $data['total_results'] > 1 ) {
			foreach ( $data['results'] as $movie ) {
				$ret .= '<div class="tmdb_select_movie">';
				$ret .= '<a id="tmdb_'.$movie['id'].'" href="#">';
				if ( $movie['poster_path'] != null )
					$ret .= '<img src="http://d3gtl9l2a4fn1j.cloudfront.net/t/p/w150/'.$movie['poster_path'].'" alt="'.$movie['title'].'" />';
				else
					$ret .= '<img src="'.WPML_URL.'/resources/images/no_poster.png" alt="'.$movie['title'].'" />';
				$ret .= '<em>'.$movie['title'].'</em>';
				$ret .= '<input type=\'hidden\' value=\''.json_encode( $movie ).'\' />';
				$ret .= '</div>';
			}
			echo $ret;
		}
		else {
			$this->wpml_get_movie_by_id( $data['results'][0]['id'] );
		}
	}

	private function wpml_get_movie_by_id( $id ) {
		require_once WPML_PATH . 'lib/wpml-tmdb.php';
		$tmdb   = new TMDb( $this->APIKey, 'fr', false, 'https://' );
		$movie  = $tmdb->getMovie( $id );
		$casts  = $tmdb->getMovieCast( $id );
		$images = $tmdb->getMovieImages( $id, '' );
		$images = ( count( $images ) ? array( 'images' => $images['backdrops'] ) : array() );

		foreach ( $casts['crew'] as $i => $c ) {
			switch ( $c['job'] ) {
				case 'Director':
					$movie['director'][] = $c;
					unset( $casts['crew'][$i] );
					break;
				case 'Author':
					$movie['author'][] = $c;
					unset( $casts['crew'][$i] );
					break;
				case 'Producer':
					$movie['production'][] = $c;
					unset( $casts['crew'][$i] );
					break;
				case 'Director of Photography':
					$movie['photography'][] = $c;
					unset( $casts['crew'][$i] );
					break;
				case 'Original Music Composer':
					$movie['soundtrack'][] = $c;
					unset( $casts['crew'][$i] );
					break;
				default:
					break;
			}
		}

		$movie = array_merge( $movie, $casts, $images );

		header('Content-type: application/json');
		echo json_encode( $movie );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_content_filter($content) {

		$post_type = get_post_type();

		# apply filter only if post type is 'movie'
		if ( $post_type == 'movie' ) {
			global $post;

			# get plugin options to use in the view
			$plugin_options = $this->wpml_get_options();

			$fields_to_display = $plugin_options['fields_to_display'];

			# display excerpt or full text?
			$archive_display_mode = $plugin_options['archive_display_mode'];

			if ( $archive_display_mode == 'excerpt' ) {
				$excerpt_length = (int)$plugin_options['excerpt_length'];
			}

			# load view
			require $this->views_path . '/wpml-content-movie.php';
		} else {
			# if it's not movie type post, don't touch the content
			return $content;
		}
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_style() {
		wp_register_style( 'wp_movie_library', WPML_URL . '/resources/css/style.css', array(), false, 'all' );
		wp_enqueue_style( 'wp_movie_library' );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_admin_header() {

		wp_register_style( 'wpml-admin', WPML_URL . '/resources/css/admin.css', array(), false, 'all' );
		wp_register_style( 'jquery-ui-progressbar', WPML_URL . '/resources/css/jquery-ui-progressbar.min.css', array(), false, 'all' );
		wp_enqueue_style( 'wpml-admin' );
		wp_enqueue_style( 'jquery-ui-progressbar' );

		wp_register_script( 'wpml-ajax', WPML_URL . '/resources/js/application.js', '', false, '' );
		wp_enqueue_script( 'wpml-ajax' );
		wp_localize_script( 'wpml-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-progressbar' );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_widgets() {
		# widgets
		require WPML_PATH . 'lib/wpml-widgets.php';
		register_widget( 'WPMovieLibraryWidgetNewests' );
		register_widget( 'WPMovieLibraryWidgetBests' );

		# slider js and css
	}
}