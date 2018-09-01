<?php

namespace wpmoly\rest;

use WP_Error;
use \wpmoly\core\Settings;

/**
 * Validate settings.
 *
 * Fallback function for settings validation.
 *
 * @since 3.0.0
 *
 * @param  mixed           $value   The value for the setting.
 * @param  WP_REST_Request $request The request object.
 * @param  string          $name    The setting name.
 *
 * @return boolean
 */
function validate_settings( $value, $request, $name ) {

	/**
	 * Validate setting value.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed  $value Setting value.
	 * @param string $name  Setting name.
	 */
	$is_valid = apply_filters( "wpmoly/filter/rest/validate/{$name}/value", $value, $name );

	return $is_valid;
}

/**
 * Validate settings expecting a boolean value.
 *
 * @since 3.0.0
 *
 * @param string $value Setting value.
 *
 * @return boolean
 */
function expect_boolean_value( $value ) {

	return is_bool( $value );
}

/**
 * Validate settings expecting a string value.
 *
 * @since 3.0.0
 *
 * @param string $value Setting value.
 *
 * @return boolean
 */
function expect_string_value( $value ) {

	return is_string( $value );
}

/**
 * Validate settings expecting a value from a list.
 *
 * @since 3.0.0
 *
 * @param string $value Setting value.
 * @param array  $enum  Allowed values.
 *
 * @return boolean
 */
function expect_enum_value( $value, $enum ) {

	$enum = (array) $enum;
	if ( isset( $enum[ $value ] ) ) {
		return true;
	}

	return false;
}

/**
 * Validate 'license_key' setting value.
 *
 * @since 3.0.0
 *
 * @param string $license_key 'license_key' setting value.
 *
 * @return boolean
 */
function validate_license_key( $license_key ) {

	$license_key = (string) $license_key;
	if ( 32 === strlen( $license_key ) ) {
		return true;
	}

	return false;
}

/**
 * Validate 'movie_poster_size' setting value.
 *
 * @since 3.0.0
 *
 * @param boolean $value Setting value.
 *
 * @return boolean
 */
function validate_movie_poster_size( $value ) {

	$settings = Settings::get_instance();
	$settings = $settings->get_setting_field( 'movie_poster_size' );
	if ( ! $settings || empty( $settings['options'] ) ) {
		return false;
	}

	$is_valid = expect_enum_value( $value, $settings['options'] );

	return $is_valid;
}

/**
 * Validate 'movie_backdrop_size' setting value.
 *
 * @since 3.0.0
 *
 * @param boolean $value Setting value.
 *
 * @return boolean
 */
function validate_movie_backdrop_size( $value ) {

	$settings = Settings::get_instance();
	$settings = $settings->get_setting_field( 'movie_backdrop_size' );
	if ( ! $settings || empty( $settings['options'] ) ) {
		return false;
	}

	$is_valid = expect_enum_value( $value, $settings['options'] );

	return $is_valid;
}

/**
 * Validate 'picture_size' setting value.
 *
 * @since 3.0.0
 *
 * @param boolean $value Setting value.
 *
 * @return boolean
 */
function validate_picture_size( $value ) {

	$settings = Settings::get_instance();
	$settings = $settings->get_setting_field( 'picture_size' );
	if ( ! $settings || empty( $settings['options'] ) ) {
		return false;
	}

	$is_valid = expect_enum_value( $value, $settings['options'] );

	return $is_valid;
}

/**
 * Validate 'api_key' setting value.
 *
 * @since 3.0.0
 *
 * @param string $key 'api_key' setting value.
 *
 * @return boolean
 */
function validate_api_key( $key ) {

	$key = (string) $key;
	if ( 32 === strlen( $key ) || empty( $key ) ) {
		return true;
	}

	return false;
}

/**
 * Validate 'api_language' setting value.
 *
 * @since 3.0.0
 *
 * @param string $language 'api_language' setting value.
 *
 * @return boolean
 */
function validate_api_language( $language ) {

	$language = get_language( (string) $language );
	if ( ! empty( $language->code ) ) {
		return true;
	}

	return false;
}

/**
 * Validate 'api_country' setting value.
 *
 * @since 3.0.0
 *
 * @param string $country 'api_country' setting Value.
 *
 * @return boolean
 */
function validate_api_country( $country ) {

	$country = get_country( (string) $country );
	if ( ! empty( $country->code ) ) {
		return true;
	}

	return false;
}

/**
 * Validate 'api_alternative_country' setting value.
 *
 * @since 3.0.0
 *
 * @param string $country 'api_alternative_country' setting value.
 *
 * @return boolean
 */
function validate_api_alternative_country( $country ) {

	return empty( $country ) || validate_api_country( $country );
}

/**
 * Validate 'permalinks' setting value.
 *
 * @since 3.0.0
 *
 * @param string $permalinks 'permalinks' setting value.
 *
 * @return boolean
 */
function validate_permalinks( $permalinks ) {

	if ( is_array( $permalinks ) ) {
		return true;
	}

	return false;
}

/**
 * Validate 'archive_pages' setting value.
 *
 * @since 3.0.0
 *
 * @param string $archive_pages 'archive_pages' setting value.
 *
 * @return boolean
 */
function validate_archive_pages( $archive_pages ) {

	if ( ! is_array( $archive_pages ) ) {
		return false;
	}

	$error = new WP_Error;

	$allowed_types = array( 'movie', 'genre', 'actor' );

	$archive_pages = (array) $archive_pages;
	foreach ( $archive_pages as $page_id => $type ) {

		if ( ! in_array( $type, $allowed_types, true ) ) {
			$error->add( 'invalid_archive_type', sprintf( __( "'%s' is not a valid archive type. Allowed types: '%s'.", 'wpmovielibrary' ), $type, implode( "', '", $allowed_types ) ), $archive_pages );
		}

		$page = get_post( $page_id );
		if ( is_null( $page ) ) {
			$error->add( 'invalid_archive_page', __( 'Invalid post ID.' ), $archive_pages );
		}
	}

	if ( empty( $error->errors ) ) {
		return true;
	}

	return $error;
}
