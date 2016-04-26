<?php
/**
 * Define the metadata class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Node;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Meta extends Node {

	/**
	 * Initialize the Node.
	 * 
	 * Set the validators and defaults values if available.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function init() {

		$allowed = wpmoly_o( 'default_meta' );
		foreach ( $allowed as $slug => $meta ) {
			$this->validates[ $slug ] = ! empty( $meta['sanitize'] ) ? $meta['sanitize'] : 'sanitize_text_field';
			$this->escapes[ $slug ]   = ! empty( $meta['escape'] )   ? $meta['escape']   : 'esc_attr';
			$this->defaults[ $slug ]  = ! empty( $meta['defaults'] ) ? $meta['defaults'] : '';
		}
	}

	/**
	 * Make the Node.
	 * 
	 * Nothing to do for meta at this stage.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function make() {

		$this->load();
	}

	/**
	 * Load the Node metadata.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	protected function load() {

		$data = get_post_meta( $this->id );
		if ( empty( $data ) ) {
			return false;
		}

		foreach ( $data as $key => $value ) {

			if ( false !== strpos( $key, '_wpmoly_movie_' ) ) {

				if ( is_array( $value ) && 1 == count( $value ) ) {
					$value = $value[0];
				}

				$key   = str_replace( '_wpmoly_movie_', '', $key );
				$value = maybe_unserialize( $value );

				$this->set( $key, $value );
			}
		}
	}

	/**
	 * Save the Meta.
	 * 
	 * Replace meta value into the postmeta table using a list of existing
	 * meta IDs.
	 * 
	 * This method if very similar to WordPress' update_metadata() function
	 * with this major difference that it updates a group of metadata in a
	 * single query to the database and does not trigger standard meta hooks.
	 * 
	 * @since    3.0
	 * 
	 * @param    boolean    $update Update or save?
	 * 
	 * @return   null
	 */
	public function save( $update = false ) {

		global $wpdb;

		// Find existing meta
		$meta_ids = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_id, meta_key FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key LIKE '_wpmoly_movie_%%'",
				$this->id
			),
			ARRAY_A
		);

		// Parse the ID list
		$ids = array();
		foreach ( $meta_ids as $id ) {
			$ids[ $id['meta_key'] ] = $id['meta_id'];
		}

		// Prepare the meta values
		$data = array();
		foreach ( $this->data as $key => $value ) {

			// Use update to avoid replacing all meta without need
			if ( true === $update && $this->previous[ $key ] == $value ) {
				continue;
			}

			$meta_key = "_wpmoly_movie_$key";
			$meta_value = wp_unslash( $value );
			$meta_value = sanitize_meta( $meta_key, $meta_value, 'movie-meta' );
			$meta_value = maybe_serialize( $meta_value );

			$meta_id = isset( $ids[ $meta_key ] ) ? $ids[ $meta_key ] : null;

			$data[] = $wpdb->prepare( "(%s, %d, %s, %s)", $meta_id, $this->id, $meta_key, $meta_value );
		}

		$data = implode( ', ', $data );
		if ( empty( $data ) ) {
			return;
		}

		// REPLACE INTO wp_postmeta} ('a', 'b', 'c', 'd') VALUES ('A', 'B', 'C', 'D')
		$wpdb->query( "REPLACE INTO {$wpdb->postmeta} (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES {$data}" );
	}

	/**
	 * Update the Meta.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function update() {

		$this->save( $update = true );
	}

	/**
	 * Remove the Meta.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function remove() {

		
	}
}