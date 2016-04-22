<?php
/**
 * Define the Editor Metabox class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Metabox;

use wpmoly\Loader;
use wpmoly\Node\Movie;
use wpmoly\Node\Backdrop;
use wpmoly\Node\Poster;
use wpmoly\Core\Template;

/**
 * Create a metabox for movie metadata.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Editor extends Metabox {

	protected $movie;

	/**
	 * Initialize.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function init() {

		$this->template = new Template( 'metabox/metabox.php' );

		// Saving actions
		$this->actions[] = array( 'save_post_movie',               $this, 'save', 10, 3 );
		/*$this->actions[] = array( 'wp_insert_post_empty_content',  $this, 'empty_content' );
		$this->actions[] = array( 'wp_insert_post_data',           $this, 'empty_title' );*/

		// Before Metabox content
		$this->actions[] = array( 'wpmoly/before/metabox/content', $this, 'movie_preview', 10, 1 );
		$this->actions[] = array( 'wpmoly/before/metabox/content', $this, 'search_menu', 10, 1 );

		$this->filters[] = array( 'post_updated_messages',         $this, 'updated_messages', 10, 1 );
	}

	/**
	 * Register the metabox hooks.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function define_admin_hooks() {

		$loader = Loader::get_instance();
		$loader->add_action( 'admin_footer-post.php',     $this, 'print_js_templates' );
		$loader->add_action( 'admin_footer-post-new.php', $this, 'print_js_templates' );
	}

	/**
	 * Print the JavaScript templates for the admin area.
	 *
	 * Output the JS templates files required for Backbone parts to render.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function print_js_templates() {

		if ( 'movie' != get_post_type() ) {
			return false;
		}

		$format  = 'json';
		$require = 'always';

		$meta      = $this->meta_panel( $format, $require );
		$details   = $this->details_panel( $format, $require );
		$backdrops = $this->backdrops_panel( $format, $require );
		$posters   = $this->posters_panel( $format, $require );

		echo "\n" . '<script type="text/html" id="tmpl-wpmoly-editor-meta">' . "\n" . $meta . "\n" . '</script>' . "\n";
		echo "\n" . '<script type="text/html" id="tmpl-wpmoly-editor-details">' . "\n" . $details . "\n" . '</script>' . "\n";
	}

	/**
	 * Build the Metabox.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function make() {

		$this->callback = array( $this, 'editor' );
	}

	/**
	 * Movie Editor Metabox content.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function editor( $post ) {

		$this->movie   = Movie::get_instance( $post->ID );
		$this->meta    = $this->movie->meta;
		$this->details = $this->movie->details;
		$this->media   = $this->movie->media;

		/**
		 * Filter the Metabox Panels to add/remove tabs.
		 *
		 * This should be used through Plugins to create additionnal
		 * Metabox panels.
		 *
		 * @since    3.0
		 *
		 * @param    array    $panels Existing Panels
		 */
		$this->panels = apply_filters( "wpmoly/filter/metabox/{$this->id}/panels", $this->panels );

		$metabox = $this;
		$tabs    = array();
		$panels  = array();

		foreach ( $this->panels as $id => $panel ) {

			if ( ! is_callable( $panel['callback'] ) ) {
				continue;
			}

			$is_default = 'meta' == $id;
			$tabs[ $id ] = array(
				'title'   => $panel['title'],
				'icon'    => $panel['icon'],
				'default' => $is_default ? ' active' : ''
			);
			$panels[ $id ] = array(
				'default' => $is_default ? ' active' : '',
				'content' => call_user_func( $panel['callback'] )
			);
		}

		$attributes = array(
			'empty'   => $this->movie->meta->is_empty(),
			'tabs'    => $tabs,
			'panels'  => $panels,
			'metabox' => $metabox
		);

		$this->template->data = $attributes;
		$this->template->render();
	}

	/**
	 * 'Preview' panel content.
	 *
	 * @since    3.0
	 *
	 * @param    string    $format Output format, 'json' or 'html'
	 * @param    string    $require Require template 'once' or 'always'
	 *
	 * @return   string
	 */
	public function preview_panel( $format = 'html', $require = 'once' ) {

		$panel = new Template( 'metabox/panels/panel-preview.php' );

		$meta = array();
		$default_meta = wpmoly_o( 'default_meta' );
		foreach ( $default_meta as $key => $field ) {
			if ( 'json' === $format ) {
				$value = "{{ data.$key }}";
			} else {
				$value = $this->meta->get_the( $key );
			}

			$default_meta[ $key ]['html'] = $this->get_field( $field, $key, $value, 'meta', 'json' );
		}

		$panel->data = array(
			'fields' => $default_meta
		);

		return $panel->prepare( $require );
	}

	/**
	 * 'Meta' panel content.
	 *
	 * @since    3.0
	 *
	 * @param    string    $format Output format, 'json' or 'html'
	 * @param    string    $require Require template 'once' or 'always'
	 *
	 * @return   string
	 */
	public function meta_panel( $format = 'html', $require = 'once' ) {

		$panel = new Template( 'metabox/panels/panel-meta.php' );

		$meta = array();
		$default_meta = wpmoly_o( 'default_meta' );
		foreach ( $default_meta as $key => $field ) {
			if ( 'json' === $format ) {
				$value = "{{ data.$key }}";
			} else {
				$value = $this->meta->get_the( $key );
			}

			$default_meta[ $key ]['html'] = $this->get_field( $field, $key, $value, 'meta', 'json' );
		}

		$panel->data = array(
			'fields' => $default_meta
		);

		return $panel->prepare( $require );
	}

	/**
	 * 'Details' panel content.
	 *
	 * @since    3.0
	 *
	 * @param    string    $format Output format, 'json' or 'html'
	 * @param    string    $require Require template 'once' or 'always'
	 *
	 * @return   string
	 */
	public function details_panel( $format = 'html', $require = 'once' ) {

		$panel = new Template( 'metabox/panels/panel-details.php' );

		$details = array();
		$default_details = wpmoly_o( 'default_details' );
		foreach ( $default_details as $key => $field ) {
			if ( 'json' === $format ) {
				$value = "{{ data.$key }}";
			} else {
				$value = $this->details->get( $key );
			}

			$default_details[ $key ]['html'] = $this->get_field( $field, $key, $value, 'detail', $format );
		}

		$panel->data = array(
			'fields' => $default_details
		);

		return $panel->prepare( $require );
	}

	/**
	 * 'Images' panel content.
	 *
	 * @since    3.0
	 *
	 * @param    string    $format Output format, 'json' or 'html'
	 * @param    string    $require Require template 'once' or 'always'
	 *
	 * @return   string
	 */
	public function backdrops_panel( $format = 'html', $require = 'once' ) {

		$panel = new Template( 'metabox/panels/panel-backdrops.php' );

		if ( 'json' === $format ) {
			$backdrops = array();
		} else {
			$backdrops = $this->media->load_backdrops();
		}

		$panel->data = compact( 'backdrops' );

		return $panel->prepare( $require );
	}

	/**
	 * 'Posters' panel content.
	 *
	 * @since    3.0
	 *
	 * @param    string    $format Output format, 'json' or 'html'
	 * @param    string    $require Require template 'once' or 'always'
	 *
	 * @return   string
	 */
	public function posters_panel( $format = 'html', $require = 'once' ) {

		$panel = new Template( 'metabox/panels/panel-posters.php' );

		if ( 'json' === $format ) {
			$posters = array();
		} else {
			$posters = $this->media->load_posters();
		}

		$panel->data = compact( 'posters' );

		return $panel->prepare( $require );
	}

	/**
	 * Save Movie Metadata.
	 *
	 * Use the 'save_post_movie' action hook to save the movie meta/details
	 * as postmeta.
	 *
	 * @since    3.0
	 *
	 * @param    int        $post_id Current Post ID
	 * @param    object     $post Post Current Post Object
	 * @param    boolean    $update Is this a post creation or an update?
	 *
	 * @return   int|WP_Error
	 */
	public function save( $post_id, $post, $update ) {

		$movie = get_movie( $post_id );

		if ( ! empty( $_POST['wpmoly']['meta'] ) ) {
			$movie->meta->set( $_POST['wpmoly']['meta'] )->save();
		}

		if ( ! empty( $_POST['wpmoly']['detail'] ) ) {
			$movie->details->set( $_POST['wpmoly']['detail'] )->save();
		}
	}

	public function empty_content() {


	}

	public function empty_title() {


	}

	/**
	 * Add a meta preview for the movie.
	 *
	 * @since    3.0
	 *
	 * @param    array    $metabox Metabox parameters
	 *
	 * @return   null
	 */
	public function movie_preview( $metabox, $format = 'html' ) {

		global $post;

		$template = new Template( 'metabox/preview.php' );

		$meta = array();
		$default_meta = wpmoly_o( 'default_meta' );
		foreach ( $default_meta as $key => $field ) {
			if ( 'json' === $format ) {
				$value = "{{ data.$key }}";
			} else {
				$value = $this->meta->get_the( $key );
			}

			$default_meta[ $key ]['html'] = $this->get_field( $field, $key, $value, 'meta', 'json' );
		}

		$movie = get_movie( $post->ID );

		/*if ( has_post_thumbnail() ) {
			$thumbnail  = get_post_thumbnail_id();
			$poster     = wp_get_attachment_image_src( $thumbnail, 'large' );
			$background = wp_get_attachment_image_src( $thumbnail, 'original' );

			$poster     = $poster ? $poster[0] : '';
			$background = $background ? $background[0] : '';
		} else {
			$media = $movie->media->get_posters()->first();
			$poster     = $media ? $media->sizes->medium->path   : Poster::get_default_url( 'medium' );
			$background = $media ? $media->sizes->original->path : Backdrop::get_default_url( 'full' );
		}*/

		$template->data = array(
			'movie'      => $movie,
			'empty'      => $movie->meta->is_empty(),
			'poster'     => $movie->get_poster(),
			'background' => $movie->get_backdrop( 'random' ),
			'fields'     => $default_meta
		);

		echo $template->prepare();
	}

	/**
	 * Add hidden inputs before the Metabox content (nonces, essentially)
	 * along with the Search Menu.
	 *
	 * @since    3.0
	 *
	 * @param    array    $metabox Metabox parameters
	 *
	 * @return   null
	 */
	public function search_menu( $metabox ) {

		global $post;

		$fields = array(
			'collection_autocomplete' => _is_bool( wpmoly_o( 'collection-autocomplete' ) ),
			'genre_autocomplete'      => _is_bool( wpmoly_o( 'genre-autocomplete' ) ),
			'actor_autocomplete'      => _is_bool( wpmoly_o( 'actor-autocomplete' ) ),
			'api_paginate'            => _is_bool( wpmoly_o( 'api-paginate' ) ),
			'api_adult'               => _is_bool( wpmoly_o( 'api-adult' ) ),
			'api_language'            => wpmoly_o( 'api-language' ),
			'actor_limit'             => wpmoly_o( 'actor-limit' ),

			'posters_featured'        => _is_bool( wpmoly_o( 'posters-featured' ) ),
			'posters_autoimport'      => _is_bool( wpmoly_o( 'posters-autoimport' ) ),
			'posters_limit'           => wpmoly_o( 'posters-limit' ),
			'posters_size'            => wpmoly_o( 'posters-size' ),

			'backdrops_autoimport'    => _is_bool( wpmoly_o( 'backdrops-autoimport' ) ),
			'backdrops_limit'         => wpmoly_o( 'backdrops-limit' ),
			'backdrops_size'          => wpmoly_o( 'backdrops-size' )
		);

		$movie = get_movie( $post->ID );
		$empty = $movie->meta->is_empty();
?>

		<div id="wpmoly-movie-search" class="wpmoly-movie-search <?php echo $empty ? '' : ' hidden'; ?>">
			<script type="text/javascript">var _wpmoly_search_settings = <?php echo json_encode( $fields ) ?>;</script>
			<div class="postbox hide-if-js"><span class="wpmolicon icon-warning"></span>&nbsp; <?php _e( 'The movie search requires JavaScript to be enabled, but it seems it isn’t… Check your browser settings to make sure JavaScript is correctly activated.', 'wpmovielibrary' ) ?></div>
		</div>
<?php
	}

	/**
	 * Add message support for movies in Post Editor.
	 *
	 * @since    3.0
	 *
	 * @param    array    $messages Default Post update messages
	 *
	 * @return   array    Updated Post update messages
	 */
	public static function updated_messages( $messages ) {

		global $post;

		$new_messages = array(
			'movie' => array(
				1 => sprintf( __( 'Movie updated. <a href="%s">View movie</a>', 'wpmovielibrary' ), esc_url( get_permalink( $post->ID ) ) ),
				2 => __( 'Custom field updated.', 'wpmovielibrary' ) ,
				3 => __( 'Custom field deleted.', 'wpmovielibrary' ),
				4 => __( 'Movie updated.', 'wpmovielibrary' ),
				5 => isset( $_GET['revision'] ) ? sprintf( __( 'Movie restored to revision from %s', 'wpmovielibrary' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6 => sprintf( __( 'Movie published. <a href="%s">View movie</a>', 'wpmovielibrary' ), esc_url( get_permalink( $post->ID ) ) ),
				7 => __( 'Movie saved.' ),
				8 => sprintf( __( 'Movie submitted. <a target="_blank" href="%s">Preview movie</a>', 'wpmovielibrary' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
				9 => sprintf( __( 'Movie scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview movie</a>', 'wpmovielibrary' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post->ID ) ) ),
				10 => sprintf( __( 'Movie draft updated. <a target="_blank" href="%s">Preview movie</a>', 'wpmovielibrary' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
				11 => __( 'Successfully converted to movie.', 'wpmovielibrary' )
			)
		);

		$messages = array_merge( $messages, $new_messages );

		return $messages;
	}
}