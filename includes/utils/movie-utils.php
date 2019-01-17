<?php
/**
 * The file that defines the movie utils functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\utils\movie;

use wpmoly\utils;

/**
 * Return a movie object.
 *
 * @since 3.0.0
 *
 * @param mixed $movie Movie ID, object or array
 *
 * @return Movie|boolean
 */
function get( $movie ) {

	return utils\get_node( $movie, '\wpmoly\nodes\posts\Movie' );
}

/**
 * Return a movie metadata.
 *
 * @since 3.0.0
 *
 * @param int     $movie_id Movie ID, object or array
 * @param string  $key      Movie Meta key to return.
 * @param boolean $single   Whether to return a single value
 *
 * @return Movie|boolean
 */
function get_meta( $movie_id, $key = '', $single = true ) {

	$key = (string) $key;
	$value = '';

	$post_type = get_post_type( (int) $movie_id );
	if ( 'movie' !== $post_type ) {
		return $value;
	}

	if ( ! empty( $key ) ) {

		/**
		 * Filter the movie meta key.
		 *
		 * @since 3.0.0
		 *
		 * @param string $key Meta key.
		 */
		$key = prefix( $key );
		$value = get_post_meta( $movie_id, $key, $single );
	} else {

		$values = array();

		$meta = get_post_meta( $movie_id );
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
 * Prefix movie meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key           Meta key.
 * @param boolean $strip_hyphens Replace hyphens with underscores?
 *
 * @return string
 */
function prefix( $key, $strip_hyphens = true ) {

	return utils\prefix_meta_key( $key, '_wpmoly_movie_', $strip_hyphens );
}

/**
 * Remove prefix from movie meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key               Prefixed meta key.
 * @param boolean $strip_underscores Replace underscores with hyphens?
 *
 * @return string
 */
function unprefix( $key, $strip_underscores = true ) {

	return utils\unprefix_meta_key( $key, '_wpmoly_movie_', $strip_underscores );
}

/**
 * Determine if the submitted meta key is a movie related meta key.
 *
 * @since 3.0.0
 *
 * @param string $key Prefixed meta key.
 *
 * @return boolean
 */
function is_meta_key( $key ) {

	$registered = utils\get_registered_movie_meta();

	$key = unprefix( $key, false );

	return array_key_exists( $key, (array) $registered );
}

/**
 * Retrieve movies archive page link.
 *
 * @since 3.0.0
 *
 * @param string $format URL format, 'relative' or 'absolute'.
 *
 * @return string|boolean
 */
function get_archive_link( $format = 'absolute' ) {

	$default = get_post_type_archive_link( 'movie' );
	if ( ! has_archives_page( 'movie' ) ) {
		return $default;
	}

	$page_id = get_archives_page_id();
	if ( false === $page_id ) {
		return $default;
	}

	$permalink = get_permalink( $page_id );
	if ( 'relative' == $format ) {
		$permalink = str_replace( home_url(), '', $permalink );
	}

	return $permalink;
}

/**
 * Retrieve 'movie' post type archive page ID.
 *
 * @since 3.0.0
 *
 * @return int
 */
function get_archives_page_id() {

	return utils\get_archives_page_id( 'movie' );
}

/**
 * Get 'movie' post type archive page if any.
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
 * Check if there is an archive page set for 'movie' post type.
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
 * Get a Movie Headbox template.
 *
 * Simple alias for get_headbox_template().
 *
 * @since 3.0.0
 *
 * @param int $movie Movie ID, object or array
 *
 * @return \wpmoly\templates\Headbox
 */
function get_headbox_template( $movie ) {

	return utils\get_headbox_template( $movie );
}
