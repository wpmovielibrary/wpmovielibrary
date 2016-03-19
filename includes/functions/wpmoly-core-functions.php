<?php
/**
 * WPMovieLibrary Core functions.
 * 
 * 
 * 
 * @since     1.3
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

require WPMOLY_PATH . 'includes/functions/wpmoly-movies-functions.php';
require WPMOLY_PATH . 'includes/functions/wpmoly-ajax-functions.php';
require WPMOLY_PATH . 'includes/functions/wpmoly-legacy-functions.php';

/**
 * Filter a string value to determine a suitable boolean value.
 * 
 * This is mostly used for Shortcodes where boolean-like values
 * can be used.
 * 
 * @since    1.1.0
 * 
 * @param    string    Value to filter
 * 
 * @return   boolean   Filtered value
 */
function wpmoly_is_boolean( $value, $default = false ) {

	$value = strtolower( $value );

	$true = array( 'true', true, 'yes', '1', 1 );
	$false = array( 'false', false, 'no', '0', 0 );

	foreach ( $true as $t )
		if ( $value === $t )
			return true;

	foreach ( $false as $f )
		if ( $value === $f )
			return false;

	return $default;
}

/**
 * Escape a string to use in SQL LIKE.
 * like_escape() is deprecated since WordPress 4.0 which introduces a $wpdb
 * method. This is for compatibility easiness with WP<4.x
 * 
 * @since    2.1
 * 
 * @param    string    $string Data to escape
 * 
 * @return   string    Escape string
 */
function wpmoly_esc_like( $string ) {

	global $wpdb;

	if ( method_exists( 'wpdb', 'esc_like' ) )
		$string = $wpdb->esc_like( $string );
	else
		$string = like_escape( $letter );

	return $string;
}

/**
 * Convert an Array shaped list to a separated string.
 * 
 * @since    1.0
 * 
 * @param    array    $array Array shaped list
 * @param    string   $subrow optional subrow to select in subitems
 * @param    string   $separator Separator string to use to implode the list
 * 
 * @return   string   Separated list
 */
function wpmoly_stringify_array( $array, $subrow = 'name', $separator = ', ' ) {

	if ( ! is_array( $array ) || empty( $array ) )
		return $array;

	foreach ( $array as $i => $row ) {
		if ( ! is_array( $row ) )
			$array[ $i ] = $row;
		else if ( false === $subrow || ! is_array( $row ) )
			$array[ $i ] = wpmoly_stringify_array( $row, $subrow, $separator );
		else if ( is_array( $row ) && isset( $row[ $subrow ] ) )
			$array[ $i ] = $row[ $subrow ];
		else if ( is_array( $row ) )
			$array[ $i ] = implode( $separator, $row );
	}

	$array = implode( $separator, $array );

	return $array;
}

/**
 * Filter an array to detect empty associative arrays.
 * Uses wpmoly_stringify_array to stringify the array and check its length.
 * 
 * @since    1.0
 * 
 * @param    array    $array Array to check
 * 
 * @return   array    Original array plus and notification row if empty
 */
function wpmoly_filter_empty_array( $array ) {

	if ( ! is_array( $array ) || empty( $array ) )
		return array();

	$_array = wpmoly_stringify_array( $array, false, '' );

	return strlen( $_array ) > 1 ? $array : array_merge( array( '_empty' => true ), $array );
}

/**
 * Filter an array to remove any sub-array, reducing multidimensionnal
 * arrays.
 * 
 * @since    1.0
 * 
 * @param    array    $array Array to check
 * 
 * @return   array    Reduced array
 */
function wpmoly_filter_undimension_array( $array ) {

	if ( ! is_array( $array ) || empty( $array ) )
		return $array;

	$_array = array();

	foreach ( $array as $key => $row ) {
		if ( is_array( $row ) )
			$_array = array_merge( $_array, wpmoly_filter_undimension_array( $row ) );
		else
			$_array[ $key ] = $row;
	}

	return $_array;
}

/**
 * Provide a plugin-wide, generic method for generating nonce.
 *
 * @since    1.0
 * 
 * @param    string    $action Action name for nonce
 * 
 * @return   string    Nonce
 */
function wpmoly_create_nonce( $action ) {

	return wp_create_nonce( 'wpmoly-' . $action );
}

/**
 * Provide a plugin-wide, generic method for generating nonce URLs.
 *
 * @since    2.1
 * 
 * @param    string    $actionurl Action URL for nonce
 * @param    string    $action Action name for nonce
 * 
 * @return   string    Nonce URL
 */
function wpmoly_nonce_url( $actionurl, $action ) {

	$nonce_action = 'wpmoly-' . $action;
	$nonce_name = '_wpmolynonce_' . str_replace( '-', '_', $action );

	return wp_nonce_url( $actionurl, $nonce_action, $nonce_name );
}

/**
 * Provide a plugin-wide, generic method for generating nonce fields.
 *
 * @since    1.0
 * 
 * @param    string    $action Action name for nonce
 * 
 * @return   string    Nonce field
 */
function wpmoly_nonce_field( $action, $referer = true, $echo = true ) {

	$nonce_action = 'wpmoly-' . $action;
	$nonce_name = '_wpmolynonce_' . str_replace( '-', '_', $action );

	return wp_nonce_field( $nonce_action, $nonce_name, $referer, $echo );
}

/**
 * Provide a plugin-wide, generic method for nonce verification.
 *
 * @since    2.1
 * 
 * @param    string    $nonce Nonce to verify
 * @param    string    $action Action name for nonce
 * 
 * @return   boolean    Nonce verification result
 */
function wpmoly_verify_nonce( $nonce, $action ) {

	$nonce_action = 'wpmoly-' . $action;

	return wp_verify_nonce( $nonce, $nonce_action );
}

/**
 * Provide a plugin-wide, generic method for checking admin nonces.
 *
 * @since    1.0
 * 
 * @param    string    $action Action name for nonce
 * 
 * @return   boolean    Nonce check result
 */
function wpmoly_check_admin_referer( $action, $query_arg = false ) {

	if ( ! $query_arg )
		$query_arg = '_wpmolynonce_' . str_replace( '-', '_', $action );

	$error = new WP_Error();
	$check = check_ajax_referer( 'wpmoly-' . $action, $query_arg );

	if ( $check )
		return true;

	$error->add( 'invalid_nonce', __( 'Are you sure you want to do this?' ) );

	return $error;
}

/**
 * Provide a plugin-wide, generic method for checking AJAX nonces.
 *
 * @since    1.0
 * 
 * @param    string    $action Action name for nonce
 */
function wpmoly_check_ajax_referer( $action, $query_arg = false, $die = false ) {

	if ( ! $query_arg )
		$query_arg = 'nonce';

	$error = new WP_Error();
	$check = check_ajax_referer( 'wpmoly-' . $action, $query_arg, $die );

	if ( $check )
		return true;

	$error->add( 'invalid_nonce', __( 'Are you sure you want to do this?' ) );
	wpmoly_ajax_response( $error, null, wpmoly_create_nonce( $action ) );
}

/**
 * Application/JSON headers content-type.
 * If no header was sent previously, send new header.
 *
 * @since    1.0
 * 
 * @param    boolean    $error Error header or normal?
 */
function wpmoly_json_header( $error = false ) {

	if ( false !== headers_sent() )
		return false;

	if ( $error ) {
		header( 'HTTP/1.1 500 Internal Server Error' );
		header( 'Content-Type: application/json; charset=UTF-8' );
	}	
	else {
		header( 'Content-type: application/json' );
	}
}
