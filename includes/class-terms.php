<?php
/**
 * 
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */

namespace wpmoly;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Terms {

	/**
	 * Sort Taxonomies by term_order.
	 *
	 * Code from Luke Gedeon, see https://core.trac.wordpress.org/ticket/9547#comment:7
	 *
	 * @since    1.0
	 *
	 * @param    array     $terms array of objects to be replaced with sorted list
	 * @param    int       $id post id
	 * @param    string    $taxonomy only 'post_tag' is changed.
	 *
	 * @return   array     Terms array of objects
	 */
	public function get_the_terms( $terms, $id, $taxonomy ) {

		// Only apply to movie-related taxonomies
		if ( ! in_array( $taxonomy, array( 'collection', 'genre', 'actor' ) ) ) {
			return $terms;
		}

		// Term ordering is killing quick/bulk edit, avoid it
		if ( is_admin() && ( function_exists( 'get_current_screen' ) && 'edit-movie' == get_current_screen()->id ) ) {
			return $terms;
		}

		$terms = wp_cache_get( $id, "{$taxonomy}_relationships_sorted" );
		if ( false === $terms ) {
			$terms = wp_get_object_terms( $id, $taxonomy, array( 'orderby' => 'term_order' ) );
			wp_cache_add( $id, $terms, $taxonomy . '_relationships_sorted' );
		}

		return $terms;
	}

	/**
	 * Retrieves the terms associated with the given object(s), in the
	 * supplied taxonomies.
	 *
	 * This is a copy of WordPress' wp_get_object_terms function with a bunch
	 * of edits to use term_order as a default sorting param.
	 *
	 * @since    1.0
	 *
	 * @param    array           $terms The post's terms
	 * @param    int|array       $object_ids The ID(s) of the object(s) to retrieve.
	 * @param    string|array    $taxonomies The taxonomies to retrieve terms from.
	 * @param    array|string    $args Change what is returned
	 *
	 * @return   array|WP_Error  The requested term data or empty array if no
	 *                           terms found. WP_Error if any of the $taxonomies
	 *                           don't exist.
	 */
	public function get_ordered_object_terms( $terms, $object_ids, $taxonomies, $args ) {

		$total = count( $terms );
		$original_terms = $terms;

		// Term ordering is killing quick/bulk edit, avoid it
		if ( is_admin() && function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( isset( $screen->id ) && 'edit-movie' == $screen->id ) {
				return $terms;
			}
		}

		$taxonomies = explode( ', ', str_replace( "'", "", $taxonomies ) );
		if ( empty( $object_ids ) || ( $taxonomies != "'collection', 'actor', 'genre'" && ( ! in_array( 'collection', $taxonomies ) && ! in_array( 'actor', $taxonomies ) && ! in_array( 'genre', $taxonomies ) ) ) ) {
			return $terms;
		}

		global $wpdb;

		foreach ( (array) $taxonomies as $taxonomy ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy' ) );
			}
		}

		$object_ids = array_map( 'intval', (array) $object_ids );
		$defaults = array( 'orderby' => 'term_order', 'order' => 'ASC', 'fields' => 'all' );
		$args = wp_parse_args( $args, $defaults );

		$terms = array();
		if ( count( $taxonomies ) > 1 ) {
			foreach ( $taxonomies as $index => $taxonomy ) {
				$t = get_taxonomy( $taxonomy );
				if ( isset( $t->args ) && is_array( $t->args ) && $args != array_merge( $args, $t->args ) ) {
					unset( $taxonomies[ $index ] );
					$terms = array_merge( $terms, $this->get_ordered_object_terms( $object_ids, $taxonomy, array_merge( $args, $t->args ) ) );
				}
			}
		} else {
			$t = get_taxonomy( $taxonomies[0] );
			if ( isset( $t->args ) && is_array( $t->args ) ) {
				$args = array_merge( $args, $t->args );
			}
		}

		extract($args, EXTR_SKIP);
		$orderby     = "ORDER BY tr.term_order";
		$order       = 'ASC';
		$taxonomies  = "'" . implode( "', '", $taxonomies ) . "'";
		$object_ids  = implode( ', ', $object_ids );
		$select_this = '';

		if ( 'all' == $fields )
		$select_this = 't.*, tt.*';
		else if ( 'ids' == $fields )
		$select_this = 't.term_id';
		else if ( 'names' == $fields )
		$select_this = 't.name';
		else if ( 'slugs' == $fields )
		$select_this = 't.slug';
		else if ( 'all_with_object_id' == $fields )
		$select_this = 't.*, tt.*, tr.object_id';

		$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ($taxonomies) AND tr.object_id IN ($object_ids) $orderby $order";

		if ( 'all' == $fields || 'all_with_object_id' == $fields ) {
			$_terms = $wpdb->get_results( $query );
			foreach ( $_terms as $key => $term ) {
				$_terms[$key] = sanitize_term( $term, $taxonomy, 'raw' );
			}
			$terms = array_merge( $terms, $_terms );
			update_term_cache( $terms );
		} else if ( 'ids' == $fields || 'names' == $fields || 'slugs' == $fields ) {
			$_terms = $wpdb->get_col( $query );
			$_field = ( 'ids' == $fields ) ? 'term_id' : 'name';
			foreach ( $_terms as $key => $term ) {
				$_terms[$key] = sanitize_term_field( $_field, $term, $term, $taxonomy, 'raw' );
			}
			$terms = array_merge( $terms, $_terms );
		} else if ( 'tt_ids' == $fields ) {
			$terms = $wpdb->get_col("SELECT tr.term_taxonomy_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tr.object_id IN ($object_ids) AND tt.taxonomy IN ($taxonomies) $orderby $order");
			foreach ( $terms as $key => $tt_id ) {
				$terms[$key] = sanitize_term_field( 'term_taxonomy_id', $tt_id, 0, $taxonomy, 'raw' ); // 0 should be the term id, however is not needed when using raw context.
			}
		}

		if ( ! $terms ) {
			$terms = array();
		}

		if ( $total != count( $terms ) ) {
			$terms = $original_terms;
		}

		return $terms;
	}

}
