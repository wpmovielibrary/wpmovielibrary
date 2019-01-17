<?php
/**
 * The file that defines the utils functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\utils;

/**
 * Retrieve a specific option.
 *
 * @since 3.0.0
 *
 * @param string $name Option name
 * @param mixed $default Option default value to return if needed
 *
 * @return mixed
 */
function o( $name, $default = null ) {

	$options = \wpmoly\core\Settings::get_instance();

	return $options->get( $name, $default );
}

/**
 * Retrieve a specific option in a boolean form.
 *
 * @since 3.0.0
 *
 * @param string $name Option name
 * @param mixed $default Option default value to return if needed
 *
 * @return boolean
 */
function is_o( $name, $default = null ) {

	$value = o( $name, $default );

	return is_bool( $value );
}


/**
 * Return a WPMoly-defined object.
 *
 * @since 3.0.0
 *
 * @param mixed $data Node ID, object or array
 *
 * @return object
 */
function get_node( $data, $object ) {

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
 * Return a headbox object.
 *
 * $object has to be an \wpmoly\nodes\headboxes\Headbox instance in order to be handled
 * correctly. Headboxes support both Terms and Posts, need to make an
 * early distinction between the two.
 *
 * TODO handle int parameter
 *
 * @since 3.0.0
 *
 * @param object $headbox Headbox object.
 *
 * @return Headbox|boolean
 */
function get_headbox( $headbox ) {

	if ( isset( $headbox->post ) ) {
		return get_post_headbox( $headbox );
	} elseif ( isset( $headbox->term ) ) {
		return get_term_headbox( $headbox );
	}

	return false;
}

/**
 * Return a post headbox object.
 *
 * @since 3.0.0
 *
 * @param mixed $post Post ID, object or array
 *
 * @return PostHeadbox|boolean
 */
function get_post_headbox( $post ) {

	return get_node( $post, '\wpmoly\nodes\headboxes\Post' );
}

/**
 * Return a term headbox object.
 *
 * @since 3.0.0
 *
 * @param mixed $term Term ID, object or array
 *
 * @return TermHeadbox|boolean
 */
function get_term_headbox( $term ) {

	return get_node( $term, '\wpmoly\nodes\headboxes\Term' );
}

/**
 * Prefix meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key           Meta key.
 * @param string  $prefix        Meta key prefix.
 * @param boolean $strip_hyphens Replace hyphens with underscores?
 *
 * @return string
 */
function prefix_meta_key( $key, $prefix = '', $strip_hyphens = true ) {

	$key = (string) $key;

	if ( true === $strip_hyphens ) {
		$key = str_replace( '-', '_', $key );
	}

	if ( ! empty( $prefix ) ) {
		$key = $prefix . $key;
	}

	return $key;
}

/**
 * Remove prefix from meta keys.
 *
 * @since 3.0.0
 *
 * @param string  $key               Prefixed meta key.
 * @param string  $prefix            Meta key prefix.
 * @param boolean $strip_underscores Replace underscores with hyphens?
 *
 * @return string
 */
function unprefix_meta_key( $key, $prefix = '', $strip_underscores = true ) {

	$key = (string) $key;
	if ( ! empty( $prefix ) ) {
		$key = str_replace( $prefix, '', $key );
	}

	if ( true === $strip_underscores ) {
		$key = str_replace( '_', '-', $key );
	}

	return $key;
}

/**
 * Get a translation country instance.
 *
 * @since 3.0.0
 *
 * @param string $country Country name or ISO code
 *
 * @return \wpmoly\utils\Country
 */
function get_country( $country ) {

	return Country::get( $country );
}

/**
 * Get a translation language instance.
 *
 * @since 3.0.0
 *
 * @param string $language Language name or ISO code
 *
 * @return \wpmoly\utils\Language
 */
function get_language( $language ) {

	return Language::get( $language );
}

/**
 * Check if a specific page is an archive page.
 *
 * @since 3.0.0
 *
 * @param int $post_id Page Post ID.
 *
 * @return boolean
 */
function is_archive_page( $post_id ) {

	$pages = get_option( '_wpmoly_archive_pages', array() );

	return in_array( $post_id, (array) $pages, true );
}

/**
 * Find the archive type corresponding to a page ID.
 *
 * @since 3.0.0
 *
 * @param int $post_id Page Post ID.
 *
 * @return string|boolean
 */
function get_archive_page_type( $post_id ) {

	if ( is_archive_page( $post_id ) ) {
		$pages = get_option( '_wpmoly_archive_pages', array() );
		return array_search( $post_id, (array) $pages );
	}

	return false;
}

/**
 * Retrieve taxonomies archive page links.
 *
 * If the submitted taxonomy does not have a set archive page, return false.
 * Otherwise, return the page's permalink with or without the site home url
 * depending on $format.
 *
 * @since 3.0.0
 *
 * @param string $type   Taxonomy type.
 * @param string $format URL format, 'relative' or 'absolute'.
 *
 * @return string|boolean
 */
function get_taxonomy_archive_link( $type = '', $format = 'absolute' ) {

	if ( ! has_archives_page( $type ) ) {
		return '';
	}

	$page_id = get_archives_page_id( $type );
	if ( false === $page_id ) {
		return false;
	}

	$permalink = get_permalink( $page_id );
	if ( 'relative' == $format ) {
		$permalink = str_replace( home_url(), '', $permalink );
	}

	return $permalink;
}

/**
 * Retrieve an archive page ID.
 *
 * @since 3.0.0
 *
 * @param string $type Archive type.
 *
 * @return int
 */
function get_archives_page_id( $type = '' ) {

	$pages = get_option( '_wpmoly_archive_pages', array() );
	if ( ! empty( $pages[ $type ] ) ) {
		return (int) $pages[ $type ];
	}

	return false;
}

/**
 * Get a post type archive page if any.
 *
 * @since 3.0.0
 *
 * @param string $type Archive type.
 *
 * @return WP_Post|null
 */
function get_archives_page( $type = '' ) {

	$post_id = get_archives_page_id( $type );
	if ( false !== $post_id ) {
		return get_post( $post_id );
	}

	return null;
}

/**
 * Check if there is an archive page set.
 *
 * @since 3.0.0
 *
 * @param string $type Archive type.
 *
 * @return boolean
 */
function has_archives_page( $type = '' ) {

	$page = get_archives_page( $type );
	if ( is_null( $page ) ) {
		return false;
	}

	return true;
}

/**
 * Strictly merge user defined arguments into defaults array.
 *
 * This function is a alternative to wp_parse_args() to merge arrays strictly,
 * ie without adding used arguments that are not explicitely defined in the
 * default array.
 *
 * @since 3.0.0
 *
 * @param string|array $args     Value to merge with $detaults
 * @param string|array $defaults Array that serves as the defaults
 *
 * @return array           Strictly merged array
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
 * @since 3.0.0
 *
 * @param array $args    Array to merge with $detaults
 * @param array $default Array that serves as the defaults
 *
 * @return array Strictly merged array
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
 * @since 3.0.0
 *
 * @param mixed $var
 *
 * @return boolean
 */
function is_bool( $var ) {

	if ( ! is_string( $var ) ) {
		return (boolean) $var;
	}

	return in_array( strtolower( $var ), array( '1', 'y', 'on', 'yes', 'true' ) );
}
