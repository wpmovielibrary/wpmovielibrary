<?php
/*
Plugin Name: WordPress Movie Library
Plugin URI: http://www.caercam.org/wp-movie-library
Description: A movie library plugin.
Version: 1.0
Author: Charlie MERLAND
Author URI: http://www.caercam.org
License: GPL3
*/

/*  Copyright 2013  Charlie MERLAND  (email : contact@caercam.org)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 3, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class WPMovieLibrary {

	protected $APIKey = '';

	protected $plugin_version = "1.0";
	protected $plugin_url;
	protected $plugin_path;
	protected $views_path;

	protected $tmdb_fields;
	protected $meta_data;
	private   $plugin_options_var = 'wp_movie_library_options';

	public function __construct() {

		# set class variables
		$this->plugin_url  = plugins_url( 'WP-Movie-Library' );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->views_path   = $this->plugin_path . 'views';

		# fields to be fetched from imdb
		$this->tmdb_fields = array(
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
			'release_date'
		);

		$this->meta_data = array(
			'title'         => __( 'Title', 'wp_movie_library' ),
			'tagline'       => __( 'Tagline', 'wp_movie_library' ),
			'overview'      => __( 'Overview', 'wp_movie_library' ),
			'director'      => __( 'Director', 'wp_movie_library' ),
			'production'    => __( 'Production', 'wp_movie_library' ),
			'country'       => __( 'Country', 'wp_movie_library' ),
			'language'      => __( 'Language', 'wp_movie_library' ),
			'runtime'       => __( 'Runtime', 'wp_movie_library' ),
			'genres'        => __( 'Genres', 'wp_movie_library' ),
			'cast'          => __( 'Cast', 'wp_movie_library' ),
			'crew'          => __( 'Crew', 'wp_movie_library' ),
			'release_date'  => __( 'Release Date', 'wp_movie_library' )
		);

		# HOOKS AND FILTERS START

		# activation hook
		register_activation_hook( __FILE__, array( $this, 'wpml_activate_plugin' ) );

		# register post type
		add_action( 'init', array( $this, 'wpml_post_type' ) );

		# add menu items
		add_action( 'admin_menu', array($this, 'wpml_admin_menus' ) );
		
		# meta_boxes
		add_action( 'add_meta_boxes', array($this, 'wpml_meta_box' ) );

		# save imdb_id on save
		//add_action( 'publish_movie', array($this, 'wpml_fetch_meta_data' ) );
		add_action( 'save_post', array($this, 'wpml_save_tmdb_data' ) );

		# css
		add_filter( 'wp_head', array($this, 'wpml_style' ) );

		# admin header
		add_filter( 'admin_head', array($this, 'wpml_admin_header' ) );

		# the_content filter
		add_filter( 'the_content', array($this, 'wpml_content_filter' ) );

		# register widgets
		add_action( 'widgets_init', array($this, 'wpml_widgets' ) );

		add_action( 'wp_ajax_tmdb_search', array($this, 'wpml_tmdb_search_callback' ) );
		add_action( 'wp_ajax_nopriv_tmdb_search', array($this, 'wpml_tmdb_search_callback' ) );
		add_action( 'wp_ajax_ajax_tmdb_search', array($this, 'wpml_tmdb_search_callback' ) );
	}

	/**
	 * Activation hook: initialize plugin's default options
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_activate_plugin() {
		$default_plugin_options = array(
			'fields_to_display'  => array( 'title', 'year', 'genres', 'plot' ),
			'allow_comments'     => true,
			'tmdb_api_key'       => $this->APIKey,
		);
		$this->wpml_set_plugin_options( $default_plugin_options );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	private function wpml_get_plugin_options() {
		return get_option( $this->plugin_options_var );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	private function wpml_set_plugin_options( $arr_options ) {
		return update_option( $this->plugin_options_var, $arr_options );
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
			'menu_icon'          => $this->plugin_url . '/resources/images/movie-icon.png',
			'menu_position'      => 5
		);

		# check if comments are enabled
		$plugin_options = $this->wpml_get_plugin_options();

		if ( $plugin_options['allow_comments'] ) {
			$args['supports'][] = 'comments';
		}

		# add movie custom post type
		register_post_type( 'movie', $args );
	}

	/**
	 * Add a submenu page to Movie menu
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_admin_menus() {
		add_submenu_page( 'edit.php?post_type=movie', __( 'Options', 'wp_movie_library' ), __( 'Options', 'wp_movie_library' ), 'manage_options', 'options', array( $this, 'wpml_admin_page_options' ) );
		add_submenu_page( '', __( 'Options', 'wp_movie_library' ), __( 'Options', 'wp_movie_library' ), 'manage_options', 'options', array( $this, 'wpml_admin_page_options' ) );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_admin_page_options() {

		if ( ! empty( $_POST ) ) {
			$comments_allowed = isset( $_POST['allow_comments'] );
			$new_options = array(
				'fields_to_display' => $_POST['fields_to_display'],
				'allow_comments'    => $comments_allowed,
				'tmdb_api_key'      => $_POST['tmdb_api_key']
			);

			# save options
			$this->wpml_set_plugin_options( $new_options );

			# set a message to display
			$this->msg_settings = __( 'Settings saved.', 'wp_movie_library' );
		}

		$current_options = get_option($this->plugin_options_var);

		require $this->views_path . '/wpml-options.php';
	}
	
	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_meta_box() {
		# meta_box for imdb_id
		add_meta_box( 'tmdb_id', __( 'TMDb − The Movie Database', 'wp_movie_library' ), array( $this, 'wpml_meta_box_html' ), 'movie', 'normal', 'high', null );

		global $post;

		error_log($post->post_type);
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_meta_box_html( $post, $metabox ) {

		$meta = get_post_meta( $post->ID, 'wpml_tmdb_data' );
		$v    = $meta[0];
?>
		<select id="tmdb_search_type" name="tmdb_search_type">
			<option value="title" selected="selected"><?php _e( 'Movie Title', 'wp_movie_library' ); ?></option>
			<option value="id"><?php _e( 'TMDb ID', 'wp_movie_library' ); ?></option>
		</select>
		<input id="tmdb_query" type="text" name="tmdb_query" value="" />
		<input id="tmdb_search" name="tmdb_search" type="button" class="button button-primary button-small" value="<?php _e( 'Fetch data', 'wp_movie_library' ); ?>" />

		<div id="tmdb_data">
		</div>

		<table class="list-table">
			<thead>
				<tr>
					<th><?php _e( 'Type', 'wp_movie_library' ); ?></th>
					<th><?php _e( 'Value', 'wp_movie_library' ); ?></th>
				</tr>
			</thead>
			<tbody>
<?php foreach ( $this->meta_data as $slug => $meta ) : ?>
				<tr>
					<td><?php echo $meta; ?></td>
					<td><input id="tmdb_data_<?php echo $slug; ?>" type="text" name="tmdb_data[<?php echo $slug; ?>]" value="<?php echo ( $v[$slug] ? $v[$slug] : '' ); ?>" size="64" /></td>
				</tr>
<?php endforeach; ?>
			</tbody>
		</table>
<?php
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_fetch_meta_data( $post_id ) {
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			# don't do anything if it is an autosave
			return false;
		}

		if ( get_post_meta($post_id, 'fetched', true) != true || isset($_POST['force_fetching']) ) {
			$post = get_post($post_id);

			# delete old posters to prevent stacking of posters
			$attachments = get_posts(array(
				'post_type' => 'attachment',
				'post_parent' => $post_id,
			));

			foreach ( $attachments as $attachment ) {
				if ( $attachment->post_title == 'poster' ) {
					wp_delete_post($attachment->ID);
				}
			}

			$imdb_id = $_POST['imdb_id'];

			# save imdb id as meta data to post
			update_post_meta($post_id, 'imdb_id', $imdb_id);

			require $this->plugin_path . 'lib/imdb.php';
			$imdb_scraper = new Imdb();

			$imdb_scrap = $imdb_scraper->getMovieInfoById($imdb_id);

			# add imdb data as meta to post
			foreach ( $this->imdb_fields as $field ) {
				update_post_meta($post_id, $field, $imdb_scrap[$field]);
			}

			# now it's time to fetch poster if exists
			$html_poster = media_sideload_image($imdb_scrap['poster'], $post_id, 'poster');

			if ( !is_object($html_poster) ) {
				# means poster is fetched successfully
				update_post_meta($post_id, 'html_poster', $html_poster);
			} else {
				# imdb didn't return any posters, so we need to put our own
				ob_start();

				?>
				<img src="<?php echo $this->plugin_url; ?>/resources/images/no_poster.png" alt="poster" />
				<?php

				$html_no_poster = ob_get_clean();
				update_post_meta($post_id, 'html_poster', $html_no_poster);
			}

			# add a marker meta data to prevent future fetches
			update_post_meta($post_id, 'fetched', true);
		}

		return true;
	}

	public function wpml_save_tmdb_data( $post_id ) {

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return false;

		$post = get_post( $post_id );
		if ( 'movie' != get_post_type( $post ) )
			return false;

		update_post_meta( $post_id, 'wpml_tmdb_data', $_POST['tmdb_data'] );
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
			$plugin_options = $this->wpml_get_plugin_options();

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

		require_once $this->plugin_path . 'lib/wpml-tmdb.php';
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
					$ret .= '<img src="'.$this->plugin_url.'/resources/images/no_poster.png" alt="'.$movie['title'].'" />';
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
		require_once $this->plugin_path . 'lib/wpml-tmdb.php';
		$tmdb = new TMDb( $this->APIKey, 'fr', false, 'https://' );
		$movie = $tmdb->getMovie( $id );
		$casts = $tmdb->getMovieCast( $id );

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

		$movie = array_merge( $movie, $casts );

		header('Content-type: application/json');
		echo json_encode( $movie );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_style() {
		wp_register_style( 'wp_movie_library', $this->plugin_url . '/resources/css/style.css', array(), false, 'all' );
		wp_enqueue_style( 'wp_movie_library' );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_admin_header() {

		wp_register_style( 'wpml-admin', $this->plugin_url . '/resources/css/admin.css', array(), false, 'all' );
		wp_enqueue_style( 'wpml-admin' );

		wp_register_script( 'wpml-ajax', $this->plugin_url . '/resources/js/application.js', '', false, '' );
		wp_enqueue_script( 'wpml-ajax' );
		wp_localize_script( 'wpml-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * 
	 * 
	 * @since WPMovieLibrary 1.0
	 */
	public function wpml_widgets() {
		# widgets
		require $this->plugin_path . 'lib/wpml-widgets.php';
		register_widget( 'WPMovieLibraryWidgetNewests' );
		register_widget( 'WPMovieLibraryWidgetBests' );

		# slider js and css
	}
}

# initiate plugin
$wp_movie_library = new WPMovieLibrary();

?>
