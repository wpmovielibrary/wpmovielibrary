<?php

namespace wpmoly\Helpers\Permalinks;

use wpmoly\Permalink;

/**
 * Build a permalink for dates.
 * 
 * Uses \wpmoly\Permalink() to generate custom URLs for release dates and local
 * release dates.
 * 
 * @since    3.0
 * 
 * @param    string    $date Filtered date.
 * @param    array     $raw_date Unfiltered date
 * @param    array     $date_parts Date parts, if need be
 * @param    string    $date_format Date format
 * @param    int       $timestamp Date UNIX Timestam
 * 
 * @return   string
 */
function date_permalink( $date, $raw_date = array(), $date_parts = array(), $date_format = '', $timestamp = '' ) {

	if ( empty( $raw_date ) ) {
		return $date;
	}

	$permalink = new Permalink;
	switch ( $date_format ) {
		case 'Y':
			$permalink->setID( 'date' );
			$permalink->setContent( date_i18n( $date_format, $timestamp ) );
			$permalink->setTitle( $date );
			$permalink->setTitleAttr( sprintf( __( 'Movies release in %s', 'wpmovielibrary' ), date_i18n( $date_format, $timestamp ) ) );
			break;
		case 'j F':
			$permalink->setID( 'date' );
			$permalink->setContent( date_i18n( 'Y-m', $timestamp ) );
			$permalink->setTitle( $date );
			$permalink->setTitleAttr( sprintf( __( 'Movies release in %s', 'wpmovielibrary' ), date_i18n( $date_format, $timestamp ) ) );
			break;
		case 'j F Y':
			$month = date_permalink( $date_parts[0], $raw_date, $date_parts, $date_format = 'j F', $timestamp );
			$year  = date_permalink( $date_parts[1], $raw_date, $date_parts, $date_format = 'Y',   $timestamp );
			$permalink = $month . ' ' . $year;
			break;
		default:
			break;
	}

	if ( ! $permalink instanceof Permalink ) {
		return $permalink;
	}

	return $permalink->toHTML();
}

/**
 * Add a meta link to the movie meta value
 * 
 * @since    2.0
 * 
 * @param    array     $args Link parameters
 * 
 * @return   string    Formatted output
 */
/*public static function add_meta_link( $args ) {

	$defaults = array(
		'key'   => null,
		'value' => null,
		'type'  => 'meta',
		'text'  => null,
		'title' => null,
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	if ( ! wpmoly_o( 'meta-links' ) || 'nowhere' == wpmoly_o( 'meta-links' ) || ( 'posts_only' == wpmoly_o( 'meta-links' ) && ! is_single() ) )
		return $text;

	if ( is_null( $key ) || is_null( $value ) || '' == $value )
		return $value;

	$baseurl = get_post_type_archive_link( 'movie' );
	$link = explode( ',', $value );
	foreach ( $link as $i => $value ) {

		$value = trim( $value );
		$link[ $i ] = self::get_meta_permalink( compact( 'key', 'value', 'text', 'type', 'title', 'baseurl' ) );
	}

	$link = implode( ', ', $link );

	return $link;
}*/

/**
 * Generate Custom Movie Meta permalinks
 * 
 * @since    1.0
 * 
 * @param    string    $args Permalink parameters
 * 
 * @return   string    HTML href of raw URL
 */
/*public static function get_meta_permalink( $args ) {

	$defaults = array(
		'key'     => null,
		'value'   => null,
		'text'    => null,
		'type'    => null,
		'format'  => null,
		'baseurl' => null,
		'title'   => null
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	if ( ! in_array( $type, array( 'meta', 'detail' ) ) )
		return null;

	if ( 'raw' !== $format )
		$format = 'html';

	if ( is_null( $title ) )
		$title = $value;

	if ( is_null( $text ) )
		$text = $value;

	$args = array(
		$type     => $key,
		'value'   => $value,
		'baseurl' => $baseurl
	);

	$url = self::build_meta_permalink( $args );

	if ( 'raw' == $format )
		return $url;

	$permalink = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $url, $title, $text );

	return $permalink;
}*/

/**
 * Build Meta URL. Use an array of parameter to build a custom
 * URLs for meta queries.
 * 
 * @since    2.1.1
 * 
 * @param    array     $args URL parameters to use
 * 
 * @return   string    Custom URL
 */
/*public static function build_meta_permalink( $args ) {

	global $wp_rewrite;
	$rewrite = ( '' != $wp_rewrite->permalink_structure );

	$defaults = array(
		'baseurl' => get_permalink(),
		'number'  => null,
		'columns' => null,
		'rows'    => null,
		'order'   => null,
		'orderby' => null,
		'paged'   => null,
		'meta'    => null,
		'detail'  => null,
		'value'   => null,
		'letter'  => null,
		'is_tax'  => false,
		'view'    => null
	);
	$args = wp_parse_args( $args, $defaults );

	$args['type'] = '';
	if ( '' != $args['meta'] && '' != $args['value'] ) {
		$args['type'] = 'meta';
	}
	else if ( '' != $args['detail'] && '' != $args['value'] ) {
		$args['type'] = 'detail';
		$args['meta'] = $args['detail'];
	}

	if ( '1' == wpmoly_o( 'rewrite-enable' ) ) {
		if ( 'production_countries' == $args['meta'] ) {
			$args['value'] = WPMOLY_L10n::get_country_standard_name( $args['value'] );
		} else if ( 'spoken_languages' == $args['meta'] ) {
			$args['value'] = WPMOLY_L10n::get_language_standard_name( $args['value'] );
		}
		$args['value'] = __( $args['value'], 'wpmovielibrary-iso' );
	}

	if ( 'rating' != $args['meta'] )
		$args['value'] = sanitize_title( $args['value'] );

	$args['meta'] = WPMOLY_L10n::translate_rewrite( $args['meta'] );
	$args['value'] = WPMOLY_L10n::translate_rewrite( $args['value'] );

	$url = '';
	if ( $rewrite )
		$url = self::build_custom_meta_permalink( $args );
	else
		$url = self::build_default_meta_permalink( $args );

	return $url;
}*/

/**
 * Build a custom meta permalink for custom permalinks settings
 * 
 * This generate a user-friendly URL to access meta-based archive
 * pages.
 * 
 * @since    2.1.1
 * 
 * @param    array    $args URL parameters
 * 
 * @return   string   Generated URL
 */
/*private static function build_custom_meta_permalink( $args ) {

	extract( $args );

	$movies = wpmoly_o( 'rewrite-movie' );
	if ( ! $movies )
		$movies = 'movies';

	$url = array();

	if ( '' != $meta && '' != $value ) {
		$url[] = $meta;
		$url[] = $value;
	}

	$grid = 'grid';
	if ( '' != $view )
		$grid = $view;

	if ( '1' == wpmoly_o( 'rewrite-enable' ) )
		$grid = __( $grid, 'wpmovielibrary' );

	$url[] = $grid;

	if ( '' != $letter )
		$url[] = $letter;

	if ( '' != $columns && '' != $rows )
		$url[] = "$columns:$rows";
	else if ( '' != $number )
		$url[] = $number;

	if ( '' != $orderby )
		$url[] = $orderby;

	if ( '' != $order )
		$url[] = $order;

	if ( 1 < $paged )
		$url[] = "page/$paged";

	if ( $grid == end( $url ) )
		$grid = array_pop( $url );

	$url = implode( '/', $url );
	$url = esc_url( $baseurl . $url );

	return $url;
}*/

/**
 * Build a custom meta permalink for default permalinks settings
 * 
 * This generate a meta URL with raw URL parameters instead of
 * nice user-friendly URLs if the user chose not to use WordPress
 * permalinks.
 * 
 * @since    2.1.1
 * 
 * @param    array    $args URL parameters
 * 
 * @return   string   Generated URL
 */
/*private static function build_default_meta_permalink( $args ) {

	if ( false !== $args['is_tax'] )
		$type = $args['is_tax'];
	else
		$type = 'movie';

	if ( ! isset( $args['baseurl'] ) || empty( $args['baseurl'] ) ) {
		$page = intval( wpmoly_o( $type . '-archives' ) );
		$base  = 'index.php?';
		if ( $page )
			$base .= 'page_id=' . $page . '&';
		$base = home_url( "/${base}" );
	} else {
		$base = $args['baseurl'];
	}

	$url = array();

	if ( '' != $args['type'] && '' != $args['meta'] && '' != $args['value'] ) {
		$url[ $args['type'] ] = $args['meta'];
		$url['value'] = $args['value'];
	}

	unset( $args['type'], $args['meta'], $args['value'], $args['baseurl'], $args['is_tax'] );

	foreach ( $args as $slug => $arg )
		if ( '' != $arg )
			$url[ $slug ] = $arg;

	$url = esc_url( add_query_arg( $url, $base ) );

	return $url;
}*/
