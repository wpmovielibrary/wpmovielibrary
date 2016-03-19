<?php
/**
 * WPMovieLibrary AJAX functions.
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

/**
 * Pre-handle AJAX Callbacks results to detect errors
 * 
 * Execute the callback and filter the result to prepare the AJAX
 * response. If errors are detected, return a WP_Error instance.
 * If no error, return the callback results.
 * 
 * @param    mixed    $callback Array containing Callback Class and Method or simple string for functions
 * @param    array    $args Array of arguments for callback
 * 
 * @return   array|WP_Error    Array of callback results if no error,
 *                             WP_Error instance if anything went wrong.
 */
function wpmoly_ajax_filter( $callback, $args = array(), $loop = false ) {

	$loop = ( true === $loop ? true : false );
	$response = array();
	$errors = new WP_Error();

	// Simple function callback
	if ( ! is_array( $callback ) && function_exists( esc_attr( $callback ) ) ) {
		// Loop through the arg
		if ( $loop && is_array( $args ) && ! empty( $args ) ) {
			foreach ( $args[0] as $arg ) {
				$_response = call_user_func_array( $callback, array( $arg ) );
				if ( is_wp_error( $_response ) )
					$errors->add( $_response->get_error_code(), $_response->get_error_message() );
				else
					$response[] = $_response;
			}
		}
		// Single callback call
		else {
			$_response = call_user_func_array( $callback, $args );
			if ( is_wp_error( $_response ) )
				$errors->add( $_response->get_error_code(), $_response->get_error_message() );
			else
				$response[] = $_response;
		}
	}
	// Class Method callback
	else if ( is_array( $callback ) && 2 == count( $callback ) && class_exists( $callback[0] ) && method_exists( $callback[0], $callback[1] ) ) {
		// Loop through the arg
		if ( $loop && is_array( $args ) && ! empty( $args ) ) {
			foreach ( $args[0] as $arg ) {
				$_response = call_user_func_array( array( $callback[0], $callback[1] ), array( $arg ) );
				if ( is_wp_error( $_response ) )
					$errors->add( $_response->get_error_code(), $_response->get_error_message() );
				else
					$response[] = $_response;
			}
		}
		// Single callback call
		else {
			$_response = call_user_func_array( array( $callback[0], $callback[1] ), $args );
			if ( is_wp_error( $_response ) )
				$errors->add( $_response->get_error_code(), $_response->get_error_message() );
			else
				$response[] = $_response;
		}
	}
	else
		$errors->add( 'callback_error', __( 'An error occured when trying to perform the request: invalid callback or data.', 'wpmovielibrary' ) );

	if ( ! empty( $errors->errors ) )
		$response = $errors;

	return $response;
}

/**
 * Handle AJAX Callbacks results, prepare and format the AJAX 
 * response and display it.
 * 
 * TODO: give priority to Nonce in args
 * 
 * @param    array    $response Array containing Callback results data
 * @param    array    $i18n Array containing Callback optional i18n
 */
function wpmoly_ajax_response( $response, $i18n = array(), $nonce = null ) {

	if ( is_wp_error( $response ) )
		$_response = $response;
	else if ( ! is_object( $response ) && ! is_int( $response ) && ! is_array( $response ) && true !== $response )
		$_response = new WP_Error( 'callback_error', __( 'An error occured when trying to perform the request.', 'wpmovielibrary' ) );
	else
		$_response = new WPMOLY_Ajax( array( 'data' => $response, 'i18n' => $i18n, 'nonce' => $nonce ) );

	wpmoly_json_header( is_wp_error( $_response ) );

	wp_die( json_encode( $_response ) );
}