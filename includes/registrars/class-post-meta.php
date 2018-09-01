<?php
/**
 * Define the Post Meta Registrar class.
 *
 * Register required .
 *
 * @link https://wpmovielibrary.com
 * @since 3.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\registrars;

use wpmoly\helpers;

/**
 * Register the plugin post meta.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Post_Meta {

	/**
	 * Register Custom Post Meta.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register() {

		$post_meta = helpers\get_registered_post_meta();
		if ( empty( $post_meta ) ) {
			return false;
		}

		foreach ( $post_meta as $slug => $params ) {

			$args = wp_parse_args( $params, array(
				'type'              => 'string',
				'post_type'         => '',
				'description'       => '',
				'default'           => '',
				'single'            => true,
				//'sanitize_callback' => array( $this, 'sanitize_callback' ),
				'auth_callback'     => array( $this, 'auth_callback' ),
			) );

			foreach ( (array) $args['post_type'] as $post_type ) {

				$rest_args = array(
					'type'        => $args['type'],
					'description' => $args['description'],
					'default'     => $args['default'],
					'single'      => $args['single'],
				);

				if ( isset( $args['show_in_rest'] ) && is_array( $args['show_in_rest'] ) ) {
					$args['show_in_rest'] = wp_parse_args( $args['show_in_rest'], $rest_args );
				} else {
					$args['show_in_rest'] = $rest_args;
				}

				// Meta key.
				$meta_key = call_user_func( "prefix_{$post_type}_meta_key", $slug );

				// Register meta to post types.
				register_post_meta( $post_type, $meta_key, $args );

			} // End foreach().
		} // End foreach().
	}

	/**
	 * Default sanitization callback.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param mixed  $meta_value  Meta value to sanitize.
   * @param string $meta_key    Meta key.
   * @param string $object_type Object type.
	 *
	 * @return mixed
	 */
	/*public function sanitize_callback( $meta_value, $meta_key, $object_type ) {

		$meta_value = wp_slash( $meta_value );

		return $meta_value;
	}*/

	/**
	 * Default authentication callback.
	 *
	 * @since 3.0.0
	 *
	 * @param bool   $allowed  Whether the user can add the post meta. Default false.
	 * @param string $meta_key The meta key.
	 * @param int    $post_id  Post ID.
	 * @param int    $user_id  User ID.
	 * @param string $cap      Capability name.
	 * @param array  $caps     User capabilities.
	 *
	 * @return bool
	 */
	public function auth_callback( $allowed, $meta_key, $post_id, $user_id, $cap, $caps ) {

		if ( user_can( $user_id, $cap, $post_id ) ) {
			$allowed = true;
		}

		return $allowed;
	}

}
