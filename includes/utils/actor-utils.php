<?php
/**
 * The file that defines the actor utils functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\utils\actor;

use wpmoly\utils;

/**
 * Return an actor object.
 *
 * @since 3.0.0
 *
 * @param mixed $actor Actor ID, object or array
 *
 * @return Actor|boolean
 */
function get( $actor ) {

	return utils\get_node( $actor, '\wpmoly\nodes\taxonomies\Actor' );
}

/**
 * Return an actor metadata.
 *
 * @since 3.0.0
 *
 * @param int     $actor_id Actor ID, object or array
 * @param string  $key      Actor Meta key to return.
 * @param boolean $single   Whether to return a single value
 *
 * @return Actor|boolean
 */
function get_meta( $actor_id, $key = '', $single = true ) {

	$key = (string) $key;
	$value = '';

	$term = get_term( (int) $actor_id );
	if ( ! isset( $term->taxonomy ) || 'actor' !== $term->taxonomy ) {
		return $value;
	}

	if ( ! empty( $key ) ) {

		/**
		 * Filter the actor meta key.
		 *
		 * @since 3.0.0
		 *
		 * @param string $key Meta key.
		 */
		$key = prefix( $key );
		$value = get_term_meta( $actor_id, $key, $single );
	} else {

		$values = array();

		$meta = get_post_meta( $actor_id );
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
 * Prefix actor meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key           Meta key.
 * @param boolean $strip_hyphens Replace hyphens with underscores?
 *
 * @return string
 */
function prefix( $key, $strip_hyphens = true ) {

	return utils\prefix_meta_key( $key, '_wpmoly_actor_', $strip_hyphens );
}

/**
 * Remove prefix from actor meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key               Prefixed meta key.
 * @param boolean $strip_underscores Replace underscores with hyphens?
 *
 * @return string
 */
function unprefix( $key, $strip_underscores = true ) {

	return utils\unprefix_meta_key( $key, '_wpmoly_actor_', $strip_underscores );
}

/**
 * Determine if the submitted meta key is an actor related meta key.
 *
 * @since 3.0.0
 *
 * @param string $key Prefixed meta key.
 *
 * @return boolean
 */
function is_meta_key( $key ) {

	return ( false !== strpos( $key, '_wpmoly_actor_' ) );
}

/**
 * Retrieve actors archive page link.
 *
 * @since 3.0.0
 *
 * @param string $format URL format, 'relative' or 'absolute'.
 *
 * @return string|boolean
 */
function get_archive_link( $format = 'absolute' ) {

	return utils\get_taxonomy_archive_link( 'actor', $format );
}

/**
 * Retrieve 'actor' taxonomy archive page ID.
 *
 * @since 3.0.0
 *
 * @return int
 */
function get_archives_page_id() {

	return utils\get_archives_page_id( 'actor' );
}

/**
 * Get 'actor' taxonomy archive page if any.
 *
 * @since 3.0.0
 *
 * @return WP_Post|null
 */
function get_archives_page() {

	$post_id = get_archives_page_id();

	return get_post( $post_id );
}

/**
 * Check if there is an archive page set for 'actor' taxonomy.
 *
 * @since 3.0.0
 *
 * @return boolean
 */
function has_archives_page() {

	$page = get_archives_page();

	return ! is_null( $page );
}

/**
 * Get a Actor Headbox template.
 *
 * Simple alias for get_headbox_template().
 *
 * @since 3.0.0
 *
 * @param mixed $actor Actor ID, object or array
 *
 * @return \wpmoly\templates\Headbox
 */
function get_headbox_template( $actor ) {

	return utils\get_headbox_template( $actor );
}
