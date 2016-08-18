<?php
/**
 * Define the Meta Ajax class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/ajax
 */

namespace wpmoly\Ajax;

use WP_Error;

/**
 * Handle all the plugin's Meta AJAX callbacks.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/ajax
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Meta {

	/**
	 * Autosave meta from the Metabox Editor.
	 * 
	 * Meta and Details can be automatically saved/updated after they're
	 * modified in the movie editor.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Meta type, 'meta' or 'details'.
	 * 
	 * @return   null
	 */
	public function save_meta( $type = 'meta' ) {

		$post_id = ! empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : false;
		if ( ! $post_id || ! get_post( $post_id ) ) {
			wp_send_json_error( __( 'Invalid Post ID.', 'wpmovielibrary' ) );
		}

		$data = isset( $_POST['data'] ) ? $_POST['data'] : false;
		if ( ! $data ) {
			wp_send_json_error( __( 'Invalid data.', 'wpmovielibrary' ) );
		}

		$movie = get_movie( $post_id );
		if ( 'meta' == $type ) {
			$movie->set( $data );
			$movie->save_meta();
		} else {
			$movie->set( $data );
			$movie->save_details();
		}

		wp_send_json_success();
	}

	/**
	 * Autosave details from the Metabox Editor.
	 * 
	 * Meta and Details can be automatically saved/updated after they're
	 * modified in the movie editor.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function save_details() {

		$this->save_meta( 'details' );
	}

	/**
	 * Autosave collections from the Metabox Editor.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function save_collections() {

		$this->save_taxonomies( 'collection' );
	}

	/**
	 * Autosave genres from the Metabox Editor.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function save_genres() {

		$this->save_taxonomies( 'genre' );
	}

	/**
	 * Autosave actors from the Metabox Editor.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function save_actors() {

		$this->save_taxonomies( 'actor' );
	}

	/**
	 * Autosave taxonomies from the Metabox Editor.
	 * 
	 * Collection, Genre and Actor taxonomies can be automatically
	 * saved/updated after they're modified in the movie editor. New terms
	 * add created if required and added to the post.
	 * 
	 * If an empty list is passed, and the corresponding setting is enabled
	 * in the plugin's settings, existing terms will be removed.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $taxonomy
	 * 
	 * @return   null
	 */
	public function save_taxonomies( $taxonomy ) {

		$post_id = ! empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : false;
		if ( ! $post_id || ! get_post( $post_id ) ) {
			wp_send_json_error( __( 'Invalid Post ID.', 'wpmovielibrary' ) );
		}

		// Special case: empty data can lead to terms removal
		$data = isset( $_POST['data'] ) ? (array) $_POST['data'] : false;
		if ( ! $data ) {
			// Should we remove existing terms?
			if ( wpmoly_o( "$taxonomy-autoempty", true ) ) {
				// Get the terms list and proceed to removal
				$terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
				if ( ! is_wp_error( $terms ) ) {
					if ( ! empty( $terms ) ) {
						$terms = wp_remove_object_terms( $post_id, $terms, $taxonomy );
						if ( is_wp_error( $terms ) ) {
							wp_send_json_error( $terms->get_error_message() );
						} else if ( ! $terms ) {
							wp_send_json_error( __( 'An error occurred while removing the terms.', 'wpmovielibrary' ) );
						} else {
							wp_send_json_success( $terms );
						}
					} else {
						wp_send_json_success( $terms );
					}
				} else {
					wp_send_json_error( $terms->get_error_message() );
				}
			// Don't remove, throw error
			} else {
				wp_send_json_error( __( 'Invalid data.', 'wpmovielibrary' ) );
			}
		}

		$errors = new WP_Error;
		$terms  = array();

		// Create terms manually
		foreach ( $data as $term ) {
			$t = wp_insert_term( $term, $taxonomy );
			if ( is_wp_error( $t ) ) {
				// If term already exists, just add it to the list
				if ( 'term_exists' == $t->get_error_code() ) {
					$_term = get_term_by( 'name', $term, $taxonomy );
					if ( $_term ) {
						$terms[] = $_term->term_id;
					}
				} else {
					wp_send_json_error( $t->get_error_message() );
				}
			} else if ( empty( $t['term_id'] ) ) {
				wp_send_json_error( sprintf( __( 'An error occurred while creating term "%s".', 'wpmovielibrary' ), $term ) );
			} else {
				$terms[] = $t['term_id'];
			}
		}

		// Link terms and movie
		$t = wp_set_object_terms( $post_id, $terms, $taxonomy );
		if ( is_wp_error( $t ) ) {
			wp_send_json_error( $t->get_error_message() );
		}

		wp_send_json_success( $terms );
	}

	/**
	 * Autosave grid settings from the Grid Builder.
	 * 
	 * Do not save anything for auto-drafts, auto-saves and revisions.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function save_grid_setting() {

		if ( ! check_ajax_referer( 'save-grid-setting' ) ) {
			wp_send_json_error();
		}

		$post_id = ! empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : false;
		if ( ! $post_id || ! get_post( $post_id ) ) {
			wp_send_json_error( __( 'Invalid Post ID.', 'wpmovielibrary' ) );
		}

		$is_autodraft = get_post_status( $post_id );
		$is_autosave  = wp_is_post_autosave( $post_id );
		$is_revision  = wp_is_post_revision( $post_id );
		if ( 'auto-draft' == $is_autodraft || $is_autosave || $is_revision ) {
			wp_send_json_error();
		}

		$data = isset( $_POST['data'] ) ? $_POST['data'] : false;
		if ( ! $data ) {
			wp_send_json_error( __( 'Invalid data.', 'wpmovielibrary' ) );
		}

		$grid = get_grid( $post_id );
		$grid->set( $data );
		$grid->save();

		wp_send_json_success();
	}
}
