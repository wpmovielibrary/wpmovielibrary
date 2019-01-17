<?php
/**
 * The file that defines the settings utils functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\utils\settings;

/**
 * Prefix settings option name.
 *
 * @since 3.0.0
 *
 * @param string  $name          Option name.
 * @param string  $prefix        Option name prefix.
 * @param boolean $strip_hyphens Replace hyphens with underscores?
 *
 * @return string
 */
function prefix( $name, $prefix = '', $strip_hyphens = true ) {

	$name = (string) $name;

	if ( true === $strip_hyphens ) {
		$name = str_replace( '-', '_', $name );
	}

	if ( empty( $prefix ) ) {
		$prefix = '_wpmoly_';
	}

	$name = $prefix . $name;

	return $name;
}

/**
 * Unprefix settings option name.
 *
 * @since 3.0.0
 *
 * @param string  $name              Option name.
 * @param string  $prefix            Option name prefix.
 * @param boolean $strip_underscores Replace underscores with hyphens?
 *
 * @return string
 */
function unprefix( $name, $prefix = '', $strip_underscores = false ) {

	$name = (string) $name;
	if ( empty( $prefix ) ) {
		$prefix = '_wpmoly_';
	}

	$name = str_replace( $prefix, '', $name );

	if ( true === $strip_underscores ) {
		$name = str_replace( '_', '-', $name );
	}

	return $name;
}
