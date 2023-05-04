<?php
/**
 * WPMovieLibrary Archives Class extension.
 * 
 * This class contains the Custom Archives pages methods and hooks.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Archives' ) ) :

	class WPMOLY_Archives extends WPMOLY_Module {

		protected $pages = array();

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {

			$this->pages = array(
				'movie'      => intval( wpmoly_o( 'movie-archives' ) ),
				'collection' => intval( wpmoly_o( 'collection-archives' ) ),
				'genre'      => intval( wpmoly_o( 'genre-archives' ) ),
				'actor'      => intval( wpmoly_o( 'actor-archives' ) )
			);
		}

		/**
		 * Constructor
		 *
		 * @since    2.1
		 */
		public function __construct() {

			$this->init();
			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    2.1
		 */
		public function register_hook_callbacks() {

			//add_action( 'admin_notices', array( $this, 'custom_pages_notice' ) );
			add_action( 'template_redirect', array( $this, 'template_redirect' ) );

			add_filter( 'the_content', array( $this, 'get_pages' ), 10, 1 );
			add_filter( 'the_title', array( $this, 'movie_archives_title' ), 10, 2 );
			add_filter( 'wp_title', array( $this, 'movie_archives_title' ), 10, 2 );

			add_action( 'pre_get_posts', __CLASS__ . '::subpages', 10, 1 );

			if ( '' == wpmoly_o( 'movie-archives' ) )
				add_action( 'pre_get_posts', __CLASS__ . '::meta_archives', 10, 1 );
		}

		/**
		 * Display an admin notice
		 *
		 * @since    2.1
		 */
		public function custom_pages_notice() {

			if ( 'yes' == get_option( 'wpmoly_has_custom_pages', 'no' ) )
				return false;

			$notice = 'custom-pages';
			if ( isset( $_GET['dismiss-custom-pages-notice'] ) )
				$notice = 'dismiss-custom-pages';

			echo self::render_admin_template( 'admin-notice.php', compact( 'notice' ) );
		}

		/**
		 * Hide Custom Pages notice
		 *
		 * @since    2.1.1
		 */
		public static function hide_custom_pages_notice() {

			update_option( 'wpmoly_has_custom_pages', 'yes' );
		}

		/**
		 * Replace WordPress' default movie archives page with custom
		 * archives page, if any, using redirection.
		 * 
		 * @since    2.1
		 */
		public function template_redirect() {

			if ( ! is_post_type_archive( 'movie' ) )
				return false;

			$page = intval( wpmoly_o( 'movie-archives' ) );
			if ( $page && get_post( $page ) )
				wp_redirect( get_permalink( $page ) );
		}

		/**
		 * 'Add Custom Pages' page callback.
		 * 
		 * @since    2.1
		 */
		public static function create_pages() {

			if ( isset( $_GET['_wpmolynonce_dismiss_custom_pages_notice'] ) )
				self::hide_custom_pages_notice();

			if ( isset( $_GET['create_pages'] ) )
				self::add_custom_pages();

			$existing = array();
			$missing  = array();
			$pages    = array(
				'movie'      => intval( wpmoly_o( 'movie-archives' ) ),
				'collection' => intval( wpmoly_o( 'collection-archives' ) ),
				'genre'      => intval( wpmoly_o( 'genre-archives' ) ),
				'actor'      => intval( wpmoly_o( 'actor-archives' ) )
			);

			foreach ( $pages as $slug => $page ) {

				if ( ! $page ) {
					$title = ucwords( $slug ) . 's';
					$missing[ $slug ] = __( $title, 'wpmovielibrary' );
				}
				else {
					$existing[ $slug ] = get_post( $page );
				}
			}

			$attributes = compact( 'existing', 'missing' );
			echo self::render_admin_template( 'add-custom-pages.php', $attributes );
		}

		/**
		 * Create Custom Archives pages when needed.
		 * 
		 * @since    2.1
		 * 
		 * @return   array    IDs a newly created pages.
		 */
		public static function add_custom_pages() {

			global $wpmoly_redux_config;

			$nonce = '_wpmolynonce_create_custom_pages';
			if ( ! isset( $_GET[ $nonce ] ) || ! wpmoly_verify_nonce( $_GET[ $nonce ], 'create-custom-pages' ) ) {
				wp_nonce_ays( 'create-custom-pages' );
				return false;
			}

			$allowed = array( 'all', 'movie', 'collection', 'genre', 'actor' );
			$create  = sanitize_text_field( $_GET['create_pages'] );

			if ( ! in_array( $create, $allowed ) )
				return false;

			switch ( $create ) {
				case 'all':
					$pages = array(
						'movie'      => __( 'Movies', 'wpmovielibrary' ),
						'collection' => __( 'Collections', 'wpmovielibrary' ),
						'genre'      => __( 'Genres', 'wpmovielibrary' ),
						'actor'      => __( 'Actors', 'wpmovielibrary' ),
					);
					break;
				case 'movie':
					$pages = array( 'movie' => __( 'Movies', 'wpmovielibrary' ) );
					break;
				case 'collection':
					$pages = array( 'collection' => __( 'Collections', 'wpmovielibrary' ) );
					break;
				case 'genre':
					$pages = array( 'genre' => __( 'Genres', 'wpmovielibrary' ) );
					break;
				case 'actor':
					$pages = array( 'actor' => __( 'Actors', 'wpmovielibrary' ) );
					break;
				default:
					$pages = array();
					break;
			}

			if ( empty( $pages ) )
				return false;

			$post = array(
				'ID'             => null,
				'post_content'   => '',
				'post_name'      => '',
				'post_title'     => '',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'ping_status'    => '',
				'post_excerpt'   => '',
				'post_date'      => '',
				'post_date_gmt'  => '',
				'comment_status' => ''
			);
			$_pages = array();

			foreach ( $pages as $slug => $page ) {

				$exists = intval( wpmoly_o( "$slug-archives" ) );
				if ( ! $exists ) {
					$post['post_title'] = $page;
					$page = wp_insert_post( $post );
					$_pages[ $slug ] = $page;

					if ( $page )
						$wpmoly_redux_config->ReduxFramework->set( "wpmoly-$slug-archives", $page );
				}
			}

			$self = new WPMOLY_Archives();
			if ( $self->has_custom_page() )
				update_option( 'wpmoly_has_custom_pages', 'yes' );

			return $_pages;
		}

		/**
		 * Filter post content to render Movies and Taxonomies Custom
		 * Archives pages.
		 * 
		 * @since    2.1
		 * 
		 * @param    string    $content Current page content
		 * 
		 * @return   string    HTML markup
		 */
		public function get_pages( $content ) {

			global $wp_query;

			if ( ! isset( $wp_query->queried_object_id ) )
				return $content;

			$id = $wp_query->queried_object_id;

			$archives = array(
				'movie'      => intval( wpmoly_o( 'movie-archives' ) ),
				'collection' => intval( wpmoly_o( 'collection-archives' ) ),
				'genre'      => intval( wpmoly_o( 'genre-archives' ) ),
				'actor'      => intval( wpmoly_o( 'actor-archives' ) )
			);

			if ( ! in_array( $id, $this->pages ) )
				return $content;

			// Fetch archives
			extract( $this->pages );
			$archive = '';
			if ( $movie && $movie == $id ) {
				$archive = self::movie_archives();
			} else if ( $collection && $collection == $id ) {
				$archive = self::taxonomy_archives( 'collection' );
			} else if ( $genre && $genre == $id ) {
				$archive = self::taxonomy_archives( 'genre' );
			} else if ( $actor && $actor == $id ) {
				$archive = self::taxonomy_archives( 'actor' );
			}

			// Determine archives position
			if ( $movie && $archive ) {
				$position = wpmoly_o( 'movie-archives-position' );
			} else if ( ( $collection || $genre || $actor ) && $archive ) {
				$position = wpmoly_o( 'tax-archives-position' );
			} else {
				$position = 'top';
			}

			// Positioning
			if ( 'bottom' == $position ) {
				$content = $content . $archive;
			} else {
				$content = $archive . $content;
			}

			return $content;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                           Movie Archives
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Filter page's title to feature letters, meta, value in archives
		 * pages' titles. Hooked to 'the_title' and 'wp_title' filters.
		 * 
		 * @since    2.1.1
		 * 
		 * @param    string        $title Current title
		 * @param    int|string    $id Current page ID if current filter if 'the_title', separator if filter is 'wp_title'
		 * 
		 * @return   string    
		 */
		public function movie_archives_title( $title, $id = null ) {

			// Exclude admin
			if ( is_admin() )
				return $title;

			// 'wp_title' filter second parameter is separator, not id
			$filter = current_filter();
			$sep = '&nbsp;|&nbsp;';
			if ( 'wp_title' == $filter ) {
				$id  = get_the_ID();
			}

			// Exclude not-archive pages 
			if ( ! in_array( $id, $this->pages ) )
				return $title;

			$is_movie = ( $id == $this->pages['movie'] );
			$translate = ( '1' == wpmoly_o( 'rewrite-enable' ) );
			$rewrite_movie = ( '1' == wpmoly_o( 'movie-archives-title-rewrite' ) );
			$rewrite_taxonomy = ( '1' == wpmoly_o( 'tax-archives-title-rewrite' ) );

			if ( ( $is_movie && ! $rewrite_movie ) || ( ! $is_movie && ! $rewrite_taxonomy ) ) {
				return $title;
			}

			$page = array_search( $id, $this->pages );
			$titles = array(
				'movie'      => __( 'Movies', 'wpmovielibrary' ),
				'collection' => __( 'Collections', 'wpmovielibrary' ),
				'genre'      => __( 'Genres', 'wpmovielibrary' ),
				'actor'      => __( 'Actors', 'wpmovielibrary' )
			);

			/**
			 * Filter Archive Pages Titles
			 * 
			 * @since    2.1.1
			 * 
			 * @param    array    $titles Default Archive Pages titles
			 * 
			 * @return   array    Filtered titles
			 */
			$titles = apply_filters( 'wpmoly_filter_archive_pages_titles', $titles );

			/**
			 * Filter Archive Page's default title
			 * 
			 * @since    2.1.1
			 * 
			 * @param    string    $title Current Archive Page title
			 * @param    int       $id Current Archive Page Post ID
			 * 
			 * @return   string    Filtered title
			 */
			$title = apply_filters( "wpmoly_filter_{$page}_archive_page_default_title", $titles[ $page ], $id );

			if ( ( ! is_single( $id ) && ! is_page( $id ) ) || ( 'the_title' == $filter && ! in_the_loop() ) )
				return $title;

			$meta   = get_query_var( 'meta' );
			$detail = get_query_var( 'detail' );
			$value  = get_query_var( 'value' );
			$sorting = get_query_var( 'sorting' );

			$_detail = '';
			$_meta   = '';

			if ( $is_movie && $rewrite_movie ) {

				if ( '' != $meta ) {

					if ( $translate )
						$meta = WPMOLY_L10n::untranslate_rewrite( $meta );

					$supported = WPMOLY_Settings::get_supported_movie_meta();
					if ( isset( $supported[ $meta ] ) )
						$_meta = $supported[ $meta ]['title'];

					if ( '' != $value )
						$value = WPMOLY_L10n::untranslate_rewrite( $value );
				}
				elseif ( '' != $detail ) {

					if ( $translate )
						$detail = WPMOLY_L10n::untranslate_rewrite( $detail );

					$supported = WPMOLY_Settings::get_supported_movie_detail();
					if ( isset( $supported[ $detail ] ) )
						$_detail = $supported[ $detail ]['title'];

					if ( '' != $value )
						$value = WPMOLY_L10n::untranslate_rewrite( $value );
				}

				if ( 'production_countries' == $meta ) {
					$value = WPMOLY_L10n::get_country_standard_name( $value );
					$value = __( $value, 'wpmovielibrary-iso' );
				} else if ( 'spoken_languages' == $meta ) {
					$value = WPMOLY_L10n::get_language_standard_name( $value );
					$value = __( $value, 'wpmovielibrary-iso' );
				} else {
					$value = __( $value, 'wpmovielibrary' );
				}

				if ( '' == $_meta && '' != $_detail )
					$_meta = $_detail;

				if ( '' != $_meta && '' == $value )
					$title = sprintf( __( 'Movies by %s', 'wpmovielibrary' ), $_meta );
				elseif ( '' != $_meta && '' != $value )
					$title = sprintf( __( 'Movies by %s: %s', 'wpmovielibrary' ), ucwords( $_meta ), $value );
				else
					$title = __( 'Movies', 'wpmovielibrary' );
			}

			if ( '' != $sorting ) {
				$sorting = self::parse_query_vars( compact( 'sorting' ) );

				if ( isset( $sorting['letter'] ) && '' != $sorting['letter'] )
					$title  .= sprintf( ' − %s ', sprintf( __( 'Letter %s', 'wpmovielibrary' ), $sorting['letter'] ) );

				if ( isset( $sorting['paged'] ) && '' != $sorting['paged'] && 'wp_title' == $filter )
					$title .= sprintf( __( ' %s Page %d ', 'wpmovielibrary' ), $sep, $sorting['paged'] );
			}

			/**
			 * Filter Page's Post title as used in (get_)the_title()
			 * 
			 * @since    2.1.1
			 * 
			 * @param    string    $title Current Archive Page title
			 * @param    int       $id Current Archive Page Post ID
			 * 
			 * @return   string    Filtered title
			 */
			$title = apply_filters( "wpmoly_filter_{$page}_archive_page_title", $title, $id );

			if ( 'wp_title' == $filter ) {
				$title = str_replace( array( ' : ', ' − ' ), $sep, $title ) . $sep;
				/**
				 * Filter Page's main title as used in wp_title()
				 * 
				 * @since    2.1.1
				 * 
				 * @param    string    $title Current Archive Page title
				 * @param    int       $id Current Archive Page Post ID
				 * 
				 * @return   string    Filtered title
				 */
				$title = apply_filters( "wpmoly_filter_{$page}_archive_page_wp_title", $title, $id );
			}

			return $title;
		}

		/**
		 * Render Custom Movie Archives pages.
		 * 
		 * This is basically a call the to movie grid method with a few
		 * preset options.
		 * 
		 * @since    2.1
		 * 
		 * @return   string    HTML markup
		 */
		public static function movie_archives() {

			$has_menu = wpmoly_o( 'movie-archives-menu', $default = true );
			$editable = wpmoly_o( 'movie-archives-frontend-edit', $default = true );

			global $wp_query;
			$params = self::parse_query_vars( $wp_query->query );
			extract( $params );

			$grid_menu = '';
			if ( $has_menu ) {
				$args = compact( 'columns', 'rows', 'number', 'order', 'orderby', 'editable', 'letter', 'view' );
				$grid_menu = WPMOLY_Grid::get_menu( $args );
			}

			$args    = compact( 'number', 'paged', 'order', 'orderby', 'columns', 'rows', 'letter', 'meta', 'detail', 'value', 'view' );
			$grid    = WPMOLY_Grid::get_content( $args );
			$content = $grid_menu . $grid;

			return $content;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                         Taxonomy Archives
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Render Custom Taxonomies Archives pages.
		 * 
		 * This method is a bit complex because it can handle a couple of
		 * things. If a letter param is set, will get the list of terms
		 * starting with that letter, plus sorting/pagination options.
		 * 
		 * If no letter is set, simply render a paginated list of all
		 * taxonomy' terms.
		 * 
		 * @since    2.1
		 * 
		 * @param    string    $taxonomy Taxonomy slug
		 * 
		 * @return   string    HTML markup
		 */
		public static function taxonomy_archives( $taxonomy ) {

			global $wpdb;

			$term_title = '';
			if ( 'collection' == $taxonomy )
				$term_title = __( 'View all movies from collection &laquo; %s &raquo;', 'wpmovielibrary' );
			else if ( 'genre' == $taxonomy )
				$term_title = __( 'View all &laquo; %s &raquo; movies', 'wpmovielibrary' );
			else if ( 'actor' == $taxonomy )
				$term_title = __( 'View all movies staring &laquo; %s &raquo;', 'wpmovielibrary' );

			global $wp_query;
			$params = self::parse_terms_query_vars( $wp_query->query );

			// Allow URL params to override settings
			$vars = array( 'number', 'orderby', 'letter' );
			foreach ( $vars as $var )
				$params[ $var ] = get_query_var( $var, $params[ $var ] );

			$name = WPMOLY_Cache::wpmoly_cache_name( "{$taxonomy}_archive" );
			$content = WPMOLY_Cache::output( $name, function() use ( $wpdb, $taxonomy, $term_title, $params ) {

				$has_menu = wpmoly_o( 'tax-archives-menu', $default = true );
				$hide_empty = wpmoly_o( 'tax-archives-hide-empty', $default = true );

				extract( $params );

				$_orderby = 't.name';
				if ( 'count' == $orderby )
					$_orderby = 'tt.count';

				// Limit the maximum number of terms to get
				$number = min( $number, wpmoly_o( 'tax-archives-terms-limit', $default = true ) );
				if ( ! $number )
					$number = wpmoly_o( 'tax-archives-terms-per-page', $default = true );

				// Calculate offset
				$offset = 0;
				if ( $paged )
					$offset = ( $number * ( $paged - 1 ) );

				$limit = sprintf( 'LIMIT %d,%d', $offset, $number );

				$where = '';
				if ( '0' != $hide_empty )
					$where = 'tt.count > 0 AND';

				// This is actually a hard rewriting of get_terms()
				// to get exactly what we want without getting into
				// trouble with multiple filters and stuff.
				if ( '' != $letter ) {
					$like  = wpmoly_esc_like( $letter ) . '%';
					$query = "SELECT SQL_CALC_FOUND_ROWS t.*, tt.*
						    FROM {$wpdb->terms} AS t
						   INNER JOIN {$wpdb->term_taxonomy} AS tt
						      ON t.term_id = tt.term_id
						   WHERE {$where} tt.taxonomy = %s
						     AND t.name LIKE %s
						   ORDER BY {$_orderby} {$order}
						   {$limit}";
					$query = $wpdb->prepare( $query, $taxonomy, $like );
					$terms = $wpdb->get_results( $query );
				}
				else {
					$query = "SELECT SQL_CALC_FOUND_ROWS t.*, tt.*
						    FROM {$wpdb->terms} AS t
						   INNER JOIN {$wpdb->term_taxonomy} AS tt
						      ON t.term_id = tt.term_id
						   WHERE {$where} tt.taxonomy = %s
						   ORDER BY {$_orderby} {$order}
						   {$limit}";
					$query = $wpdb->prepare( $query, $taxonomy );
					$terms = $wpdb->get_results( $query );
				}

				$total = $wpdb->get_var( 'SELECT FOUND_ROWS() AS total' );
				$terms = apply_filters( 'get_terms', $terms, (array) $taxonomy, array() );
				$links = array();

				// Setting up the terms list...
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

				// ... the main menu...
				$menu = '';
				if ( $has_menu ) {
					$args = compact( 'order', 'orderby', 'number', 'letter' );
					// PHP 5.3
					$menu = WPMOLY_Archives::taxonomy_archive_menu( $taxonomy, $args );
				}

				$args['letter'] = $letter;
				$args['baseurl'] = get_permalink();
				$url = WPMOLY_Utils::build_meta_permalink( $args );

				global $wp_rewrite;
				$format = '/page/%#%';
				if ( '' == $wp_rewrite->permalink_structure )
					$format = '&paged=%#%';

				// ... and the pagination menu.
				$args = array(
					'type'    => 'list',
					'total'   => ceil( ( $total - 1 ) / $number ),
					'current' => max( 1, $paged ),
					'format'  => $url . $format,
				);
				$pagination = WPMOLY_Utils::paginate_links( $args );
				$pagination = '<div id="wpmoly-movies-pagination">' . $pagination . '</div>';

				$attributes = array( 'taxonomy' => $taxonomy, 'links' => $links );
				$content = WPMovieLibrary::render_template( 'archives/archives.php', $attributes, $require = 'always' );

				$content = $menu . $content . $pagination;

				return $content;
			});

			return $content;
		}

		/**
		 * Generate Custom Taxonome Archives pages menu.
		 * 
		 * Similar to the version 2.0 grid shortcode, this generated a
		 * double menu: alphabetical selection of taxonomies, and basic
		 * sorting menu including asc/descending alphabetical/numeric
		 * sorting, number limitation and pagination.
		 * 
		 * @since    2.1
		 * 
		 * @param    string    $taxonomy Taxonomy type: collection, genre or actor
		 * @param    array     $args Taxonomy Menu arguments
		 * 
		 * @return   string    HTML content
		 */
		public static function taxonomy_archive_menu( $taxonomy, $args ) {

			global $wpdb;

			$defaults = array(
				'letter'   => '',
				'order'    => wpmoly_o( 'tax-archives-terms-order', $default = true ),
				'orderby'  => wpmoly_o( 'tax-archives-terms-orderby', $default = true ),
				'number'   => wpmoly_o( 'tax-archives-terms-per-page', $default = true ),
				'editable' => wpmoly_o( 'tax-archives-frontend-edit', $default = true )
			);
			$args = wp_parse_args( $args, $defaults );
			extract( $args );

			$default = str_split( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
			$letters = array();
			
			$result = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT DISTINCT LEFT(t.name, 1) as letter
					   FROM {$wpdb->terms} AS t
					  INNER JOIN {$wpdb->term_taxonomy} AS tt
					     ON t.term_id = tt.term_id
					  WHERE tt.taxonomy = %s
					  ORDER BY t.name ASC",
					$taxonomy
				)
			);
			foreach ( $result as $r )
				$letters[] = $r->letter;

			$baseurl = get_permalink();
			$is_tax = $taxonomy;

			$args = compact( 'order', 'orderby', 'number', 'baseurl', 'is_tax' );
			$attributes = compact( 'letters', 'default', 'letter', 'order', 'orderby', 'number', 'letter_url', 'default_url', 'editable' );

			$urls = array();
			$urls['all'] = WPMOLY_Utils::build_meta_permalink( $args );
			$args['order'] = $order;

			$args['letter'] = ':letter:';
			$urls['letter'] = WPMOLY_Utils::build_meta_permalink( $args );
			$args['letter'] = $letter;

			$args['order'] = 'ASC';
			$args['orderby'] = 'count';
			$urls['count_asc'] = WPMOLY_Utils::build_meta_permalink( $args );
			$args['orderby'] = 'title';
			$urls['title_asc'] = WPMOLY_Utils::build_meta_permalink( $args );

			$args['order'] = 'DESC';
			$args['orderby'] = 'count';
			$urls['count_desc'] = WPMOLY_Utils::build_meta_permalink( $args );
			$args['orderby'] = 'title';
			$urls['title_desc'] = WPMOLY_Utils::build_meta_permalink( $args );

			$attributes['urls'] = $urls;

			$content = self::render_template( 'archives/menu.php', $attributes, $require = 'always' );

			return $content;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                            Meta Archives
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Add support for Movie Details to the current WP_Query.
		 * 
		 * If current WP_Query has a WPMOLY meta var set, edit the query to
		 * return the movies matching the wanted detail.
		 *
		 * @since    1.0
		 * 
		 * @param    object      $wp_query Current WP_Query instance
		 */
		public static function meta_archives( $wp_query ) {

			if ( is_admin() || ! is_post_type_archive( 'movie' ) )
				return false;

			$vars = self::parse_query_vars( $wp_query->query_vars );
			extract( $vars );

			if ( ! isset( $meta ) || ! isset( $value ) )
				return false;

			$meta_query = call_user_func( "WPMOLY_Search::by_{$meta}", $value );

			$wp_query->set( 'meta_query', $meta_query );
		}

		/**
		 * Prevent custom permalinks from breaking pages hierarchy.
		 * 
		 * The permalink structure for meta query could interfere with
		 * pages with three levels or hierarchy, ie. page with URLs like
		 * http://domain/page_a/{matching_meta_or_detail}/page_c/,
		 * resulting in page A being displayed instead of page C. Very
		 * unlike, but still possible.
		 *
		 * @since    2.1.3
		 * 
		 * @param    object      $wp_query Current WP_Query instance
		 */
		public static function subpages( $wp_query ) {

			global $wp;

			// Don't run this in dashboard or on posts
			$request = $wp->request;
			if ( is_admin() || is_null( $request ) || ! isset( $wp_query->queried_object_id ) || is_null( $wp_query->queried_object_id ) )
				return false;

			// Page is correct, dismiss
			$post_id = $wp_query->queried_object_id;
			$pageuri = get_page_uri( $post_id );
			if ( $request == $pageuri )
				return false;

			$page = get_page_by_path( $request );
			if ( is_null( $page ) )
				return false;

			$wp_query->queried_object = $page;
			$wp_query->queried_object_id = $page->ID;
			$wp_query->set( 'pagename', $page->post_name );
			unset( $wp_query->query['meta'], $wp_query->query['value'], $wp_query->query_vars['meta'], $wp_query->query_vars['value'] );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                                Utils
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Check if Archive Pages have been set
		 * 
		 * @since    2.1
		 * 
		 * @return   boolean
		 */
		public function has_custom_page() {

			$has_pages = ! in_array( 0, $this->pages );

			return $has_pages;
		}

		/**
		 * Parse grid sorting parameters
		 * 
		 * @since    2.1.1
		 * 
		 * @param    array    $args Query parameters
		 * 
		 * @return   array    Parsed parameters
		 */
		public static function parse_query_vars( $vars ) {

			$translate = ( '1' == wpmoly_o( 'rewrite-enable' ) );
			$defaults = array(
				'letter', 'paged', 'columns', 'rows', 'order', 'meta', 'detail', 'value', 'view'
			);
			$params = array();

			foreach ( $defaults as $default ) {
				if ( isset( $vars[ $default ] ) ) {
					$var = $vars[ $default ];
					if ( $translate || in_array( $default, array( 'meta', 'detail' ) ) )
						$var = WPMOLY_L10n::untranslate_rewrite( $var );
					$params[ $default ] = $var;
				}
			}

			// I can haz sortingz!
			if ( isset( $vars['sorting'] ) && '' != $vars['sorting'] ) {

				$sorting = '/' . $vars['sorting'];
				$regex = array(
					'letter'  => '(\/([0-9A-Za-z]{1}))\/',
					'number'  => '(([0-9]{1,})\:([0-9]{1,})|([0-9]{1,}))\/?',
					'order'   => '(asc|desc|ASC|DESC)\/?',
					'orderby' => '(title|year|date|localdate|rating)\/?',
					'paged'   => '(page\/([0-9]{1,}))\/?'
				);

				// Has letter?
				$preg = preg_match( "/{$regex['letter']}/", $sorting, $matches );
				if ( $preg && isset( $matches[2] ) && '' != $matches[2] ) {
					$params['letter'] = $matches[2];
				}

				// Has number/columns?
				$preg = preg_match( "/{$regex['number']}/", $sorting, $matches );
				if ( $preg && ( isset( $matches[2] ) && '' != $matches[2] ) && ( isset( $matches[3] ) && '' != $matches[3] ) ) {
					$params['columns'] = $matches[2];
					$params['rows'] = $matches[3];
				}

				// Has sorting?
				$preg = preg_match( "/{$regex['order']}/", $sorting, $matches );
				if ( $preg && isset( $matches[1] ) && '' != $matches[1] ) {
					$params['order'] = strtoupper( $matches[1] );
				}

				$preg = preg_match( "/{$regex['orderby']}/", $sorting, $matches );
				if ( $preg && isset( $matches[1] ) && '' != $matches[1] ) {
					$params['orderby'] = $matches[1];
				}

				// Has pagination?
				$preg = preg_match( "/{$regex['paged']}/", $sorting, $matches );
				if ( $preg ) {
					$params['paged'] = $matches[2];
				}
			}

			return $params;
		}

		/**
		 * Parse taxonomy archives sorting parameters
		 * 
		 * @since    2.1.1
		 * 
		 * @param    array    $args Query parameters
		 * 
		 * @return   array    Parsed parameters
		 */
		public static function parse_terms_query_vars( $vars ) {

			$defaults = array(
				'letter'  => '',
				'paged'   => '1',
				'number'  => wpmoly_o( "tax-archives-terms-per-page", $default = true ),
				'order'   => wpmoly_o( "tax-archives-terms-order", $default = true ),
				'orderby' => wpmoly_o( "tax-archives-terms-orderby", $default = true ),
			);
			$params = array();

			// I can haz sortingz!
			if ( isset( $vars['sorting'] ) && '' != $vars['sorting'] ) {

				$sorting = '/' . $vars['sorting'];
				$regex = array(
					'letter'  => '(\/([0-9A-Za-z]{1}))\/',
					'number'  => '([0-9]{1,})\/?',
					'order'   => '(asc|desc|ASC|DESC)\/?',
					'orderby' => '(count|title)\/?',
					'paged'   => '(page\/([0-9]{1,}))\/?'
				);

				// Has letter?
				$preg = preg_match( "/{$regex['letter']}/", $sorting, $matches );
				if ( $preg && isset( $matches[2] ) && '' != $matches[2] ) {
					$params['letter'] = $matches[2];
				}

				// Has number/columns?
				$preg = preg_match( "/{$regex['number']}/", $sorting, $matches );
				if ( $preg && isset( $matches[1] ) && '' != $matches[1] ) {
					$params['number'] = $matches[1];
				}

				// Has sorting?
				$preg = preg_match( "/{$regex['order']}/", $sorting, $matches );
				if ( $preg && isset( $matches[1] ) && '' != $matches[1] ) {
					$params['order'] = strtoupper( $matches[1] );
				}

				// Has sorting?
				$preg = preg_match( "/{$regex['orderby']}/", $sorting, $matches );
				if ( $preg && isset( $matches[1] ) && '' != $matches[1] ) {
					$params['orderby'] = strtolower( $matches[1] );
				}

				// Has pagination?
				$preg = preg_match( "/{$regex['paged']}/", $sorting, $matches );
				if ( $preg ) {
					$params['paged'] = $matches[2];
				}

				$params = wp_parse_args( $params, $defaults );

			} else {
				$params = wp_parse_args( $vars, $defaults );
			}

			return $params;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {

			delete_option( 'wpmoly_has_custom_pages' );
			if ( ! $this->has_custom_page() )
				add_option( 'wpmoly_has_custom_pages', 'no' );
			else
				add_option( 'wpmoly_has_custom_pages', 'yes' );
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {

			delete_option( 'wpmoly_has_custom_pages' );
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.0
		 */
		public static function uninstall() {

			delete_option( 'wpmoly_has_custom_pages' );
		}

	}

endif;
