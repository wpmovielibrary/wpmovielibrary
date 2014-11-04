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

if ( ! class_exists( 'WPMOLY_Formatting_Meta' ) )
	require_once WPMOLY_PATH . '/includes/classes/class-wpmoly-formatting-meta.php';
if ( ! class_exists( 'WPMOLY_Formatting_Details' ) )
	require_once WPMOLY_PATH . '/includes/classes/class-wpmoly-formatting-details.php';

if ( ! class_exists( 'WPMOLY_Utils' ) ) :

	class WPMOLY_Utils extends WPMOLY_Module {

		/**
		 * Formatting and filtering hooks
		 *
		 * @since    2.0
		 * @var      array
		 */
		private $filters = array();

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {

			$this->init();
			$this->register_hook_callbacks();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {

			$methods = array(
				'WPMOLY_Formatting_Meta'    => get_class_methods( 'WPMOLY_Formatting_Meta' ),
				'WPMOLY_Formatting_Details' => get_class_methods( 'WPMOLY_Formatting_Details' )
			);

			if ( empty( $methods ) )
				return false;

			foreach ( $methods as $class => $methods ) {
				if ( ! empty( $methods ) ) {
					foreach ( $methods as $method ) {
						if ( class_exists( 'ReflectionMethod' ) ) {
							$reflection = new ReflectionMethod( $class, $method );
							$args = count( $reflection->getParameters() );
						}
						else {
							$args = 4;
						}

						$tag      = "wpmoly_$method";
						$function = "$class::$method";
						$priority = $class::$priority;

						$this->filters[] = compact( 'tag', 'function', 'priority', 'args' );
					}
				}
			}
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

			foreach ( $this->filters as $filter )
				add_filter( $filter['tag'], $filter['function'], $filter['priority'], $filter['args'] );

			add_filter( 'wpmoly_movie_meta_link', __CLASS__ . '::add_meta_link', 10, 3 );

			add_filter( 'wpmoly_movie_rating_stars', __CLASS__ . '::get_movie_rating_stars', 10, 3 );

			add_filter( 'post_thumbnail_html', __CLASS__ . '::filter_default_thumbnail', 10, 5 );

			add_filter( 'get_the_terms', __CLASS__ . '::get_the_terms', 10, 3 );
			add_filter( 'wp_get_object_terms', __CLASS__ . '::get_ordered_object_terms', 10, 4 );

			//add_action( 'template_redirect', __CLASS__ . '::filter_404', 10 );
			add_filter( 'post_type_archive_title', __CLASS__ . '::filter_post_type_archive_title', 10, 2 );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                            Permalinks
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

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

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                            Miscellaneous
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

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

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Filtering
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

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

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                              Utils
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

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
		 * Generate rating stars block.
		 * 
		 * @since    2.0
		 * 
		 * @param    float      $rating movie to turn into stars
		 * @param    int        $post_id movie's post ID
		 * @param    int        $base 5-stars or 10-stars?
		 * 
		 * @return   string    Formatted output
		 */
		public static function get_movie_rating_stars( $rating, $post_id = null, $base = null ) {

			$defaults = WPMOLY_Settings::get_supported_movie_details();

			if ( is_null( $post_id ) || ! intval( $post_id ) )
				$post_id = '';

			if ( is_null( $base ) )
				$base = wpmoly_o( 'format-rating' );
			if ( 10 != $base )
				$base = 5;

			if ( 0 > $rating )
				$rating = 0.0;
			if ( 5.0 < $rating )
				$rating = 5.0;

			$title = '';
			if ( isset( $defaults['rating']['options'][ $rating ] ) )
				$title = $defaults['rating']['options'][ $rating ];

			$_rating = preg_replace( '/([0-5])(\.|_)(0|5)/i', '$1-$3', $rating );

			$class   = "wpmoly-movie-rating wpmoly-movie-rating-{$_rating}";

			$filled  = '<span class="wpmolicon icon-star-filled"></span>';
			$half    = '<span class="wpmolicon icon-star-half"></span>';
			$empty   = '<span class="wpmolicon icon-star-empty"></span>';

			$_filled = floor( $rating );
			$_half   = ceil( $rating - floor( $rating ) );
			$_empty  = ceil( 5.0 - ( $_filled + $_half ) );

			if ( 10 == $base ) {
				$_filled = $rating * 2;
				$_empty  = 10 - $_filled;
				$title   = "{$rating}/10 − {$title}";

				$stars  = '<div id="wpmoly-movie-rating-' . $post_id . '" class="' . $class . '" title="' . $title . '">';
				$stars .= str_repeat( $filled, $_filled );
				$stars .= str_repeat( $empty, $_empty );
				$stars .= '</div>';
			}
			else {
				$title   = "{$rating}/5 − {$title}";
				$stars  = '<div id="wpmoly-movie-rating-' . $post_id . '" class="' . $class . '" title="' . $title . '">';
				$stars .= str_repeat( $filled, $_filled );
				$stars .= str_repeat( $half, $_half );
				$stars .= str_repeat( $empty, $_empty );
				$stars .= '</div>';
			}

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
		public static function format_movie_terms_list( $data, $taxonomy ) {

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

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Used meta
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Generate a list of all movies years available
		 * 
		 * @since    2.0
		 * 
		 * @param    boolean    $count Shall we add count?
		 * 
		 * @return   array   
		 */
		public static function get_used_years( $count = false ) {

			$used_years = array();

			if ( true !== $count )
				$count = false;

			$years = self::get_used_meta( 'release_date' );
			foreach ( $years as $i => $year ) {

				$year = date_i18n( 'Y', strtotime( $year['name'] ) );

				if ( ! isset( $used_years[ $year ] ) )
					$used_years[ $year ] = array( 'name' => '', 'count' => '' );

				$used_years[ $year ]['name'] = $year;
				if ( $count )
					$used_years[ $year ]['count']++;
			}

			if ( $count ) {
				usort( $used_years, __CLASS__ . '::order_by_count' );
				$used_years = array_reverse( $used_years );
			}

			foreach ( $used_years as $i => $year ) {
				unset( $used_years[ $i ] );
				if ( ! $count )
					$used_years[ $year['name'] ] = $year['name'];
				else
					$used_years[ $year['name'] ] = sprintf( '%s (%s)', $year['name'], sprintf( _n( '%d movie', '%d movies', $year['count'], 'wpmovielibrary' ), $year['count'] ) );
			}

			return $used_years;
		}

		/**
		 * Generate a list of all languages used in movies
		 * 
		 * @since    2.0
		 * 
		 * @param    boolean    $count Shall we add count?
		 * 
		 * @return   array   
		 */
		public static function get_used_languages( $count = false ) {

			$used_languages = array();

			if ( true !== $count )
				$count = false;

			$supported = WPMOLY_Settings::get_available_languages();
			foreach ( $supported as $i => $s ) {
				unset( $supported[ $i ] );
				$supported[ $s['native'] ] = $s['name'];
			}

			$languages = self::get_used_meta( 'spoken_languages', $count );
			foreach ( $languages as $i => $language ) {
				if ( isset( $supported[ $language['name'] ] ) ) {
					if ( ! $count )
						$used_languages[ $language['name'] ] = $supported[ $language['name'] ];
					else
						$used_languages[ $language['name'] ] = sprintf( '%s (%s)', $supported[ $language['name'] ], sprintf( _n( '%d movie', '%d movies', $language['count'], 'wpmovielibrary' ), $language['count'] ) );
				}
			}

			$used_languages = array_unique( $used_languages );

			return $used_languages;
		}

		/**
		 * Generate a list of all countries used in movies
		 * 
		 * @since    2.0
		 * 
		 * @param    boolean    $count Shall we add count?
		 * 
		 * @return   array   
		 */
		public static function get_used_countries( $count = false ) {

			$used_countries = array();

			if ( true !== $count )
				$count = false;

			$supported = WPMOLY_Settings::get_supported_countries();
			foreach ( $supported as $i => $s ) {
				unset( $supported[ $i ] );
				$supported[ $s['native'] ] = $s['name'];
			}

			$countries = self::get_used_meta( 'production_countries', $count );
			foreach ( $countries as $i => $country ) {
				if ( isset( $supported[ $country['name'] ] ) ) {
					if ( ! $count )
						$used_countries[ $country['name'] ] = $supported[ $country['name'] ];
					else
						$used_countries[ $country['name'] ] = sprintf( '%s (%s)', $supported[ $country['name'] ], sprintf( _n( '%d movie', '%d movies', $country['count'], 'wpmovielibrary' ), $country['count'] ) );
				}
			}

			return $used_countries;
		}

		/**
		 * Generate a list of all production companies used in movies
		 * 
		 * @since    2.0
		 * 
		 * @param    boolean    $count Shall we add count?
		 * 
		 * @return   array   
		 */
		public static function get_used_companies( $count = false ) {

			$used_companies = array();

			if ( true !== $count )
				$count = false;

			$companies = self::get_used_meta( 'production_companies', $count );
			foreach ( $companies as $i => $company ) {
				if ( ! $count )
					$used_companies[ $company['name'] ] = $company['name'];
				else
					$used_companies[ $company['name'] ] = sprintf( '%s (%s)', $company['name'], sprintf( _n( '%d movie', '%d movies', $company['count'], 'wpmovielibrary' ), $company['count'] ) );
			}

			return $used_companies;
		}

		/**
		 * Generate a list of all certifications used in movies
		 * 
		 * @since    2.0
		 * 
		 * @param    boolean    $count Shall we add count?
		 * 
		 * @return   array   
		 */
		public static function get_used_certifications( $count = false ) {

			$used_certification = array();

			if ( true !== $count )
				$count = false;

			$certifications = self::get_used_meta( 'certification', $count );
			foreach ( $certifications as $i => $certification ) {
				if ( ! $count )
					$used_certification[ $certification['name'] ] = $certification['name'];
				else
					$used_certification[ $certification['name'] ] = sprintf( '%s (%s)', $certification['name'], sprintf( _n( '%d movie', '%d movies', $certification['count'], 'wpmovielibrary' ), $certification['count'] ) );
			}

			return $used_certification;
		}

		/**
		 * Generate a list of all meta used in movies
		 * 
		 * This method returns a list of all distinct values for a specific
		 * meta.
		 * 
		 * @since    2.0
		 * 
		 * @param    string     $meta meta_key to collect
		 * @param    boolean    $count Shall we add count?
		 * 
		 * @return   array   
		 */
		public static function get_used_meta( $meta, $count = false ) {

			$supported = array_keys( WPMOLY_Settings::get_supported_movie_meta() );
			if ( ! in_array( $meta, $supported ) )
				return array();

			$used = array();
			if ( true !== $count )
				$count = false;

			$distinct = ( ! $count ? 'DISTINCT' : '' );
			global $wpdb;
			$values = $wpdb->get_results(
				"SELECT {$distinct} meta_value
				 FROM {$wpdb->postmeta}
				 WHERE meta_key LIKE '_wpmoly_movie_{$meta}'
				 ORDER BY meta_value"
			);

			foreach ( $values as $i => $value ) {
				$value = explode( ',', $value->meta_value );
				foreach ( $value as $v ) {
					$v = trim( $v );
					if ( $v ) {
						if ( ! isset( $used[ $v ] ) )
							$used[ $v ] = array( 'name' => '', 'count' => '' );
						$used[ $v ]['name'] = $v;
						if ( $count )
							$used[ $v ]['count']++;
					}
				}
			}

			if ( $count ) {
				usort( $used, __CLASS__ . '::order_by_count' );
				$used = array_reverse( $used );
			}

			return $used;
		}

		/**
		 * Order an array by a counter value set in subarrays
		 * 
		 * @since    2.0
		 * 
		 * @param    int    First count
		 * @param    int    Second count
		 * 
		 * @return   int
		 */
		public static function order_by_count( $a, $b ) {
			return $a['count'] - $b['count'];
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                      Overrides and bypassings
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

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
		 * Deactivate all child plugins when deactivating WPMovieLibrary
		 * to avoid errors.
		 *
		 * @since    2.0
		 *
		 * @param    bool    $network_wide
		 */
		public static function wpmoly_required() {

			$chidren = array();
			$wpmoly  = WPMOLY_PLUGIN;
			$parent  = plugin_basename( WPMOLY_PATH );
			$plugins = get_plugins();

			foreach ( $plugins as $slug => $plugin ) {
				if ( false !== stripos( $slug, $parent ) && $slug != $wpmoly && is_plugin_active( $slug ) ) {
					$chidren[] = "<strong>{$plugin['Name']}</strong>";
					deactivate_plugins( $slug );
				}
			}
		
			if ( ! empty( $chidren ) )
				wp_die( sprintf( __( '<strong>%s</strong> is required by the following plugins: %s. These plugins have been deactivated to prevent errors.<br /><br />Back to the WordPress <a href="%s">Plugins page</a>.', 'wpmovielibrary' ), WPMOLY_NAME, implode( ', ', $chidren ), admin_url( 'plugins.php' ) ) );
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

			// About page
			update_option( '_wpmoly_fresh_install', 'yes' );
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {

			WPMOLY_Cache::clean_transient( 'deactivate' );
			delete_option( 'rewrite_rules' );

			self::wpmoly_required();
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

	}

endif;
