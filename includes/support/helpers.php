<?php
/**
 * Define the helper functions for the plugin.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary\Support\Helpers;

/**
 * Get a configuration value.
 * 
 * @since 6.0.0
 * 
 * @param string $name The name of the configuration value, using dot notation for nested values.
 * @param mixed $default The default value to return if the configuration value is not found.
 * 
 * @return mixed The configuration value, or the default value if not found.
 */
function config( string $name, $default = null ) {

	$name = explode( '.', $name );
	$name = array_map( 'sanitize_key', $name );
	$file = array_shift( $name );

	$config_file = WPMOLY_PATH . 'config/' . $file . '.php';

	if ( ! file_exists( $config_file ) ) {
		return $default;
	}

	$config = require( $config_file );
	if ( empty( $name ) ) {
		return $config;
	}

	foreach ( $name as $segment ) {
		if ( ! isset( $config[ $segment ] ) ) {
			return $default;
		}
		$config = $config[ $segment ];
	}

	return $config ?? $default;
}
