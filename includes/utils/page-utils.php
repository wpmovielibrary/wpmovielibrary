<?php
/**
 * The file that defines the page utils functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\utils\page;

use wpmoly\utils;

/**
 * Prefix page meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key           Meta key.
 * @param boolean $strip_hyphens Replace hyphens with underscores?
 *
 * @return string
 */
function prefix( $key, $strip_hyphens = true ) {

	return utils\prefix_meta_key( $key, '_wpmoly_', $strip_hyphens );
}

/**
 * Remove prefix from page meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key               Prefixed meta key.
 * @param boolean $strip_underscores Replace underscores with hyphens?
 *
 * @return string
 */
function unprefix( $key, $strip_underscores = true ) {

	return utils\unprefix_meta_key( $key, '_wpmoly_', $strip_underscores );
}

/**
 * Determine if the submitted value is a page meta key.
 *
 * @since 3.0.0
 *
 * @param string $key Prefixed meta key.
 *
 * @return boolean
 */
function is_meta_key( $key ) {

	$registered = utils\get_registered_page_meta();

	$key = unprefix( $key, false );

	return array_key_exists( $key, $registered );
}
