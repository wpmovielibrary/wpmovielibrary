<?php
/**
 * Define the Posts Editor class.
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
class Posts {

	/**
	 * Set Grid Editor 'Discover' Block data.
	 *
	 * @since 3.0.0
	 *
	 * @param Block $block Block instance.
	 */
	public function set_grid_editor_discover_block_data( $block ) {

		$counts = (array) wp_count_posts( 'grid' );
		if ( is_wp_error( $counts ) ) {
			return false;
		}

		$block->set_data( array(
			'counts' => array_map( 'intval', $counts ),
			'counts' => array_filter( $counts ),
			'total'  => array_sum( $counts ),
			'edit'   => admin_url( 'edit.php?post_type=grid' ),
		) );
	}

	/**
	 * Set Movie Editor 'Discover' Block data.
	 *
	 * @since 3.0.0
	 *
	 * @param Block $block Block instance.
	 */
	public function set_movie_editor_discover_block_data( $block ) {

		$counts = (array) wp_count_posts( 'movie' );
		if ( is_wp_error( $counts ) ) {
			return false;
		}

		$block->set_data( array(
			'counts' => array_map( 'intval', $counts ),
			'counts' => array_filter( $counts ),
			'total'  => array_sum( $counts ),
			'edit'   => admin_url( 'edit.php?post_type=movie' ),
		) );
	}

}
