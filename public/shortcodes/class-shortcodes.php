<?php
/**
 * WPMovieLibrary Shortcodes Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

if ( ! class_exists( 'WPML_Shortcodes' ) ) :

	class WPML_Shortcodes extends WPML_Module {

		/**
		 * Shortcodes
		 * 
		 * @since    1.1.0
		 * @var      array
		 */
		protected $shortcodes;

		/**
		 * Constructor
		 *
		 * @since    1.1.0
		 */
		public function __construct() {

			$this->init();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.1.0
		 */
		public function init() {

			$this->shortcodes = WPML_Settings::get_available_shortcodes();

			$this->register_shortcodes();
		}

		/**
		 * Register all shortcodes
		 * 
		 * Shortcodes can have their own callback or handle aliases.
		 *
		 * @since    1.1.0
		 */
		private function register_shortcodes() {

			if ( ! is_array( $this->shortcodes ) || empty( $this->shortcodes ) )
				return false;

			foreach ( $this->shortcodes as $slug => $shortcode ) {

				$callback = array( $this, 'default_shortcode' );

				if ( ! is_null( $shortcode['callback'] ) && method_exists( $this, $shortcode['callback'] ) )
					$callback = array( $this, $shortcode['callback'] );
				else if ( method_exists( $this, "{$slug}_shortcode" ) )
					$callback = array( $this, "{$slug}_shortcode" );

				if ( ! is_null( $shortcode['aliases'] ) )
					foreach ( $shortcode['aliases'] as $alias )
						add_shortcode( $alias, $callback );

				add_shortcode( $slug, $callback );
			}
		}

		/**
		 * Default shortcodes' callback method
		 *
		 * @since    1.1.0
		 * 
		 * @param    array     Shortcode attributes
		 * @param    string    Shortcode content
		 * 
		 * @return   string    Shortcode display
		 */
		public function default_shortcode( $atts = array(), $content = null ) {

			return $content;
		}

		/**
		 * Movies shortcode. Display a list of movies with various sorting
		 * and display options.
		 *
		 * @since    1.1.0
		 * 
		 * @param    array     Shortcode attributes
		 * @param    string    Shortcode content
		 * 
		 * @return   string    Shortcode display
		 */
		public function movies_shortcode( $atts = array(), $content = null ) {

			$atts = apply_filters( 'wpml_filter_shortcode_atts', 'movies', $atts );
			extract( $atts );

			$query = array(
				'post_type=movie',
				'post_status=publish',
				'posts_per_page=' . $count
			);

			if ( ! is_null( $order ) )
				$query[] = 'order=' . $order;

			if ( ! is_null( $orderby ) ) {
				if ( 'rating' == $orderby ) {
					$query[] = 'orderby=meta_value_num';
					$query[] = 'meta_key=_wpml_movie_rating';
				}
				else {
					$query[] = 'orderby=' . $orderby;
				}
			}

			if ( ! is_null( $collection ) )
				$query[] = 'collection=' . $collection;
			elseif ( ! is_null( $genre ) )
				$query[] = 'genre=' . $genre;
			elseif ( ! is_null( $actor ) )
				$query[] = 'actor=' . $actor;

			$query = implode( '&', $query );
			$query = new WP_Query( $query );

			$movies = array();

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {

					$query->the_post();

					$movies[ $query->current_post ] = array(
						'id'      => get_the_ID(),
						'title'   => get_the_title(),
						'url'     => get_permalink(),
						'poster'  => null,
						'meta'    => null,
						'details' => null
					);

					if ( ! is_null( $poster ) && has_post_thumbnail( get_the_ID() ) )
						$movies[ $query->current_post ]['poster'] = get_the_post_thumbnail( get_the_ID(), $poster );

					if ( ! is_null( $meta ) ) {

						$_meta = WPML_Utils::get_movie_data( get_the_ID() );
						$_meta = WPML_Utils::filter_undimension_array( $_meta );

						foreach ( $_meta as $slug => $m ) {
							if ( ! in_array( $slug, $meta ) ) {
								unset( $_meta[ $slug ] );
							}
							else {
								if ( has_filter( "wpml_format_movie_{$slug}" ) )
									$_meta[ $slug ] = apply_filters( "wpml_format_movie_{$slug}", $_meta[ $slug ] );
								else
									$_meta[ $slug ] = apply_filters( "wpml_format_movie_field", $_meta[ $slug ] );
							}
						}

						$movies[ $query->current_post ]['meta'] = array_reverse( $_meta );
					}

					if ( ! is_null( $details ) ) {
						if ( in_array( 'media', $details ) )
							$movies[ $query->current_post ]['details']['media'] = WPML_Utils::get_movie_media( get_the_ID() );
						if ( in_array( 'status', $details ) )
							$movies[ $query->current_post ]['details']['status'] = WPML_Utils::get_movie_status( get_the_ID() );
						if ( in_array( 'rating', $details ) )
							$movies[ $query->current_post ]['details']['rating'] = WPML_Utils::get_movie_rating( get_the_ID() );
					}
				}
			}

			ob_start();
			include( plugin_dir_path( __FILE__ ) . '/views/movies.php' );
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		/**
		 * Movie shortcode. Display a single movie with various display
		 * options.
		 *
		 * @since    1.1.0
		 * 
		 * @param    array     Shortcode attributes
		 * @param    string    Shortcode content
		 * 
		 * @return   string    Shortcode display
		 */
		public function movie_shortcode( $atts = array(), $content = null ) {

			$atts = apply_filters( 'wpml_filter_shortcode_atts', 'movie', $atts );
			extract( $atts );

			$query = array(
				'post_type=movie',
				'post_status=publish'
			);

			if ( ! is_null( $id ) )
				$query[] = 'p=' . $id;
			else if ( ! is_null( $title ) )
				$query[] = 'name=' . sanitize_title_with_dashes( remove_accents( $title ) );
			

			$query = implode( '&', $query );
			$query = new WP_Query( $query );

			$default_fields = WPML_Settings::get_supported_movie_meta();
			$movies = array();

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {

					$query->the_post();

					$movies[ $query->current_post ] = array(
						'id'      => get_the_ID(),
						'title'   => get_the_title(),
						'url'     => get_permalink(),
						'poster'  => null,
						'meta'    => null,
						'details' => null
					);

					if ( ! is_null( $poster ) && has_post_thumbnail( get_the_ID() ) )
						$movies[ $query->current_post ]['poster'] = get_the_post_thumbnail( get_the_ID(), $poster );

					if ( ! is_null( $meta ) ) {

						$_meta = WPML_Utils::get_movie_data( get_the_ID() );
						$_meta = WPML_Utils::filter_undimension_array( $_meta );

						$metadata = array();

						foreach ( $_meta as $slug => $m ) {
							if ( in_array( $slug, $meta ) ) {
								if ( has_filter( "wpml_format_movie_{$slug}" ) )
									$metadata[ array_search( $slug, $meta ) ] = array( 'title' => $default_fields[ $slug ]['title'], 'value' => apply_filters( "wpml_format_movie_{$slug}", $_meta[ $slug ] ) );
								else
									$metadata[ array_search( $slug, $meta ) ] = array( 'title' => $default_fields[ $slug ]['title'], 'value' => apply_filters( "wpml_format_movie_field", $_meta[ $slug ] ) );
							}
						}

						ksort( $metadata );

						$movies[ $query->current_post ]['meta'] = $metadata;
					}

					if ( ! is_null( $details ) ) {
						if ( in_array( 'media', $details ) )
							$movies[ $query->current_post ]['details']['media'] = WPML_Utils::get_movie_media( get_the_ID() );
						if ( in_array( 'status', $details ) )
							$movies[ $query->current_post ]['details']['status'] = WPML_Utils::get_movie_status( get_the_ID() );
						if ( in_array( 'rating', $details ) )
							$movies[ $query->current_post ]['details']['rating'] = WPML_Utils::get_movie_rating( get_the_ID() );
					}
				}
			}
			wp_reset_postdata();

			ob_start();
			include( plugin_dir_path( __FILE__ ) . '/views/movies.php' );
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		/**
		 * Movie Meta shortcode. Display various movie metas with or 
		 * without label. This shortcode supports aliases.
		 *
		 * @since    1.1.0
		 * 
		 * @param    array     Shortcode attributes
		 * @param    string    Shortcode content
		 * @param    string    Shortcode tag name
		 * 
		 * @return   string    Shortcode display
		 */
		public function movie_meta_shortcode( $atts = array(), $content = null, $tag = null ) {

			// Is this an alias?
			if ( ! is_null( $tag ) && "{$tag}_shortcode" != __FUNCTION__ )
				$atts['key'] = str_replace( 'movie_', '', $tag );

			$atts = apply_filters( 'wpml_filter_shortcode_atts', 'movie_meta', $atts );
			extract( $atts );

			if ( ! is_null( $id ) )
				$movie_id = $id;
			else if ( ! is_null( $title ) ) {
				$movie_id = get_page_by_title( $title, OBJECT, 'movie' );
				if ( ! is_null( $movie_id ) )
					$movie_id = $movie_id->ID;
			}

			$meta = WPML_Utils::get_movie_data( $movie_id );
			$meta = apply_filters( 'wpml_filter_undimension_array', $meta );

			if ( ! isset( $meta[ $key ] ) )
				return $content;

			$meta = apply_filters( "wpml_format_movie_$key", $meta[ $key ] );
			$meta = '' != $meta ? $meta : ' &mdash; ';

			if ( $label ) {
				$_meta = WPML_Settings::get_supported_movie_meta();
				$meta = '<div class="wpml_shortcode_spans"><span class="wpml_shortcode_span wpml_shortcode_span_title wpml_movie_' . $key . '_title">' . __( $_meta[ $key ]['title'], WPML_SLUG ) . '</span><span class="wpml_shortcode_span wpml_shortcode_span_value wpml_movie_' . $key . '_value">' . $meta . '</span></div>';
			}
			else
				$meta = '<span class="wpml_shortcode_span wpml_movie_' . $key . '">' . $meta . '</span>';

			return $meta;
		}

		/**
		 * Movie Poster shortcode.
		 *
		 * @since    1.1.0
		 * 
		 * @param    array     Shortcode attributes
		 * @param    string    Shortcode content
		 * 
		 * @return   string    Shortcode display
		 */
		public function movie_poster_shortcode( $atts = array(), $content = null ) {

			$atts = apply_filters( 'wpml_filter_shortcode_atts', 'movie_poster', $atts );
			extract( $atts );

			if ( ! is_null( $id ) )
				$movie_id = $id;
			else if ( ! is_null( $title ) ) {
				$movie_id = get_page_by_title( $title, OBJECT, 'movie' );
				if ( ! is_null( $movie_id ) )
					$movie_id = $movie_id->ID;
			}

			if ( ! has_post_thumbnail( $movie_id ) )
				return $content;

			$thumbnail = get_the_post_thumbnail( $movie_id, $size );
			$thumbnail = '<div class="wpml_shortcode_div wpml_movie_poster wpml_movie_poster_' . $size . '">' . $thumbnail . '</div>';

			return $thumbnail;
		}

		/**
		 * Movie Detail shortcode. This shortcode supports aliases.
		 *
		 * @since    1.1.0
		 * 
		 * @param    array     Shortcode attributes
		 * @param    string    Shortcode content
		 * @param    string    Shortcode tag name
		 * 
		 * @return   string    Shortcode display
		 */
		public function movie_detail_shortcode( $atts = array(), $content = null, $tag = null ) {

			// Is this an alias?
			if ( ! is_null( $tag ) && "{$tag}_shortcode" != __FUNCTION__ )
				$atts['key'] = str_replace( 'movie_', '', $tag );

			$atts = apply_filters( 'wpml_filter_shortcode_atts', 'movie_meta', $atts );
			extract( $atts );

			if ( ! is_null( $id ) )
				$movie_id = $id;
			else if ( ! is_null( $title ) ) {
				$movie_id = get_page_by_title( $title, OBJECT, 'movie' );
				if ( ! is_null( $movie_id ) )
					$movie_id = $movie_id->ID;
			}

			if ( ! method_exists( 'WPML_Utils', 'get_movie_' . $key ) )
				return $content;

			$detail = call_user_func( 'WPML_Utils::get_movie_' . $key, $movie_id );

			if ( ! $raw )
				$detail = apply_filters( 'wpml_format_movie_' . $key, $detail );

			return $detail;
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.1.0
		 */
		public function register_hook_callbacks() {}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.1.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.1.0
		 */
		public function deactivate() {}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.1.0
		 */
		public static function uninstall() {}

	}

endif;