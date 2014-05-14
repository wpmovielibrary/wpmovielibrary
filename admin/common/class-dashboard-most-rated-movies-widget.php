<?php
/**
 * WPMovieLibrary Dashboard Class extension.
 * 
 * Create a Movies preview Widget.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Dashboard_Most_Rated_Movies_Widget' ) ) :

	class WPML_Dashboard_Most_Rated_Movies_Widget extends WPML_Dashboard {

		/**
		 * Widget ID
		 * 
		 * @since    1.0.0
		 * 
		 * @var      string
		 */
		protected $widget_id = '';

		/**
		 * Widget Name.
		 * 
		 * @since    1.0.0
		 * 
		 * @var      string
		 */
		protected $widget_name = '';

		/**
		 * Widget callback method.
		 * 
		 * @since    1.0.0
		 * 
		 * @var      array
		 */
		protected $callback = null;

		/**
		 * Widget Controls callback method.
		 * 
		 * @since    1.0.0
		 * 
		 * @var      array
		 */
		protected $control_callback = null;

		/**
		 * Widget callback method arguments.
		 * 
		 * @since    1.0.0
		 * 
		 * @var      array
		 */
		protected $callback_args = null;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 */
		public function __construct() {

			$this->init();
			$this->register_hook_callbacks();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {

			$this->widget_id = 'wpml_dashboard_most_rated_movies_widget';
			$this->widget_name = __( 'Your most rated movies', WPML_SLUG );
			$this->callback = array( $this, 'dashboard_widget' );
			$this->control_callback = array( $this, 'dashboard_widget_handle' );
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'wpml_dashboard_setup', array( $this, '_add_dashboard_widget' ), 10 );
		}

		/**
		 * Register the Widget
		 * 
		 * @since    1.0.0
		 */
		public function _add_dashboard_widget() {

			$this->add_dashboard_widget( $this->widget_id, $this->widget_name, $this->callback, $this->control_callback );
		}

		public function dashboard_widget() {

			global $wpdb;

			$movies = $wpdb->get_results(
				'SELECT p.*, m.meta_value AS meta, mm.meta_value AS rating
				 FROM ' . $wpdb->posts . ' AS p
				 LEFT JOIN ' . $wpdb->postmeta . ' AS m ON m.post_id=p.ID AND m.meta_key="_wpml_movie_data"
				 LEFT JOIN ' . $wpdb->postmeta . ' AS mm ON mm.post_id=p.ID AND mm.meta_key="_wpml_movie_rating"
				 WHERE post_type="movie"
				   AND post_status="publish"
				 GROUP BY p.ID
				 ORDER BY rating DESC
				 LIMIT 0,4'
			);

			if ( ! empty( $movies ) ) {
				foreach ( $movies as $movie ) {

					$movie->meta = unserialize( $movie->meta );
					$movie->meta = array(
						'title' => apply_filters( 'the_title', $movie->meta['meta']['title'] ),
						'runtime' => apply_filters( 'wpml_filter_filter_runtime', $movie->meta['meta']['runtime'] ),
						'release_date' => apply_filters( 'wpml_filter_filter_release_date', $movie->meta['meta']['release_date'] ),
						'overview' => apply_filters( 'the_content', $movie->meta['meta']['overview'] )
					);
					$movie->meta = json_encode( $movie->meta );

					if ( has_post_thumbnail( $movie->ID ) ) {
						$movie->poster = wp_get_attachment_image_src( get_post_thumbnail_id( $movie->ID ), 'large' );
						$movie->poster = $movie->poster[0];
					}
					else
						$movie->poster = WPML_DEFAULT_POSTER_URL;

					$attachments = get_children( $args = array( 'post_parent' => $movie->ID, 'post_type' => 'attachment' ) );
					if ( ! empty( $attachments ) ) {
						shuffle( $attachments );
						$movie->backdrop = wp_get_attachment_image_src( $attachments[0]->ID, 'full' );
						$movie->backdrop = $movie->backdrop[0];
					}
					else
						$movie->backdrop = $movie->poster;
				}
			}

			include_once( WPML_PATH . '/admin/common/views/dashboard-most-rated-movies-widget.php' );
		}

		public function dashboard_widget_handle() {

			
		}

	}

endif;