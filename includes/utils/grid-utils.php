<?php
/**
 * The file that defines the grid utils functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\utils\grid;

use wpmoly\utils;

/**
 * Return a grid object.
 *
 * @since 3.0.0
 *
 * @param mixed $grid Grid ID, object or array
 *
 * @return Grid|boolean
 */
function get( $grid = null ) {

	return utils\get_node( $grid, '\wpmoly\nodes\posts\Grid' );
}

/**
 * Return a grid metadata.
 *
 * @since 3.0.0
 *
 * @param int $grid_id Grid ID, object or array.
 * @param string $key Grid Meta key to return.
 * @param boolean $single Whether to return a single value.
 *
 * @return Grid|boolean
 */
function get_meta( $grid_id, $key = '', $single = true ) {

	$key = (string) $key;
	$value = '';

	$post_type = get_post_type( (int) $grid_id );
	if ( 'grid' !== $post_type ) {
		return $value;
	}

	if ( ! empty( $key ) ) {

		/**
		 * Filter the grid meta key.
		 *
		 * @since 3.0.0
		 *
		 * @param string $key Meta key.
		 */
		$key = prefix( $key );
		$value = get_post_meta( $grid_id, $key, $single );
	} else {

		$values = array();

		$meta = get_post_meta( $grid_id );
		foreach ( $meta as $key => $value ) {
			if ( is_meta_key( $key ) ) {
				$values[ unprefix( $key, false ) ] = maybe_unserialize( $value[0] );
			}
		}

		$value = $values;
	}

	return $value;
}

/**
 * Prefix grid meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key           Meta key.
 * @param boolean $strip_hyphens Replace hyphens with underscores?
 *
 * @return string
 */
function prefix( $key, $strip_hyphens = true ) {

	return utils\prefix_meta_key( $key, '_wpmoly_grid_', $strip_hyphens );
}

/**
 * Remove prefix from grid meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key               Prefixed meta key.
 * @param boolean $strip_underscores Replace underscores with hyphens?
 *
 * @return string
 */
function unprefix( $key, $strip_underscores = true ) {

	return utils\unprefix_meta_key( $key, '_wpmoly_grid_', $strip_underscores );
}

/**
 * Determine if the submitted meta key is a grid related meta key.
 *
 * @since 3.0.0
 *
 * @param string $key Prefixed meta key.
 *
 * @return boolean
 */
function is_meta_key( $key ) {

	$registered = utils\get_registered_grid_meta();

	$key = unprefix( $key, false );

	return array_key_exists( $key, $registered );
}

/**
 * Get a Grid template.
 *
 * @since 3.0.0
 *
 * @param mixed $grid Grid
 *
 * @return \wpmoly\templates\Grid
 */
function get_template( $grid ) {

	return new \wpmoly\templates\Grid( $grid );
}
