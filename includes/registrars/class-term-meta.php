<?php
/**
 * Define the Term Meta Registrar class.
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
 * Register the plugin term meta.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Term_Meta {

	/**
	 * Register Custom Term Meta.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register() {

		$term_meta = helpers\get_registered_term_meta();
		if ( empty( $term_meta ) ) {
			return false;
		}

		foreach ( $term_meta as $slug => $params ) {

			$args = wp_parse_args( $params, array(
				'type'              => 'string',
				'taxonomy'          => '',
				'description'       => '',
				'default'           => '',
				'single'            => true,
				'sanitize_callback' => null,
				'auth_callback'     => array( $this, 'auth_callback' ),
			) );

			foreach ( (array) $args['taxonomy'] as $taxonomy ) {

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
				$meta_key = call_user_func( "prefix_{$taxonomy}_meta_key", $slug );

				register_term_meta( $taxonomy, $meta_key, $args );
			}
		}
	}

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
