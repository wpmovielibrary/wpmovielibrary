<?php
/**
 * WPMovieLibrary Media Class extension.
 * 
 * Add and manage Movie Images and Posters
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Media' ) ) :

	class WPML_Media extends WPML_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_filter( 'wpml_check_for_existing_images', __CLASS__ . '::wpml_check_for_existing_images', 10, 3 );
			add_filter( 'wpml_jsonify_movie_images', __CLASS__ . '::fake_jsonify_movie_images', 10, 3 );
			add_action( 'wp_ajax_tmdb_save_image', __CLASS__ . '::wpml_save_image_callback' );
			add_action( 'wp_ajax_tmdb_set_featured', __CLASS__ . '::wpml_set_featured_image_callback' );
		}

		/**
		 * Get the movie's featured image.
		 * If a poster was uploaded and set as featured image for the moive's
		 * post, return the image URL. If no featured image is set, return the
		 * default poster.
		 *
		 * @since     1.0.0
		 * 
		 * @param     int       $post_id The movie's post ID
		 *
		 * @return    string    Featured image URL
		 */
		public static function wpml_get_featured_image( $post_id, $size = 'thumbnail' ) {
			$_id = get_post_thumbnail_id( $post_id );
			$img = ( $_id ? wp_get_attachment_image_src( $_id, $size ) : array( WPML_URL . '/admin/assets/img/no_poster.png' ) );
			return $img[0];
		}

		/**
		 * Check for previously imported images to avoid duplicates.
		 * 
		 * If any attachment has one or more postmeta matching the current
		 * Movie's TMDb ID, we don't want to import the image again, so we return
		 * the last found image's ID to be used instead.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    $tmdb_id    The Movie's TMDb ID.
		 * @param    string    $image_type Optional. Which type of image we're
		 *                                 dealing with, simple image or poster.
		 * 
		 * @return   string|boolean        Return the last found image's ID if
		 *                                 any, false if no matching image was
		 *                                 found.
		 */
		public static function wpml_check_for_existing_images( $tmdb_id, $image_type = 'image', $image = null ) {

			if ( ! isset( $tmdb_id ) || '' == $tmdb_id )
				return false;

			if ( ! in_array( $image_type, array( 'image', 'poster' ) ) )
				$image_type = 'image';

			$check = get_posts(
				array(
					'post_type' => 'attachment',
					'meta_query' => array(
						array(
							'key'     => '_wpml_' . $image_type . '_related_tmdb_id',
							'value'   => $tmdb_id,
						)
					)
				)
			);

			if ( ! is_null( $image ) ) {
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
		 * This is used by WPML_Edit_Movies::load_images_callback() to
		 * show movie images in Media Modal instead of regular images,
		 * which needs to fed JSONified Attachments to the AJAX callback
		 * to append to the modal.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $images The images to prepare
		 * @param    object   $post Related Movie Posts
		 * 
		 * @return   array    The prepared images
		 */
		public function fake_jsonify_movie_images( $images, $post ) {

			$base_url = WPML_TMDb::wpml_tmdb_get_base_url( 'image' );
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

/*{
"id":3450,
"title":"Image from L'\u00c2ge de Glace 4 : La D\u00e9rive des Continents",
"filename":"2cmr8B28yH358vapdcJogulemk9.jpg",
"url":"http:\/\/wpthemes\/wp-content\/uploads\/2014\/04\/2cmr8B28yH358vapdcJogulemk9.jpg",
"link":"http:\/\/wpthemes\/movies\/lage-de-glace-4-la-derive-des-continents\/image-from-lage-de-glace-4-la-derive-des-continents-3\/",
"alt":"",
"author":"1",
"description":"",
"caption":"",
"name":"image-from-lage-de-glace-4-la-derive-des-continents-3",
"status":"inherit",
"uploadedTo":3446,
"date":1396865738000,
"modified":1396865738000,
"menuOrder":0,
"mime":"image\/jpeg",
"type":"image",
"subtype":"jpeg",
"icon":"http:\/\/wpthemes\/wp-includes\/images\/crystal\/default.png",
"dateFormatted":"7 April 2014",
"nonces":{"update":"e411542c88","delete":"26b9591eb5"},
"editLink":"http:\/\/wpthemes\/wp-admin\/post.php?post=3450&action=edit",
"sizes":{
"thumbnail":{"height":150,"width":150,"url":"http:\/\/wpthemes\/wp-content\/uploads\/2014\/04\/2cmr8B28yH358vapdcJogulemk9-150x150.jpg","orientation":"landscape"},
"medium":{"height":168,"width":300,"url":"http:\/\/wpthemes\/wp-content\/uploads\/2014\/04\/2cmr8B28yH358vapdcJogulemk9-300x168.jpg","orientation":"landscape"},
"large":{"height":351,"width":625,"url":"http:\/\/wpthemes\/wp-content\/uploads\/2014\/04\/2cmr8B28yH358vapdcJogulemk9-1024x576.jpg","orientation":"landscape"},
"full":{"url":"http:\/\/wpthemes\/wp-content\/uploads\/2014\/04\/2cmr8B28yH358vapdcJogulemk9.jpg","height":1080,"width":1920,"orientation":"landscape"}
},
"height":1080,
"width":1920,
"orientation":"landscape",
"compat":{"item":"","meta":""}
}*/

				$json_images[] = array(
					'id' 		=> "{$post->ID}_{$i}",
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
					'editLink' 	=> get_edit_post_link( $post->ID ),
					'sizes' => array(
						'thumbnail' => array(
							'height' => 154,
							'orientation' => $_orientation,
							'url' => $base_url['xx-small'] . $image['file_path'],
							'width' => 154,
						),
						'medium' => array(
							'height' => floor( 300 / $image['aspect_ratio'] ),
							'orientation' => $_orientation,
							'url' => $base_url['small'] . $image['file_path'],
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
				);
			}

			return $json_images;
		}

		/**
		 * Upload a movie image.
		 * 
		 * Extract params from $_POST values. Image URL and post ID are
		 * required, title is optional. If no title is submitted file's
		 * basename will be used as image name.
		 *
		 * @since     1.0.0
		 * 
		 * @param string $image Image url
		 * @param int $post_id ID of the post the image will be attached to
		 * @param string $title Post title to use as image title to avoir crappy TMDb images names.
		 *
		 * @return    string    Uploaded image ID
		 */
		public static function wpml_save_image_callback() {

			check_ajax_referer( 'wpml-callbacks-nonce', 'wpml_check' );

			$image   = ( isset( $_GET['image'] )   && '' != $_GET['image']   ? $_GET['image']   : '' );
			$post_id = ( isset( $_GET['post_id'] ) && '' != $_GET['post_id'] ? $_GET['post_id'] : '' );
			$title   = ( isset( $_GET['title'] )   && '' != $_GET['title']   ? $_GET['title']   : '' );
			$tmdb_id = ( isset( $_GET['tmdb_id'] ) && '' != $_GET['tmdb_id'] ? $_GET['tmdb_id'] : '' );

			if ( ! is_array( $image ) || '' == $post_id )
				return false;

			echo self::wpml_image_upload( $image['file_path'], $post_id, $tmdb_id, $title, $image );
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
		 * @since     1.0.0
		 * 
		 * @param string $image Image url
		 * @param int $post_id ID of the post the image will be attached to
		 * @param string $title Post title to use as image title to avoir crappy TMDb images names.
		 *
		 * @return    string    Uploaded image ID
		 */
		public static function wpml_set_featured_image_callback() {

			check_ajax_referer( 'wpml-callbacks-nonce', 'wpml_check' );

			$image   = ( isset( $_GET['image'] )   && '' != $_GET['image']   ? $_GET['image']   : '' );
			$post_id = ( isset( $_GET['post_id'] ) && '' != $_GET['post_id'] ? $_GET['post_id'] : '' );
			$title   = ( isset( $_GET['title'] )   && '' != $_GET['title']   ? $_GET['title']   : '' );
			$tmdb_id = ( isset( $_GET['tmdb_id'] ) && '' != $_GET['tmdb_id'] ? $_GET['tmdb_id'] : '' );

			if ( '' == $image || '' == $post_id || 1 != WPML_Settings::tmdb__poster_featured() )
				return false;

			echo self::wpml_set_image_as_featured( $image, $post_id, $tmdb_id, $title );
			die();
		}

		/**
		 * Get all the imported images related to current movie and format them
		 * to be showed in the Movie Edit page. Featured image (most likely the
		 * movie poster) is excluded from the list.
		 * 
		 * @since    1.0.0
		 * 
		 * @return   array    Movie list
		 */
		public static function wpml_get_movie_imported_images() {

			global $post;

			if ( 'movie' != get_post_type() )
				return false;

			$html = '';

			$args = array(
				'post_type'   => 'attachment',
				'orderby'     => 'title',
				'numberposts' => -1,
				'post_status' => null,
				'post_parent' => get_the_ID(),
				'exclude'     => get_post_thumbnail_id()
			);

			$attachments = get_posts( $args );

			if ( $attachments )
				foreach ( $attachments as $attachment )
					$html .= '<div class="tmdb_movie_images tmdb_movie_imported_image"><a href="' . get_edit_post_link( $attachment->ID ) . '">' . wp_get_attachment_image( $attachment->ID, 'medium' ) . '</a></div>';

			return $html;
		}

		/**
		 * Set the image as featured image.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    int    $image The ID of the image to set as featured
		 * @param    int    $post_id The post ID the image is to be associated with
		 * 
		 * @return   string|WP_Error Populated HTML img tag on success
		 */
		public static function wpml_set_image_as_featured( $file, $post_id, $tmdb_id, $title ) {

			$size = WPML_Settings::tmdb__poster_size();

			$existing = apply_filters( 'wpml_check_for_existing_images', $tmdb_id, 'poster' );

			if ( false !== $existing )
				return $existing[0]->ID;

			$image = self::wpml_image_upload( $file, $post_id, $tmdb_id, $title, 'poster' );

			if ( is_array( $image ) && isset( $image[0]->ID ) )
				return $image[0]->ID;
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
		 * @since    1.0.0
		 * 
		 * @param    string    $file The URL of the image to download
		 * @param    int       $post_id The post ID the media is to be associated with
		 * @param    string    $title Optional. Title of the image
		 * 
		 * @return   string|WP_Error Populated HTML img tag on success
		 */
		private static function wpml_image_upload( $file, $post_id, $tmdb_id, $title, $image_type = 'image', $data = null ) {

			if ( empty( $file ) )
				return false;

			if ( ! in_array( $image_type, array( 'image', 'poster' ) ) )
				$image_type = 'image';

			$size = WPML_Settings::tmdb__images_size();
			$path = WPML_TMDb::wpml_tmdb_get_base_url( $image_type, $size );

			if ( is_array( $file ) ) {
				$data = $file;
				$file = $path . $file['file_path'];
				$image = $file['file_path'];
			}
			else {
				$image = $file;
				$file = $path . $file;
			}

			$image = substr( $image, 1 );

			$existing = self::wpml_check_for_existing_images( $tmdb_id, $image_type, $image );

			if ( false !== $existing )
				return $existing;

			$tmp = download_url( $file );

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

			update_post_meta( $id, '_wpml_' . $image_type . '_related_tmdb_id', $tmdb_id );
			update_post_meta( $id, '_wpml_' . $image_type . '_related_tmdb_data', $data );

			return $id;
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
		public function init() {}

	}

endif;