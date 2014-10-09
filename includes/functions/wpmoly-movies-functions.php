<?php
/**
 * WPMovieLibrary Movies functions.
 * 
 * 
 * @since     2.0
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Return Movie's stored TMDb data.
 *
 * @since    1.0
 * 
 * @param    int    Movie Post ID
 *
 * @return   array|string    WPMOLY Movie TMDb data if stored, empty string else.
 */
function wpmoly_get_movie_meta( $post_id = null, $meta = 'data' ) {
	return WPMOLY_Movies::get_movie_meta( $post_id, $meta );
}

/**
 * Return Movie's Status.
 *
 * @since    1.0
 * 
 * @param    int    Movie Post ID
 *
 * @return   array|string    WPMOLY Movie Status if stored, empty string else.
 */
function wpmoly_get_movie_status( $post_id = null ) {
	return WPMOLY_Movies::get_movie_meta( $post_id, 'status' );
}

/**
 * Return Movie's Media.
 *
 * @since    1.0
 * 
 * @param    int    Movie Post ID
 *
 * @return   array|string    WPMOLY Movie Media if stored, empty string else.
 */
function wpmoly_get_movie_media( $post_id = null ) {
	return WPMOLY_Movies::get_movie_meta( $post_id, 'media' );
}

/**
 * Return Movie's Rating.
 *
 * @since    1.0
 * 
 * @param    int    Movie Post ID
 *
 * @return   array|string    WPMOLY Movie Rating if stored, empty string else.
 */
function wpmoly_get_movie_rating( $post_id = null ) {
	return WPMOLY_Movies::get_movie_meta( $post_id, 'rating' );
}

/**
 * Return Movie's Language.
 *
 * @since    2.0
 * 
 * @param    int    Movie Post ID
 *
 * @return   array|string    WPMOLY Movie Language if stored, empty string else.
 */
function wpmoly_get_movie_language( $post_id = null ) {
	return WPMOLY_Movies::get_movie_meta( $post_id, 'language' );
}

/**
 * Return Movie's Subtitles.
 *
 * @since    2.0
 * 
 * @param    int    Movie Post ID
 *
 * @return   array|string    WPMOLY Movie Subtitles if stored, empty string else.
 */
function wpmoly_get_movie_subtitles( $post_id = null ) {
	return WPMOLY_Movies::get_movie_meta( $post_id, 'subtitles' );
}

/**
 * Return Movie's Format.
 *
 * @since    2.0
 * 
 * @param    int    Movie Post ID
 *
 * @return   array|string    WPMOLY Movie Format if stored, empty string else.
 */
function wpmoly_get_movie_format( $post_id = null ) {
	return WPMOLY_Movies::get_movie_meta( $post_id, 'format' );
}
