<?php
/**
 * WPMovieLibrary Movie Grid Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Grid' ) ) :

	class WPMOLY_Grid extends WPMOLY_Movies {

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                              Movie Grid
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Generate alphanumerical breadcrumb menu for Grid view
		 * 
		 * @since    2.0
		 * 
		 * @param    array       Shortcode arguments to use as parameters
		 * @param    boolean     Are we actually doing a Shortcode?
		 * 
		 * @return   string    HTML content
		 */
		public static function get_menu( $args, $shortcode = false ) {

			global $wpdb, $wp_query;

			$defaults = array(
				'order'    => wpmoly_o( 'movie-archives-movies-order', $default = true ),
				'orderby'  => wpmoly_o( 'movie-archives-movies-orderby', $default = true ),
				'columns'  => wpmoly_o( 'movie-archives-grid-columns', $default = true ),
				'rows'     => wpmoly_o( 'movie-archives-grid-rows', $default = true ),
				'editable' => wpmoly_o( 'movie-archives-frontend-edit', $default = true ),
				'advanced' => wpmoly_o( 'movie-archives-frontend-advanced-edit', $default = false ),
				'meta'     => '',
				'detail'   => '',
				'value'    => '',
				'letter'   => '',
				'view'     => 'grid'
			);
			$args = wp_parse_args( $args, $defaults );

			// Allow URL params to override settings
			$_args = WPMOLY_Archives::parse_query_vars( $wp_query->query );
			$args = wp_parse_args( $_args, $args );

			extract( $args );

			$baseurl = get_permalink();
			/*if ( true === $shortcode ) {
				$baseurl = get_permalink();
			} else {
				$baseurl = get_post_type_archive_link( 'movie' );
			}*/

			$views = array( 'grid', 'archives', 'list' );
			if ( '1' == wpmoly_o( 'rewrite-enable' ) )
				$views = array( 'grid' => __( 'grid', 'wpmovielibrary' ), 'archives' => __( 'archives', 'wpmovielibrary' ), 'list' => __( 'list', 'wpmovielibrary' ) );

			$_view = array_search( $view, $views );
			if ( false !== $_view )
				$view = $_view;

			$default = str_split( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
			$letters = array();
			
			$result = $wpdb->get_results( "SELECT DISTINCT LEFT(post_title, 1) as letter FROM {$wpdb->posts} WHERE post_type='movie' AND post_status='publish' ORDER BY letter" );
			foreach ( $result as $r )
				$letters[] = $r->letter;

			$limit = wpmoly_o( 'movie-archives-movies-limit' );

			$attributes = compact( 'letters', 'default', 'letter', 'order', 'orderby', 'columns', 'rows', 'meta', 'detail', 'value', 'editable', 'advanced', 'limit', 'view' );

			$urls = array();
			$l10n = false;

			$args = compact( 'order', 'orderby', 'columns', 'rows', 'meta', 'detail', 'value', 'l10n', 'baseurl', 'view' );
			$urls['all'] = WPMOLY_Utils::build_meta_permalink( $args );

			$args['letter'] = $letter;
			$args['view'] = 'list';
			$urls['list'] = WPMOLY_Utils::build_meta_permalink( $args );
			$args['view'] = 'archives';
			$urls['archives'] = WPMOLY_Utils::build_meta_permalink( $args );
			$args['view'] = 'grid';
			$urls['grid'] = WPMOLY_Utils::build_meta_permalink( $args );
			$args['view'] = $view;

			$args['letter'] = ':letter:';
			$urls['letter'] = WPMOLY_Utils::build_meta_permalink( $args );
			$args['letter'] = $letter;

			$args['order'] = 'ASC';
			$urls['asc'] = WPMOLY_Utils::build_meta_permalink( $args );

			$args['order'] = 'DESC';
			$urls['desc'] = WPMOLY_Utils::build_meta_permalink( $args );

			$attributes['urls'] = $urls;

			$theme = wp_get_theme();
			if ( ! is_null( $theme->stylesheet ) )
				$attributes['theme'] = ' theme-' . $theme->stylesheet;
			else
				$attributes['theme'] = '';

			$content = self::render_template( 'movies/grid/menu.php', $attributes, $require = 'always' );

			return $content;
		}

		/**
		 * Generate Movie Grid
		 * 
		 * If a current letter is passed to the query use it to narrow
		 * the list of movies.
		 * 
		 * @since    2.0
		 * 
		 * @param    array       Shortcode arguments to use as parameters
		 * @param    boolean     Are we actually doing a Shortcode?
		 * 
		 * @return   string    HTML content
		 */
		public static function get_content( $args = array(), $shortcode = false ) {

			global $wpdb, $wp_query;

			$defaults = array(
				'columns'    => wpmoly_o( 'movie-archives-grid-columns', $default = true ),
				'rows'       => wpmoly_o( 'movie-archives-grid-rows', $default = true ),
				'paged'      => 1,
				'category'   => null,
				'tag'        => null,
				'collection' => null,
				'actor'      => null,
				'genre'      => null,
				'meta'       => null,
				'detail'     => null,
				'value'      => null,
				'title'      => false,
				'year'       => false,
				'rating'     => false,
				'letter'     => null,
				'order'      => wpmoly_o( 'movie-archives-movies-order', $default = true ),
				'orderby'    => 'post_title',
				'view'       => 'grid'
			);
			$args = wp_parse_args( $args, $defaults );

			// Allow URL params to override Shortcode settings
			$_args = WPMOLY_Archives::parse_query_vars( $wp_query->query );
			$args = wp_parse_args( $_args, $args );

			// debug
			$main_args = $args;

			extract( $args, EXTR_SKIP );
			$total  = 0;

			$grid_meta = (array) wpmoly_o( 'movie-archives-movies-meta', $default = true );
			$grid_meta = array_keys( $grid_meta['used'] );
			$title  = ( $title || in_array( 'title', $grid_meta ) );
			$rating = ( $rating || in_array( 'rating', $grid_meta ) );
			$year   = ( $year || in_array( 'year', $grid_meta ) );

			$views = array( 'grid', 'archives', 'list' );
			if ( '1' == wpmoly_o( 'rewrite-enable' ) )
				$views = array( 'grid' => __( 'grid', 'wpmovielibrary' ), 'archives' => __( 'archives', 'wpmovielibrary' ), 'list' => __( 'list', 'wpmovielibrary' ) );

			if ( ! isset( $views[ $view ] ) ) {
				$_view = array_search( $view, $views );
				if ( false != $_view )
					$view = $_view;
				else
					$view = 'grid';
			}

			$movies = array();
			$total  = wp_count_posts( 'movie' );
			$total  = $total->publish;

			$select = array( 'SQL_CALC_FOUND_ROWS DISTINCT ID' );

			// Limit the maximum number of terms to get
			$number = $columns * $rows;
			$limit = wpmoly_o( 'movie-archives-movies-limit', $default = true );
			if ( -1 == $number )
				$number = $limit;

			$columns = min( $columns, 8 );
			if ( 0 > $columns )
				$columns = wpmoly_o( 'movie-archives-grid-columns', $default = true );

			$rows = min( $rows, 12 );
			if ( 0 > $rows )
				$rows = wpmoly_o( 'movie-archives-grid-rows', $default = true );

			// Calculate offset
			$offset = 0;
			if ( $paged )
				$offset = max( 0, $number * ( $paged - 1 ) );

			if ( '' == $meta && '' != $detail ) {
				$meta = $detail;
				$type = 'detail';
			}
			else {
				$type = 'meta';
			}

			// Don't use LIMIT with weird values
			$limit = "LIMIT 0,$number";
			if ( $offset >= $number )
				$limit = sprintf( 'LIMIT %d,%d', $offset, $number );

			$where = array( "post_type='movie'", " AND post_status='publish'" );
			if ( '' != $letter )
				$where[] = " AND post_title LIKE '" . wpmoly_esc_like( $letter ) . "%'";

			$join = array();
			$meta_query = array( 'join' => array(), 'where' => array() );
			if ( '' != $value && '' != $meta ) {

				$meta_query = call_user_func( "WPMOLY_Search::by_$meta", $value, 'sql' );

				$join[]  = $meta_query['join'];
				$where[] = $meta_query['where'];
			}

			$tax_query = array();
			if ( ! is_null( $collection ) && ! empty( $collection ) ) {
				$tax_query = array(
					'taxonomy' => 'collection',
					'terms'    => $collection,
				);
			} elseif ( ! is_null( $genre ) && ! empty( $genre ) ) {
				$tax_query = array(
					'taxonomy' => 'genre',
					'terms'    => $genre,
				);
			} elseif ( ! is_null( $actor ) && ! empty( $actor ) ) {
				$tax_query = array(
					'taxonomy' => 'actor',
					'terms'    => $actor,
				);
			} elseif ( ! is_null( $category ) && ! empty( $category ) ) {
				$tax_query = array(
					'taxonomy' => 'category',
					'terms'    => $category,
				);
			} elseif ( ! is_null( $tag ) && ! empty( $tag ) ) {
				$tax_query = array(
					'taxonomy' => 'post_tag',
					'terms'    => $tag,
				);
			}

			if ( ! empty( $tax_query ) ) {

				$tax_query = array(
					'relation' => 'OR',
					array(
						'taxonomy' => $tax_query['taxonomy'],
						'field'    => 'slug',
						'terms'    => $tax_query['terms'],
					),
					array(
						'taxonomy' => $tax_query['taxonomy'],
						'field'    => 'name',
						'terms'    => $tax_query['terms'],
					)
				);
				$tax_query = get_tax_sql( $tax_query, $wpdb->posts, 'ID' );

				$join[]  = $tax_query['join'];
				$where[] = $tax_query['where'];
			}

			$_orderby = array(
				'year'       => 'release_date',
				'date'       => 'release_date',
				'localdate'  => 'local_release_date',
				'rating'     => 'rating'
			);

			if ( in_array( $orderby, array_keys( $_orderby ) ) ) {
				$select[] = ' pm.meta_value AS value';
				$join[]   = ' INNER JOIN wp_postmeta AS pm ON ( wp_posts.ID = pm.post_id )';
				$where[]  = ' AND pm.meta_key = "_wpmoly_movie_' . $_orderby[ $orderby ] . '"';
				$orderby = 'value';
			} elseif ( 'post_date' == $orderby ) {
				$orderby = 'post_date';
			} else {
				$orderby = 'post_title';
			}

			$where  = implode( '', $where );
			$join   = implode( '', $join );
			$select = implode( ',', $select );

			$query = "SELECT {$select} FROM {$wpdb->posts} {$join} WHERE {$where} ORDER BY {$orderby} {$order} {$limit}";

			$movies = $wpdb->get_col( $query );
			$total  = $wpdb->get_var( 'SELECT FOUND_ROWS() AS total' );
			$movies = array_map( 'get_post', $movies );

			if ( 'list' == $view )
				$movies = self::prepare_list_view( $movies );

			$baseurl = get_permalink();
			/*if ( true === $shortcode ) {
				$baseurl = get_permalink();
			} else {
				$baseurl = get_post_type_archive_link( 'movie' );
			}*/

			$args = array(
				'order'   => $order,
				'columns' => $columns,
				'rows'    => $rows,
				'letter'  => $letter,
				'value'   => $value,
				$type     => $meta,
				'l10n'    => false,
				'view'    => $view,
				'baseurl' => $baseurl
			);
			$url = WPMOLY_Utils::build_meta_permalink( $args );

			// debug
			$permalinks_args = $args;

			global $wp_rewrite;
			$format = '/page/%#%';
			if ( '' == $wp_rewrite->permalink_structure )
				$format = '&paged=%#%';

			$args = array(
				'type'    => 'list',
				'total'   => ceil( ( $total ) / $number ),
				'current' => max( 1, $paged ),
				'format'  => $url . $format,
			);

			$paginate = WPMOLY_Utils::paginate_links( $args );
			$paginate = '<div id="wpmoly-movies-pagination">' . $paginate . '</div>';

			$theme = wp_get_theme();
			if ( ! is_null( $theme->stylesheet ) )
				$theme = ' theme-' . $theme->stylesheet;
			else
				$theme = '';

			// debug
			$debug = null;
			if ( current_user_can( 'manage_options' ) && '1' == wpmoly_o( 'debug-mode' ) ) {
				$debug = compact( 'main_args', 'permalinks_args' );
			}

			$attributes = compact( 'movies', 'columns', 'title', 'year', 'rating', 'theme', 'debug' );

			$content  = self::render_template( "movies/grid/$view-loop.php", $attributes, $require = 'always' );
			$content  = $content . $paginate;

			return $content;
		}

		/**
		 * Prepare the list view movie list
		 * 
		 * Explode the movie list by letters to show an alphabetical list
		 * 
		 * @since    2.1.1
		 * 
		 * @param    array    $movies Movies to list
		 * 
		 * @return   array    Multidimensionnal array containing prepared movies
		 */
		public static function prepare_list_view( $movies ) {

			global $post;

			$list    = array();
			$default = str_split( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
			$current = '';

			if ( empty( $movies ) )
				return $movies;

			foreach ( $movies as $post ) {

				setup_postdata( $post );

				$_current = substr( remove_accents( get_the_title() ), 0, 1 );
				if ( $_current != $current )
					$current = $_current;

				$list[ $current ][] = array( 'id' => get_the_ID(), 'url' => get_permalink(), 'title' => get_the_title() );
			}
			wp_reset_postdata();

			return $list;
		}

	}

endif;
