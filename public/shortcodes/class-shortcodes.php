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
						'id'    => get_the_ID(),
						'title' => get_the_title(),
						'url'   => get_permalink()
					);

					if ( ! is_null( $poster ) && has_post_thumbnail( get_the_ID() ) )
						$movies[ $query->current_post ]['poster'] = get_the_post_thumbnail( get_the_ID(), $poster );

					if ( ! is_null( $meta ) ) {

						$_meta = WPML_Utils::get_movie_data( get_the_ID() );
						$_meta = WPML_Utils::filter_undimension_array( $_meta );

						foreach ( $_meta as $slug => $m )
							if ( ! in_array( $slug, $meta ) )
								unset( $_meta[ $slug ] );

						$movies[ $query->current_post ]['meta'] = $_meta;
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

			include( plugin_dir_path( __FILE__ ) . '/views/movies.php' );

			return $content;
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