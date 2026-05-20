<?php
/**
 * Define the helper functions for the plugin.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

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
