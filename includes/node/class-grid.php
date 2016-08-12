<?php
/**
 * Define the grid class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 */

namespace wpmoly\Node;

/**
 * Handle grids.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 * 
 * @property    int       $id Grid ID.
 * @property    string    $type Grid type: movie, actor, genre…
 * @property    string    $mode Grid mode: grid, list or archive
 * @property    string    $preset Grid content preset.
 * @property    string    $order_by Grid content order by.
 * @property    string    $order Grid content order.
 * @property    int       $columns Number of columns to use.
 * @property    int       $rows Number of rows to use.
 * @property    int       $total Number of Nodes to use.
 * @property    int       $show_menu Show the Grid menu to users.
 * @property    int       $mode_control Allow users to control the Grid mode.
 * @property    int       $content_control Allow users to control the Grid content.
 * @property    int       $display_control Allow users to control the Grid display.
 * @property    int       $order_control Allow users to control the Grid content ordering.
 * @property    int       $show_pagination Show the Grid pagination to users.
 */
class Grid extends Node {

	/**
	 * Initialize the Grid.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function init() {

		$this->suffix = '_wpmoly_' . $this->type . '_grid_';
	}

	/**
	 * Make sure a Grid mode is supported.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $mode Grid mode to validate.
	 * 
	 * @return   string
	 */
	public function validate_mode( $mode ) {

		return in_array( $mode, array( 'grid', 'list', 'archive' ) ) ? $mode : 'grid';
	}

	/**
	 * Make sure a Grid type is supported.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Grid type to validate.
	 * 
	 * @return   string
	 */
	public function validate_type( $type ) {

		return in_array( $type, array( 'movie', 'actor', 'genre' ) ) ? $type : 'movie';
	}
}
