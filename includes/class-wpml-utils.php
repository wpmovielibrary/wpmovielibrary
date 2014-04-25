<?php
/**
 * WPMovieLibrary Utils Class extension.
 * 
 * This class contains various tools needed by WPML such as array manipulating
 * filters, terms ordering methods or permalinks creation.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Utils' ) ) :

	class WPML_Utils extends WPML_Module {

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

			add_filter( 'rewrite_rules_array', __CLASS__ . '::register_permalinks', 11 );

			add_filter( 'wpml_format_widget_lists', __CLASS__ . '::format_widget_lists', 10, 4 );
			add_filter( 'wpml_format_widget_lists_thumbnails', __CLASS__ . '::format_widget_lists_thumbnails', 10, 1 );

			add_filter( 'wpml_summarize_settings', __CLASS__ . '::summarize_settings', 10, 1 );

			add_filter( 'wpml_filter_meta_data', __CLASS__ . '::filter_meta_data', 10, 1 );
			add_filter( 'wpml_filter_crew_data', __CLASS__ . '::filter_crew_data', 10, 1 );
			add_filter( 'wpml_filter_cast_data', __CLASS__ . '::filter_cast_data', 10, 1 );

			add_filter( 'wpml_stringify_array', __CLASS__ . '::stringify_array', 10, 3 );
			add_filter( 'wpml_filter_empty_array', __CLASS__ . '::filter_empty_array', 10, 1 );
			add_filter( 'wpml_filter_undimension_array', __CLASS__ . '::filter_undimension_array', 10, 1 );

			add_filter( 'get_the_terms', __CLASS__ . '::get_the_terms', 10, 3 );
			add_filter( 'wp_get_object_terms', __CLASS__ . '::get_ordered_object_terms', 10, 4 );
		}

		/**
		 * Create a new set of permalinks for Movie Details
		 * 
		 * We want to list movies by media, status and rating. This method
		 * is called whenever permalinks are edited using the filter
		 * hook 'rewrite_rules_array'.
		 * 
		 * This also add permalink structures for custom taxonomies as
		 * they seem not to be declared correctly when usin the regular
		 * register_taxonomy 'rewrite' param.
		 *
		 * @since    1.0.0
		 *
		 * @param    object     $wp_rewrite Instance of WordPress WP_Rewrite Class
		 */
		public static function register_permalinks( $rules = null ) {

			$new_rules = array(
				'movies/(dvd|vod|bluray|vhs|cinema|other)/?$' => 'index.php?post_type=movie&wpml_movie_media=$matches[1]',
				'movies/(dvd|vod|bluray|vhs|cinema|other)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_media=$matches[1]',
				'movies/(available|loaned|scheduled)/?$' => 'index.php?post_type=movie&wpml_movie_status=$matches[1]',
				'movies/(available|loaned|scheduled)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_status=$matches[1]&paged=$matches[2]',
				'movies/(0.0|0.5|1.0|1.5|2.0|2.5|3.0|3.5|4.0|4.5|5.0)/?$' => 'index.php?post_type=movie&wpml_movie_rating=$matches[1]',
				'movies/(0.0|0.5|1.0|1.5|2.0|2.5|3.0|3.5|4.0|4.5|5.0)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_rating=$matches[1]&paged=$matches[2]',
				'collection/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?collection=$matches[1]&feed=$matches[2]',
				'collection/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?collection=$matches[1]&feed=$matches[2]',
				'collection/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?collection=$matches[1]&paged=$matches[2]',
				'collection/([^/]+)/?$' => 'index.php?collection=$matches[1]',
				'genre/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?genre=$matches[1]&feed=$matches[2]',
				'genre/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?genre=$matches[1]&feed=$matches[2]',
				'genre/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?genre=$matches[1]&paged=$matches[2]',
				'genre/([^/]+)/?$' => 'index.php?genre=$matches[1]',
				'actor/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?actor=$matches[1]&feed=$matches[2]',
				'actor/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?actor=$matches[1]&feed=$matches[2]',
				'actor/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?actor=$matches[1]&paged=$matches[2]',
				'actor/([^/]+)/?$' => 'index.php?actor=$matches[1]',
			);

			if ( ! is_null( $rules ) )
				return $new_rules + $rules;

			foreach ( $new_rules as $regex => $rule )
				add_rewrite_rule( $regex, $rule, 'top' );

			add_permastruct(
				'collection',
				'/collection/%collection%',
				array(
					'with_front'  => 1,
					'ep_mask'     => 0,
					'paged'       => 1,
					'feed'        => 1,
					'forcomments' => null,
					'walk_dirs'   => 1,
					'endpoints'   => 1
				)
			);

			add_permastruct(
				'genre',
				'/genre/%genre%',
				array(
					'with_front'  => 1,
					'ep_mask'     => 0,
					'paged'       => 1,
					'feed'        => 1,
					'forcomments' => null,
					'walk_dirs'   => 1,
					'endpoints'   => 1
				)
			);

			add_permastruct(
				'actor',
				'/actor/%actor%',
				array(
					'with_front'  => 1,
					'ep_mask'     => 0,
					'paged'       => 1,
					'feed'        => 1,
					'forcomments' => null,
					'walk_dirs'   => 1,
					'endpoints'   => 1
				)
			);

		}

		/**
		 * Render a notice box.
		 * 
		 * Mostly used to inform about specific plugin needs or info
		 * such as missing API Key, movie import, etc.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    $notice The notice message
		 * @param    string    $type Notice type: update, error, wpml?
		 */
		public static function admin_notice( $notice, $type = 'update' ) {

			if ( '' == $notice )
				return false;

			if ( ! in_array( $type, array( 'error', 'warning', 'update', WPML_SLUG ) ) || 'update' == $type )
				$class = 'updated';
			else if ( 'wpml' == $type )
				$class = 'updated wpml';
			else if ( 'error' == $type )
				$class = 'error';
			else if ( 'warning' == $type )
				$class = 'update-nag';

			echo '<div class="' . $class . '"><p>' . $notice . '</p></div>';
		}

		/**
		 * Return Movie's stored TMDb data.
		 * 
		 * @uses wpml_get_movie_postmeta()
		 *
		 * @since    1.0.0
		 * 
		 * @param    int    Movie Post ID
		 *
		 * @return   array|string    WPML Movie TMDb data if stored, empty string else.
		 */
		public static function get_movie_data( $post_id = null ) {
			return WPML_Utils::get_movie_postmeta( 'data', $post_id );
		}

		/**
		 * Return Movie's Status.
		 * 
		 * @uses wpml_get_movie_postmeta()
		 *
		 * @since    1.0.0
		 * 
		 * @param    int    Movie Post ID
		 *
		 * @return   array|string    WPML Movie Status if stored, empty string else.
		 */
		public static function get_movie_status( $post_id = null ) {
			return WPML_Utils::get_movie_postmeta( 'status', $post_id );
		}

		/**
		 * Return Movie's Media.
		 * 
		 * @uses wpml_get_movie_postmeta()
		 *
		 * @since    1.0.0
		 * 
		 * @param    int    Movie Post ID
		 *
		 * @return   array|string    WPML Movie Media if stored, empty string else.
		 */
		public static function get_movie_media( $post_id = null ) {
			return WPML_Utils::get_movie_postmeta( 'media', $post_id );
		}

		/**
		 * Return Movie's Rating.
		 * 
		 * @uses wpml_get_movie_postmeta()
		 *
		 * @since    1.0.0
		 * 
		 * @param    int    Movie Post ID
		 *
		 * @return   array|string    WPML Movie Rating if stored, empty string else.
		 */
		public static function get_movie_rating( $post_id = null ) {
			return WPML_Utils::get_movie_postmeta( 'rating', $post_id );
		}

		/**
		 * Return various Movie's Post Meta. Possible meta: status, media, rating
		 * and data.
		 *
		 * @since    1.0.0
		 * 
		 * @param    string    Meta type to return: data, status, media or rating
		 * @param    int       Movie Post ID
		 *
		 * @return   array|string    WPML Movie Meta if available, empty string else.
		 */
		private static function get_movie_postmeta( $meta, $post_id = null ) {

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
		 * @return    string     cleaned up movie title
		 */
		public static function clean_search_title( $query ) {
			$s = trim( $query );
			$s = preg_replace( '/[^\p{L}\p{N}\s]/u', '', $s );
			return $s;
		}

		/**
		 * Generate Movies dropdown or classic lists.
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
		public static function format_widget_lists( $items, $dropdown = false, $styling = false, $title = null ) {

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
		 * Generate Movies lists including Poster.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $items Array of Movies objects
		 * 
		 * @return   string   HTML string of movies' links and Posters
		 */
		public static function format_widget_lists_thumbnails( $items ) {

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
		 * Filter Plugin Settings to obtain a single dimension array with
		 * all prefixed settings.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $array Plugin Settings
		 * 
		 * @return   array    Summarized Plugin Settings
		 */
		public static function summarize_settings( $settings ) {

			$_settings = array();

			foreach ( $settings as $id => $section )
				if ( isset( $section['settings'] ) )
					foreach ( $section['settings'] as $slug => $setting )
						$_settings[ $id ][ $slug ] = $setting['default'];
			

			return $_settings;
		}

		/**
		 * Filter a Movie's Metadata to extract only supported data.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $data Movie metadata
		 * 
		 * @return   array    Filtered Metadata
		 */
		public static function filter_meta_data( $data ) {

			if ( ! is_array( $data ) || empty( $data ) )
				return $data;

			$filter = array();
			$_data = array();

			foreach ( WPML_Settings::get_supported_movie_meta( 'meta' ) as $slug => $f ) {
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
		 * @param    array    $data Movie Crew
		 * 
		 * @return   array    Filtered Crew
		 */
		public static function filter_crew_data( $data ) {

			if ( ! is_array( $data ) || empty( $data ) || ! isset( $data['crew'] ) )
				return $data;

			$filter = array();
			$_data = array();

			$cast = apply_filters( 'wpml_filter_cast_data', $data['cast'] );
			$data = $data['crew'];

			foreach ( WPML_Settings::get_supported_movie_meta( 'crew' ) as $slug => $f ) {
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
		 * @param    array    $data Movie Cast
		 * 
		 * @return   array    Filtered Cast
		 */
		public static function filter_cast_data( $data ) {

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
		public static function stringify_array( $array, $subrow = 'name', $separator = ', ' ) {

			if ( ! is_array( $array ) || empty( $array ) )
				return $array;

			foreach ( $array as $i => $row ) {
				if ( ! is_array( $row ) )
					$array[ $i ] = $row;
				else if ( false === $subrow || ! is_array( $row ) )
					$array[ $i ] = self::stringify_array( $row, $subrow, $separator );
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
		public static function filter_empty_array( $array ) {

			if ( ! is_array( $array ) || empty( $array ) )
				return array();

			$_array = self::stringify_array( $array, false, '' );

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
		public static function filter_undimension_array( $array ) {

			if ( ! is_array( $array ) || empty( $array ) )
				return $array;

			$_array = array();

			foreach ( $array as $key => $row ) {
				if ( is_array( $row ) )
					$_array = array_merge( $_array, self::filter_undimension_array( $row ) );
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
		public static function get_the_terms( $terms, $id, $taxonomy ) {

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
		 * @param    array           $terms The post's terms
		 * @param    int|array       $object_ids The ID(s) of the object(s) to retrieve.
		 * @param    string|array    $taxonomies The taxonomies to retrieve terms from.
		 * @param    array|string    $args Change what is returned
		 * 
		 * @return   array|WP_Error  The requested term data or empty array if no
		 *                           terms found. WP_Error if any of the $taxonomies
		 *                           don't exist.
		 */
		public static function get_ordered_object_terms( $terms, $object_ids, $taxonomies, $args ) {

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
						$terms = array_merge($terms, $this->get_ordered_object_terms($object_ids, $taxonomy, array_merge($args, $t->args)));
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
		 * Handle Deactivation/Uninstallation actions.
		 * 
		 * Depending on the Plugin settings, delete all Plugin's related
		 * movie transient.
		 * 
		 * @param    string    $action Are we deactivating or uninstalling
		 *                             the plugin?
		 */
		public static function clean_transient( $action ) {

			global $wpdb, $_wp_using_ext_object_cache;

			$_action = get_option( 'wpml_settings' );
			if ( ! $_action || ! isset( $_action[ $action ] ) || ! isset( $_action[ $action ]['cache'] ) )
				return false;

			$action = $_action[ $action ]['cache'];
			if ( is_array( $action ) )
				$action = $action[0];

			if ( ! $_wp_using_ext_object_cache && 'empty' == $action ) {
				$result = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE \"_transient_%_movies_%\"" );
				$wpdb->query( 'OPTIMIZE TABLE ' . $wpdb->options );
			}
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {

			self::clean_transient( 'deactivate' );
			delete_option( 'rewrite_rules' );
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.0.0
		 */
		public static function uninstall() {

			self::clean_transient( 'uninstall' );
			delete_option( 'rewrite_rules' );
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;