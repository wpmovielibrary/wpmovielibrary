<?php
/**
 * WPMovieLibrary Utils Class extension.
 * 
 * 
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Utils' ) ) :

	class WPML_Utils extends WPMovieLibrary {

		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', __CLASS__ . '::wpml_flush_rewrite_rules' );

			add_filter( 'wpml_format_widget_lists', __CLASS__ . '::wpml_format_widget_lists', 10, 4 );
			add_filter( 'wpml_format_widget_lists_thumbnails', __CLASS__ . '::wpml_format_widget_lists_thumbnails', 10, 1 );

			add_filter( 'wpml_filter_meta_data', __CLASS__ . '::wpml_filter_meta_data', 10, 1 );
			add_filter( 'wpml_filter_crew_data', __CLASS__ . '::wpml_filter_crew_data', 10, 1 );
			add_filter( 'wpml_filter_cast_data', __CLASS__ . '::wpml_filter_cast_data', 10, 1 );

			add_filter( 'wpml_stringify_array', __CLASS__ . '::wpml_stringify_array', 10, 3 );
			add_filter( 'wpml_filter_empty_array', __CLASS__ . '::wpml_filter_empty_array', 10, 1 );
			add_filter( 'wpml_filter_undimension_array', __CLASS__ . '::wpml_filter_undimension_array', 10, 1 );

			add_filter( 'get_the_terms', __CLASS__ . '::wpml_get_the_terms', 10, 3 );
			add_filter( 'wp_get_object_terms', __CLASS__ . '::wpml_get_ordered_object_terms', 10, 4 );
		}

		public static function admin_notice( $notice, $type = 'update' ) {

			if ( '' == $notice )
				return false;

			if ( ! in_array( $type, array( 'error', 'update', 'wpml' ) ) || 'update' == $type )
				$class = 'updated';
			else if ( 'wpml' == $type )
				$class = 'updated wpml';
			else if ( 'error' == $type )
				$class = 'error';

			echo '<div class="' . $class . '"><p>' . $notice . '</p></div>';
		}

		/**
		 * Return Movie's stored TMDb data.
		 * 
		 * @uses wpml_get_movie_postmeta()
		 *
		 * @since    1.0.0
		 *
		 * @return   array|string    WPML Movie TMDb data if stored, empty string else.
		 */
		public static function wpml_get_movie_data( $post_id = null ) {
			return WPML_Utils::wpml_get_movie_postmeta( 'data', $post_id );
		}

		/**
		 * Return Movie's Status.
		 * 
		 * @uses wpml_get_movie_postmeta()
		 *
		 * @since    1.0.0
		 *
		 * @return   array|string    WPML Movie Status if stored, empty string else.
		 */
		public static function wpml_get_movie_status( $post_id = null ) {
			return WPML_Utils::wpml_get_movie_postmeta( 'status', $post_id );
		}

		/**
		 * Return Movie's Media.
		 * 
		 * @uses wpml_get_movie_postmeta()
		 *
		 * @since    1.0.0
		 *
		 * @return   array|string    WPML Movie Media if stored, empty string else.
		 */
		public static function wpml_get_movie_media( $post_id = null ) {
			return WPML_Utils::wpml_get_movie_postmeta( 'media', $post_id );
		}

		/**
		 * Return Movie's Rating.
		 * 
		 * @uses wpml_get_movie_postmeta()
		 *
		 * @since    1.0.0
		 *
		 * @return   array|string    WPML Movie Rating if stored, empty string else.
		 */
		public static function wpml_get_movie_rating( $post_id = null ) {
			return WPML_Utils::wpml_get_movie_postmeta( 'rating', $post_id );
		}

		/**
		 * Return various Movie's Post Meta. Possible meta: status, media, rating
		 * and data.
		 *
		 * @since    1.0.0
		 *
		 * @return   array|string    WPML Movie Meta if available, empty string else.
		 */
		private static function wpml_get_movie_postmeta( $meta, $post_id = null ) {

			$allowed_meta = array( 'data', 'status', 'media', 'rating' );

			if ( is_null( $post_id ) )
				$post_id =  get_the_ID();

			if ( ! $post = get_post( $post_id ) || 'movie' != get_post_type( $post_id ) || ! in_array( $meta, $allowed_meta ) )
				return false;

			return get_post_meta( $post_id, "_wpml_movie_{$meta}", true );
		}

		/**
		 * Clean movie title prior to search.
		 * 
		 * Remove non-alphanumerical characters.
		 *
		 * @since     1.0.0
		 * 
		 * @param     string     $query movie title to clean up
		 * 
		 * @return     string     cleaned up movie title
		 */
		public static function wpml_clean_search_title( $query ) {
			$s = trim( $query );
			$s = preg_replace( '/[^\p{L}\p{N}\s]/u', '', $s );
			return $s;
		}

		/**
		 * Filter Hook
		 * 
		 * Used to generate Movies dropdown or classic lists.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array      $items Array of Movies objects
		 * @param    boolean    $dropdown Whether to return a dropdown or a regular list
		 * @param    boolean    $styling Add custom styling or not
		 * @param    string     $title First Option content if dropdown
		 * 
		 * @return   string     HTML string List of movies
		 */
		public static function wpml_format_widget_lists( $items, $dropdown = false, $styling = false, $title = null ) {

			if ( ! is_array( $items ) || empty( $items ) )
				return null;

			$html = array();
			$style = 'wpml-list';
			$first = '';

			if ( false !== $styling )
				$style = 'wpml-list custom';

			if ( ! is_null( $title ) )
				$first = sprintf( '<option value="">%s</option>', esc_attr( $title ) );

			foreach ( $items as $item ) {
				if ( $dropdown )
					$html[] = '<option value="' . esc_url( $item['link'] ) . '">' . esc_attr( $item['title'] ) . '</option>';
				else
					$html[] = '<li><a href="' . esc_url( $item['link'] ) . '" title="' . esc_attr( $item['attr_title'] ) . '">' . $item['title'] . '</a></li>';
			}

			if ( false !== $dropdown )
				$html = '<select class="' . $style . '">' . $first . join( $html ) . '</select>';
			else
				$html = '<ul>' . join( $html ) . '</ul>';

			return $html;
		}

		/**
		 * Filter Hook
		 * 
		 * Used to generate Movies lists including Poster.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $items Array of Movies objects
		 * 
		 * @return   string   HTML string of movies' links and Posters
		 */
		public static function wpml_format_widget_lists_thumbnails( $items ) {

			if ( ! is_array( $items ) || empty( $items ) )
				return null;

			$html = array();

			foreach ( $items as $item ) {
				$html[] = '<a href="' . esc_url( $item['link'] ) . '" title="' . esc_attr( $item['attr_title'] ) . '">';
				$html[] = '<figure class="widget-movie">';
				$html[] = get_the_post_thumbnail( $item['ID'], 'thumbnail' );
				$html[] = '</figure>';
				$html[] = '</a>';
			}

			$html = '<div class="widget-movies">' . implode( "\n", $html ) . '</div>';

			return $html;
		}

		/**
		 * Filter a Movie's Metadata to extract only supported data.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $array Movie metadata
		 * 
		 * @return   array    Filtered Metadata
		 */
		public static function wpml_filter_meta_data( $data ) {

			if ( ! is_array( $data ) || empty( $data ) )
				return $data;

			$filter = array();
			$_data = array();

			foreach ( WPML_Settings::wpml_get_supported_movie_meta( 'meta' ) as $slug => $f ) {
				$filter[] = $slug;
				$_data[ $slug ] = '';
			}

			foreach ( $data as $slug => $d ) {
				if ( in_array( $slug, $filter ) ) {
					if ( is_array( $d ) ) {
						foreach ( $d as $_d ) {
							if ( is_array( $_d ) && isset( $_d['name'] ) ) {
								$_data[ $slug ][] = $_d['name'];
							}
						}
					}
					else {
						$_data[ $slug ] = $d;
					}
				}
			}

			return $_data;
		}

		/**
		 * Filter a Movie's Crew to extract only supported data.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $array Movie Crew
		 * 
		 * @return   array    Filtered Crew
		 */
		public static function wpml_filter_crew_data( $data ) {

			if ( ! is_array( $data ) || empty( $data ) || ! isset( $data['crew'] ) )
				return $data;

			$filter = array();
			$_data = array();

			$cast = apply_filters( 'wpml_filter_cast_data', $data['cast'] );
			$data = $data['crew'];

			foreach ( WPML_Settings::wpml_get_supported_movie_meta( 'crew' ) as $slug => $f ) {
				$filter[ $slug ] = $f['title'];
				$_data[ $slug ] = '';
			}

			foreach ( $data as $i => $d )
				if ( isset( $d['job'] ) && false !== ( $key = array_search( $d['job'], $filter ) ) && isset( $_data[ $key ] ) )
					$_data[ $key ][] = $d['name'];

			$_data['cast'] = $cast;

			return $_data;
		}

		/**
		 * Filter a Movie's Cast to extract only supported data.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $array Movie Cast
		 * 
		 * @return   array    Filtered Cast
		 */
		public static function wpml_filter_cast_data( $data ) {

			if ( ! is_array( $data ) || empty( $data ) )
				return $data;

			foreach ( $data as $i => $d )
				$data[ $i ] = $d['name'];

			return $data;
		}

		/**
		 * Convert an Array shaped list to a separated string.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $array Array shaped list
		 * @param    string   $subrow optional subrow to select in subitems
		 * @param    string   $separator Separator string to use to implode the list
		 * 
		 * @return   string   Separated list
		 */
		public static function wpml_stringify_array( $array, $subrow = 'name', $separator = ', ' ) {

			if ( ! is_array( $array ) || empty( $array ) )
				return $array;

			foreach ( $array as $i => $row ) {
				if ( ! is_array( $row ) )
					$array[ $i ] = $row;
				else if ( false === $subrow || ! is_array( $row ) )
					$array[ $i ] = self::wpml_stringify_array( $row, $subrow, $separator );
				else if ( is_array( $row ) && isset( $row[ $subrow ] ) )
					$array[ $i ] = $row[ $subrow ];
				else if ( is_array( $row ) )
					$array[ $i ] = implode( $separator, $row );
			}

			$array = implode( $separator, $array );

			return $array;
		}

		/**
		 * Filter an array to detect empty associative arrays.
		 * Uses wpml_stringify_array to stringify the array and check its length.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $array Array to check
		 * 
		 * @return   array    Original array plus and notification row if empty
		 */
		public static function wpml_filter_empty_array( $array ) {

			if ( ! is_array( $array ) || empty( $array ) )
				return array();

			$_array = self::wpml_stringify_array( $array, false, '' );

			return strlen( $_array ) > 0 ? $array : array_merge( array( '_empty' => true ), $array );
		}

		/**
		 * Filter an array to remove any sub-array, reducing multidimensionnal
		 * arrays.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $array Array to check
		 * 
		 * @return   array    Reduced array
		 */
		public static function wpml_filter_undimension_array( $array ) {

			if ( ! is_array( $array ) || empty( $array ) )
				return $array;

			$_array = array();

			foreach ( $array as $key => $row ) {
				if ( is_array( $row ) )
					$_array = array_merge( $_array, self::wpml_filter_undimension_array( $row ) );
				else
					$_array[ $key ] = $row;
			}

			return $_array;
		}

		/**
		 * Sort Taxonomies by term_order.
		 * 
		 * Code from Luke Gedeon, see https://core.trac.wordpress.org/ticket/9547#comment:7
		 *
		 * @since    1.0.0
		 *
		 * @param    array      $terms array of objects to be replaced with sorted list
		 * @param    integer    $id post id
		 * @param    string     $taxonomy only 'post_tag' is changed.
		 * 
		 * @return   array      Terms array of objects
		 */
		public static function wpml_get_the_terms( $terms, $id, $taxonomy ) {

			if ( ! in_array( $taxonomy, array( 'collection', 'genre', 'actor' ) ) )
				return $terms;

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
		 * @since 1.0.0
		 *
		 * @param    int|array       $object_ids The ID(s) of the object(s) to retrieve.
		 * @param    string|array    $taxonomies The taxonomies to retrieve terms from.
		 * @param    array|string    $args Change what is returned
		 * 
		 * @return   array|WP_Error  The requested term data or empty array if no
		 *                           terms found. WP_Error if any of the $taxonomies
		 *                           don't exist.
		 */
		public static function wpml_get_ordered_object_terms( $terms, $object_ids, $taxonomies, $args ) {

			global $wpdb;

			$taxonomies = explode( ', ', str_replace( "'", "", $taxonomies ) );

			if ( empty( $object_ids ) || ( $taxonomies != "'collection', 'actor', 'genre'" && ( ! in_array( 'collection', $taxonomies ) && ! in_array( 'actor', $taxonomies ) && ! in_array( 'genre', $taxonomies ) ) ) )
				return $terms;

			foreach ( (array) $taxonomies as $taxonomy ) {
				if ( ! taxonomy_exists( $taxonomy ) )
					return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy' ) );
			}

			if ( ! is_array( $object_ids ) )
				$object_ids = array( $object_ids );
			$object_ids = array_map( 'intval', $object_ids );

			$defaults = array('orderby' => 'term_order', 'order' => 'ASC', 'fields' => 'all');
			$args = wp_parse_args( $args, $defaults );

			$terms = array();
			if ( count($taxonomies) > 1 ) {
				foreach ( $taxonomies as $index => $taxonomy ) {
					$t = get_taxonomy($taxonomy);
					if ( isset($t->args) && is_array($t->args) && $args != array_merge($args, $t->args) ) {
						unset($taxonomies[$index]);
						$terms = array_merge($terms, $this->wpml_get_ordered_object_terms($object_ids, $taxonomy, array_merge($args, $t->args)));
					}
				}
			}
			else {
				$t = get_taxonomy($taxonomies[0]);
				if ( isset($t->args) && is_array($t->args) )
					$args = array_merge($args, $t->args);
			}

			extract($args, EXTR_SKIP);

			$orderby = "ORDER BY term_order";
			$order = 'ASC';

			$taxonomies = "'" . implode("', '", $taxonomies) . "'";
			$object_ids = implode(', ', $object_ids);

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

			if ( ! $terms )
				$terms = array();

			return $terms;
		}

		/**
		 * Flush WordPress Rewrite Rules on plugin activation.
		 *
		 * @since    1.0.0
		 */
		public static function wpml_flush_rewrite_rules() {

			if ( false !== get_transient( '_wpml_just_activated' ) ) {
				flush_rewrite_rules();
				delete_transient( '_wpml_just_activated' );
			}
		}

	}

endif;