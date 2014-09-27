<?php
/**
 * WPMovieLibrary Utils Class extension.
 * 
 * This class contains various tools needed by WPMOLY such as array manipulating
 * filters, terms ordering methods or permalinks creation.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Utils' ) ) :

	class WPMOLY_Utils extends WPMOLY_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {
			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_filter( 'rewrite_rules_array', __CLASS__ . '::register_permalinks', 11 );

			add_filter( 'wpmoly_filter_meta_data', __CLASS__ . '::filter_meta_data', 10, 1 );
			add_filter( 'wpmoly_filter_crew_data', __CLASS__ . '::filter_crew_data', 10, 1 );
			add_filter( 'wpmoly_filter_cast_data', __CLASS__ . '::filter_cast_data', 10, 1 );
			add_filter( 'wpmoly_filter_movie_meta_aliases', __CLASS__ . '::filter_movie_meta_aliases', 10, 1 );

			add_filter( 'wpmoly_format_movie_genres', __CLASS__ . '::format_movie_genres', 10, 2 );
			add_filter( 'wpmoly_format_movie_actors', __CLASS__ . '::format_movie_actors', 10, 2 );
			add_filter( 'wpmoly_format_movie_cast', __CLASS__ . '::format_movie_cast', 10, 2 );
			add_filter( 'wpmoly_format_movie_release_date', __CLASS__ . '::format_movie_release_date', 10, 2 );
			add_filter( 'wpmoly_format_movie_runtime', __CLASS__ . '::format_movie_runtime', 10, 2 );
			add_filter( 'wpmoly_format_movie_director', __CLASS__ . '::format_movie_director', 10, 2 );
			add_filter( 'wpmoly_format_movie_field', __CLASS__ . '::format_movie_field', 10, 2 );

			add_filter( 'wpmoly_format_movie_media', __CLASS__ . '::format_movie_media', 10, 2 );
			add_filter( 'wpmoly_format_movie_status', __CLASS__ . '::format_movie_status', 10, 2 );
			add_filter( 'wpmoly_format_movie_rating', __CLASS__ . '::format_movie_rating', 10, 2 );

			add_filter( 'post_thumbnail_html', __CLASS__ . '::filter_default_thumbnail', 10, 5 );

			add_filter( 'get_the_terms', __CLASS__ . '::get_the_terms', 10, 3 );
			add_filter( 'wp_get_object_terms', __CLASS__ . '::get_ordered_object_terms', 10, 4 );

			add_filter( 'pre_get_posts', __CLASS__ . '::filter_search_query', 10, 1 );

			add_action( 'template_redirect', __CLASS__ . '::filter_404', 10 );
			add_filter( 'post_type_archive_title', __CLASS__ . '::filter_post_type_archive_title', 10, 2 );
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
		 * @since    1.0
		 *
		 * @param    object     $wp_rewrite Instance of WordPress WP_Rewrite Class
		 */
		public static function register_permalinks( $rules = null ) {

			$movies     = wpmoly_o( 'rewrite-movies' );
			$collection = wpmoly_o( 'rewrite-collection' );
			$genre      = wpmoly_o( 'rewrite-genre' );
			$actor      = wpmoly_o( 'rewrite-actor' );
			$details    = wpmoly_o( 'rewrite-details' );

			$movies     = ( '' != $movies ? $movies : 'movies' );
			$collection = ( '' != $collection ? $collection : 'collection' );
			$genre      = ( '' != $genre ? $genre : 'genre' );
			$actor      = ( '' != $actor ? $actor : 'actor' );

			$i18n = array();

			$i18n['unavailable'] = ( $_i18n ? __( 'unavailable', 'wpmovielibrary' ) : 'unavailable' );
			$i18n['available']   = ( $_i18n ? __( 'available', 'wpmovielibrary' ) : 'available' );
			$i18n['loaned']      = ( $_i18n ? __( 'loaned', 'wpmovielibrary' ) : 'loaned' );
			$i18n['scheduled']   = ( $_i18n ? __( 'scheduled', 'wpmovielibrary' ) : 'scheduled' );
			$i18n['bluray']      = ( $_i18n ? __( 'bluray', 'wpmovielibrary' ) : 'bluray' );
			$i18n['cinema']      = ( $_i18n ? __( 'cinema', 'wpmovielibrary' ) : 'cinema' );
			$i18n['other']       = ( $_i18n ? __( 'other', 'wpmovielibrary' ) : 'other' );

			$new_rules = array(
				$movies . '/(dvd|vod|divx|' . $i18n['bluray'] . '|vhs|' . $i18n['cinema'] . '|' . $i18n['other'] . ')/?$' => 'index.php?post_type=movie&wpmoly_movie_media=$matches[1]',
				$movies . '/(dvd|vod|divx|' . $i18n['bluray'] . '|vhs|' . $i18n['cinema'] . '|' . $i18n['other'] . ')/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpmoly_movie_media=$matches[1]&paged=$matches[2]',
				$movies . '/(' . $i18n['unavailable'] . '|' . $i18n['available'] . '|' . $i18n['loaned'] . '|' . $i18n['scheduled'] . ')/?$' => 'index.php?post_type=movie&wpmoly_movie_status=$matches[1]',
				$movies . '/(' . $i18n['unavailable'] . '|' . $i18n['available'] . '|' . $i18n['loaned'] . '|' . $i18n['scheduled'] . ')/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpmoly_movie_status=$matches[1]&paged=$matches[2]',
				$movies . '/(0\.0|0\.5|1\.0|1\.5|2\.0|2\.5|3\.0|3\.5|4\.0|4\.5|5\.0)/?$' => 'index.php?post_type=movie&wpmoly_movie_rating=$matches[1]',
				$movies . '/(0\.0|0\.5|1\.0|1\.5|2\.0|2\.5|3\.0|3\.5|4\.0|4\.5|5\.0)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpmoly_movie_rating=$matches[1]&paged=$matches[2]',
				$movies . '/([^/]+)/?$' => 'index.php?movie=$matches[1]',
				$collection . '/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?collection=$matches[1]&feed=$matches[2]',
				$collection . '/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?collection=$matches[1]&feed=$matches[2]',
				$collection . '/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?collection=$matches[1]&paged=$matches[2]',
				$collection . '/([^/]+)/?$' => 'index.php?collection=$matches[1]',
				$genre . '/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?genre=$matches[1]&feed=$matches[2]',
				$genre . '/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?genre=$matches[1]&feed=$matches[2]',
				$genre . '/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?genre=$matches[1]&paged=$matches[2]',
				$genre . '/([^/]+)/?$' => 'index.php?genre=$matches[1]',
				$actor . '/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?actor=$matches[1]&feed=$matches[2]',
				$actor . '/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?actor=$matches[1]&feed=$matches[2]',
				$actor . '/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?actor=$matches[1]&paged=$matches[2]',
				$actor . '/([^/]+)/?$' => 'index.php?actor=$matches[1]',
			);

			if ( ! is_null( $rules ) )
				return $new_rules + $rules;

			foreach ( $new_rules as $regex => $rule )
				add_rewrite_rule( $regex, $rule, 'top' );

			add_permastruct(
				'collection',
				'/' . $collection . '/%collection%',
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
				'/' . $genre . '/%genre%',
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
				'/' . $actor . '/%actor%',
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
		 * Setup an internal, dummy Archive page that will be used to
		 * render archive pages for taxonomies. This should only be called
		 * only while activating.
		 * 
		 * @since    1.0
		 */
		public static function set_archive_page() {

			$page = get_page_by_title( 'WPMovieLibrary Archives', OBJECT, 'wpmoly_page' );

			if ( ! is_null( $page ) )
				return false;

			$post = array(
				'ID'             => null,
				'post_content'   => '',
				'post_name'      => 'wpmoly_wpmovielibrary',
				'post_title'     => 'WPMovieLibrary Archives',
				'post_status'    => 'publish',
				'post_type'      => 'wpmoly_page',
				'post_author'    => 1,
				'ping_status'    => 'closed',
				'post_excerpt'   => '',
				'post_date'      => '0000-00-00 00:00:00',
				'post_date_gmt'  => '0000-00-00 00:00:00',
				'comment_status' => 'closed'
			);

			wp_insert_post( $post );
		}

		/**
		 * Delete the Archive page. This should only be called only while
		 * uninstalling.
		 * 
		 * @since    1.0
		 */
		public static function delete_archive_page() {

			$page = get_page_by_title( 'WPMovieLibrary Archives', OBJECT, 'wpmoly_page' );

			if ( is_null( $page ) )
				return false;

			wp_delete_post( $page->ID, $force_delete = true );
		}

		/**
		 * Render a notice box.
		 * 
		 * Mostly used to inform about specific plugin needs or info
		 * such as missing API Key, movie import, etc.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $notice The notice message
		 * @param    string    $type Notice type: update, error, wpmoly?
		 */
		public static function admin_notice( $notice, $type = 'update' ) {

			if ( '' == $notice )
				return false;

			if ( ! in_array( $type, array( 'error', 'warning', 'update', 'wpmovielibrary' ) ) || 'update' == $type )
				$class = 'updated';
			else if ( 'wpmoly' == $type )
				$class = 'updated wpmoly';
			else if ( 'error' == $type )
				$class = 'error';
			else if ( 'warning' == $type )
				$class = 'update-nag';

			echo '<div class="' . $class . '"><p>' . $notice . '</p></div>';
		}

		/**
		 * Filter a Movie's Metadata to extract only supported data.
		 * 
		 * @since    1.0
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
			$_meta = WPMOLY_Settings::get_supported_movie_meta( 'meta' );

			foreach ( $_meta as $slug => $f ) {
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
		 * TODO find some cleaner way to validate crew
		 * 
		 * @since    1.0
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

			$cast = apply_filters( 'wpmoly_filter_cast_data', $data['cast'] );
			$data = $data['crew'];

			foreach ( WPMOLY_Settings::get_supported_movie_meta( 'crew' ) as $slug => $f ) {
				$filter[ $slug ] = $f['job'];
				$_data[ $slug ] = '';
			}

			foreach ( $data as $i => $d ) {
				if ( isset( $d['job'] ) ) {
					$key = array_search( $d['job'], $filter );
					if ( false !== $key && isset( $_data[ $key ] ) )
						$_data[ $key ][] = $d['name'];
				}
			}

			$_data['cast'] = $cast;

			return $_data;
		}

		/**
		 * Filter a Movie's Cast to extract only supported data.
		 * 
		 * @since    1.0
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
		 * Filter a Movie's Metadata slug to handle aliases.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $slug Metadata slug
		 * 
		 * @return   string    Filtered slug
		 */
		public static function filter_movie_meta_aliases( $slug ) {

			$aliases = WPMOLY_Settings::get_supported_movie_meta_aliases();
			$_slug = str_replace( 'movie_', '', $slug );

			if ( isset( $aliases[ $_slug ] ) )
				$slug = $aliases[ $_slug ];

			return $slug;
		}

		/**
		 * Format a Movie's genres for display
		 * 
		 * Match each genre against the genre taxonomy to detect missing
		 * terms. If term genre exists, provide a link, raw text value
		 * if no matching term could be found.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_genres( $data ) {

			$output = self::format_movie_terms_list( $data, 'genre' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's casting for display
		 * This is an alias for self::format_movie_cast()
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * @param    int       $post_id Movie's post ID if needed (required for shortcodes)
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_actors( $data ) {

			return self::format_movie_cast( $data );
		}

		/**
		 * Format a Movie's casting for display
		 * 
		 * Match each actor against the actor taxonomy to detect missing
		 * terms. If term actor exists, provide a link, raw text value
		 * if no matching term could be found.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_cast( $data ) {

			$output = self::format_movie_terms_list( $data,  'actor' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's release date for display
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_release_date( $data, $format = null ) {

			if ( is_null( $data ) || '' == $data )
				return $data;

			if ( is_null( $format ) )
				$format = wpmoly_o( 'format-date' );

			if ( '' == $format )
				$format = 'j F Y';

			$output = date_i18n( $format, strtotime( $data ) );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's runtime for display
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_runtime( $data, $format = null ) {

			if ( is_null( $data ) || '' == $data )
				return $data;

			if ( is_null( $format ) )
				$format = wpmoly_o( 'format-time' );

			if ( '' == $format )
				$format = 'G \h i \m\i\n';

			$output = date_i18n( $format, mktime( 0, $data ) );
			if ( false !== stripos( $output, 'am' ) || false !== stripos( $output, 'pm' ) )
				$output = date_i18n( 'G:i', mktime( 0, $data ) );

			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's director for display
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * @param    int       $post_id Movie's post ID if needed (required for shortcodes)
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_director( $data ) {

			$output = self::format_movie_terms_list( $data, 'collection' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's misc field for display
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_field( $data ) {

			if ( '' == $data )
				$data = '&mdash;';

			return $data;
		}

		/**
		 * Format a Movie's media. If format is HTML, will return a
		 * HTML formatted string; will return the value without change
		 * if raw is asked.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data rating value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_media( $data, $format = 'html' ) {

			$format = ( 'raw' == $format ? 'raw' : 'html' );

			if ( '' == $data )
				return $data;

			if ( wpmoly_o( 'details-icons' ) && 'html' == $format  ) {
				$data = WPMovieLibrary::render_template( 'shortcodes/detail-icon.php', array( 'detail' => 'media', 'data' => $data ), $require = 'always' );
			}
			else if ( 'html' == $format ) {
				$default_fields = WPMOLY_Settings::get_available_movie_media();
				$data = WPMovieLibrary::render_template( 'shortcodes/detail.php', array( 'detail' => 'media', 'data' => $data, 'title' => __( $default_fields[ $data ], 'wpmovielibrary' ) ), $require = 'always' );
			}

			return $data;
		}

		/**
		 * Format a Movie's status. If format is HTML, will return a
		 * HTML formatted string; will return the value without change
		 * if raw is asked.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data rating value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_status( $data, $format = 'html' ) {

			$format = ( 'raw' == $format ? 'raw' : 'html' );

			if ( '' == $data )
				return $data;

			if ( wpmoly_o( 'details-icons' ) && 'html' == $format  ) {
				$data = WPMovieLibrary::render_template( 'shortcodes/detail-icon.php', array( 'detail' => 'status', 'data' => $data ), $require = 'always' );
			}
			else if ( 'html' == $format ) {
				$default_fields = WPMOLY_Settings::get_available_movie_status();
				$data = WPMovieLibrary::render_template( 'shortcodes/detail.php', array( 'detail' => 'status', 'data' => $data, 'title' => __( $default_fields[ $data ], 'wpmovielibrary' ) ), $require = 'always' );
			}

			return $data;
		}

		/**
		 * Format a Movie's rating. If format is HTML, will return a
		 * HTML formatted string; will return the value without change
		 * if raw is asked.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data rating value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_rating( $data, $format = 'html' ) {

			$format = ( 'raw' == $format ? 'raw' : 'html' );

			if ( '' == $data )
				return $data;

			if ( 'html' == $format )
				$data = WPMovieLibrary::render_template( 'shortcodes/rating.php', array( 'style' => ( '' == $data ? '0_0' : str_replace( '.', '_', $data ) ) ), $require = 'always' );

			return $data;
		}

		/**
		 * Format a Movie's misc actors/genres list depending on
		 * existing terms.
		 * 
		 * This is used to provide links for actors and genres lists
		 * by using the metadata lists instead of taxonomies. But since
		 * actors and genres can be added to the metadata and not terms,
		 * we rely on metadata to show a correct list.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * @param    string    $taxonomy taxonomy we're dealing with
		 * 
		 * @return   string    Formatted output
		 */
		private static function format_movie_terms_list( $data, $taxonomy ) {

			$has_taxonomy = wpmoly_o( "enable-{$taxonomy}" );
			$_data = explode( ',', $data );

			foreach ( $_data as $key => $term ) {
				
				$term = trim( $term );
				$_term = ( $has_taxonomy ? get_term_by( 'name', $term, $taxonomy ) : $term );

				if ( ! $_term )
					$_term = $term;

				if ( is_object( $_term ) && '' != $_term->name ) {
					$link = get_term_link( $_term, $taxonomy );
					$_term = ( is_wp_error( $link ) ? $_term->name : sprintf( '<a href="%s">%s</a>', $link, $_term->name ) );
				}
				$_data[ $key ] = $_term;
			}

			$_data = ( ! empty( $_data ) ? implode( ', ', $_data ) : '&mdash;' );

			return $_data;
		}

		/**
		 * Filter the post thumbnail HTML to return the plugin's default
		 * poster.
		 *
		 * @since 1.1.0
		 *
		 * @param    string    $html The post thumbnail HTML.
		 * @param    string    $post_id The post ID.
		 * @param    string    $post_thumbnail_id The post thumbnail ID.
		 * @param    string    $size The post thumbnail size.
		 * @param    string    $attr Query string of attributes.
		 * 
		 * @return   string    Default poster HTML markup
		 */
		public static function filter_default_thumbnail( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

			if ( '' != $html || 'movie' != get_post_type( $post_id ) )
				return $html;

			// Filter available sizes
			switch ( $size ) {
				case 'post-thumbnail':
					$size = '-large';
					break;
				case 'thumb':
				case 'thumbnail':
				case 'medium':
				case 'large':
					$size = '-' . $size;
					break;
				case 'full':
					$size = '';
					break;
				default:
					$size = '-large';
					break;
			}

			$url = str_replace( '{size}', $size, WPMOLY_DEFAULT_POSTER_URL );
			$html = '<img class="attachment-post-thumbnail wp-post-image" src="' . $url . '" alt="" />';

			return $html;
		}

		/**
		 * Sort Taxonomies by term_order.
		 * 
		 * Code from Luke Gedeon, see https://core.trac.wordpress.org/ticket/9547#comment:7
		 *
		 * @since    1.0
		 *
		 * @param    array      $terms array of objects to be replaced with sorted list
		 * @param    integer    $id post id
		 * @param    string     $taxonomy only 'post_tag' is changed.
		 * 
		 * @return   array      Terms array of objects
		 */
		public static function get_the_terms( $terms, $id, $taxonomy ) {

			if ( ! in_array( $taxonomy, array( 'collection',  'genre',  'actor' ) ) )
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
		 * Filter search query to add support for movies
		 * 
		 * @since    1.3
		 * 
		 * @return   object    WP_Query object
		 */
		public static function filter_search_query( $wp_query ) {

			if ( is_admin() )
				return $wp_query;

			if ( ! is_search() )
				return $wp_query;

			$wp_query->set( 'post_type', array( 'post', 'movie' ) );

			return $wp_query;
		}

		/**
		 * Filter 404 error pages to intercept taxonomies listing pages.
		 * 
		 * Query should be 404 with no posts found and matching either one
		 * of the taxonomies slug.
		 * 
		 * @since    1.0
		 * 
		 * @return   boolean    Filter result: true if template filtered,
		 *                      false else.
		 */
		public static function filter_404() {

			global $wp_query;
			$_query = $wp_query;

			// 404 only
			if ( true !== $wp_query->is_404 || 0 !== $wp_query->post_count )
				return false;

			// Custom taxonomies only
			$collection = wpmoly_o( 'rewrite-collection' );
			$genre      = wpmoly_o( 'rewrite-genre' );
			$actor      = wpmoly_o( 'rewrite-actor' );
			$slugs = array(
				'collection'	=> ( '' != $collection ? $collection : 'collection' ),
				'genre'		=> ( '' != $genre ? $genre : 'genre' ),
				'actor'		=> ( '' != $actor ? $actor : 'actor' )
			);

			if ( ! in_array( $wp_query->query_vars['name'], $slugs ) && ! in_array( $wp_query->query_vars['category_name'], $slugs ) )
				return false;

			// Change type of query
			$wp_query->is_404 = false;
			$wp_query->is_archive = true;
			$wp_query->has_archive = true;
			$wp_query->is_post_type_archive = true;
			$wp_query->query_vars['post_type'] = 'movie';

			$post = new WP_Post( new StdClass );
			$post = get_page_by_title( 'WPMovieLibrary Archives', OBJECT, 'wpmoly_page' );

			if ( is_null( $post ) ) {
				$wp_query = $_query;
				return false;
			}

			// WP_Query trick: use an internal dummy page
			$posts_per_page = $wp_query->query_vars['posts_per_page'];

			// Term selection
			if ( in_array( $slugs['collection'], array( $wp_query->query_vars['name'], $wp_query->query_vars['category_name'] ) ) ) {
				$term_slug = 'collection';
				$term_title = __( 'View all movies from collection &laquo; %s &raquo;', 'wpmovielibrary' );
			}
			else if (in_array( $slugs['genre'], array( $wp_query->query_vars['name'], $wp_query->query_vars['category_name'] ) ) ) {
				$term_slug = 'genre';
				$term_title = __( 'View all &laquo; %s &raquo; movies', 'wpmovielibrary' );
			}
			else if ( in_array( $slugs['actor'], array( $wp_query->query_vars['name'], $wp_query->query_vars['category_name'] ) ) ) {
				$term_slug = 'actor';
				$term_title = __( 'View all movies staring &laquo; %s &raquo;', 'wpmovielibrary' );
			}
			else {
				$wp_query = $_query;
				return false;
			}

			// Caching
			// TODO: this is nasty. Should be a way to make sure the filter
			// is added before all this to avoid the has_filter() call...
			if ( has_filter( 'wpmoly_cache_name' ) )
				$name = apply_filters( 'wpmoly_cache_name', $term_slug . '_archive', $wp_query->query_vars );
			else
				$name = WPMOLY_Cache::wpmoly_cache_name( $term_slug . '_archive', $wp_query->query_vars );

			$content = WPMOLY_Cache::output( $name, function() use ( $wp_query, $slugs, $term_slug, $term_title ) {

				$wp_query->query_vars['wpmoly_archive_page'] = 1;
				$wp_query->query_vars['wpmoly_archive_title'] = __( ucwords( $term_slug . 's' ), 'wpmovielibrary' );

				$args = 'hide_empty=true&number=50';
				$paged = $wp_query->get( 'paged' );

				if ( $paged )
					$args .= '&offset=' . ( 50 * ( $paged - 1 ) );

				$terms = get_terms( $term_slug, $args );
				$total = wp_count_terms( $term_slug, 'hide_empty=true' );
				$links = array();

				if ( is_wp_error( $terms ) )
					$links = $terms;
				else 
					foreach ( $terms as $term )
						$links[] = array(
							'url'        => get_term_link( $term ),
							'attr_title' => sprintf( $term_title, $term->name ),
							'title'      => $term->name,
							'count'      => sprintf( _n( '%d movie', '%d movies', $term->count, 'wpmovielibrary' ), $term->count )
						);

				$args = array(
					'type'    => 'list',
					'total'   => ceil( ( $total - 1 ) / 50 ),
					'current' => max( 1, $paged ),
					'format'  => home_url( $slugs[ $term_slug ] . '/page/%#%/' ),
				);

				$attributes = array( 'taxonomy' => $term_slug, 'links' => $links );
				$content = WPMovieLibrary::render_template( 'archives/archives.php', $attributes, $require = 'always' );

				$pagination = WPMOLY_Utils::paginate_links( $args );

				$content = $content . $pagination;

				return $content;
			});

			$post->post_content = $content;

			$wp_query->posts[] = $post;
			$wp_query->post_count = 1;
			$wp_query->found_posts = 1;

			// Make sure HTTP status is good
			status_header( '200' );
		}

		/**
		 * Filter page titles to replace custom archive pages titles
		 * with the correct term title.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $name Current page title
		 * @param    string    $args Current page post_type
		 * 
		 * @return   string    Updated page title.
		*/
		public static function filter_post_type_archive_title( $name, $post_type ) {

			global $wp_query;

			if ( 'movie' != $post_type )
				return $name;

			if ( 1 == $wp_query->get( 'wpmoly_archive_page' ) && '' != $wp_query->get( 'wpmoly_archive_title' ) )
				$name = $wp_query->get( 'wpmoly_archive_title' );

			return $name;
		}

		/**
		 * Retrieve paginated link for archive post pages.
		 * 
		 * This is a partial rewrite of WordPress paginate_links() function
		 * that doesn't work on the plugin's built-in archive pages.
		 * 
		 * @since    1.1
		 * 
		 * @param    array    $args Optional. Override defaults.
		 * 
		 * @return   string   String of page links or array of page links.
		*/
		public static function paginate_links( $args = '' ) {

			$defaults = array(
				'base'      => '%_%',
				'format'    => '/page/%#%/',
				'total'     => 1,
				'current'   => 0,
				'prev_text' => __( '&laquo; Previous' ),
				'next_text' => __( 'Next &raquo;' ),
				'end_size'  => 1,
				'mid_size'  => 2,
			);

			$args = wp_parse_args( $args, $defaults );

			extract( $args, EXTR_SKIP );

			// Who knows what else people pass in $args
			$total = (int) $total;
			if ( $total < 2 )
				return;
			$current  = (int) $current;
			$end_size = 0  < (int) $end_size ? (int) $end_size : 1; // Out of bounds?  Make it the default.
			$mid_size = 0 <= (int) $mid_size ? (int) $mid_size : 2;
			$r = '';
			$links = array();
			$n = 0;
			$dots = false;

			if ( $current && 1 < $current ) {
				$link = str_replace( '%_%', $format, $base );
				$link = str_replace( '%#%', $current - 1, $link );
				$links[] = array( 'url' => esc_url( $link ), 'class' => 'prev page-numbers', 'title' => $prev_text );
			}

			for ( $n = 1; $n <= $total; $n++ ) {
				if ( $n == $current ) {
					$links[] = array( 'url' => null, 'class' => 'page-numbers current', 'title' => number_format_i18n( $n ) );
					$dots = true;
				}
				else {
					if ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) {
						$link = str_replace( '%_%', $format, $base );
						$link = str_replace( '%#%', $n, $link );
						$links[] = array( 'url' => esc_url( $link ), 'class' => 'page-numbers', 'title' => number_format_i18n( $n ) );
						$dots = true;
					}
					else if ( $dots ) {
						$links[] = array( 'url' => null, 'class' => 'page-numbers dots', 'title' => __( '&hellip;' ) );
						$dots = false;
					}
				}
			}

			if ( $current && ( $current < $total || -1 == $total ) ) {
				$link = str_replace( '%_%', $format, $base );
				$link = str_replace( '%#%', $current + 1, $link );
				$links[] = array( 'url' => esc_url( $link ), 'class' => 'next page-numbers', 'title' => $next_text );
			}

			$attributes = array( 'links' => $links );

			$content = WPMovieLibrary::render_template( 'archives/pagination.php', $attributes, $require = 'always' );

			return $content;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {

			self::set_archive_page();
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {

			WPMOLY_Cache::clean_transient( 'deactivate' );
			delete_option( 'rewrite_rules' );
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.0
		 */
		public static function uninstall() {

			WPMOLY_Cache::clean_transient( 'uninstall' );
			delete_option( 'rewrite_rules' );

			self::delete_archive_page();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}

endif;
