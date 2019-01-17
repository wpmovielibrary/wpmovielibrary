<?php
/**
 * The file that defines the genre utils functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\utils\genre;

use wpmoly\utils;

/**
 * Return an genre object.
 *
 * @since 3.0.0
 *
 * @param mixed $genre Genre ID, object or array
 *
 * @return Genre|boolean
 */
function get( $genre ) {

	return utils\get_node( $genre, '\wpmoly\nodes\taxonomies\Genre' );
}

/**
 * Return an genre metadata.
 *
 * @since 3.0.0
 *
 * @param int     $genre_id Genre ID, object or array
 * @param string  $key      Genre Meta key to return.
 * @param boolean $single   Whether to return a single value
 *
 * @return Genre|boolean
 */
function get_meta( $genre_id, $key = '', $single = true ) {

	$key = (string) $key;
	$value = '';

	$term = get_term( (int) $genre_id );
	if ( ! isset( $term->taxonomy ) || 'genre' !== $term->taxonomy ) {
		return $value;
	}

	if ( ! empty( $key ) ) {

		/**
		 * Filter the genre meta key.
		 *
		 * @since 3.0.0
		 *
		 * @param string $key Meta key.
		 */
		$key = prefix( $key );
		$value = get_term_meta( $genre_id, $key, $single );
	} else {

		$values = array();

		$meta = get_post_meta( $genre_id );
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
 * Prefix genre meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key           Meta key.
 * @param boolean $strip_hyphens Replace hyphens with underscores?
 *
 * @return string
 */
function prefix( $key, $strip_hyphens = true ) {

	return utils\prefix_meta_key( $key, '_wpmoly_genre_', $strip_hyphens );
}

/**
 * Remove prefix from genre meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key               Prefixed meta key.
 * @param boolean $strip_underscores Replace underscores with hyphens?
 *
 * @return string
 */
function unprefix( $key, $strip_underscores = true ) {

	return utils\unprefix_meta_key( $key, '_wpmoly_genre_', $strip_underscores );
}

/**
 * Determine if the submitted meta key is a genre related meta key.
 *
 * @since 3.0.0
 *
 * @param string $key Prefixed meta key.
 *
 * @return boolean
 */
function is_meta_key( $key ) {

	return ( false !== strpos( $key, '_wpmoly_genre_' ) );
}

/**
 * Retrieve genres archive page link.
 *
 * @since 3.0.0
 *
 * @param string $format URL format, 'relative' or 'absolute'.
 *
 * @return string|boolean
 */
function get_archive_link( $format = 'absolute' ) {

	return utils\get_taxonomy_archive_link( 'genre', $format );
}

/**
 * Retrieve 'genre' taxonomy archive page ID.
 *
 * @since 3.0.0
 *
 * @return int
 */
function get_archives_page_id() {

	return utils\get_archives_page_id( 'genre' );
}

/**
 * Get 'genre' taxonomy archive page if any.
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
 * Check if there is an archive page set for 'genre' taxonomy.
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
 * Get a Genre Headbox template.
 *
 * Simple alias for get_headbox_template().
 *
 * @since 3.0.0
 *
 * @param mixed $genre Genre ID, object or array
 *
 * @return \wpmoly\templates\Headbox
 */
function get_headbox_template( $genre ) {

	return utils\get_headbox_template( $genre );
}
