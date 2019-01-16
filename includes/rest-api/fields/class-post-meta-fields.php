<?php
/**
 * REST API:Post_Meta_Fields Controller class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\rest\fields;

use WP_Http;
use WP_Error;
use WP_REST_Post_Meta_Fields;

/**
 * Core class used to manage meta values for posts via the REST API.
 *
 * Replace WP_REST_Post_Meta_Fields to fix bug #42810.
 *
 * @see https://core.trac.wordpress.org/ticket/42810
 * @see https://core.trac.wordpress.org/ticket/42069
 *
 * @since 3.0.0
 *
 * @see WP_REST_Post_Meta_Fields
 */
class Post_Meta_Fields extends WP_REST_Post_Meta_Fields {

  /**
   * Updates a meta value for an object.
   *
   * @since 3.0.0
   *
   * @param int    $object_id Object ID to update.
   * @param string $meta_key  Key for the custom field.
   * @param string $name      Name for the field that is exposed in the REST API.
   * @param mixed  $value     Updated value.
   * @return bool|WP_Error True if the meta field was updated, WP_Error otherwise.
   */
  protected function update_meta_value( $object_id, $meta_key, $name, $value ) {
    $meta_type = $this->get_meta_type();
    if ( ! current_user_can(  "edit_{$meta_type}_meta", $object_id, $meta_key ) ) {
      return new WP_Error(
        'rest_cannot_update',
        /* translators: %s: custom field key */
        sprintf( __( 'Sorry, you are not allowed to edit the %s custom field.' ), $name ),
        array( 'key' => $name, 'status' => rest_authorization_required_code() )
      );
    }

    $meta_key   = wp_slash( $meta_key );
    $meta_value = wp_slash( $value );

    // Do the exact same check for a duplicate value as in update_metadata() to avoid update_metadata() returning false.
    $old_value = get_metadata( $meta_type, $object_id, $meta_key );

    if ( 1 === count( $old_value ) ) {
      // Fix bug #42810.
      if ( (string) sanitize_meta( $meta_key, wp_unslash( $meta_value ), $meta_type ) === $old_value[0] ) {
        return true;
      }
      // Consequence of bug #42810: details are not compared correctly.
      if ( is_string( $meta_value ) && is_array( $old_value[0] ) ) {
        if ( $meta_value === implode( ', ', $old_value[0] ) ) {
          return true;
        }
      }
    }

    if ( ! update_metadata( $meta_type, $object_id, $meta_key, $meta_value ) ) {
      return new WP_Error(
        'rest_meta_database_error',
        __( 'Could not update meta value in database.' ),
        array( 'key' => $name, 'status' => WP_Http::INTERNAL_SERVER_ERROR )
      );
    }

    return true;
  }
}
