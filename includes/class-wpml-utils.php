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
			add_filter( 'wpml_filter_movie_meta_aliases', __CLASS__ . '::filter_movie_meta_aliases', 10, 1 );

			add_filter( 'wpml_format_movie_genres', __CLASS__ . '::format_movie_genres', 10, 2 );
			add_filter( 'wpml_format_movie_actors', __CLASS__ . '::format_movie_actors', 10, 2 );
			add_filter( 'wpml_format_movie_cast', __CLASS__ . '::format_movie_cast', 10, 2 );
			add_filter( 'wpml_format_movie_release_date', __CLASS__ . '::format_movie_release_date', 10, 2 );
			add_filter( 'wpml_format_movie_runtime', __CLASS__ . '::format_movie_runtime', 10, 2 );
			add_filter( 'wpml_format_movie_director', __CLASS__ . '::format_movie_director', 10, 2 );
			add_filter( 'wpml_format_movie_field', __CLASS__ . '::format_movie_field', 10, 2 );

			add_filter( 'wpml_format_movie_media', __CLASS__ . '::format_movie_media', 10, 2 );
			add_filter( 'wpml_format_movie_status', __CLASS__ . '::format_movie_status', 10, 2 );
			add_filter( 'wpml_format_movie_rating', __CLASS__ . '::format_movie_rating', 10, 2 );

			add_filter( 'wpml_filter_filter_runtime', __CLASS__ . '::filter_runtime', 10, 1 );
			add_filter( 'wpml_filter_filter_release_date', __CLASS__ . '::filter_release_date', 10, 2 );
			add_filter( 'wpml_validate_meta_data', __CLASS__ . '::validate_meta_data', 10, 1 );
			add_filter( 'wpml_filter_shortcode_atts', __CLASS__ . '::filter_shortcode_atts', 10, 2 );
			add_filter( 'wpml_is_boolean', __CLASS__ . '::is_boolean', 10, 1 );

			add_filter( 'wpml_stringify_array', __CLASS__ . '::stringify_array', 10, 3 );
			add_filter( 'wpml_filter_empty_array', __CLASS__ . '::filter_empty_array', 10, 1 );
			add_filter( 'wpml_filter_undimension_array', __CLASS__ . '::filter_undimension_array', 10, 1 );

			add_filter( 'post_thumbnail_html', __CLASS__ . '::filter_default_thumbnail', 10, 5 );

			add_filter( 'get_the_terms', __CLASS__ . '::get_the_terms', 10, 3 );
			add_filter( 'wp_get_object_terms', __CLASS__ . '::get_ordered_object_terms', 10, 4 );

			add_action( 'template_redirect', __CLASS__ . '::filter_404' );
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
		 * @since    1.0.0
		 *
		 * @param    object     $wp_rewrite Instance of WordPress WP_Rewrite Class
		 */
		public static function register_permalinks( $rules = null ) {

			$movies     = WPML_Settings::wpml__movie_rewrite();
			$collection = WPML_Settings::taxonomies__collection_rewrite();
			$genre      = WPML_Settings::taxonomies__genre_rewrite();
			$actor      = WPML_Settings::taxonomies__actor_rewrite();

			$movies     = ( '' != $movies ? $movies : 'movies' );
			$collection = ( '' != $collection ? $collection : 'collection' );
			$genre      = ( '' != $genre ? $genre : 'genre' );
			$actor      = ( '' != $actor ? $actor : 'actor' );

			$i18n = array();
			$_i18n = WPML_Settings::wpml__details_rewrite();

			$i18n['unavailable'] = ( $_i18n ? __( 'unavailable', WPML_SLUG ) : 'unavailable' );
			$i18n['available']   = ( $_i18n ? __( 'available', WPML_SLUG ) : 'available' );
			$i18n['loaned']      = ( $_i18n ? __( 'loaned', WPML_SLUG ) : 'loaned' );
			$i18n['scheduled']   = ( $_i18n ? __( 'scheduled', WPML_SLUG ) : 'scheduled' );
			$i18n['bluray']      = ( $_i18n ? __( 'bluray', WPML_SLUG ) : 'bluray' );
			$i18n['cinema']      = ( $_i18n ? __( 'cinema', WPML_SLUG ) : 'cinema' );
			$i18n['other']       = ( $_i18n ? __( 'other', WPML_SLUG ) : 'other' );

			$new_rules = array(
				$movies . '/(dvd|vod|divx|' . $i18n['bluray'] . '|vhs|' . $i18n['cinema'] . '|' . $i18n['other'] . ')/?$' => 'index.php?post_type=movie&wpml_movie_media=$matches[1]',
				$movies . '/(dvd|vod|divx|' . $i18n['bluray'] . '|vhs|' . $i18n['cinema'] . '|' . $i18n['other'] . ')/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_media=$matches[1]&paged=$matches[2]',
				$movies . '/(' . $i18n['unavailable'] . '|' . $i18n['available'] . '|' . $i18n['loaned'] . '|' . $i18n['scheduled'] . ')/?$' => 'index.php?post_type=movie&wpml_movie_status=$matches[1]',
				$movies . '/(' . $i18n['unavailable'] . '|' . $i18n['available'] . '|' . $i18n['loaned'] . '|' . $i18n['scheduled'] . ')/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_status=$matches[1]&paged=$matches[2]',
				$movies . '/(0\.0|0\.5|1\.0|1\.5|2\.0|2\.5|3\.0|3\.5|4\.0|4\.5|5\.0)/?$' => 'index.php?post_type=movie&wpml_movie_rating=$matches[1]',
				$movies . '/(0\.0|0\.5|1\.0|1\.5|2\.0|2\.5|3\.0|3\.5|4\.0|4\.5|5\.0)/page/([0-9]{1,})/?$' => 'index.php?post_type=movie&wpml_movie_rating=$matches[1]&paged=$matches[2]',
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
		 * @since    1.0.0
		 */
		public static function set_archive_page() {

			$page = get_page_by_title( 'WPMovieLibrary Archives', OBJECT, 'wpml_page' );

			if ( ! is_null( $page ) )
				return false;

			$post = array(
				'ID'             => null,
				'post_content'   => '',
				'post_name'      => 'wpml_wpmovielibrary',
				'post_title'     => 'WPMovieLibrary Archives',
				'post_status'    => 'publish',
				'post_type'      => 'wpml_page',
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
		 * @since    1.0.0
		 */
		public static function delete_archive_page() {

			$page = get_page_by_title( 'WPMovieLibrary Archives', OBJECT, 'wpml_page' );

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
		 * Simple function to check WordPress version. This is mainly
		 * used for styling as WP3.8 introduced a brand new dashboard
		 * look n feel.
		 *
		 * @since    1.0.0
		 *
		 * @return   boolean    Older/newer than WordPress 3.8?
		 */
		public static function is_modern_wp() {
			return version_compare( get_bloginfo( 'version' ), '3.8', '>=' );
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

			$value = get_post_meta( $post_id, "_wpml_movie_{$meta}", true );
			if ( 'rating' == $meta )
				$value = number_format( floatval( $value ), 1 );

			return $value;
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
		 * @param    array    $items Array of Movies objects
		 * @param    array    $args Filter params
		 * 
		 * @return   string     HTML string List of movies
		 */
		public static function format_widget_lists( $items, $args = array() ) {

			if ( ! is_array( $items ) || empty( $items ) )
				return null;

			$defaults = array(
				'dropdown'	=> false,
				'styling'	=> false,
				'title'		=> null,
				'attr_filter'	=> 'esc_attr__',
				'attr_args'	=> WPML_SLUG,
				'title_filter'	=> 'esc_attr__',
				'title_args'	=> WPML_SLUG
			);
			$args = wp_parse_args( $args, $defaults );
			extract( $args, EXTR_SKIP );

			$html = array();
			$style = 'wpml-list';
			$first = '';

			if ( $styling )
				$style = 'wpml-list custom';

			if ( ! is_null( $title ) )
				$first = sprintf( '<option value="">%s</option>', esc_attr( $title ) );

			foreach ( $items as $item ) {
				$item_title = ( function_exists( $title_filter ) ? call_user_func( $title_filter, $item['title'], $title_args ) : $item['title'] );
				$item_attr_title = ( function_exists( $attr_filter ) ? call_user_func( $attr_filter, $item['attr_title'], $attr_args ) : $item['attr_title'] );
				$item_url = esc_url( $item['link'] );

				if ( $dropdown )
					$html[] = '<option value="' . $item_url . '">' . $item_title . '</option>';
				else
					$html[] = '<li><a href="' . $item_url . '" title="' . $item_attr_title . '">' . $item_title . '</a></li>';
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

			if ( is_null( $settings ) || ! is_array( $settings ) )
				return $_settings;

			foreach ( $settings as $id => $section )
				if ( isset( $section['settings'] ) )
					foreach ( $section['settings'] as $slug => $setting )
						$_settings[ $id ][ $slug ] = $setting['default'];
			

			return $_settings;
		}

		/**
		 * Filter a Movie's Runtime to match WordPress time format
		 * 
		 * Check if the time format is 12-hour, in which case set the
		 * format to a standard hours:minutes form to avoid some weird
		 * "2:38AM" runtime.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    $runtime Movie runtime
		 * @param    string    $time_format Optional time format to apply
		 * 
		 * @return   string    Filtered runtime
		 */
		public static function filter_runtime( $runtime, $time_format = null ) {

			if ( is_null( $runtime ) || '' == $runtime )
				return $runtime;

			$time_format = WPML_Settings::wpml__time_format();
			if ( '' == $time_format )
				$time_format = 'H \h i \m\i\n';

			$time = date_i18n( $time_format, mktime( 0, $runtime ) );
			if ( false !== stripos( $time, 'am' ) || false !== stripos( $time, 'pm' ) )
				$time = date_i18n( 'g:i', mktime( 0, $runtime ) );

			return $time;
		}


		/**
		 * Filter a Movie's Release Date to match WordPress date format
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    $release_date Movie release date
		 * @param    string    $date_format Optional date format to apply
		 * 
		 * @return   string    Filtered release date
		 */
		public static function filter_release_date( $release_date, $date_format = null ) {

			if ( is_null( $release_date ) || '' == $release_date )
				return $release_date;

			if ( is_null( $date_format ) )
				$date_format = WPML_Settings::wpml__date_format();

			if ( '' == $date_format )
				$date_format = 'F Y';

			$date = date_i18n( $date_format, strtotime( $release_date ) );

			return $date;
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
		 * Filter a Movie's Metadata slug to handle aliases.
		 * 
		 * @since    1.1.0
		 * 
		 * @param    string    $slug Metadata slug
		 * 
		 * @return   string    Filtered slug
		 */
		public static function filter_movie_meta_aliases( $slug ) {

			$aliases = WPML_Settings::get_supported_movie_meta_aliases();
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
		 * @since    1.1.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_genres( $data ) {

			$output = self::format_movie_terms_list( $data, 'genre' );

			return $output;
		}

		/**
		 * Format a Movie's casting for display
		 * This is an alias for self::format_movie_cast()
		 * 
		 * @since    1.1.0
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
		 * @since    1.1.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_cast( $data ) {

			$output = self::format_movie_terms_list( $data,  'actor' );

			return $output;
		}

		/**
		 * Format a Movie's release date for display
		 * 
		 * @since    1.1.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_release_date( $data ) {

			$output = WPML_Utils::filter_release_date( $data );
			$output = ( '' != $output ? $output : '<em>&ndash;</em>' );

			return $output;
		}

		/**
		 * Format a Movie's runtime for display
		 * 
		 * @since    1.1.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_runtime( $data ) {

			$output = WPML_Utils::filter_runtime( $data );
			$output = ( '' != $output ? $output : '<em>&ndash;</em>' );

			return $output;
		}

		/**
		 * Format a Movie's director for display
		 * 
		 * @since    1.1.0
		 * 
		 * @param    string    $data field value
		 * @param    int       $post_id Movie's post ID if needed (required for shortcodes)
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_director( $data ) {

			$output = self::format_movie_terms_list( $data, 'collection' );

			return $output;
		}

		/**
		 * Format a Movie's misc field for display
		 * 
		 * @since    1.1.0
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
		 * @since    1.1.0
		 * 
		 * @param    string    $data rating value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_media( $data, $format = 'html' ) {

			$format = ( 'raw' == $format ? 'raw' : 'html' );

			if ( '' == $data )
				return $data;

			if ( WPML_Settings::wpml__details_as_icons() ) {
				$data = '<div class="wpml_movie_media ' . $data . ' wpml_detail_icon"></div>';
			}
			else if ( 'html' == $format ) {
				$default_fields = WPML_Settings::get_available_movie_media();
				$data = '<div class="wpml_movie_media ' . $data . ' wpml_detail_label"><span class="wpml_movie_detail_item">' . __( $default_fields[ $data ], WPML_SLUG ) . '</span></div>';
			}

			return $data;
		}

		/**
		 * Format a Movie's status. If format is HTML, will return a
		 * HTML formatted string; will return the value without change
		 * if raw is asked.
		 * 
		 * @since    1.1.0
		 * 
		 * @param    string    $data rating value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_status( $data, $format = 'html' ) {

			$format = ( 'raw' == $format ? 'raw' : 'html' );

			if ( '' == $data )
				return $data;

			if ( WPML_Settings::wpml__details_as_icons() ) {
				$data = '<div class="wpml_movie_status ' . $data . ' wpml_detail_icon"></div>';
			}
			else if ( 'html' == $format ) {
				$default_fields = WPML_Settings::get_available_movie_status();
				$data = '<div class="wpml_movie_status ' . $data . ' wpml_detail_label"><span class="wpml_movie_detail_item">' . __( $default_fields[ $data ], WPML_SLUG ) . '</span></div>';
			}

			return $data;
		}

		/**
		 * Format a Movie's rating. If format is HTML, will return a
		 * HTML formatted string; will return the value without change
		 * if raw is asked.
		 * 
		 * @since    1.1.0
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
				$data = sprintf( '<div class="wpml_movie_rating wpml_detail_icon"><div class="movie_rating_display stars_%s"></div></div>', ( '' == $data ? '0_0' : str_replace( '.', '_', $data ) ) );

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
		 * @since    1.1.0
		 * 
		 * @param    string    $data field value
		 * @param    string    $taxonomy taxonomy we're dealing with
		 * 
		 * @return   string    Formatted output
		 */
		private static function format_movie_terms_list( $data, $taxonomy ) {

			$has_taxonomy = call_user_func( "WPML_Settings::taxonomies__enable_$taxonomy" );
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

			$_data = ( ! empty( $_data ) ? implode( ', ', $_data ) : '<em>&ndash;</em>' );

			return $_data;
		}

		/**
		 * Filter the Movie Metadata submitted when saving a post to
		 * avoid storing unexpected data to the database.
		 * 
		 * The Metabox array makes a distinction between pure metadata
		 * and crew data, so we filter them separately. If the data slug
		 * is valid, the value is escaped and added to the return array.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $data The Movie Metadata to filter
		 * 
		 * @return   array    The filtered Metadata
		 */
		public static function validate_meta_data( $data ) {

			if ( ! is_array( $data ) || empty( $data ) || ! isset( $data['tmdb_id'] ) || ! isset( $data['meta'] ) || ! isset( $data['crew'] ) )
				return $data;

			$supported = WPML_Settings::get_supported_movie_meta();
			$keys = array_keys( $supported );
			$movie_tmdb_id = esc_attr( $data['tmdb_id'] );
			$movie_post_id = ( isset( $data['post_id'] ) && '' != $data['post_id'] ? esc_attr( $data['post_id'] ) : null );
			$movie_poster = ( isset( $data['poster'] ) && '' != $data['poster'] ? esc_attr( $data['poster'] ) : null );
			$movie_meta = array();
			$movie_crew = array();

			foreach ( $data['meta'] as $slug => $_meta ) {
				if ( in_array( $slug, $keys ) ) {
					$filter = ( isset( $supported[ $slug ]['filter'] ) && function_exists( $supported[ $slug ]['filter'] ) ? $supported[ $slug ]['filter'] : 'esc_html' );
					$args   = ( isset( $supported[ $slug ]['filter_args'] ) && ! is_null( $supported[ $slug ]['filter_args'] ) ? $supported[ $slug ]['filter_args'] : null );
					$movie_meta[ $slug ] = call_user_func( $filter, $_meta, $args );
				}
			}

			foreach ( $data['crew'] as $slug => $_meta ) {
				if ( in_array( $slug, $keys ) ) {
					$filter = ( isset( $supported[ $slug ]['filter'] ) && function_exists( $supported[ $slug ]['filter'] ) ? $supported[ $slug ]['filter'] : 'esc_html' );
					$args   = ( isset( $supported[ $slug ]['filter_args'] ) && ! is_null( $supported[ $slug ]['filter_args'] ) ? $supported[ $slug ]['filter_args'] : null );
					$movie_crew[ $slug ] = call_user_func( $filter, $_meta, $args );
				}
			}

			$_data = array(
				'tmdb_id' => $movie_tmdb_id,
				'post_id' => $movie_post_id,
				'poster'  => $movie_poster,
				'meta'    => $movie_meta,
				'crew'    => $movie_crew
			);

			return $_data;
		}

		/**
		 * Filter an array of Shortcode attributes.
		 * 
		 * Shortcodes have limited attributes and possibly limited values
		 * for some attributes. This method matches each submitted attr
		 * to its limited values if available, and apply a filter to the
		 * value before returning the array.
		 * 
		 * @since    1.1.0
		 * 
		 * @param    string    $shortcode Shortcode's ID
		 * @param    array     $atts Attributes to filter
		 * 
		 * @return   array    Filtered Attributes
		 */
		public static function filter_shortcode_atts( $shortcode, $atts = array() ) {

			if ( ! is_array( $atts ) || empty( $atts ) )
				return $atts;

			$defaults = WPML_Settings::get_available_shortcodes();
			$defaults = $defaults[ $shortcode ][ 'atts' ];

			$attributes = array();

			// Loop through the Shortcode's attributes
			foreach ( $defaults as $slug => $default ) {

				if ( isset( $atts[ $slug ] ) ) {

					$attr = $atts[ $slug ];

					// Attribute is not null
					if ( is_null( $attr ) ) {
						$attributes[ $slug ] = $default[ 'default' ];
					}
					else if ( ! is_null( $attr ) ) {

						$value = $attr;

						// Attribute has limited values
						if ( ! is_null( $default[ 'values' ] ) ) {

							// Value should be boolean
							if ( 'boolean' == $default[ 'values' ] && in_array( strtolower( $attr ), array( 'true', 'false', 'yes', 'no' ) ) ) {
								$value = apply_filters( 'wpml_is_boolean', $attr );
							}
							// Value is array
							else if ( is_array( $default[ 'values' ] ) ) {
								// multiple values
								if ( false !== strpos( $attr, '|' ) ) {
									$value = str_replace( 'actors', 'cast', $attr );
									$value = explode( '|', $value );
									foreach ( $value as $i => $v )
										if ( ! in_array( $v, $default[ 'values' ] ) )
											unset( $value[ $i ] );

									array_unique( $value );
								}
								// single value
								else if ( in_array( strtolower( $attr ), $default[ 'values' ] ) )
									$value = $attr;
							}
						}

						// Attribute has a valid filter
						if ( is_string( $value ) && function_exists( $default[ 'filter' ] ) && is_callable( $default[ 'filter' ] ) )
							$value = call_user_func( $default[ 'filter' ], $value );

						$attributes[ $slug ] = $value;
					}
				}
				else
					$attributes[ $slug ] = $default[ 'default' ];
			}

			return $attributes;
		}

		/**
		 * Filter a string value to determine a suitable boolean value.
		 * 
		 * This is mostly used for Shortcodes where boolean-like values
		 * can be used.
		 * 
		 * @since    1.1.0
		 * 
		 * @param    string    Value to filter
		 * 
		 * @return   boolean   Filtered value
		 */
		public static function is_boolean( $value ) {

			$value = strtolower( $value );

			$true = array( 'true', true, 'yes', '1', 1 );
			$false = array( 'false', false, 'no', '0', 0 );

			foreach ( $true as $t )
				if ( $value === $t )
					return true;

			foreach ( $false as $f )
				if ( $value === $f )
					return false;

			return false;
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

			$url = str_replace( '{size}', $size, WPML_DEFAULT_POSTER_URL );
			$html = '<img class="attachment-post-thumbnail wp-post-image" src="' . $url . '" alt="" />';

			return $html;
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
		 * Provide a plugin-wide, generic method for generating nonce.
		 *
		 * @since    1.0.0
		 * 
		 * @param    string    $action Action name for nonce
		 */
		public static function create_nonce( $action ) {

			return wp_create_nonce( 'wpml-' . $action );
		}

		/**
		 * Provide a plugin-wide, generic method for generating nonce fields.
		 *
		 * @since    1.0.0
		 * 
		 * @param    string    $action Action name for nonce
		 */
		public static function _nonce_field( $action, $referer = true, $echo = true ) {

			$nonce_action = 'wpml-' . $action;
			$nonce_name = '_wpmlnonce_' . str_replace( '-', '_', $action );

			return wp_nonce_field( $nonce_action, $nonce_name, $referer, $echo );
		}

		/**
		 * Provide a plugin-wide, generic method for checking AJAX nonces.
		 *
		 * @since    1.0.0
		 * 
		 * @param    string    $action Action name for nonce
		 */
		public static function check_admin_referer( $action, $query_arg = false ) {

			if ( ! $query_arg )
				$query_arg = '_wpmlnonce_' . str_replace( '-', '_', $action );

			$error = new WP_Error();
			$check = check_ajax_referer( 'wpml-' . $action, $query_arg, $die );

			if ( $check )
				return true;

			$error->add( 'invalid_nonce', __( 'Are you sure you want to do this?' ) );

			return $error;
		}

		/**
		 * Provide a plugin-wide, generic method for checking AJAX nonces.
		 *
		 * @since    1.0.0
		 * 
		 * @param    string    $action Action name for nonce
		 */
		public static function check_ajax_referer( $action, $query_arg = false, $die = false ) {

			if ( ! $query_arg )
				$query_arg = 'nonce';

			$error = new WP_Error();
			$check = check_ajax_referer( 'wpml-' . $action, $query_arg, $die );

			if ( $check )
				return true;

			$error->add( 'invalid_nonce', __( 'Are you sure you want to do this?' ) );
			self::ajax_response( $error, null, self::create_nonce( $action ) );
		}

		/**
		 * Application/JSON headers content-type.
		 * If no header was sent previously, send new header.
		 *
		 * @since    1.0.0
		 * 
		 * @param    boolean    $error Error header or normal?
		 */
		private static function json_header( $error = false ) {

			if ( false !== headers_sent() )
				return false;

			if ( $error ) {
				header( 'HTTP/1.1 500 Internal Server Error' );
				header( 'Content-Type: application/json; charset=UTF-8' );
			}	
			else {
				header( 'Content-type: application/json' );
			}
		}

		/**
		 * Pre-handle AJAX Callbacks results to detect errors
		 * 
		 * Execute the callback and filter the result to prepare the AJAX
		 * response. If errors are detected, return a WP_Error instance.
		 * If no error, return the callback results.
		 * 
		 * @param    mixed    $callback Array containing Callback Class and Method or simple string for functions
		 * @param    array    $args Array of arguments for callback
		 * 
		 * @return   array|WP_Error    Array of callback results if no error,
		 *                             WP_Error instance if anything went wrong.
		 */
		public static function ajax_filter( $callback, $args = array(), $loop = false ) {

			$loop = ( true === $loop ? true : false );
			$response = array();
			$errors = new WP_Error();

			// Simple function callback
			if ( ! is_array( $callback ) && function_exists( esc_attr( $callback ) ) ) {
				// Loop through the arg
				if ( $loop && is_array( $args ) && ! empty( $args ) ) {
					foreach ( $args[0] as $arg ) {
						$_response = call_user_func_array( $callback, array( $arg ) );
						if ( is_wp_error( $_response ) )
							$errors->add( $_response->get_error_code(), $_response->get_error_message() );
						else
							$response[] = $_response;
					}
				}
				// Single callback call
				else {
					$_response = call_user_func_array( $callback, $args );
					if ( is_wp_error( $_response ) )
						$errors->add( $_response->get_error_code(), $_response->get_error_message() );
					else
						$response[] = $_response;
				}
			}
			// Class Method callback
			else if ( is_array( $callback ) && 2 == count( $callback ) && class_exists( $callback[0] ) && method_exists( $callback[0], $callback[1] ) ) {
				// Loop through the arg
				if ( $loop && is_array( $args ) && ! empty( $args ) ) {
					foreach ( $args[0] as $arg ) {
						$_response = call_user_func_array( array( $callback[0], $callback[1] ), array( $arg ) );
						if ( is_wp_error( $_response ) )
							$errors->add( $_response->get_error_code(), $_response->get_error_message() );
						else
							$response[] = $_response;
					}
				}
				// Single callback call
				else {
					$_response = call_user_func_array( array( $callback[0], $callback[1] ), $args );
					if ( is_wp_error( $_response ) )
						$errors->add( $_response->get_error_code(), $_response->get_error_message() );
					else
						$response[] = $_response;
				}
			}
			else
				$errors->add( 'callback_error', __( 'An error occured when trying to perform the request: invalid callback or data.', WPML_SLUG ) );

			if ( ! empty( $errors->errors ) )
				$response = $errors;

			return $response;
		}

		/**
		 * Handle AJAX Callbacks results, prepare and format the AJAX
		 * response and display it.
		 * 
		 * TODO: give priority to Nonce in args
		 * 
		 * @param    array    $response Array containing Callback results data
		 * @param    array    $i18n Array containing Callback optional i18n
		 */
		public static function ajax_response( $response, $i18n = array(), $nonce = null ) {

			if ( is_wp_error( $response ) )
				$_response = $response;
			else if ( ! is_object( $response ) && ! is_int( $response ) && ! is_array( $response ) && true !== $response )
				$_response = new WP_Error( 'callback_error', __( 'An error occured when trying to perform the request.', WPML_SLUG ) );
			else
				$_response = new WPML_Ajax( array( 'data' => $response, 'i18n' => $i18n, 'nonce' => $nonce ) );

			self::json_header( is_wp_error( $_response ) );
			wp_die( json_encode( $_response ) );
		}

		/**
		 * General method for cache cleaning.
		 * 
		 * @since    1.0.0
		 * 
		 * @return   string|WP_Error    Result notification or WP_Error
		 */
		public static function empty_cache() {

			global $wpdb;

			$transient = self::clean_transient( null, $force = true );

			if ( false === $transient )
				return new WP_Error( 'transient_error', sprintf( __( 'An error occured when trying to delete transients: %s', WPML_SLUG ), $wpdb->last_error ) );
			else if ( ! $transient )
				return __( 'No transient found.', WPML_SLUG );
			else if ( $transient )
				return sprintf( _n( '1 transient deleted', '%s transients deleted.', $transient, WPML_SLUG ), $transient );
		}

		/**
		 * Handle Transients cleaning. Mainly used for deactivation and
		 * uninstallation actions, and occasionally manual cache cleaning.
		 * 
		 * When deactivating/uninstalling, delete all Plugin's related
		 * movie transient, depending on the Plugin settings.
		 * 
		 * @param    string     $action Are we deactivating or uninstalling
		 *                             the plugin?
		 * @param    boolean    $force Force cleaning
		 * 
		 * @return   int        $result Number of deleted rows
		 */
		public static function clean_transient( $action, $force = false ) {

			global $wpdb, $_wp_using_ext_object_cache;

			$force = ( true === $force );
			$result = 0;

			if ( ! $force ) {
				$_action = get_option( 'wpml_settings' );
				if ( ! $_action || ! isset( $_action[ $action ] ) || ! isset( $_action[ $action ]['cache'] ) )
					return false;

				$action = $_action[ $action ]['cache'];
				if ( is_array( $action ) )
					$action = $action[0];
			}

			if ( $force || ( ! $_wp_using_ext_object_cache && 'empty' == $action ) ) {
				$result = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE \"_transient_%wpml%\"" );
				$wpdb->query( 'OPTIMIZE TABLE ' . $wpdb->options );
			}

			return $result;
		}

		/**
		 * Filter 4040 error pages to intercept taxonomies listing pages.
		 * 
		 * Query should be 404 with no posts found and matching either one
		 * of the taxonomies slug.
		 * 
		 * @since    1.0.0
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
			$collection = WPML_Settings::taxonomies__collection_rewrite();
			$genre = WPML_Settings::taxonomies__genre_rewrite();
			$actor = WPML_Settings::taxonomies__actor_rewrite();
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
			$post = get_page_by_title( 'WPMovieLibrary Archives', OBJECT, 'wpml_page' );

			if ( is_null( $post ) ) {
				$wp_query = $_query;
				return false;
			}

			// WP_Query trick: use an internal dummy page
			$posts_per_page = $wp_query->query_vars['posts_per_page'];

			// Term selection
			if ( in_array( $slugs['collection'], array( $wp_query->query_vars['name'], $wp_query->query_vars['category_name'] ) ) ) {
				$term_slug = 'collection';
				$term_title = __( 'View all movies from collection &laquo; %s &raquo;', WPML_SLUG );
			}
			else if (in_array( $slugs['genre'], array( $wp_query->query_vars['name'], $wp_query->query_vars['category_name'] ) ) ) {
				$term_slug = 'genre';
				$term_title = __( 'View all &laquo; %s &raquo; movies', WPML_SLUG );
			}
			else if ( in_array( $slugs['actor'], array( $wp_query->query_vars['name'], $wp_query->query_vars['category_name'] ) ) ) {
				$term_slug = 'actor';
				$term_title = __( 'View all movies staring &laquo; %s &raquo;', WPML_SLUG );
			}
			else {
				$wp_query = $_query;
				return false;
			}

			$wp_query->query_vars['wpml_archive_page'] = 1;
			$wp_query->query_vars['wpml_archive_title'] = __( ucwords( $term_slug . 's' ), WPML_SLUG );

			$args = 'hide_empty=true&number=50';
			$paged = $wp_query->get( 'paged' );

			if ( $paged )
				$args .= '&offset=' . ( 50 * ( $paged - 1 ) );

			$terms = get_terms( $term_slug, $args );
			$total = wp_count_terms( $term_slug, 'hide_empty=true' );
			$content = '';

			if ( is_wp_error( $terms ) )
				$content = $terms->get_error_message();
			else 
				foreach ( $terms as $term )
					$content[] = sprintf(
						'<li><a href="%s" title="%s">%s (%s)</a></li>',
						get_term_link( $term ),
						sprintf( $term_title, $term->name ),
						$term->name,
						sprintf( _n( '%d movie', '%d movies', $term->count, WPML_SLUG ), $term->count )
					);

			if ( is_array( $content ) )
				$content = '<ul class="wpml_archives wpml_' . $term_slug . '_archives">' . implode( "\n", $content ) . '</ul>';

			$args = array(
				'type'    => 'list',
				'total'   => ceil( ( $total - 1 ) / 50 ),
				'current' => max( 1, $paged ),
				'format'  => home_url( $slugs[ $term_slug ] . '/page/%#%/' ),
			);
			$content .= self::paginate_links( $args );

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
		 * @since    1.1.0
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

			if ( 1 == $wp_query->get( 'wpml_archive_page' ) && '' != $wp_query->get( 'wpml_archive_title' ) )
				$name = $wp_query->get( 'wpml_archive_title' );

			return $name;
		}

		/**
		 * Retrieve paginated link for archive post pages.
		 * 
		 * This is a partial rewrite of WordPress paginate_links() function
		 * that doesn't work on the plugin's built-in archive pages.
		 * 
		 * @since    1.1.0
		 * 
		 * @param    array    $args Optional. Override defaults.
		 * 
		 * @return   string   String of page links or array of page links.
		*/
		private static function paginate_links( $args = '' ) {

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
			$page_links = array();
			$n = 0;
			$dots = false;

			if ( $current && 1 < $current ) :
				$link = str_replace( '%_%', $format, $base );
				$link = str_replace( '%#%', $current - 1, $link );
				$page_links[] = '<a class="prev page-numbers" href="' . esc_url( $link ) . '">' . $prev_text . '</a>';
			endif;
			for ( $n = 1; $n <= $total; $n++ ) :
				if ( $n == $current ) :
					$page_links[] = "<span class='page-numbers current'>" . number_format_i18n( $n ) . "</span>";
					$dots = true;
				else :
					if ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) :
						$link = str_replace( '%_%', $format, $base );
						$link = str_replace( '%#%', $n, $link );
						$page_links[] = "<a class='page-numbers' href='" . esc_url( $link ) . "'>" . number_format_i18n( $n ) . "</a>";
						$dots = true;
					elseif ( $dots ) :
						$page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';
						$dots = false;
					endif;
				endif;
			endfor;
			if ( $current && ( $current < $total || -1 == $total ) ) :
				$link = str_replace( '%_%', $format, $base );
				$link = str_replace( '%#%', $current + 1, $link );
				$page_links[] = '<a class="next page-numbers" href="' . esc_url( $link ) . '">' . $next_text . '</a>';
			endif;

			$r = '<ul class="page-numbers"><li>' . join( '</li><li>', $page_links ) . '</li></ul>';

			return $r;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {

			self::set_archive_page();
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

			self::delete_archive_page();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;
