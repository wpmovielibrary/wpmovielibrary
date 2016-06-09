<?php
/**
 * The file that defines the plugin public functions.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */

/**
 * Get WPMovieLibrary instance.
 * 
 * @since    3.0
 * 
 * @return   Library
 */
function get_wpmoly() {

	$wpmoly = wpmoly\Library::get_instance();

	return $wpmoly;
}

/**
 * Retrieve a specific option.
 * 
 * @since    3.0
 * 
 * @param    string    $name Option name
 * @param    mixed     $default Option default value to return if needed
 * 
 * @return   mixed
 */
function wpmoly_o( $name, $default = null ) {

	$wpmoly = wpmoly\Library::get_instance();

	return $wpmoly->options->get( $name, $default );
}

/**
 * Retrieve a specific option in a boolean form.
 * 
 * @since    3.0
 * 
 * @param    string    $name Option name
 * @param    mixed     $default Option default value to return if needed
 * 
 * @return   boolean
 */
function wpmoly_is_o( $name, $default = null ) {

	$value = wpmoly_o( $name, $default );

	return _is_bool( $value );
}


/**
 * Return a WPMoly-defined object.
 * 
 * @since    3.0
 * 
 * @param    mixed    $data Node ID, object or array
 * 
 * @return   object
 */
function _get_object( $data, $object ) {

	if ( ! class_exists( $object ) ) {
		return (object) $data;
	}

	if ( $data instanceof $object ) {
		return $data;
	}

	if ( ( is_object( $data ) || is_array( $data ) ) ) {
		return new $object( $data );
	}

	$data = $object::get_instance( $data );

	return $data;
}

/**
 * Return a movie object.
 * 
 * @since    3.0
 * 
 * @param    mixed    $movie Movie ID, object or array
 * 
 * @return   Movie|boolean
 */
function get_movie( $movie ) {

	return _get_object( $movie, '\wpmoly\Node\Movie' );
}

/**
 * Return a movie metadata.
 * 
 * @since    3.0
 * 
 * @param    int        $movie_id Movie ID, object or array
 * @param    string     $key Movie Meta key to return.
 * @param    boolean    $single Whether to return a single value
 * 
 * @return   Movie|boolean
 */
function get_movie_meta( $movie_id, $key = '', $single = false ) {

	$key = (string) $key;

	$value = '';
	if ( 'movie' != get_post_type( $movie_id ) ) {
		return $value;
	}

	if ( ! empty( $key ) ) {
		$key = '_wpmoly_movie_' . $key;
	}

	$value = get_post_meta( $movie_id, $key, $single );

	return $value;
}

function get_country( $country ) {

	return \wpmoly\Helpers\Country::get( $country );
}

/**
 * Strictly merge user defined arguments into defaults array.
 * 
 * This function is a alternative to wp_parse_args() to merge arrays strictly,
 * ie without adding used arguments that are not explicitely defined in the
 * default array.
 * 
 * @since    3.0
 * 
 * @param    string|array    $args Value to merge with $detaults
 * @param    string|array    $defaults Array that serves as the defaults
 * 
 * @return   array           Strictly merged array
 */
function parse_args_strict( $args, $defaults ) {

	if ( is_object( $args ) ) {
		$r = get_object_vars( $args );
	} elseif ( is_array( $args ) ) {
		$r =& $args;
	} else {
		wp_parse_str( $args, $r );
	}

	if ( is_array( $defaults ) ) {
		_parse_args_strict( $r, $defaults );
	}

	return $r;
}

/**
 * Strictly merge arrays. Any key from $args that is not present in $default
 * will be stripped from the result array.
 * 
 * @since    3.0
 * 
 * @param    array    $args Array to merge with $detaults
 * @param    array    $default Array that serves as the defaults
 * 
 * @return   array    Strictly merged array
 */
function _parse_args_strict( $args, $default ) {

	$parsed = array();
	foreach ( $default as $key => $value ) {
		if ( isset( $args[ $key ] ) ) {
			$parsed[ $key ] = $args[ $key ];
		} else {
			$parsed[ $key ] = $value;
		}
	}

	return $parsed;
}

/**
 * Literal boolean check.
 * 
 * @since    3.0
 * 
 * @param    mixed    $var
 * 
 * @return   boolean
 */
function _is_bool( $var ) {

	if ( ! is_string( $var ) ) {
		return (boolean) $var;
	}

	return in_array( strtolower( $var ), array( '1', 'y', 'on', 'yes', 'true' ) );
}
