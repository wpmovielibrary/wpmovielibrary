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
	 * @access public
	 *
	 * @param Block $block Block instance.
	 */
	public function set_grid_editor_discover_block_data( $block ) {

		$this->set_post_editor_discover_block_data( 'grid', $block );
	}

	/**
	 * Set Movie Editor 'Discover' Block data.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param Block $block Block instance.
	 */
	public function set_movie_editor_discover_block_data( $block ) {

		$this->set_post_editor_discover_block_data( 'movie', $block );
	}

	/**
	 * Set Person Editor 'Discover' Block data.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param Block $block Block instance.
	 */
	public function set_person_editor_discover_block_data( $block ) {

		$this->set_post_editor_discover_block_data( 'person', $block );
	}

	/**
	 * Set Post Editor 'Discover' Block data.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $post_type Post type.
	 * @param Block  $block     Block instance.
	 */
	private function set_post_editor_discover_block_data( $post_type, $block ) {

		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		$counts = (array) wp_count_posts( $post_type );
		if ( is_wp_error( $counts ) ) {
			return false;
		}

		$block->set_data( array(
			'counts' => array_map( 'intval', $counts ),
			'counts' => array_filter( $counts ),
			'total'  => array_sum( $counts ),
			'edit'   => admin_url( "edit.php?post_type={$post_type}" ),
		) );
	}

}
