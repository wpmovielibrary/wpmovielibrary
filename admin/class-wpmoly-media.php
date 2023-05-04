<?php
/**
 * WPMovieLibrary Media Class extension.
 * 
 * Add and manage Movie Images and Posters
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Media' ) ) :

	class WPMOLY_Media extends WPMOLY_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {

			if ( ! is_admin() )
				return false;

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'before_delete_post', __CLASS__ . '::delete_movies_attachments', 10, 1 );
			add_action( 'admin_post_thumbnail_html', __CLASS__ . '::load_posters_link', 10, 2 );

			add_filter( 'wpmoly_check_for_existing_images', __CLASS__ . '::check_for_existing_images', 10, 3 );
			add_filter( 'wpmoly_jsonify_movie_images', __CLASS__ . '::fake_jsonify_movie_images', 10, 3 );
			add_action( 'wp_ajax_wpmoly_upload_image', __CLASS__ . '::upload_image_callback' );
			add_action( 'wp_ajax_wpmoly_set_featured', __CLASS__ . '::set_featured_image_callback' );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Callbacks
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Upload a movie image.
		 * 
		 * Extract params from $_POST values. Image URL and post ID are
		 * required, title is optional. If no title is submitted file's
		 * basename will be used as image name.
		 *
		 * @since    1.0
		 */
		public static function upload_image_callback() {

			wpmoly_check_ajax_referer( 'upload-movie-image' );

			$image   = ( isset( $_POST['image'] )   && '' != $_POST['image']   ? $_POST['image']   : null );
			$post_id = ( isset( $_POST['post_id'] ) && '' != $_POST['post_id'] ? $_POST['post_id'] : null );
			$tmdb_id = ( isset( $_POST['tmdb_id'] ) && '' != $_POST['tmdb_id'] ? $_POST['tmdb_id'] : null );

			if ( ! is_array( $image ) || is_null( $post_id ) )
				return new WP_Error( 'invalid', __( 'An error occured when trying to import image: invalid data or Post ID.', 'wpmovielibrary' ) );

			$response = self::image_upload( $image['file_path'], $post_id, $tmdb_id, 'backdrop', $image );
			wpmoly_ajax_response( $response );
		}

		/**
		 * Upload an image and set it as featured image of the submitted
		 * post.
		 * 
		 * Extract params from $_POST values. Image URL and post ID are
		 * required, title is optional. If no title is submitted file's
		 * basename will be used as image name.
		 * 
		 * Return the uploaded image ID to updated featured image preview
		 * in editor.
		 *
		 * @since    1.0
		 */
		public static function set_featured_image_callback() {

			wpmoly_check_ajax_referer( 'set-movie-poster' );

			$image   = ( isset( $_POST['image'] )   && '' != $_POST['image']   ? $_POST['image']   : null );
			$post_id = ( isset( $_POST['post_id'] ) && '' != $_POST['post_id'] ? $_POST['post_id'] : null );
			$title   = ( isset( $_POST['title'] )   && '' != $_POST['title']   ? $_POST['title']   : null );
			$tmdb_id = ( isset( $_POST['tmdb_id'] ) && '' != $_POST['tmdb_id'] ? $_POST['tmdb_id'] : null );

			if ( 1 != wpmoly_o( 'poster-featured' ) )
				return new WP_Error( 'no_featured', __( 'Movie Posters as featured images option is deactivated. Update your settings to activate this.', 'wpmovielibrary' ) );

			if ( is_null( $image ) || is_null( $post_id ) )
				return new WP_Error( 'invalid', __( 'An error occured when trying to import image: invalid data or Post ID.', 'wpmovielibrary' ) );

			$response = self::set_image_as_featured( $image, $post_id, $tmdb_id, $title );
			wpmoly_ajax_response( $response );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                       Images & Posters
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Perform delete actions on movies' images and posters.
		 * 
		 * User can set through the Settings whether the imported images,
		 * posters or both should be deleted along with the movie's Post.
		 * 
		 * @since    1.0
		 * 
		 * @param    int    Post ID
		 * 
		 * @return   mixed    Attachment deletion status
		 */
		public static function delete_movies_attachments( $post_id ) {

			$post = get_post( $post_id );
			if ( ! $post || 'movie' != get_post_type( $post ) )
				return false;

			// Do nothing
			if ( ! wpmoly_o( 'images-delete' ) && ! wpmoly_o( 'posters-delete' ) )
				return false;

			// Delete posters only
			if ( ! wpmoly_o( 'images-delete' ) && wpmoly_o( 'posters-delete' ) )
				if ( has_post_thumbnail( $post_id ) )
					return wp_delete_attachment( get_post_thumbnail_id( $post_id ), $force_delete = true );

			// Delete images only
			$args = array(
				'post_parent' => $post_id,
				'post_type' => 'attachment'
			);

			if ( ! wpmoly_o( 'posters-delete' ) )
				if ( has_post_thumbnail( $post_id ) )
					$args['exclude'] = get_post_thumbnail_id( $post_id );

			$attached = get_children( $args );

			if ( empty( $attached ) )
				return false;

			foreach ( $attached as $a )
				if ( '' != get_post_meta( $a->ID, '_wpmoly_backdrop_related_tmdb_id', true ) ||
				     '' != get_post_meta( $a->ID, '_wpmoly_poster_related_tmdb_id', true ) )
					wp_delete_attachment( $a->ID, $force_delete = true );

			return $post_id;
		}

		/**
		 * Get related images for the current movie. Shortcut for the
		 * get_movie_imported_attachments() method.
		 * 
		 * @since    2.0
		 * 
		 * @return   array    Images list
		 */
		public static function get_movie_imported_images() {

			return self::get_movie_imported_attachments( $type = 'image' );
		}

		/**
		 * Get related posters for the current movie. Shortcut for the
		 * get_movie_imported_attachments() method.
		 * 
		 * @since    2.0
		 * 
		 * @return   array    Posters list
		 */
		public static function get_movie_imported_posters() {

			return self::get_movie_imported_attachments( $type = 'poster' );
		}

		/**
		 * Get all the imported images/posters related to current movie
		 * and format them to be showed in the Movie Edit page.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $type Attachment type, 'image' or 'poster'
		 * 
		 * @return   array    Attachment list
		 */
		public static function get_movie_imported_attachments( $type = 'image' ) {

			global $post;

			if ( 'movie' != get_post_type() )
				return false;

			if ( 'poster' != $type )
				$type = 'image';

			$args = array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'meta_key'       => "_wpmoly_{$type}_related_tmdb_id",
				'posts_per_page' => -1,
				'post_parent'    => get_the_ID()
			);

			$attachments = new WP_Query( $args );
			$images = array();

			if ( $attachments->posts ) {
				foreach ( $attachments->posts as $attachment ) {
					$images[] = array(
						'id'     => $attachment->ID,
						//'meta'   => wp_get_attachment_metadata( $attachment->ID ),
						'type'   => ( isset( $meta['sizes']['medium']['mime-type'] ) ? str_replace( 'image/', ' subtype-', $meta['sizes']['medium']['mime-type'] ) : '' ),
						'height' => ( isset( $meta['sizes']['medium']['height'] ) ? $meta['sizes']['medium']['height'] : 0 ),
						'width'  => ( isset( $meta['sizes']['medium']['width'] ) ? $meta['sizes']['medium']['width'] : 0 ),
						'format' => ( 'poster' == $type ? ' landscape' : ' portrait' ),
						'image'  => wp_get_attachment_image_src( $attachment->ID, 'medium' ),
						'link'   => get_edit_post_link( $attachment->ID )
					);
				}
			}

			return $images;
		}

		/**
		 * Set the image as featured image.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $file The image file name to set as featured
		 * @param    int       $post_id The post ID the image is to be associated with
		 * @param    int       $tmdb_id The TMDb Movie ID the image is associated with
		 * @param    string    $title The related Movie title
		 * 
		 * @return   int|WP_Error Uploaded image ID if successfull, WP_Error if an error occured.
		 */
		public static function set_image_as_featured( $file, $post_id, $tmdb_id ) {

			$image = self::image_upload( $file, $post_id, $tmdb_id, 'poster' );
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
		 * @since    1.0
		 * 
		 * @param    string    $file The filename of the image to download
		 * @param    int       $post_id The post ID the media is to be associated with
		 * @param    int       $tmdb_id The TMDb Movie ID the image is associated with
		 * @param    string    $image_type Optional. Image type, 'backdrop' or 'poster'
		 * @param    array     $data Optional. Image metadata
		 * 
		 * @return   string|WP_Error Populated HTML img tag on success
		 */
		private static function image_upload( $file, $post_id, $tmdb_id, $image_type = 'backdrop', $data = null ) {

			if ( empty( $file ) )
				return new WP_Error( 'invalid', __( 'The image you\'re trying to upload is empty.', 'wpmovielibrary' ) );

			$image_type = ( 'poster' == $image_type ? 'poster' : 'backdrop' );

			if ( 'poster' == $image_type ) {
				$size = wpmoly_o( 'poster-size' );
			} else {
				$size = wpmoly_o( 'images-size' );
			}

			if ( is_array( $file ) ) {
				$data = $file;
				$file = WPMOLY_TMDb::get_image_url( $file['file_path'], $image_type, $size );
				$image = $file;
			}
			else {
				$image = $file;
				$file = WPMOLY_TMDb::get_image_url( $file, $image_type, $size );
			}

			$image = substr( $image, 1 );

			$existing = self::check_for_existing_images( $tmdb_id, $image_type, $image );
			if ( false !== $existing )
				return new WP_Error( 'invalid', __( 'The image you\'re trying to upload already exists.', 'wpmovielibrary' ) );

			$tmp = download_url( $file );

			preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
			$file_array['name'] = basename( $matches[0] );
			$file_array['tmp_name'] = $tmp;

			if ( is_wp_error( $tmp ) ) {
				@unlink( $file_array['tmp_name'] );
				$file_array['tmp_name'] = '';
			}

			$id = media_handle_sideload( $file_array, $post_id );
			if ( is_wp_error( $id ) ) {
				@unlink( $file_array['tmp_name'] );
				return new WP_Error( $id->get_error_code(), $id->get_error_message() );
			}

			self::update_attachment_meta( $id, $post_id, $image_type, $data );

			return $id;
		}

		/**
		 * Update movie images/posters title, description, caption...
		 * 
		 * @since    2.0
		 * 
		 * @param    int       $attachment_id Current image Post ID
		 * @param    int       $post_id Related Post ID
		 * @param    string    $image_type Image type, 'backdrop' or 'poster'
		 * @param    array     $data Image data
		 */
		private static function update_attachment_meta( $attachment_id, $post_id, $image_type, $data ) {

			if ( ! get_post( $attachment_id ) || ! get_post( $post_id ) )
				return false;

			if ( 'poster' != $image_type )
				$image_type = 'image';

			$tmdb_id    = WPMOLY_Movies::get_movie_meta( $post_id, 'tmdb_id' );
			$title      = WPMOLY_Movies::get_movie_meta( $post_id, 'title' );
			$production = WPMOLY_Movies::get_movie_meta( $post_id, 'production_companies' );
			$director   = WPMOLY_Movies::get_movie_meta( $post_id, 'director' );
			$original_title = WPMOLY_Movies::get_movie_meta( $post_id, 'original_title' );

			$production = explode( ',', $production );
			$production = trim( array_shift( $production ) );
			$year       = WPMOLY_Movies::get_movie_meta( $post_id, 'release_date' );
			$year       = apply_filters( 'wpmoly_format_movie_date',  $year, 'Y' );

			$find = array( '{title}', '{originaltitle}', '{year}', '{production}', '{director}' );
			$replace = array( $title, $original_title, $year, $production, $director );

			$_description = str_replace( $find, $replace, wpmoly_o( "{$image_type}-description" ) );
			$_title       = str_replace( $find, $replace, wpmoly_o( "{$image_type}-title" ) );

			$attachment = array(
				'ID'           => $attachment_id,
				'post_title'   => $_title,
				'post_content' => $_description,
				'post_excerpt' => $_description
			);

			update_post_meta( $attachment_id, '_wpmoly_' . $image_type . '_related_tmdb_id', $tmdb_id );
			update_post_meta( $attachment_id, '_wpmoly_' . $image_type . '_related_meta_data', $data );
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', $_title );

			$update = wp_update_post( $attachment );
		}

		/**
		 * Add a link to the current Post's Featured Image Metabox to trigger
		 * a Modal window. This will be used by the future Movie Posters
		 * selection Modal, yet to be implemented.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $content Current Post's Featured Image Metabox content, ready to be edited.
		 * @param    string    $post_id Current Post's ID (unused at that point)
		 * 
		 * @return   string    Updated $content
		 */
		public static function load_posters_link( $content, $post_id ) {

			$post = get_post( $post_id );
			if ( ! $post || 'movie' != get_post_type( $post ) )
				return $content;

			$content .= '<a id="tmdb_load_posters" class="hide-if-no-js" href="#">' . __( 'See available Movie Posters', 'wpmovielibrary' ) . '</a>';
			$content .= wpmoly_nonce_field( 'set-movie-poster', false, false );

			return $content;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                            Utils
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Check for previously imported images to avoid duplicates.
		 * 
		 * If any attachment has one or more postmeta matching the current
		 * Movie's TMDb ID, we don't want to import the image again. If
		 * we're testing a poster, make sure it isn't there already, in
		 * which case it should have a metafield storing its original
		 * TMDb file name. If we're testing an image we make sure its
		 * file name doesn't match a previously imported image.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $tmdb_id    The Movie's TMDb ID.
		 * @param    string    $image_type Optional. Which type of image we're dealing with, simple image or poster.
		 * @param    string    $image Image name to compare
		 * 
		 * @return   mixed     Return the last found image's ID if any, false if no matching image was found.
		 */
		public static function check_for_existing_images( $tmdb_id, $image_type = 'backdrop', $image = null ) {

			if ( ! isset( $tmdb_id ) || '' == $tmdb_id )
				return false;

			$image_type = ( 'poster' == $image_type ? 'poster' : 'image' );

			$check = get_posts(
				array(
					'post_type' => 'attachment',
					'meta_query' => array(
						array(
							'key'     => '_wpmoly_' . $image_type . '_related_tmdb_id',
							'value'   => $tmdb_id,
						)
					)
				)
			);

			// Check for matching files
			if ( 'poster' == $image_type && ! empty( $check ) ) {
				foreach ( $check as $c ) {
					$meta = get_post_meta( $c->ID, '_wpmoly_' . $image_type . '_related_meta_data' );
					if ( isset( $meta['file_path'] ) && in_array( $meta['file_path'], array( $image, '/' . $image ) ) )
						return $c;
				}
			}
			else if ( ! is_null( $image ) ) {
				foreach ( $check as $c ) {
					$try = get_attached_file( $c->ID );
					if ( $image == basename ( $try ) ) {
						return $try;
					}
				}
			}
			else if ( ! empty( $check ) )
				return $check;

			return false;
		}

		/**
		 * Prepare movie images to Media Modal query creating an array
		 * matching wp_prepare_attachment_for_js() filtered attachments.
		 * 
		 * This is used by WPMOLY_Edit_Movies::load_images_callback() to
		 * show movie images in Media Modal instead of regular images,
		 * which needs to fed JSONified Attachments to the AJAX callback
		 * to append to the modal.
		 * 
		 * @since    1.0
		 * 
		 * @param    array     $images The images to prepare
		 * @param    object    $post Related Movie Posts
		 * @param    string    $image_type Which type of image we're dealing with, simple image or poster.
		 * 
		 * @return   array    The prepared images
		 */
		public static function fake_jsonify_movie_images( $images, $post, $image_type ) {

			$image_type = ( 'poster' == $image_type ? 'poster' : 'backdrop' );

			$base_url = WPMOLY_TMDb::get_image_url( null, $image_type );
			$json_images = array();
			$i = 0;

			foreach ( $images as $image ) {

				$i++;
				$_date = time();
				$_title = $post->post_title;
				$_orientation = $image['aspect_ratio'] > 1 ? 'landscape' : 'portrait';

				$delete_nonce = current_user_can( 'delete_post', $post->ID ) ? wp_create_nonce( 'delete-post_' . $post->ID ) : false;
				$edit_nonce = current_user_can( 'edit_post', $post->ID ) ? wp_create_nonce( 'update-post_' . $post->ID ) : false;
				$image_editor_none = current_user_can( 'edit_post', $post->ID ) ? wp_create_nonce( 'image_editor-' . $post->ID ) : false;

				$json_images[] = array(
					'id' 		=> $post->ID . '_' . $i,
					'title' 	=> $_title,
					'filename' 	=> substr( $image['file_path'], 1 ),
					'url' 		=> $base_url['original'] . $image['file_path'],
					'link' 		=> get_permalink( $post->ID ),
					'alt'		=> '',
					'author' 	=> "" . get_current_user_id(),
					'description' 	=> '',
					'caption' 	=> '',
					'name' 		=> substr( $image['file_path'], 1, -4 ),
					'status' 	=> "inherit",
					'uploadedTo' 	=> $post->ID,
					'date' 		=> $_date * 1000,
					'modified' 	=> $_date * 1000,
					'menuOrder' 	=> 0,
					'mime' 		=> "image/jpeg",
					'type' 		=> "image",
					'subtype' 	=> "jpeg",
					'icon' 		=> includes_url( 'images/crystal/default.png' ),
					'dateFormatted' => date( get_option( 'date_format' ), $_date ),
					'nonces' 	=> array(
						'delete' 	=> $delete_nonce,
						'update' 	=> $edit_nonce,
						'edit' 		=> $image_editor_none
					),
					'editLink' 	=> "#",
					'sizes' => array(
						'thumbnail' => array(
							'height' => 154,
							'orientation' => $_orientation,
							'url' => $base_url['small'] . $image['file_path'],
							'width' => 154,
						),
						'medium' => array(
							'height' => floor( 300 / $image['aspect_ratio'] ),
							'orientation' => $_orientation,
							// Modal thumbs are actually Medium size, so we set a small one
							// for posters, a really small one
							'url' => ( 'poster' == $image_type ? $base_url['x-small'] : $base_url['small'] ) . $image['file_path'],
							'width' => 300,
						),
						'large' => array(
							'height' => floor( 500 / $image['aspect_ratio'] ),
							'orientation' => $_orientation,
							'url' => $base_url['full'] . $image['file_path'],
							'width' => 500,
						),
						'full' => array(
							'height' => $image['height'],
							'orientation' => $_orientation,
							'url' => $base_url['original'] . $image['file_path'],
							'width' => $image['width'],
						),
					),
					'height' 	=> $image['height'],
					'width' 	=> $image['width'],
					'orientation' 	=> $_orientation,
					'compat' 	=> array( 'item' => '', 'meta' => '' ),
					'metadata' 	=> $image
				);
			}

			return $json_images;
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

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}

endif;