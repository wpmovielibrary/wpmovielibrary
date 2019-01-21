<?php
/**
 * The file that defines the person utils functions.
 *
 * @link https://wppersonlibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\utils\person;

use wpmoly\utils;

/**
 * Return a person object.
 *
 * @since 3.0.0
 *
 * @param mixed $person Person ID, object or array
 *
 * @return Person|boolean
 */
function get( $person ) {

	return utils\get_node( $person, '\wpmoly\nodes\posts\Person' );
}

/**
 * Return a person metadata.
 *
 * @since 3.0.0
 *
 * @param int     $person_id Person ID, object or array.
 * @param string  $key       Person Meta key to return.
 * @param boolean $single    Whether to return a single value.
 *
 * @return Person|boolean
 */
function get_meta( $person_id, $key = '', $single = true ) {

	$key = (string) $key;
	$value = '';

	$post_type = get_post_type( (int) $person_id );
	if ( 'person' !== $post_type ) {
		return $value;
	}

	if ( ! empty( $key ) ) {

		/**
		 * Filter the person meta key.
		 *
		 * @since 3.0.0
		 *
		 * @param string $key Meta key.
		 */
		$key = prefix( $key );
		$value = get_post_meta( $person_id, $key, $single );
	} else {

		$values = array();

		$meta = get_post_meta( $person_id );
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
 * Prefix person meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key           Meta key.
 * @param boolean $strip_hyphens Replace hyphens with underscores?
 *
 * @return string
 */
function prefix( $key, $strip_hyphens = true ) {

	return utils\prefix_meta_key( $key, '_wpmoly_person_', $strip_hyphens );
}

/**
 * Remove prefix from person meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key               Prefixed meta key.
 * @param boolean $strip_underscores Replace underscores with hyphens?
 *
 * @return string
 */
function unprefix( $key, $strip_underscores = true ) {

	return utils\unprefix_meta_key( $key, '_wpmoly_person_', $strip_underscores );
}

/**
 * Determine if the submitted meta key is a person related meta key.
 *
 * @since 3.0.0
 *
 * @param string $key Prefixed meta key.
 *
 * @return boolean
 */
function is_meta_key( $key ) {

	$registered = utils\get_registered_person_meta();

	$key = unprefix( $key, false );

	return array_key_exists( $key, (array) $registered );
}

/**
 * Retrieve persons archive page link.
 *
 * @since 3.0.0
 *
 * @param string $format URL format, 'relative' or 'absolute'.
 *
 * @return string|boolean
 */
function get_archive_link( $format = 'absolute' ) {

	$link = get_post_type_archive_link( 'person' );
	if ( 'relative' == $format ) {
		$link = str_replace( home_url(), '', $link );
	}

	return $link;
}

/**
 * Retrieve 'person' post type archive page ID.
 *
 * @since 3.0.0
 *
 * @return int
 */
function get_archives_page_id() {

	return utils\get_archives_page_id( 'person' );
}

/**
 * Get 'person' post type archive page if any.
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
 * Check if there is an archive page set for 'person' post type.
 *
 * @since 3.0.0
 *
 * @return boolean
 */
function has_archives_page() {

	$page = get_archives_page();

	return ! is_null( $page );
}
