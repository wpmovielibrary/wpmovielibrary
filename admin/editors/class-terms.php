<?php
/**
 * Define the Terms Editor class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\admin\editors;

/**
 * Provide a tool to manage custom terms.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Terms {

	/**
	 * Set Actor Browser 'Discover' Block data.
	 *
	 * @since 3.0.0
	 *
	 * @param Block $block Block instance.
	 */
	public function set_actor_browser_discover_block_data( $block ) {

		$count = wp_count_terms( 'actor' );
		if ( is_wp_error( $count ) ) {
			return false;
		}

		$block->set_data( array(
			'total' => (int) $count,
			'edit'  => admin_url( 'edit-tags.php?taxonomy=actor' ),
		) );
	}

	/**
	 * Set Collection Browser 'Discover' Block data.
	 *
	 * @since 3.0.0
	 *
	 * @param Block $block Block instance.
	 */
	public function set_collection_browser_discover_block_data( $block ) {

		$count = wp_count_terms( 'collection' );
		if ( is_wp_error( $count ) ) {
			return false;
		}

		$block->set_data( array(
			'total' => (int) $count,
			'edit'  => admin_url( 'edit-tags.php?taxonomy=collection' ),
		) );
	}

	/**
	 * Set Genre Browser 'Discover' Block data.
	 *
	 * @since 3.0.0
	 *
	 * @param Block $block Block instance.
	 */
	public function set_genre_browser_discover_block_data( $block ) {

		$count = wp_count_terms( 'genre' );
		if ( is_wp_error( $count ) ) {
			return false;
		}

		$block->set_data( array(
			'total' => (int) $count,
			'edit'  => admin_url( 'edit-tags.php?taxonomy=genre' ),
		) );
	}

}
