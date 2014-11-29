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
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Archives' ) ) :

	class WPMOLY_Archives extends WPMOLY_Module {

		/**
		 * Constructor
		 *
		 * @since    2.1
		 */
		public function __construct() {

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    2.1
		 */
		public function register_hook_callbacks() {

			add_action( 'admin_notices', array( $this, 'custom_pages_notice' ) );
			add_action( 'template_redirect', array( $this, 'template_redirect' ) );

			add_filter( 'the_content', __CLASS__ . '::get_pages', 10, 1 );
		}

		/**
		 * Display an admin notice
		 *
		 * @since    2.1
		 */
		public function custom_pages_notice() {

			if ( 'yes' != get_option( 'wpmoly_has_custom_pages', 'no' ) )
				echo self::render_admin_template( 'admin-notice.php', array( 'notice' => 'custom-pages' ) );
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

			$page = wpmoly_o( 'movie-archives' );
			if ( get_post( $page ) )
				wp_redirect( get_permalink( $page ) );
		}

		/**
		 * 'Add Custom Pages' page callback.
		 * 
		 * @since    2.1
		 */
		public static function create_pages() {

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

			if ( self::has_custom_page() )
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
		public static function get_pages( $content ) {

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

			if ( ! in_array( $id, $archives ) )
				return $content;

			extract( $archives );
			$archive = '';
			if ( $movie && $movie == $id )
				$archive = self::movie_archives();
			elseif ( $collection && $collection == $id )
				$archive = self::taxonomy_archives( 'collection' );
			elseif ( $genre && $genre == $id )
				$archive = self::taxonomy_archives( 'genre' );
			elseif ( $actor && $actor == $id )
				$archive = self::taxonomy_archives( 'actor' );

			return $archive . $content;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                           Movie Archives
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

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

			$letter  = get_query_var( 'letter' );
			$paged   = (int) get_query_var( '_page' );
			$number  = (int) get_query_var( 'number' );
			$columns = (int) get_query_var( 'columns' );
			$order   = get_query_var( 'order' );

			if ( ! isset( $_GET['order'] ) || '' == $_GET['order'] )
				$order = wpmoly_o( 'movie-archives-movies-order', $default = true );

			if ( 'DESC' != $order )
				$order = 'ASC';

			if ( ! $number )
				$number = wpmoly_o( 'movie-archives-movies-per-page', $default = true );

			if ( ! $columns )
				$columns = wpmoly_o( 'movie-archives-grid-columns', $default = true );

			$grid_menu = '';
			if ( $has_menu ) {
				$args = compact( 'columns', 'number', 'order', 'editable' );
				$grid_menu = WPMOLY_Movies::get_grid_menu( $args );
			}

			$args    = compact( 'number', 'paged', 'order', 'columns', 'letter' );
			$grid    = WPMOLY_Movies::get_the_grid( $args );
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

			$name = WPMOLY_Cache::wpmoly_cache_name( "{$taxonomy}_archive" );
			$content = WPMOLY_Cache::output( $name, function() use ( $wpdb, $taxonomy, $term_title ) {

				$has_menu = wpmoly_o( 'tax-archives-menu', $default = true );
				$hide_empty = wpmoly_o( 'tax-archives-hide-empty', $default = true );

				$letter  = get_query_var( 'letter' );
				$order   = get_query_var( 'order' );
				$orderby = get_query_var( 'orderby' );
				$paged   = (int) get_query_var( '_page' );
				$number  = (int) get_query_var( 'number' );

				if ( ! isset( $_GET['order'] ) )
					$order = wpmoly_o( 'tax-archives-terms-order', $default = true );
				if ( '' == $orderby )
					$orderby = wpmoly_o( 'tax-archives-terms-orderby', $default = true );

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

				// Don't use LIMIT with weird values
				$limit = '';
				if ( $offset < $number )
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
					$args = compact( 'order', 'orderby', 'number' );
					$menu = self::taxonomy_archive_menu( $taxonomy, $args );
				}

				$args['letter'] = $letter;
				$url = add_query_arg( $args, get_permalink() );

				// ... and the pagination menu.
				$args = array(
					'type'    => 'list',
					'total'   => ceil( ( $total - 1 ) / $number ),
					'current' => max( 1, $paged ),
					'format'  => $url . '&_page=%#%',
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
				'order'    => wpmoly_o( 'tax-archives-terms-order', $default = true ),
				'orderby'  => wpmoly_o( 'tax-archives-terms-sort', $default = true ),
				'number'   => wpmoly_o( 'tax-archives-terms-per-page', $default = true ),
				'editable' => wpmoly_o( 'tax-archives-frontend-edit', $default = true )
			);
			$args = wp_parse_args( $args, $defaults );
			extract( $args );

			$default = str_split( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
			$letters = array();
			$letter  = get_query_var( 'letter' );
			
			$result = $wpdb->get_results( "SELECT DISTINCT LEFT(t.name, 1) as letter FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('collection') ORDER BY t.name ASC" );
			foreach ( $result as $r )
				$letters[] = $r->letter;

			$letter_url  = add_query_arg( compact( 'order', 'orderby', 'number' ), get_permalink() );
			$default_url = add_query_arg( compact( 'order', 'orderby', 'number', 'letter' ), get_permalink() );

			$attributes = compact( 'letters', 'default', 'letter', 'order', 'orderby', 'number', 'letter_url', 'default_url', 'editable' );

			$content = self::render_template( 'archives/menu.php', $attributes );

			return $content;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                                Utils
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		public static function has_custom_page() {

			$archives = array(
				'movie'      => intval( wpmoly_o( 'movie-archives' ) ),
				'collection' => intval( wpmoly_o( 'collection-archives' ) ),
				'genre'      => intval( wpmoly_o( 'genre-archives' ) ),
				'actor'      => intval( wpmoly_o( 'actor-archives' ) )
			);

			$has_pages = ! in_array( 0, $archives );

			return $has_pages;
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
			if ( ! self::has_custom_page() )
				add_option( 'wpmoly_has_custom_pages', 'no', null, 'no' );
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

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}

endif;
