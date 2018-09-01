<?php

namespace wpmoly\rest;

use WP_Error;
use \wpmoly\core\Settings;

/**
 * Sanitize settings.
 *
 * Fallback function for settings sanitization.
 *
 * @since 3.0.0
 *
 * @param mixed           $value   The value for the setting.
 * @param  WP_REST_Request $request The request object.
 * @param  string          $name    The setting name.
 *
 * @return mixed
 */
function sanitize_settings( $value, $request, $name ) {

	/**
	 * Validate setting value.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed  $value Setting value.
	 * @param string $name  Setting name.
	 */
	$value = apply_filters( "wpmoly/filter/rest/sanitize/{$name}/value", $value, $name );

	return $value;
}

/**
 * Sanitize boolean values.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_boolean_value( $value ) {

	$value = rest_sanitize_boolean( $value );

	return $value;
}

/**
 * Sanitize values from a list.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 * @param array  $enum  Allowed values.
 *
 * @return boolean
 */
function sanitize_enum_value( $value, $enum ) {

	$enum = (array) $enum;
	if ( empty( $enum[ $value ] ) ) {
		$value = null;
	}

	return $value;
}

/**
 * Sanitize 'license_key' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_license_key( $value ) {

	$value = (string) $value;
	if ( 32 !== strlen( $value ) ) {
		$value = null;
	}

	return $value;
}

/**
 * Sanitize 'date_format' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_date_format( $value ) {

	$value = sanitize_option( 'date_format', $value );

	return $value;
}

/**
 * Sanitize 'time_format' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_time_format( $value ) {

	$value = sanitize_option( 'time_format', $value );

	return $value;
}

/**
 * Sanitize 'movie_poster_size' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_movie_poster_size( $value ) {

	$settings = Settings::get_instance();
	$settings = $settings->get_setting_field( 'movie_poster_size' );
	if ( ! $settings || empty( $settings['options'] ) ) {
		return null;
	}

	$value = sanitize_enum_value( $value, $settings['options'] );
	if ( is_null( $value ) && ! empty( $settings['default'] ) ) {
		$value = $settings['default'];
	}

	return $value;
}

/**
 * Sanitize 'movie_poster_title' setting value.
 *
 * @TODO filter image title tags.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_movie_poster_title( $value ) {

	$value = wp_strip_all_tags( $value );

	return $value;
}

/**
 * Sanitize 'movie_poster_description' setting value.
 *
 * @TODO filter image description tags.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_movie_poster_description( $value ) {

	$value = wp_strip_all_tags( $value );

	return $value;
}

/**
 * Sanitize 'movie_backdrop_size' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_movie_backdrop_size( $value ) {

	$settings = Settings::get_instance();
	$settings = $settings->get_setting_field( 'movie_backdrop_size' );
	if ( ! $settings || empty( $settings['options'] ) ) {
		return null;
	}

	$value = sanitize_enum_value( $value, $settings['options'] );
	if ( is_null( $value ) && ! empty( $settings['default'] ) ) {
		$value = $settings['default'];
	}

	return $value;
}

/**
 * Sanitize 'movie_backdrop_title' setting value.
 *
 * @TODO filter image title tags.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_movie_backdrop_title( $value ) {

	$value = wp_strip_all_tags( $value );

	return $value;
}

/**
 * Sanitize 'movie_backdrop_description' setting value.
 *
 * @TODO filter image description tags.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_movie_backdrop_description( $value ) {

	$value = wp_strip_all_tags( $value );

	return $value;
}

/**
 * Sanitize 'picture_size' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_picture_size( $value ) {

	$settings = Settings::get_instance();
	$settings = $settings->get_setting_field( 'picture_size' );
	if ( ! $settings || empty( $settings['options'] ) ) {
		return null;
	}

	$value = sanitize_enum_value( $value, $settings['options'] );
	if ( is_null( $value ) && ! empty( $settings['default'] ) ) {
		$value = $settings['default'];
	}

	return $value;
}

/**
 * Sanitize 'picture_title' setting value.
 *
 * @TODO filter image title tags.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_picture_title( $value ) {

	$value = wp_strip_all_tags( $value );

	return $value;
}

/**
 * Sanitize 'picture_description' setting value.
 *
 * @TODO filter image description tags.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_picture_description( $value ) {

	$value = wp_strip_all_tags( $value );

	return $value;
}

/**
 * Sanitize 'api_key' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_api_key( $key ) {

	$key = (string) $key;
	if ( 32 !== strlen( $key ) || empty( $key ) ) {
		$key = null;
	}

	return $key;
}

/**
 * Sanitize 'api_language' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_api_language( $value ) {

	$language = get_language( (string) $value );
	if ( empty( $language->code ) ) {
		$value = null;
	}

	return $value;
}

/**
 * Sanitize 'api_country' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_api_country( $value ) {

	$country = get_country( (string) $value );
	if ( empty( $country->code ) ) {
		$value = null;
	}

	return $value;
}

/**
 * Sanitize 'api_alternative_country' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_api_alternative_country( $value ) {

	return sanitize_api_country( $value );
}

/**
 * Sanitize 'permalinks' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_permalinks( $permalinks ) {

	if ( is_object( $permalinks ) ) {
		$permalinks = get_object_vars( $permalinks );
	}

	if ( ! is_array( $permalinks ) ) {
		$permalinks = (array) $permalinks;
	}

	return $permalinks;
}

/**
 * Sanitize 'archive_pages' setting value.
 *
 * @since 3.0.0
 *
 * @param mixed $value Setting value.
 *
 * @return boolean
 */
function sanitize_archive_pages( $pages ) {

	if ( is_object( $pages ) ) {
		$pages = get_object_vars( $pages );
	}

	if ( ! is_array( $pages ) ) {
		$pages = (array) $pages;
	}

	return $pages;
}

function sanitize_movie_details( $value ) {

	if ( is_array( $value ) || is_object( $value ) ) {
		return $value;
	}

	$values = explode( ',', (string) $value );
	$values = array_map( 'trim', $values );

	return $values;
}
