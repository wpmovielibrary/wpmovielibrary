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
 * @copyright 2016 CaerCam.org
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

			global $wp_version;

			if ( $this->has_permalinks_changed() )
				add_action( 'admin_notices', array( $this, 'permalinks_changed_notice' ), 15 );

			add_filter( 'rewrite_rules_array', __CLASS__ . '::register_permalinks', 11 );

			add_filter( 'wpmoly_filter_meta_data', __CLASS__ . '::filter_meta_data', 10, 1 );
			add_filter( 'wpmoly_filter_crew_data', __CLASS__ . '::filter_crew_data', 10, 1 );
			add_filter( 'wpmoly_filter_cast_data', __CLASS__ . '::filter_cast_data', 10, 1 );
			add_filter( 'wpmoly_filter_movie_meta_aliases', __CLASS__ . '::filter_movie_meta_aliases', 10, 1 );

			foreach ( $this->filters as $filter )
				add_filter( $filter['tag'], $filter['function'], $filter['priority'], $filter['args'] );

			add_filter( 'wpmoly_movie_meta_link', __CLASS__ . '::add_meta_link', 10, 1 );

			add_filter( 'wpmoly_movie_rating_stars', __CLASS__ . '::get_movie_rating_stars', 10, 4 );

			add_filter( 'post_thumbnail_html', __CLASS__ . '::filter_default_thumbnail', 10, 5 );

			add_filter( 'get_the_terms', __CLASS__ . '::get_the_terms', 10, 3 );

			if ( version_compare( $wp_version, '4.4', '>=' ) ) {
				add_filter( 'wp_get_object_terms', __CLASS__ . '::get_clifford_ordered_object_terms', 10, 4 );
			} else {
				add_filter( 'wp_get_object_terms', __CLASS__ . '::get_ordered_object_terms', 10, 4 );
			}
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
				'wpmoly-movie-archives',
				'wpmoly-collection-archives',
				'wpmoly-genre-archives',
				'wpmoly-actor-archives'
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

			$new_rules = self::generate_custom_rules( $rules );

			return $new_rules;
		}

		/**
		 * Create a new set of permalinks for movies to access movies by
		 * details and meta. Support custom views and sorting for the
		 * archive pages.
		 * 
		 * This also add permalink structures for custom taxonomies and
		 * implement translation support for all custom permalinks.
		 *
		 * @since    2.0
		 * 
		 * @param    array    $rules Current rewrite rules
		 *
		 * @return   array    $new_rules Updated rewrite rules
		 */
		private static function generate_custom_rules( $rules ) {

			$new_rules = array();

			WPMOLY_L10n::delete_l10n_rewrite();
			WPMOLY_L10n::delete_l10n_rewrite_rules();
			$l10n_rules = WPMOLY_L10n::set_l10n_rewrite_rules();

			$meta = sprintf(
				'(%s|%s)',
				implode( '|', $l10n_rules['meta'] ),
				implode( '|', $l10n_rules['detail'] )
			);

			$translate = wpmoly_o( 'rewrite-enable' );
			$archives = array(
				'movie'      => intval( wpmoly_o( 'movie-archives' ) ),
				'collection' => intval( wpmoly_o( 'collection-archives' ) ),
				'genre'      => intval( wpmoly_o( 'genre-archives' ) ),
				'actor'      => intval( wpmoly_o( 'actor-archives' ) )
			);
			extract( $archives );

			// Support alternative grid views
			$grid = '(grid|archives|list)';
			if ( '1' == $translate )
				$grid = sprintf( '(%s|%s|%s)', __( 'grid', 'wpmovielibrary' ), __( 'archives', 'wpmovielibrary' ), __( 'list', 'wpmovielibrary' ) );

			if ( $movie ) {
				$_movie = 'page_id=' . $movie;
			} else {
				$_movie = 'post_type=movie';
			}

			$new_rules[ $l10n_rules['movies'] . '/?$' ] = 'index.php?' . $_movie;
			$new_rules[ $l10n_rules['movies'] . '/' . $grid . '/?$' ] = 'index.php?' . $_movie . '&view=$matches[1]';
			$new_rules[ $l10n_rules['movies'] . '/' . $grid . '/(.*?)/?$' ] = 'index.php?' . $_movie . '&view=$matches[1]&sorting=$matches[2]';
			$new_rules[ $l10n_rules['movies'] . '/' . $meta . '/([^/]+)/?$' ] = 'index.php?' . $_movie . '&meta=$matches[1]&value=$matches[2]';
			$new_rules[ $l10n_rules['movies'] . '/' . $meta . '/([^/]+)/' . $grid . '/?$' ] = 'index.php?' . $movie . '&meta=$matches[1]&value=$matches[2]&view=$matches[3]';
			$new_rules[ $l10n_rules['movies'] . '/' . $meta . '/([^/]+)/' . $grid . '/(.*?)/?$' ] = 'index.php?' . $_movie . '&meta=$matches[1]&value=$matches[2]&view=$matches[3]&sorting=$matches[4]';
			$new_rules[ $l10n_rules['movies'] . '/page/([0-9]+)/?$' ] = 'index.php?' . $_movie . '&paged=$matches[1]';
			$new_rules[ $l10n_rules['movies'] . '/([^/]+)/([^/]+)/?$' ] = 'index.php?' . $_movie . '&meta=$matches[1]&value=$matches[2]';
			$new_rules[ $l10n_rules['movies'] . '/([^/]+)/([^/]+)/page/([0-9]+)/?$' ] = 'index.php?' . $_movie . '&meta=$matches[1]&value=$matches[2]&paged=$matches[3]';
			$new_rules[ $l10n_rules['movies'] . '/([^/]+)/([^/]+)/' . $grid . '/?$' ] = 'index.php?' . $_movie . '&meta=$matches[1]&value=$matches[2]&view=$matches[3]';
			$new_rules[ $l10n_rules['movies'] . '/([^/]+)/([^/]+)/' . $grid . '/(.*?)/?$' ] = 'index.php?' . $_movie . '&meta=$matches[1]&value=$matches[2]&view=$matches[3]&sorting=$matches[4]';

			if ( $collection && get_post( $collection ) ) {
				$title = get_post( $collection )->post_name;
				$slug  = 'collection';
				if ( '1' == $translate )
					$slug = $l10n_rules['collection'];

				$new_rules[ $slug . '/?$' ] = 'index.php?page_id=' . $collection;
				$new_rules[ $title . '/' . $grid . '/(.*?)/?$' ] = 'index.php?page_id=' . $collection . '&view=$matches[1]&sorting=$matches[2]';
			}

			if ( $genre && get_post( $genre ) ) {
				$title = get_post( $genre )->post_name;
				$slug  = 'genre';
				if ( '1' == $translate )
					$slug = $l10n_rules['genre'];

				$new_rules[ $slug . '/?$' ] = 'index.php?page_id=' . $genre;
				$new_rules[ $title . '/' . $grid . '/(.*?)/?$' ] = 'index.php?page_id=' . $genre . '&view=$matches[1]&sorting=$matches[2]';
			}

			if ( $actor && get_post( $actor ) ) {
				$title = get_post( $actor )->post_name;
				$slug  = 'actor';
				if ( '1' == $translate )
					$slug = $l10n_rules['actor'];

				$new_rules[ $slug . '/?$' ] = 'index.php?page_id=' . $actor;
				$new_rules[ $title . '/' . $grid . '/(.*?)/?$' ] = 'index.php?page_id=' . $actor . '&view=$matches[1]&sorting=$matches[2]';
			}

			$new_rules[ '([^/]+)/' . $grid . '/?$' ] = 'index.php?pagename=$matches[1]&view=$matches[2]';
			$new_rules[ '([^/]+)/' . $grid . '/(.*?)/?$' ] = 'index.php?pagename=$matches[1]&view=$matches[2]&sorting=$matches[3]';
			$new_rules[ '([^/]+)/' . $meta . '/([^/]+)/?$' ] = 'index.php?pagename=$matches[1]&meta=$matches[2]&value=$matches[3]';
			$new_rules[ '([^/]+)/' . $meta . '/([^/]+)/' . $grid . '/?$' ] = 'index.php?pagename=$matches[1]&meta=$matches[2]&value=$matches[3]&view=$matches[4]';
			$new_rules[ '([^/]+)/' . $meta . '/([^/]+)/' . $grid . '/(.*?)/?$' ] = 'index.php?pagename=$matches[1]&meta=$matches[2]&value=$matches[3]&view=$matches[4]&sorting=$matches[5]';

			$new_rules[ '([^/]+)/([^/]+)/' . $grid . '/?$' ] = 'index.php?pagename=$matches[1]/$matches[2]&view=$matches[3]';
			$new_rules[ '([^/]+)/([^/]+)/' . $grid . '/(.*?)/?$' ] = 'index.php?pagename=$matches[1]/$matches[2]&view=$matches[3]&sorting=$matches[4]';
			$new_rules[ '([^/]+)/([^/]+)/' . $meta . '/([^/]+)/?$' ] = 'index.php?pagename=$matches[1]/$matches[2]&meta=$matches[3]&value=$matches[4]';
			$new_rules[ '([^/]+)/([^/]+)/' . $meta . '/([^/]+)/' . $grid . '/?$' ] = 'index.php?pagename=$matches[1]/$matches[2]&meta=$matches[3]&value=$matches[4]&view=$matches[5]';
			$new_rules[ '([^/]+)/([^/]+)/' . $meta . '/([^/]+)/' . $grid . '/(.*?)/?$' ] = 'index.php?pagename=$matches[1]/$matches[2]&meta=$matches[3]&value=$matches[4]&view=$matches[5]&sorting=$matches[6]';

			$new_rules = $new_rules + $rules;

			WPMOLY_L10n::set_l10n_rewrite();

			return $new_rules;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                            Miscellaneous
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

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

			$filtered_meta = array();
			$supported_meta = WPMOLY_Settings::get_supported_movie_meta( 'meta' );

			foreach ( array_keys( $supported_meta ) as $key ) {
				if ( ! empty( $data[ $key ] ) ) {
					if ( is_array( $data[ $key ] ) ) {
						$filtered_meta[ $key ] = wp_list_pluck( $data[ $key ], 'name' );
					} else {
						$filtered_meta[ $key ] = $data[ $key ];
					}
				} else {
					$filtered_meta[ $key ] = '';
				}
			}

			if ( ! empty( $data['id'] ) ) {
				$filtered_meta['tmdb_id'] = $data['id'];
			}

			return $filtered_meta;
		}

		/**
		 * Filter a Movie's Crew to extract only supported data.
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

			$filtered_crew = array();

			$supported_jobs = WPMOLY_Settings::get_supported_movie_meta( 'crew' );
			foreach ( wp_list_pluck( $supported_jobs, 'job' ) as $slug => $job ) {
				$jobs = wp_filter_object_list( $data['crew'], array( 'job' => $job ) );
				if ( ! empty( $jobs ) ) {
					$filtered_crew[ $slug ] = array_values( wp_list_pluck( $jobs, 'name' ) );
				} else {
					$filtered_crew[ $slug ] = '';
				}
			}

			$filtered_crew['cast'] = apply_filters( 'wpmoly_filter_cast_data', $data['cast'] );

			return $filtered_crew;
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

			$data = wp_list_pluck( $data, 'name' );

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
		 *                          Custom Permalinks
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Add a meta link to the movie meta value
		 * 
		 * @since    2.0
		 * 
		 * @param    array     $args Link parameters
		 * 
		 * @return   string    Formatted output
		 */
		public static function add_meta_link( $args ) {

			$defaults = array(
				'key'   => null,
				'value' => null,
				'type'  => 'meta',
				'text'  => null,
				'title' => null,
			);
			$args = wp_parse_args( $args, $defaults );
			extract( $args );

			if ( ! wpmoly_o( 'meta-links' ) || 'nowhere' == wpmoly_o( 'meta-links' ) || ( 'posts_only' == wpmoly_o( 'meta-links' ) && ! is_single() ) )
				return $text;

			if ( is_null( $key ) || is_null( $value ) || '' == $value )
				return $value;

			$baseurl = get_post_type_archive_link( 'movie' );
			$link = explode( ',', $value );
			foreach ( $link as $i => $value ) {

				$value = trim( $value );
				$link[ $i ] = self::get_meta_permalink( compact( 'key', 'value', 'text', 'type', 'title', 'baseurl' ) );
			}

			$link = implode( ', ', $link );

			return $link;
		}

		/**
		 * Generate Custom Movie Meta permalinks
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $args Permalink parameters
		 * 
		 * @return   string    HTML href of raw URL
		 */
		public static function get_meta_permalink( $args ) {

			$defaults = array(
				'key'     => null,
				'value'   => null,
				'text'    => null,
				'type'    => null,
				'format'  => null,
				'baseurl' => null,
				'title'   => null
			);
			$args = wp_parse_args( $args, $defaults );
			extract( $args );

			if ( ! in_array( $type, array( 'meta', 'detail' ) ) )
				return null;

			if ( 'raw' !== $format )
				$format = 'html';

			if ( is_null( $title ) )
				$title = $value;

			if ( is_null( $text ) )
				$text = $value;

			$args = array(
				$type     => $key,
				'value'   => $value,
				'baseurl' => $baseurl
			);

			$url = self::build_meta_permalink( $args );

			if ( 'raw' == $format )
				return $url;

			$permalink = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $url, $title, $text );

			return $permalink;
		}

		/**
		 * Build Meta URL. Use an array of parameter to build a custom
		 * URLs for meta queries.
		 * 
		 * @since    2.1.1
		 * 
		 * @param    array     $args URL parameters to use
		 * 
		 * @return   string    Custom URL
		 */
		public static function build_meta_permalink( $args ) {

			global $wp_rewrite;
			$rewrite = ( '' != $wp_rewrite->permalink_structure );

			$defaults = array(
				'baseurl' => get_permalink(),
				'number'  => null,
				'columns' => null,
				'rows'    => null,
				'order'   => null,
				'orderby' => null,
				'paged'   => null,
				'meta'    => null,
				'detail'  => null,
				'value'   => null,
				'letter'  => null,
				'is_tax'  => false,
				'view'    => null
			);
			$args = wp_parse_args( $args, $defaults );

			$args['type'] = '';
			if ( '' != $args['meta'] && '' != $args['value'] ) {
				$args['type'] = 'meta';
			}
			else if ( '' != $args['detail'] && '' != $args['value'] ) {
				$args['type'] = 'detail';
				$args['meta'] = $args['detail'];
			}

			if ( '1' == wpmoly_o( 'rewrite-enable' ) ) {
				if ( 'production_countries' == $args['meta'] ) {
					$args['value'] = WPMOLY_L10n::get_country_standard_name( $args['value'] );
				} else if ( 'spoken_languages' == $args['meta'] ) {
					$args['value'] = WPMOLY_L10n::get_language_standard_name( $args['value'] );
				}
				$args['value'] = __( $args['value'], 'wpmovielibrary-iso' );
			}

			if ( 'rating' != $args['meta'] )
				$args['value'] = sanitize_title( $args['value'] );

			$args['meta'] = WPMOLY_L10n::translate_rewrite( $args['meta'] );
			$args['value'] = WPMOLY_L10n::translate_rewrite( $args['value'] );

			$url = '';
			if ( $rewrite )
				$url = self::build_custom_meta_permalink( $args );
			else
				$url = self::build_default_meta_permalink( $args );

			return $url;
		}

		/**
		 * Build a custom meta permalink for custom permalinks settings
		 * 
		 * This generate a user-friendly URL to access meta-based archive
		 * pages.
		 * 
		 * @since    2.1.1
		 * 
		 * @param    array    $args URL parameters
		 * 
		 * @return   string   Generated URL
		 */
		private static function build_custom_meta_permalink( $args ) {

			extract( $args );

			$movies = wpmoly_o( 'rewrite-movie' );
			if ( ! $movies )
				$movies = 'movies';

			$url = array();

			if ( '' != $meta && '' != $value ) {
				$url[] = $meta;
				$url[] = $value;
			}

			$grid = 'grid';
			if ( '' != $view )
				$grid = $view;

			if ( '1' == wpmoly_o( 'rewrite-enable' ) )
				$grid = __( $grid, 'wpmovielibrary' );

			$url[] = $grid;

			if ( '' != $letter )
				$url[] = $letter;

			if ( '' != $columns && '' != $rows )
				$url[] = "$columns:$rows";
			else if ( '' != $number )
				$url[] = $number;

			if ( '' != $orderby )
				$url[] = $orderby;

			if ( '' != $order )
				$url[] = $order;

			if ( 1 < $paged )
				$url[] = "page/$paged";

			if ( $grid == end( $url ) )
				$grid = array_pop( $url );

			$url = implode( '/', $url );
			$url = esc_url( $baseurl . $url );

			return $url;
		}

		/**
		 * Build a custom meta permalink for default permalinks settings
		 * 
		 * This generate a meta URL with raw URL parameters instead of
		 * nice user-friendly URLs if the user chose not to use WordPress
		 * permalinks.
		 * 
		 * @since    2.1.1
		 * 
		 * @param    array    $args URL parameters
		 * 
		 * @return   string   Generated URL
		 */
		private static function build_default_meta_permalink( $args ) {

			if ( false !== $args['is_tax'] )
				$type = $args['is_tax'];
			else
				$type = 'movie';

			if ( ! isset( $args['baseurl'] ) || empty( $args['baseurl'] ) ) {
				$page = intval( wpmoly_o( $type . '-archives' ) );
				$base  = 'index.php?';
				if ( $page )
					$base .= 'page_id=' . $page . '&';
				$base = home_url( "/${base}" );
			} else {
				$base = $args['baseurl'];
			}

			$url = array();

			if ( '' != $args['type'] && '' != $args['meta'] && '' != $args['value'] ) {
				$url[ $args['type'] ] = $args['meta'];
				$url['value'] = $args['value'];
			}

			unset( $args['type'], $args['meta'], $args['value'], $args['baseurl'], $args['is_tax'] );

			foreach ( $args as $slug => $arg )
				if ( '' != $arg )
					$url[ $slug ] = $arg;

			$url = esc_url( add_query_arg( $url, $base ) );

			return $url;
		}

		/**
		 * Generate Custom Taxonomies permalinks
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $taxonomy Taxonomy name
		 * @param    string    $value Text for the link
		 * 
		 * @return   string    HTML href of raw URL
		 */
		public static function get_taxonomy_permalink( $taxonomy, $value ) {

			$page_id = intval( wpmoly_o( "{$taxonomy}-archives" ) );
			if ( ! $page_id || ! get_post( $page_id ) )
				return $value;

			if ( '1' != wpmoly_o( 'rewrite-enable' ) )
				$url = home_url( sanitize_title( get_the_title( $page_id ) ) );
			else
				$url = get_permalink( $page_id );

			if ( false === $value )
				return $url;

			$permalink = sprintf( '<a href="%s" title="%s">%s</a>', $url, strip_tags( $value ), $value );

			return $permalink;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                              Utils
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

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
		public static function get_movie_rating_stars( $rating, $post_id = null, $base = null, $include_empty = false ) {

			$defaults = WPMOLY_Settings::get_supported_movie_details();

			if ( is_null( $post_id ) || ! intval( $post_id ) )
				$post_id = get_the_ID();

			if ( is_null( $base ) )
				$base = wpmoly_o( 'format-rating' );
			if ( 10 != $base )
				$base = 5;

			$rating = floatval( $rating );
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

			if ( 0.0 == $rating ) {
				if ( true === $include_empty ) {
					$stars  = '<div id="wpmoly-movie-rating-' . $post_id . '" class="' . $class . '" title="' . $title . '">';
					$stars .= str_repeat( $empty, 10 );
					$stars .= '</div>';
				} else {
					$stars  = '<div id="wpmoly-movie-rating-' . $post_id . '" class="not-rated" title="' . $title . '">';
					$stars .= sprintf( '<small><em>%s</em></small>', __( 'Not rated yet!', 'wpmovielibrary' ) );
					$stars .= '</div>';
				}
			}
			else if ( 10 == $base ) {
				$_filled = $rating * 2;
				$_empty  = 10 - $_filled;
				$title   = "{$_filled}/10 − {$title}";

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
				
				$term = trim( str_replace( array( '&#039;', "’" ), "'", $term ) );

				if ( $has_taxonomy ) {
					$_term = get_term_by( 'name', $term, $taxonomy );
					if ( ! $_term )
						$_term = get_term_by( 'slug', sanitize_title( $term ), $taxonomy );
				}
				else {
					$_term = $term;
				}

				if ( ! $_term )
					$_term = $term;

				if ( is_object( $_term ) && '' != $_term->name ) {
					$link = get_term_link( $_term, $taxonomy );
					$_term = ( is_wp_error( $link ) ? $_term->name : sprintf( '<a href="%s" title="%s">%s</a>', $link, sprintf( __( 'More movies from %s', 'wpmovielibrary' ), $_term->name ), $_term->name ) );
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

			$languages = self::get_used_meta( 'spoken_languages', $count );
			foreach ( $languages as $i => $language ) {
				$_language = WPMOLY_L10n::get_language_standard_name( $language['name'] );
				if ( ! $count ) {
					$used_languages[ $language['name'] ] = __( $_language, 'wpmovielibrary-iso' );
				} else {
					$used_languages[ $language['name'] ] = sprintf( '%s (%s)', __( $_language, 'wpmovielibrary-iso' ), sprintf( _n( '%d movie', '%d movies', $language['count'], 'wpmovielibrary' ), $language['count'] ) );
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

			$countries = self::get_used_meta( 'production_countries', $count );
			foreach ( $countries as $i => $country ) {
				$_country = WPMOLY_L10n::get_country_standard_name( $country['name'] );
				if ( ! $count ) {
					$used_countries[ $country['name'] ] = __( $_country, 'wpmovielibrary-iso' );
				} else {
					$used_countries[ $country['name'] ] = sprintf( '%s (%s)', __( $_country, 'wpmovielibrary-iso' ), sprintf( _n( '%d movie', '%d movies', $country['count'], 'wpmovielibrary' ), $country['count'] ) );
				}
			}

			$used_countries = array_unique( $used_countries );

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

			// Only apply to "our" taxonomies
			if ( ! in_array( $taxonomy, array( 'collection',  'genre',  'actor' ) ) )
				return $terms;

			// Term ordering is killing quick/bulk edit, avoid it
			if ( is_admin() && ( function_exists( 'get_current_screen' ) && 'edit-movie' == get_current_screen()->id ) )
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
		 * This method takes into consideration the changes brought by
		 * WordPress 4.4 which break the old self::get_ordered_object_terms()
		 * method.
		 * 
		 * @since    2.1.4.3
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
		public static function get_clifford_ordered_object_terms( $terms, $object_ids, $taxonomies, $args ) {

			$total = count( $terms );
			$original_terms = $terms;

			// Term ordering is killing quick/bulk edit, avoid it
			if ( is_admin() && ( function_exists( 'get_current_screen' ) && 'edit-movie' == get_current_screen()->id ) )
				return $terms;

			$taxonomies = explode( ', ', str_replace( "'", "", $taxonomies ) );
			if ( $taxonomies != "'collection', 'actor', 'genre'" && ( ! in_array( 'collection', $taxonomies ) && ! in_array( 'actor', $taxonomies ) && ! in_array( 'genre', $taxonomies ) ) )
				return $terms;

			global $wpdb;

			if ( empty( $object_ids ) || empty( $taxonomies ) )
				return array();

			if ( !is_array($taxonomies) )
				$taxonomies = array($taxonomies);

			foreach ( $taxonomies as $taxonomy ) {
				if ( ! taxonomy_exists($taxonomy) )
					return new WP_Error('invalid_taxonomy', __('Invalid taxonomy'));
			}

			if ( !is_array($object_ids) )
				$object_ids = array($object_ids);
			$object_ids = array_map('intval', $object_ids);

			$defaults = array(
				'orderby' => 'term_order',
				'order'   => 'ASC',
				'fields'  => 'all',
				'parent'  => '',
				'update_term_meta_cache' => true,
				'meta_query' => '',
			);
			$args = wp_parse_args( $args, $defaults );

			$terms = array();
			if ( count($taxonomies) > 1 ) {
				foreach ( $taxonomies as $index => $taxonomy ) {
					$t = get_taxonomy($taxonomy);
					if ( isset($t->args) && is_array($t->args) && $args != array_merge($args, $t->args) ) {
						unset($taxonomies[$index]);
						$terms = array_merge($terms, self::get_clifford_ordered_object_terms($object_ids, $taxonomy, array_merge($args, $t->args)));
					}
				}
			} else {
				$t = get_taxonomy($taxonomies[0]);
				if ( isset($t->args) && is_array($t->args) )
					$args = array_merge($args, $t->args);
			}

			$orderby = 'term_order';	// Replace $args['orderby']
			$order = 'ASC';			// Replace $args['order']
			$fields = $args['fields'];

			if ( in_array( $orderby, array( 'term_id', 'name', 'slug', 'term_group' ) ) ) {
				$orderby = "t.$orderby";
			} elseif ( in_array( $orderby, array( 'count', 'parent', 'taxonomy', 'term_taxonomy_id' ) ) ) {
				$orderby = "tt.$orderby";
			} elseif ( 'term_order' === $orderby ) {
				$orderby = 'tr.term_order';
			} elseif ( 'none' === $orderby ) {
				$orderby = '';
				$order = '';
			} else {
				$orderby = 't.term_id';
			}

			// tt_ids queries can only be none or tr.term_taxonomy_id
			if ( ('tt_ids' == $fields) && !empty($orderby) )
				$orderby = 'tr.term_taxonomy_id';

			if ( !empty($orderby) )
				$orderby = "ORDER BY $orderby";

			$order = strtoupper( $order );
			if ( '' !== $order && ! in_array( $order, array( 'ASC', 'DESC' ) ) )
				$order = 'ASC';

			$taxonomy_array = $taxonomies;
			$object_id_array = $object_ids;
			$taxonomies = "'" . implode("', '", $taxonomies) . "'";
			$object_ids = implode(', ', $object_ids);

			$select_this = '';
			if ( 'all' == $fields ) {
				$select_this = 't.*, tt.*';
			} elseif ( 'ids' == $fields ) {
				$select_this = 't.term_id';
			} elseif ( 'names' == $fields ) {
				$select_this = 't.name';
			} elseif ( 'slugs' == $fields ) {
				$select_this = 't.slug';
			} elseif ( 'all_with_object_id' == $fields ) {
				$select_this = 't.*, tt.*, tr.object_id';
			}

			$where = array(
				"tt.taxonomy IN ($taxonomies)",
				"tr.object_id IN ($object_ids)",
			);

			if ( '' !== $args['parent'] ) {
				$where[] = $wpdb->prepare( 'tt.parent = %d', $args['parent'] );
			}

			// Meta query support.
			$meta_query_join = '';
			if ( ! empty( $args['meta_query'] ) ) {
				$mquery = new WP_Meta_Query( $args['meta_query'] );
				$mq_sql = $mquery->get_sql( 'term', 't', 'term_id' );

				$meta_query_join .= $mq_sql['join'];

				// Strip leading AND.
				$where[] = preg_replace( '/^\s*AND/', '', $mq_sql['where'] );
			}

			$where = implode( ' AND ', $where );

			$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id $meta_query_join WHERE $where $orderby $order";

			$objects = false;
			if ( 'all' == $fields || 'all_with_object_id' == $fields ) {
				$_terms = $wpdb->get_results( $query );
				$object_id_index = array();
				foreach ( $_terms as $key => $term ) {
					$term = sanitize_term( $term, $taxonomy, 'raw' );
					$_terms[ $key ] = $term;

					if ( isset( $term->object_id ) ) {
						$object_id_index[ $key ] = $term->object_id;
					}
				}

				update_term_cache( $_terms );
				$_terms = array_map( 'get_term', $_terms );

				// Re-add the object_id data, which is lost when fetching terms from cache.
				if ( 'all_with_object_id' === $fields ) {
					foreach ( $_terms as $key => $_term ) {
						if ( isset( $object_id_index[ $key ] ) ) {
							$_term->object_id = $object_id_index[ $key ];
						}
					}
				}

				$terms = array_merge( $terms, $_terms );
				$objects = true;

			} elseif ( 'ids' == $fields || 'names' == $fields || 'slugs' == $fields ) {
				$_terms = $wpdb->get_col( $query );
				$_field = ( 'ids' == $fields ) ? 'term_id' : 'name';
				foreach ( $_terms as $key => $term ) {
					$_terms[$key] = sanitize_term_field( $_field, $term, $term, $taxonomy, 'raw' );
				}
				$terms = array_merge( $terms, $_terms );
			} elseif ( 'tt_ids' == $fields ) {
				$terms = $wpdb->get_col("SELECT tr.term_taxonomy_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tr.object_id IN ($object_ids) AND tt.taxonomy IN ($taxonomies) $orderby $order");
				foreach ( $terms as $key => $tt_id ) {
					$terms[$key] = sanitize_term_field( 'term_taxonomy_id', $tt_id, 0, $taxonomy, 'raw' ); // 0 should be the term id, however is not needed when using raw context.
				}
			}

			// Update termmeta cache, if necessary.
			if ( $args['update_term_meta_cache'] && ( 'all' === $fields || 'all_with_object_ids' === $fields || 'term_id' === $fields ) ) {
				if ( 'term_id' === $fields ) {
					$term_ids = $fields;
				} else {
					$term_ids = wp_list_pluck( $terms, 'term_id' );
				}

				update_termmeta_cache( $term_ids );
			}

			if ( ! $terms ) {
				$terms = array();
			} elseif ( $objects && 'all_with_object_id' !== $fields ) {
				$_tt_ids = array();
				$_terms = array();
				foreach ( $terms as $term ) {
					if ( in_array( $term->term_taxonomy_id, $_tt_ids ) ) {
						continue;
					}

					$_tt_ids[] = $term->term_taxonomy_id;
					$_terms[] = $term;
				}
				$terms = $_terms;
			} elseif ( ! $objects ) {
				$terms = array_values( array_unique( $terms ) );
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

			$total = count( $terms );
			$original_terms = $terms;

			// Term ordering is killing quick/bulk edit, avoid it
			if ( is_admin() && ( function_exists( 'get_current_screen' ) && 'edit-movie' == get_current_screen()->id ) )
				return $terms;

			if ( empty( $object_ids ) || empty( $taxonomies ) )
				return array();

			$taxonomies = explode( ', ', str_replace( "'", "", $taxonomies ) );
			if ( $taxonomies != "'collection', 'actor', 'genre'" && ( ! in_array( 'collection', $taxonomies ) && ! in_array( 'actor', $taxonomies ) && ! in_array( 'genre', $taxonomies ) ) )
				return $terms;

			global $wpdb;

			if ( !is_array( $taxonomies ) )
				$taxonomies = array( $taxonomies );

			foreach ( $taxonomies as $taxonomy ) {
				if ( ! taxonomy_exists( $taxonomy ) )
					return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy' ) );
			}

			if ( ! is_array( $object_ids ) )
				$object_ids = array( $object_ids );
			$object_ids = array_map( 'intval', $object_ids );

			$defaults = array(
				'orderby' => 'name',
				'order'   => 'ASC',
				'fields'  => 'all',
				'parent'  => '',
			);
			$args = wp_parse_args( $args, $defaults );

			$terms = array();
			if ( count( $taxonomies ) > 1 ) {
				foreach ( $taxonomies as $index => $taxonomy ) {
					$t = get_taxonomy( $taxonomy );
					if ( isset( $t->args ) && is_array( $t->args ) && $args != array_merge( $args, $t->args ) ) {
						unset( $taxonomies[ $index ] );
						$terms = array_merge( $terms, self::get_ordered_object_terms( $object_ids, $taxonomy, array_merge( $args, $t->args ) ) );
					}
				}
			}
			else {
				$t = get_taxonomy( $taxonomies[0] );
				if ( isset( $t->args ) && is_array( $t->args ) )
					$args = array_merge( $args, $t->args );
			}

			$orderby = 'term_order';	// Replace $args['orderby']
			$order = 'ASC';			// Replace $args['order']
			$fields = $args['fields'];

			if ( in_array( $orderby, array( 'term_id', 'name', 'slug', 'term_group' ) ) ) {
				$orderby = "t.$orderby";
			} else if ( in_array( $orderby, array( 'count', 'parent', 'taxonomy', 'term_taxonomy_id' ) ) ) {
				$orderby = "tt.$orderby";
			} else if ( 'term_order' === $orderby ) {
				$orderby = 'tr.term_order';
			} else if ( 'none' === $orderby ) {
				$orderby = '';
				$order = '';
			} else {
				$orderby = 't.term_id';
			}

			// tt_ids queries can only be none or tr.term_taxonomy_id
			if ( ('tt_ids' == $fields) && !empty($orderby) )
				$orderby = 'tr.term_taxonomy_id';

			if ( !empty($orderby) )
				$orderby = "ORDER BY $orderby";

			$order = strtoupper( $order );
			if ( '' !== $order && ! in_array( $order, array( 'ASC', 'DESC' ) ) )
				$order = 'ASC';

			$taxonomy_array = $taxonomies;
			$object_id_array = $object_ids;
			$taxonomies = "'" . implode("', '", array_map( 'esc_sql', $taxonomies ) ) . "'";
			$object_ids = implode(', ', $object_ids);

			$select_this = '';
			if ( 'all' == $fields ) {
				$select_this = 't.*, tt.*';
			} elseif ( 'ids' == $fields ) {
				$select_this = 't.term_id';
			} elseif ( 'names' == $fields ) {
				$select_this = 't.name';
			} elseif ( 'slugs' == $fields ) {
				$select_this = 't.slug';
			} elseif ( 'all_with_object_id' == $fields ) {
				$select_this = 't.*, tt.*, tr.object_id';
			}

			$where = array(
				"tt.taxonomy IN ($taxonomies)",
				"tr.object_id IN ($object_ids)",
			);

			if ( '' !== $args['parent'] ) {
				$where[] = $wpdb->prepare( 'tt.parent = %d', $args['parent'] );
			}

			$where = implode( ' AND ', $where );

			$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE $where $orderby $order";

			$objects = false;
			if ( 'all' == $fields || 'all_with_object_id' == $fields ) {
				$_terms = $wpdb->get_results( $query );
				foreach ( $_terms as $key => $term ) {
					$_terms[$key] = sanitize_term( $term, $taxonomy, 'raw' );
				}
				$terms = array_merge( $terms, $_terms );
				update_term_cache( $terms );
				$objects = true;
			} elseif ( 'ids' == $fields || 'names' == $fields || 'slugs' == $fields ) {
				$_terms = $wpdb->get_col( $query );
				$_field = ( 'ids' == $fields ) ? 'term_id' : 'name';
				foreach ( $_terms as $key => $term ) {
					$_terms[$key] = sanitize_term_field( $_field, $term, $term, $taxonomy, 'raw' );
				}
				$terms = array_merge( $terms, $_terms );
			} elseif ( 'tt_ids' == $fields ) {
				$terms = $wpdb->get_col("SELECT tr.term_taxonomy_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tr.object_id IN ($object_ids) AND tt.taxonomy IN ($taxonomies) $orderby $order");
				foreach ( $terms as $key => $tt_id ) {
					$terms[$key] = sanitize_term_field( 'term_taxonomy_id', $tt_id, 0, $taxonomy, 'raw' ); // 0 should be the term id, however is not needed when using raw context.
				}
			}

			if ( ! $terms ) {
				$terms = array();
			} elseif ( $objects && 'all_with_object_id' !== $fields ) {
				$_tt_ids = array();
				$_terms = array();
				foreach ( $terms as $term ) {
					if ( in_array( $term->term_taxonomy_id, $_tt_ids ) ) {
						continue;
					}
					$_tt_ids[] = $term->term_taxonomy_id;
					$_terms[] = $term;
				}
				$terms = $_terms;
			} elseif ( ! $objects ) {
				$terms = array_values( array_unique( $terms ) );
			}

			return $terms;
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
		}

	}

endif;
