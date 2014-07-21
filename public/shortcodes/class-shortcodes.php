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

			$default_fields = WPML_Settings::get_supported_movie_meta();

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

			$movies = self::prepare_movies( $query, $atts );

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

			$movies = self::prepare_movies( $query, $atts );

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
			if ( ! is_null( $tag ) && "{$tag}_shortcode" != __FUNCTION__ ) {
				$tag = apply_filters( 'wpml_filter_movie_meta_aliases', $tag );
				$atts['key'] = str_replace( 'movie_', '', $tag );
			}

			$atts = apply_filters( 'wpml_filter_shortcode_atts', 'movie_meta', $atts );
			extract( $atts );

			$movie_id = self::find_movie_id( $id, $title );
			if ( is_null( $movie_id ) )
				return $content;

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
		 * Movie Actors shortcode. This shortcode supports aliases.
		 *
		 * @since    1.1.0
		 * 
		 * @param    array     Shortcode attributes
		 * @param    string    Shortcode content
		 * @param    string    Shortcode tag name
		 * 
		 * @return   string    Shortcode display
		 */
		public function movie_actors_shortcode( $atts = array(), $content = null, $tag = null ) {

			// Is this an alias?
			if ( ! is_null( $tag ) && "{$tag}_shortcode" != __FUNCTION__ )
				$atts['key'] = str_replace( 'movie_', '', $tag );

			$atts = apply_filters( 'wpml_filter_shortcode_atts', 'movie_actors', $atts );
			extract( $atts );

			$movie_id = self::find_movie_id( $id, $title );
			if ( is_null( $movie_id ) )
				return $content;

			$actors = WPML_Utils::get_movie_data( $movie_id );
			$actors = $actors['crew']['cast'];
			$actors = apply_filters( "wpml_format_movie_actors", $actors, $movie_id );

			if ( ! is_null( $count ) ) {
				$actors = explode( ', ', $actors );
				$actors = array_splice( $actors, 0, $count );
				$actors = implode( ', ', $actors );
			}

			if ( $label )
				$actors = '<div class="wpml_shortcode_spans"><span class="wpml_shortcode_span wpml_shortcode_span_title wpml_movie_actor_title">' . __( 'Actors', WPML_SLUG ) . '</span><span class="wpml_shortcode_span wpml_shortcode_span_value wpml_movie_actor_value">' . $actors . '</span></div>';
			else
				$actors = '<span class="wpml_shortcode_span wpml_movie_actor">' . $actors . '</span>';

			return $actors;
		}

		/**
		 * Movie Genres shortcode.
		 *
		 * @since    1.1.0
		 * 
		 * @param    array     Shortcode attributes
		 * @param    string    Shortcode content
		 * 
		 * @return   string    Shortcode display
		 */
		public function movie_genres_shortcode( $atts = array(), $content = null ) {

			$atts = apply_filters( 'wpml_filter_shortcode_atts', 'movie_genres', $atts );
			extract( $atts );

			$movie_id = self::find_movie_id( $id, $title );
			if ( is_null( $movie_id ) )
				return $content;

			$genres = WPML_Utils::get_movie_data( $movie_id );
			$genres = $genres['meta']['genres'];
			$genres = apply_filters( "wpml_format_movie_genres", $genres, $movie_id );

			if ( ! is_null( $count ) ) {
				$genres = explode( ', ', $genres );
				$genres = array_splice( $genres, 0, $count );
				$genres = implode( ', ', $genres );
			}

			if ( $label )
				$genres = '<div class="wpml_shortcode_spans"><span class="wpml_shortcode_span wpml_shortcode_span_title wpml_movie_actor_title">' . __( 'Genres', WPML_SLUG ) . '</span><span class="wpml_shortcode_span wpml_shortcode_span_value wpml_movie_actor_value">' . $genres . '</span></div>';
			else
				$genres = '<span class="wpml_shortcode_span wpml_movie_actor">' . $genres . '</span>';

			return $genres;
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

			$movie_id = self::find_movie_id( $id, $title );
			if ( is_null( $movie_id ) )
				return $content;

			if ( ! has_post_thumbnail( $movie_id ) )
				return $content;

			$thumbnail = get_the_post_thumbnail( $movie_id, $size );
			$thumbnail = '<div class="wpml_shortcode_div wpml_movie_poster wpml_movie_poster_' . $size . '">' . $thumbnail . '</div>';

			return $thumbnail;
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
		public function movie_images_shortcode( $atts = array(), $content = null ) {

			$atts = apply_filters( 'wpml_filter_shortcode_atts', 'movie_images', $atts );
			extract( $atts );

			$movie_id = self::find_movie_id( $id, $title );
			if ( is_null( $movie_id ) )
				return $content;

			$images = '';

			$args = array(
				'post_type'   => 'attachment',
				'orderby'     => 'title',
				'numberposts' => -1,
				'post_status' => null,
				'post_parent' => $movie_id,
				'exclude'     => get_post_thumbnail_id( $movie_id )
			);

			$attachments = get_posts( $args );

			if ( $attachments ) {

				if ( ! is_null( $count ) )
					$attachments = array_splice( $attachments, 0, $count );

				$images .= '<ul class="wpml_shortcode_ul wpml_movie_images ' . $size . '">';

				foreach ( $attachments as $attachment )
					$images .= '<li class="wpml_movie_image wpml_movie_imported_image ' . $size . '">' . wp_get_attachment_image( $attachment->ID, $size ) . '</li>';

				$images .= '</ul>';
			}

			return $images;
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

			$atts = apply_filters( 'wpml_filter_shortcode_atts', 'movie_detail', $atts );
			extract( $atts );

			$movie_id = self::find_movie_id( $id, $title );
			if ( is_null( $movie_id ) )
				return $content;

			if ( ! method_exists( 'WPML_Utils', 'get_movie_' . $key ) )
				return $content;

			$detail = call_user_func( 'WPML_Utils::get_movie_' . $key, $movie_id );

			$format = ( ! $raw ? 'html' : 'raw' );
			$detail = apply_filters( 'wpml_format_movie_' . $key, $detail );

			return $detail;
		}

		/**
		 * Prepare movies for Movies and Movie shortcodes.
		 *
		 * @since    1.1.0
		 * 
		 * @param    object    WP_Query object
		 * @param    acrray    Shortcode attributes
		 * 
		 * @return   array    Usable data for Shortcodes
		 */
		private static function prepare_movies( $query, $atts ) {

			if ( ! $query->have_posts() )
				return array();

			extract( $atts );

			$movies = array();
			$default_fields = WPML_Settings::get_supported_movie_meta();

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

				/* 
				    * Meta are passed to the template as an array of values and titles
				    * This gives more freedom to adapt the template
				    */
				if ( ! is_null( $meta ) ) {

					if ( ! is_array( $meta ) )
						$meta = array( $meta );

					$_meta = WPML_Utils::get_movie_data( get_the_ID() );
					$_meta = WPML_Utils::filter_undimension_array( $_meta );

					$metadata = array();

					foreach ( $_meta as $slug => $m ) {
						if ( in_array( $slug, $meta ) ) {
							$title = __( $default_fields[ $slug ]['title'], WPML_SLUG );
							$value = $_meta[ $slug ];
							if ( has_filter( "wpml_format_movie_{$slug}" ) )
								$value = apply_filters( "wpml_format_movie_{$slug}", $value );
							$value = apply_filters( "wpml_format_movie_field", $value );
							$metadata[ array_search( $slug, $meta ) ] = array( 'title' => $title, 'value' => $value );
						}
					}

					ksort( $metadata );

					$movies[ $query->current_post ]['meta'] = $metadata;
				}

				/* 
				    * Details are passed to the template as a string
				    * This is simpler because formatting is already
				    * done via filters
				    */
				if ( ! is_null( $details ) ) {

					$movies[ $query->current_post ]['details'] = '';

					if ( ! is_array( $details ) )
						$details = array( $details );

					foreach ( $details as $detail ) {
						$value = call_user_func( "WPML_Utils::get_movie_$detail", get_the_ID() );
						$movies[ $query->current_post ]['details'] .= apply_filters( "wpml_format_movie_$detail", $value );
					}
				}
			}
			wp_reset_postdata();

			return $movies;
		}

		/**
		 * Find the Movie ID the Shortcode needs. If an ID is passed and
		 * corresponds to a valid movie, use it; else, check if a movie
		 * exists with the title passed and return its ID. If we still
		 * don't have an ID, return the current movie ID.
		 *
		 * @since    1.1.0
		 * 
		 * @param    object    $id Submitted id
		 * @param    array     $title Submitted title
		 * 
		 * @return   int|null  Movie ID if available, null else
		 */
		private static function find_movie_id( $id = null, $title = null ) {

			$movie_id = null;

			if ( ! is_null( $id ) && 'movie' == get_post_type( $id ) ) {
				$movie_id = $id;
			}
			else if ( ! is_null( $title ) ) {
				$movie_id = get_page_by_title( $title, OBJECT, 'movie' );
				if ( ! is_null( $movie_id ) )
					$movie_id = $movie_id->ID;
			}
			else if ( 'movie' == get_post_type() ){
				$movie_id = get_the_ID();
			}

			return $movie_id;
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