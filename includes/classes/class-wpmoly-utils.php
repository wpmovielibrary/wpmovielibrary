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

			if ( $this->has_permalinks_changed() )
				add_action( 'admin_notices', array( $this, 'permalinks_changed_notice' ), 15 );

			add_filter( 'rewrite_rules_array', __CLASS__ . '::register_permalinks', 11 );

			add_filter( 'wpmoly_filter_meta_data', __CLASS__ . '::filter_meta_data', 10, 1 );
			add_filter( 'wpmoly_filter_crew_data', __CLASS__ . '::filter_crew_data', 10, 1 );
			add_filter( 'wpmoly_filter_cast_data', __CLASS__ . '::filter_cast_data', 10, 1 );
			add_filter( 'wpmoly_filter_movie_meta_aliases', __CLASS__ . '::filter_movie_meta_aliases', 10, 1 );

			add_filter( 'wpmoly_format_movie_genres', __CLASS__ . '::format_movie_genres', 10, 1 );
			add_filter( 'wpmoly_format_movie_actors', __CLASS__ . '::format_movie_actors', 10, 1 );
			add_filter( 'wpmoly_format_movie_cast', __CLASS__ . '::format_movie_cast', 10, 1 );
			add_filter( 'wpmoly_format_movie_date', __CLASS__ . '::format_movie_release_date', 10, 2 );
			add_filter( 'wpmoly_format_movie_release_date', __CLASS__ . '::format_movie_release_date', 10, 2 );
			add_filter( 'wpmoly_format_movie_runtime', __CLASS__ . '::format_movie_runtime', 10, 2 );
			add_filter( 'wpmoly_format_movie_spoken_languages', __CLASS__ . '::format_movie_languages', 10, 1 );
			add_filter( 'wpmoly_format_movie_languages', __CLASS__ . '::format_movie_languages', 10, 1 );
			add_filter( 'wpmoly_format_movie_countries', __CLASS__ . '::format_movie_countries', 10, 1 );
			add_filter( 'wpmoly_format_movie_production_countries', __CLASS__ . '::format_movie_countries', 10, 1 );
			add_filter( 'wpmoly_format_movie_production_companies', __CLASS__ . '::format_movie_producer', 10, 1 );
			add_filter( 'wpmoly_format_movie_director', __CLASS__ . '::format_movie_director', 10, 1 );
			add_filter( 'wpmoly_format_movie_producer', __CLASS__ . '::format_movie_producer', 10, 1 );
			add_filter( 'wpmoly_format_movie_composer', __CLASS__ . '::format_movie_composer', 10, 1);
			add_filter( 'wpmoly_format_movie_editor', __CLASS__ . '::format_movie_editor', 10, 1);
			add_filter( 'wpmoly_format_movie_author', __CLASS__ . '::format_movie_author', 10, 1);
			add_filter( 'wpmoly_format_movie_photography', __CLASS__ . '::format_movie_photography', 10, 1);
			add_filter( 'wpmoly_format_movie_field', __CLASS__ . '::format_movie_field', 10, 2 );

			add_filter( 'wpmoly_format_movie_media', __CLASS__ . '::format_movie_media', 10, 3 );
			add_filter( 'wpmoly_format_movie_status', __CLASS__ . '::format_movie_status', 10, 3 );
			add_filter( 'wpmoly_format_movie_rating', __CLASS__ . '::format_movie_rating', 10, 2 );
			add_filter( 'wpmoly_format_movie_language', __CLASS__ . '::format_movie_language', 10, 2 );
			add_filter( 'wpmoly_format_movie_subtitles', __CLASS__ . '::format_movie_subtitles', 10, 2 );
			add_filter( 'wpmoly_format_movie_format', __CLASS__ . '::format_movie_format', 10, 2 );

			add_filter( 'wpmoly_movie_meta_link', __CLASS__ . '::add_meta_link', 10, 3 );
			add_filter( 'wpmoly_format_movie_country_flag', __CLASS__ . '::add_country_flag', 10, 2 );

			add_filter( 'wpmoly_movie_rating_stars', __CLASS__ . '::get_movie_rating_stars', 10, 3 );
			add_filter( 'wpmoly_editable_rating_stars', __CLASS__ . '::get_editable_rating_stars', 10, 2 );

			add_filter( 'post_thumbnail_html', __CLASS__ . '::filter_default_thumbnail', 10, 5 );

			add_filter( 'get_the_terms', __CLASS__ . '::get_the_terms', 10, 3 );
			add_filter( 'wp_get_object_terms', __CLASS__ . '::get_ordered_object_terms', 10, 4 );

			add_action( 'template_redirect', __CLASS__ . '::filter_404', 10 );
			add_filter( 'post_type_archive_title', __CLASS__ . '::filter_post_type_archive_title', 10, 2 );
		}

		/**
		 * Check for a transient indicating permalinks were changed and
		 * structure not updated.
		 * 
		 * @since    2.0
		 * 
		 * @return   bool|string    False is no change was made or structure updated, changed permalinks option slug else
		 */
		private function has_permalinks_changed() {

			$changed = get_transient( 'wpmoly-permalinks-changed' );
			if ( false === $changed )
				return false;

			return $changed;
		}

		/**
		 * Check for changes on the URL Rewriting of Taxonomies to
		 * update the Rewrite Rules if needed. We need this to avoid
		 * users to get 404 when they try to access their content if they
		 * didn't previously reload the Dashboard Permalink page.
		 * 
		 * @since    2.0
		 * 
		 * @param    array    $field Settings field array
		 * @param    array    $value New setting value
		 * @param    array    $existing_value previous setting value
		 * 
		 * @return   array    Validated setting
		 */
		public static function permalinks_changed( $field, $value, $existing_value ) {

			$rewrites = array(
				'wpmoly-rewrite-movie',
				'wpmoly-rewrite-collection',
				'wpmoly-rewrite-genre',
				'wpmoly-rewrite-actor',
			);

			if ( ! isset( $field['id'] ) || ! in_array( $field['id'], $rewrites ) )
				return $value;

			if ( $existing_value == $value )
				return array( 'error' => false, 'value' => $value );

			$changed = set_transient( 'wpmoly-permalinks-changed', $field['id'] );

			return array( 'value' => $value );

		}

		/**
		 * Show a simple notice for admins to update their permalinks.
		 * 
		 * Hide the notice on Permalinks page, though, to avoid confusion
		 * as it could be interpreted as a failure to update permalinks,
		 * which it is not.
		 * 
		 * @since    2.0
		 */
		public static function permalinks_changed_notice() {

			global $hook_suffix;

			if ( 'options-permalink.php' == $hook_suffix )
				return false;

			echo self::render_template( 'admin/admin-notice.php', array( 'notice' => 'permalinks-changed' ), $require = 'always' );
		}

		/**
		 * Create a new set of permalinks for Movie Details
		 * 
		 * This method is called whenever permalinks are edited using
		 * the filter hook 'rewrite_rules_array'.
		 *
		 * @since    1.0
		 *
		 * @param    array     $rules Existing rewrite rules
		 */
		public static function register_permalinks( $rules = null ) {

			global $wp_rewrite;
			if ( '' == $wp_rewrite->permalink_structure )
				return $rules;

			$changed = delete_transient( 'wpmoly-permalinks-changed' );

			$new_rules = self::generate_custom_permalinks();

			if ( ! is_null( $rules ) )
				return $new_rules + $rules;
		}

		/**
		 * Create a new set of permalinks for movies to access movies by
		 * details and meta. 
		 * 
		 * This also add permalink structures for custom taxonomies and
		 * implement translation support for all custom permalinks.
		 *
		 * @since    2.0
		 *
		 * @return   array    $new_rules List of new to rules to add to the current rewrite rules.
		 */
		private static function generate_custom_permalinks() {

			$new_rules  = array();

			WPMOLY_L10n::delete_l10n_rewrite();
			WPMOLY_L10n::delete_l10n_rewrite_rules();
			$l10n_rules = WPMOLY_L10n::set_l10n_rewrite_rules();

			foreach ( $l10n_rules['taxonomies'] as $slug => $tax ) {
				$new_rules[ $tax . '/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?' . $slug . '=$matches[1]&feed=$matches[2]';
				$new_rules[ $tax . '/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?' . $slug . '=$matches[1]&feed=$matches[2]';
				$new_rules[ $tax . '/([^/]+)/page/?([0-9]{1,})/?$' ] = 'index.php?' . $slug . '=$matches[1]&paged=$matches[2]';
				$new_rules[ $tax . '/([^/]+)/?$' ] = 'index.php?' . $slug . '=$matches[1]';
			}

			foreach ( $l10n_rules['detail'] as $slug => $detail ) {

				$_detail = apply_filters( 'wpmoly_filter_rewrites', $detail );
				$new_rules[ $l10n_rules['movies'] . '/(' . $_detail . ')/([^/]+)/?$' ] = 'index.php?detail=$matches[1]&value=$matches[2]';
				$new_rules[ $l10n_rules['movies'] . '/(' . $_detail . ')/([^/]+)/page/?([0-9]{1,})/?$' ] = 'index.php?detail=$matches[1]&value=$matches[2]&paged=$matches[3]';
			}

			foreach ( $l10n_rules['meta'] as $slug => $meta ) {

				$_meta = apply_filters( 'wpmoly_filter_rewrites', $meta );
				$new_rules[ $l10n_rules['movies'] . '/(' . $_meta . ')/([^/]+)/?$' ] = 'index.php?meta=$matches[1]&value=$matches[2]';
				$new_rules[ $l10n_rules['movies'] . '/(' . $_meta . ')/([^/]+)/page/?([0-9]{1,})/?$' ] = 'index.php?meta=$matches[1]&value=$matches[2]&paged=$matches[3]';
			}

			return $new_rules;
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
		 * Format a Movie's languages for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_languages( $data ) {

			if ( is_null( $data ) || '' == $data )
				return $data;

			$languages = WPMOLY_Settings::get_available_languages();

			$data = explode( ',', $data );
			foreach ( $data as $i => $d ) {

				$d = trim( $d );
				foreach ( $languages as $lang ) {
					if ( $d == $lang['native'] ) {
						$url = apply_filters( 'wpmoly_movie_meta_link', 'spoken_languages', $lang['name'], 'meta' );
						$data[ $i ] = $url;
					}
				}
			}

			$data = implode( ', ', $data );
			$output = self::format_movie_field( $data );

			return $output;
		}

		/**
		 * Format a Movie's countries for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_countries( $data ) {

			if ( is_null( $data ) || '' == $data )
				return $data;

			$countries = WPMOLY_Settings::get_supported_countries();
			$format    = wpmoly_o( 'countries-format' );
			//$format    = array_map( create_function( '$item', 'return "[{$item}]";'), $format );

			$data = explode( ',', $data );
			foreach ( $data as $i => $d ) {

				$d = trim( $d );
				foreach ( $countries as $code => $country ) {

					if ( $d == $country['native'] ) {

						$_format = $format;
						foreach ( $_format as $_i => $_f ) {
							switch ( $_f ) {
								case 'flag':
									$_format[ $_i ] = apply_filters( 'wpmoly_format_movie_country_flag', $code, $country['name'] );
									break;
								case 'original':
									$_format[ $_i ] = $country['native'];
									break;
								case 'translated':
									$_format[ $_i ] = $country['name'];
									break;
								case 'ptranslated':
									$_format[ $_i ] = sprintf( '(%s)', $country['native'] );
									break;
								case 'poriginal':
									$_format[ $_i ] = sprintf( '(%s)', $country['name'] );
									break;
								default:
									$_format[ $_i ] = '';
									break;
							}

							if ( 'flag' != $_f && '' != $_format[ $_i ] )
								$_format[ $_i ] = apply_filters( 'wpmoly_movie_meta_link', 'production_countries', $_format[ $_i ], 'meta' );
						}

						$_format = implode( '&nbsp;', $_format );

						$data[ $i ] = $_format;
					}
				}
			}

			$data = implode( ',&nbsp; ', $data );
			$output = self::format_movie_field( $data );

			return $output;
		}

		/**
		 * Format a Movie's director for display
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_director( $data ) {

			$output = self::format_movie_terms_list( $data, 'collection' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's producer for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_producer( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'producer', $data, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's composer for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_composer( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'composer', $data, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's editor for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_editor( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'editor', $data, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's author for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_author( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'author', $data, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's  for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_photography( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'photography', $data, 'meta' );
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
		 * @param    string    $data detail value
		 * @param    string    $format data format, raw or HTML
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_media( $data, $format = 'html', $icon = false ) {

			return self::format_movie_detail( 'media', $data, $format, $icon );
		}

		/**
		 * Format a Movie's status. If format is HTML, will return a
		 * HTML formatted string; will return the value without change
		 * if raw is asked.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data detail value
		 * @param    string    $format data format, raw or HTML
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_status( $data, $format = 'html', $icon = false ) {

			return self::format_movie_detail( 'status', $data, $format, $icon );
		}

		/**
		 * Format a Movie's rating. If format is HTML, will return a
		 * HTML formatted string; will return the value without change
		 * if raw is asked.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data rating value
		 * @param    string    $format data format, raw or HTML
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_rating( $data, $format = 'html' ) {

			$format = ( 'raw' == $format ? 'raw' : 'html' );

			if ( '' == $data )
				return $data;

			if ( 'html' == $format ) {
				$data = apply_filters( 'wpmoly_movie_rating_stars', $data );
				$data = WPMovieLibrary::render_template( 'shortcodes/rating.php', array( 'data' => $data ), $require = 'always' );
			}

			return $data;
		}

		/**
		 * Format a Movie's language. If format is HTML, will return a
		 * HTML formatted string; will return the value without change
		 * if raw is asked.
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data detail value
		 * @param    string    $format data format, raw or HTML
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_language( $data, $format = 'html' ) {

			$format = ( 'raw' == $format ? 'raw' : 'html' );

			if ( '' == $data )
				return $data;

			if ( wpmoly_o( 'details-icons' ) && 'html' == $format  ) {
				$view = 'shortcodes/detail-icon-title.php';
			} else if ( 'html' == $format ) {
				$view = 'shortcodes/detail.php';
			}

			$title = array();
			$lang  = WPMOLY_Settings::get_available_movie_language();

			if ( ! is_array( $data ) )
				$data = array( $data );

			foreach ( $data as $d )
				if ( isset( $lang[ $d ] ) )
					$title[] = __( $lang[ $d ], 'wpmovielibrary' );

			$data = WPMovieLibrary::render_template( $view, array( 'detail' => 'lang', 'data' => 'lang', 'title' => implode( ', ', $title ) ), $require = 'always' );

			return $data;
		}

		/**
		 * Format a Movie's . If format is HTML, will return a
		 * HTML formatted string; will return the value without change
		 * if raw is asked.
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data detail value
		 * @param    string    $format data format, raw or HTML
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_subtitles( $data, $format = 'html' ) {

			$format = ( 'raw' == $format ? 'raw' : 'html' );

			if ( '' == $data )
				return $data;

			if ( wpmoly_o( 'details-icons' ) && 'html' == $format  ) {
				$view = 'shortcodes/detail-icon-title.php';
			} else if ( 'html' == $format ) {
				$view = 'shortcodes/detail.php';
			}

			$title = array();
			$lang  = WPMOLY_Settings::get_available_movie_language();

			if ( ! is_array( $data ) )
				$data = array( $data );

			foreach ( $data as $d )
				if ( isset( $lang[ $d ] ) )
					$title[] = __( $lang[ $d ], 'wpmovielibrary' );

			$data = WPMovieLibrary::render_template( $view, array( 'detail' => 'subtitle', 'data' => 'subtitles', 'title' => implode( ', ', $title ) ), $require = 'always' );

			return $data;
		}

		/**
		 * Format a Movie's . If format is HTML, will return a
		 * HTML formatted string; will return the value without change
		 * if raw is asked.
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data detail value
		 * @param    string    $format data format, raw or HTML
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_format( $data, $format = 'html', $icon = false ) {

			return self::format_movie_detail( 'status', $data, $format, $icon );
		}

		/**
		 * Format a Movie detail. If format is HTML, will return a
		 * HTML formatted string; will return the value without change
		 * if raw is asked.
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $detail details slug
		 * @param    string    $data detail value
		 * @param    string    $format data format, raw or HTML
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_detail( $detail, $data, $format = 'html', $icon = false ) {

			$format = ( 'raw' == $format ? 'raw' : 'html' );

			if ( '' == $data )
				return $data;

			if ( true === $icon || ( wpmoly_o( 'details-icons' ) && 'html' == $format ) ) {
				$view = 'shortcodes/detail-icon.php';
			} else if ( 'html' == $format ) {
				$view = 'shortcodes/detail.php';
			}

			$title = '';
			$default_fields = call_user_func( "WPMOLY_Settings::get_available_movie_{$detail}" );

			if ( ! is_array( $data ) )
				$data = array( $data );

			$_data = '';
			foreach ( $data as $d ) {
				if ( isset( $default_fields[ $d ] ) ) {
					$title = __( $default_fields[ $d ], 'wpmovielibrary' );
					$_data .= WPMovieLibrary::render_template( $view, array( 'detail' => $detail, 'data' => $d, 'title' => $title ), $require = 'always' );
				}
			}

			return $_data;
		}

		/**
		 * Add a meta link to the movie meta value
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $key Meta key
		 * @param    string    $value Meta value
		 * @param    string    $type Meta type, 'detail' or 'meta'
		 * 
		 * @return   string    Formatted output
		 */
		public static function add_meta_link( $key, $value, $type ) {

			if ( ! wpmoly_o( 'meta-links' ) || 'nowhere' == wpmoly_o( 'meta-links' ) || ( 'posts_only' == wpmoly_o( 'meta-links' ) && ! is_single() ) )
				return $value;

			if ( is_null( $key ) || is_null( $value ) || '' == $value )
				return $value;

			$link = explode( ',', $value );
			foreach ( $link as $i => $l ) {
				$l = trim( $l );
				$link[ $i ] = WPMOLY_L10n::get_meta_permalink( $key, $l, $type );
			}

			$link = implode( ', ', $link );

			return $link;
		}

		/**
		 * Add tiny flags before country names.
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $code Country ISO code
		 * @param    string    $name Country nam
		 * 
		 * @return   string    Formatted output
		 */
		public static function add_country_flag( $code, $name ) {

			if ( ! in_array( 'flag', wpmoly_o( 'countries-format' ) ) )
				return $name;

			$flag = '<span class="flag flag-%s" title="%s"></span>';
			$flag = sprintf( $flag, strtolower( $code ), $name );

			/**
			 * Apply filter to the rendered country flag
			 * 
			 * @since    2.0
			 * 
			 * @param    string    $flag HTML markup
			 * @param    string    $code Country ISO code
			 * @param    string    $name Country name
			 */
			$flag = apply_filters( 'wpmoly_filter_country_flag_html', $flag, $code, $name );

			return $flag;
		}

		/**
		 * Generate rating stars block.
		 * 
		 * If $editable is set to true and we're in admin, stars can be
		 * edited. $post_id isn't required but can be usefull as it is
		 * used to generated DOM element IDs.
		 * 
		 * @since    2.0
		 * 
		 * @param    float      $rating movie to turn into stars
		 * @param    int        $post_id movie's post ID
		 * @param    boolean    $editable Should the stars be editable
		 * 
		 * @return   string    Formatted output
		 */
		public static function get_movie_rating_stars( $rating, $post_id = null, $editable = false ) {

			if ( is_null( $post_id ) || ! intval( $post_id ) )
				$post_id = '';

			if ( 0 > $rating )
				$rating = 0.0;
			if ( 5.0 < $rating )
				$rating = 5.0;

			$_rating = preg_replace( '/([0-5])(\.|_)(0|5)/i', '$1-$3', $rating );

			$editable = ( is_admin() && true === $editable ? true : false );

			$class = "wpmoly-movie-rating wpmoly-movie-rating-{$_rating}";
			$prop  = array();
			if ( true === $editable ) {
				$class .= ' wpmoly-movie-editable-rating';
				$prop[] = 'onclick="wpmoly_rating.rate( ' . $post_id . ' );"';
				$prop[] = 'onmousemove="wpmoly_rating.change_in( event, ' . $post_id . ' );"';
				$prop[] = 'onmouseleave="wpmoly_rating.change_out( ' . $post_id . ' );"';
				$prop[] = 'data-rating="' . $rating . '"';
				$prop[] = 'data-rated=""';
			}

			$filled  = '<span class="wpmolicon icon-star-filled"></span>';
			$half    = '<span class="wpmolicon icon-star-half"></span>';
			$empty   = '<span class="wpmolicon icon-star-empty"></span>';

			$_filled = floor( $rating );
			$_half   = ceil( $rating - floor( $rating ) );
			$_empty  = ceil( 5.0 - ( $_filled + $_half ) );

			$stars  = '<div id="wpmoly-movie-rating-' . $post_id . '" class="' . $class . '"' . implode( ' ', $prop ) . '>';
			$stars .= str_repeat( $filled, $_filled );
			$stars .= str_repeat( $half, $_half );
			$stars .= str_repeat( $empty, $_empty );
			$stars .= '</div>';

			/**
			 * Filter generated HTML markup.
			 * 
			 * @since    2.0
			 * 
			 * @param    string    Stars HTML markup
			 * @param    float     Rating value
			 */
			$stars = apply_filters( 'wpmoly_movie_rating_stars_html', $stars, $rating );

			return $stars;
		}

		public static function get_editable_rating_stars( $rating, $post_id = null ) {

			/**
			 * Convert movie rating in HTML stars block.
			 * 
			 * @since    2.0
			 * 
			 * @param    float      $rating movie to turn into stars
			 * @param    int        $post_id movie's post ID
			 * @param    boolean    $editable Should the stars be editable
			 */
			return apply_filters( 'wpmoly_movie_rating_stars', $rating, $post_id, $editable = true );
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
		 * @since    1.1.0
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

			// Term ordering is killing quick/bulk edit, avoid it
			if ( is_admin() && 'edit-movie' == get_current_screen()->id )
				return $terms;

			// Only apply to "our" taxonomies
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
		public static function get_ordered_object_terms( $terms, $object_ids, $taxonomies, $args ) {

			if ( ! function_exists( 'get_current_screen' ) )
				return $terms;

			// Term ordering is killing quick/bulk edit, avoid it
			if ( is_admin() && 'edit-movie' == get_current_screen()->id )
				return $terms;

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
		 * @param    bool    $network_wide
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
