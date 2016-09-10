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

	$options = wpmoly\Core\Options::get_instance();

	return $options->get( $name, $default );
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

	$data = new $object( $data );

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
 * Return a grid object.
 * 
 * @since    3.0
 * 
 * @param    mixed    $grid Grid ID, object or array
 * 
 * @return   Grid|boolean
 */
function get_grid( $grid ) {

	return _get_object( $grid, '\wpmoly\Node\Grid' );
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

/**
 * Get a translation country instance.
 * 
 * @since    3.0
 * 
 * @param    string    $country Country name or ISO code
 * 
 * @return   \wpmoly\Helpers\Country
 */
function get_country( $country ) {

	return \wpmoly\Helpers\Country::get( $country );
}

/**
 * Get a translation language instance.
 * 
 * @since    3.0
 * 
 * @param    string    $language Language name or ISO code
 * 
 * @return   \wpmoly\Helpers\Language
 */
function get_language( $language ) {

	return \wpmoly\Helpers\Language::get( $language );
}

/**
 * Get a Movie Headbox instance.
 * 
 * Actually returns a Shortcode instance like used for the [movie] Shortcode.
 * 
 * @since    3.0
 * 
 * @param    int    $post_id Post ID
 * 
 * @return   \wpmoly\Shortcodes\Movie
 */
function get_movie_headbox( $post_id = null ) {

	if ( is_null( $post_id ) ) {
		$post_id = get_the_ID();
	}

	$headbox = new \wpmoly\Shortcodes\Movie( array(
		'id' => $post_id
	) );
	$headbox->run();

	return $headbox;
}

/**
 * Retrieve 'actor' taxonomy archive page ID.
 * 
 * @since    3.0
 * 
 * @param    int    $default Default ID.
 * 
 * @return   int
 */
function get_actor_archives_page_id( $default = 0 ) {

	return $post_id = wpmoly_o( 'actor-archives', (int) $default );
}

/**
 * Get 'actor' taxonomy archive page if any.
 * 
 * @since    3.0
 * 
 * @return   WP_Post|null
 */
function get_actor_archives_page() {

	$post_id = get_actor_archives_page_id();

	return $page = get_post( $post_id );
}

/**
 * Check if there is an archive page set for 'actor' taxonomy.
 * 
 * @since    3.0
 * 
 * @return   int
 */
function has_actor_archives_page() {

	$page = get_actor_archives_page();

	return ! is_null( $page );
}

/**
 * Retrieve 'collection' taxonomy archive page ID.
 * 
 * @since    3.0
 * 
 * @param    int    $default Default ID.
 * 
 * @return   int
 */
function get_collection_archives_page_id( $default = 0 ) {

	return $post_id = wpmoly_o( 'collection-archives', (int) $default );
}

/**
 * Get 'collection' taxonomy archive page if any.
 * 
 * @since    3.0
 * 
 * @return   WP_Post|null
 */
function get_collection_archives_page() {

	$post_id = get_collection_archives_page_id();

	return $page = get_post( $post_id );
}

/**
 * Check if there is an archive page set for 'collection' taxonomy.
 * 
 * @since    3.0
 * 
 * @return   int
 */
function has_collection_archives_page() {

	$page = get_collection_archives_page();

	return ! is_null( $page );
}

/**
 * Retrieve 'genre' taxonomy archive page ID.
 * 
 * @since    3.0
 * 
 * @param    int    $default Default ID.
 * 
 * @return   int
 */
function get_genre_archives_page_id( $default = 0 ) {

	return $post_id = wpmoly_o( 'genre-archives', (int) $default );
}

/**
 * Get 'genre' taxonomy archive page if any.
 * 
 * @since    3.0
 * 
 * @return   WP_Post|null
 */
function get_genre_archives_page() {

	$post_id = get_genre_archives_page_id();

	return $page = get_post( $post_id );
}

/**
 * Check if there is an archive page set for 'genre' taxonomy.
 * 
 * @since    3.0
 * 
 * @return   int
 */
function has_genre_archives_page() {

	$page = get_genre_archives_page();

	return ! is_null( $page );
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
