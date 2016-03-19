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
 * @copyright 2016 CaerCam.org
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *                                Movies
 * 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**
 * Return a specific Movie.
 *
 * @since    2.1
 * 
 * @param    int|WP_Post    $post Optional. Post ID or post object. Defaults to global $post.
 * @param    string         $output Optional, default is Object. Accepts OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
 * @param    string         $filter Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return   WP_Post|null WP_Post on success or null on failure
 */
function wpmoly_get_movie( $post_id = null, $output = OBJECT, $filter = 'raw' ) {
	return WPMOLY_Movies::get_movie( $post_id, $output, $filter );
}

/**
 * Return a specific Movie.
 *
 * @since    2.1
 * 
 * @param    string       $movie_title Page title
 * @param    string       $output Optional. Output type. OBJECT, ARRAY_N, or ARRAY_A. Default OBJECT.
 * 
 * @return   WP_Post|null WP_Post on success or null on failure
 */
function wpmoly_get_movie_by_title( $movie_title, $output = OBJECT ) {
	return WPMOLY_Movies::get_movie_by_title( $movie_title, $output );
}

/**
 * Retrieve a list of Movies based on media
 * 
 * @since    2.1
 * 
 * @param    array    $args Arguments to retrieve movies
 * 
 * @return   array    Array of Post objects
 */
function wpmoly_get_movies( $args = null ) {
	return WPMOLY_Movies::get_movies( $args );
}

/**
 * Retrieve a list of Movies based on detail
 * 
 * @since    2.1
 * 
 * @param    string    $detail Detail to search upon
 * @param    string    $value Detail value 
 * 
 * @return   array     Array of Post objects
 */
function wpmoly_get_movies_by_detail( $detail, $value ) {
	return WPMOLY_Movies::get_movies_by_meta( $meta, $value );
}

/**
 * Retrieve a list of Movies based on meta
 * 
 * @since    2.1
 * 
 * @param    string    $meta Meta to search upon
 * @param    string    $value Meta value 
 * 
 * @return   array     Array of Post objects
 */
function wpmoly_get_movies_by_meta( $meta, $value ) {
	return WPMOLY_Movies::get_movies_by_meta( $meta, $value );
}

/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *                        Movies Meta & Details
 * 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

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
 * Return Movie's details.
 *
 * @since    2.1.3
 * 
 * @param    int    Movie Post ID
 *
 * @return   array|string    WPMOLY Movie details.
 */
function wpmoly_get_movie_details( $post_id = null ) {
	return WPMOLY_Movies::get_movie_meta( $post_id, 'details' );
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

/**
 * Check for a specific Movie Meta/Detail.
 * 
 * Has to be called inside the loop.
 *
 * @since    2.1.4.2
 * 
 * @param    string    Movie format.
 *
 * @return   boolean
 */
function wpmoly_has_movie_meta( $meta, $value ) {

	$metas = (array) WPMOLY_Movies::get_movie_meta( null, $meta );

	return in_array( $value, $metas );
}

/**
 * Check for a specific Movie media.
 * 
 * Alias for wpmoly_has_movie_meta().
 *
 * @since    2.1.4.2
 * 
 * @param    string    Movie media.
 *
 * @return   boolean
 */
function wpmoly_is_movie_media( $media ) {

	return wpmoly_has_movie_meta( 'media', $media );
}

/**
 * Check for a specific Movie status.
 * 
 * Alias for wpmoly_has_movie_meta().
 *
 * @since    2.1.4.2
 * 
 * @param    string    Movie status.
 *
 * @return   boolean
 */
function wpmoly_is_movie_status( $status ) {

	return wpmoly_has_movie_meta( 'status', $status );
}

/**
 * Check for a specific Movie rating.
 * 
 * Alias for wpmoly_has_movie_meta().
 *
 * @since    2.1.4.2
 * 
 * @param    string    Movie rating.
 *
 * @return   boolean
 */
function wpmoly_is_movie_rating( $rating ) {

	return wpmoly_has_movie_meta( 'rating', $rating );
}

/**
 * Check for a specific Movie language.
 * 
 * Alias for wpmoly_has_movie_meta().
 *
 * @since    2.1.4.2
 * 
 * @param    string    Movie language.
 *
 * @return   boolean
 */
function wpmoly_is_movie_language( $language ) {

	return wpmoly_has_movie_meta( 'language', $language );
}

/**
 * Check for a specific Movie subtitle.
 * 
 * Alias for wpmoly_has_movie_meta().
 *
 * @since    2.1.4.2
 * 
 * @param    string    Movie subtitle.
 *
 * @return   boolean
 */
function wpmoly_is_movie_subtitles( $subtitles ) {

	return wpmoly_has_movie_meta( 'subtitles', $subtitles );
}

/**
 * Check for a specific Movie format.
 * 
 * Alias for wpmoly_has_movie_meta().
 *
 * @since    2.1.4.2
 * 
 * @param    string    Movie format.
 *
 * @return   boolean
 */
function wpmoly_is_movie_format( $format ) {

	return wpmoly_has_movie_meta( 'format', $format );
}
