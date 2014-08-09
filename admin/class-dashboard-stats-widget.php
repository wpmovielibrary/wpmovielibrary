<?php
/**
 * WPMovieLibrary Dashboard Class extension.
 * 
 * Create a Statistics Widget.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Dashboard_Stats_Widget' ) ) :

	class WPML_Dashboard_Stats_Widget extends WPML_Dashboard {

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

			$this->widget_id = 'wpml_dashboard_stats_widget';
			$this->widget_name = __( 'Your library', 'wpmovielibrary' );
			$this->callback = array( $this, 'dashboard_widget' );
			$this->control_callback = null;
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

			$count = (array) wp_count_posts( 'movie' );
			$count = array(
				'movie'       => $count['publish'],
				'imported'    => $count['import-draft'],
				'queued'      => $count['import-queued'],
				'draft'       => $count['draft'],
				'total'       => 0,
			);
			$count['total'] = array_sum( $count );
			$count['collections'] = wp_count_terms( 'collection' );
			$count['genres'] = wp_count_terms( 'genre' );
			$count['actors'] = wp_count_terms( 'actor' );

			$links = array();
			$list = array(
				'movie' => array(
					'single' => __( 'One movie', 'wpmovielibrary' ),
					'plural' => __( '%d movies', 'wpmovielibrary' ),
					'empty'  => __( 'No movie added yet.', 'wpmovielibrary' ),
					'url'    => admin_url( 'edit.php?post_type=movie' ),
					'icon'   => 'dashicons dashicons-format-video',
					'string' => '<a href="%s">%s</a>'
				),
				'draft' => array(
					'single' => __( 'One movie draft', 'wpmovielibrary' ),
					'plural' => __( '%d movies drafts', 'wpmovielibrary' ),
					'empty'  => __( 'No draft', 'wpmovielibrary' ),
					'url'    => admin_url( 'edit.php?post_status=draft&post_type=movie' ),
					'icon'   => 'dashicons dashicons-edit',
					'string' => '<a href="%s">%s</a>'
				),
				'queued' => array(
					'single' => __( 'One queued movie', 'wpmovielibrary' ),
					'plural' => __( '%d queued movies', 'wpmovielibrary' ),
					'empty'  => __( 'No queued movie.', 'wpmovielibrary' ),
					'url'    => admin_url( 'admin.php?page=wpml_import&amp;wpml_section=wpml_import_queue' ),
					'icon'   => 'dashicons dashicons-list-view',
					'string' => '<a href="%s">%s</a>'
				),
				'imported' => array(
					'single' => __( 'One imported movie', 'wpmovielibrary' ),
					'plural' => __( '%d imported movies', 'wpmovielibrary' ),
					'empty'  => __( 'No imported movie.', 'wpmovielibrary' ),
					'url'    => admin_url( 'admin.php?page=wpml_import&amp;wpml_section=wpml_imported' ),
					'icon'   => 'dashicons dashicons-download',
					'string' => '<a href="%s">%s</a>'
				)
			);

			foreach ( $list as $status => $data ) {
				if ( isset( $count[ $status ] ) ) {
					$movies = $count[ $status ];
					if ( $movies ) {
						$plural = ( 1 < $movies ? sprintf( $data['plural'], $movies ) : $data['single'] );
						$link = sprintf( $data['string'], $data['url'], $plural, $movies );
					}
					else
						$link = $data['empty'];

					$links[] = '<li><span class="' . $data['icon'] . '"></span> ' . $link . '</li>';

				}
			}

			$links = implode( '', $links );

			echo self::render_template( '/dashboard-statistics/statistics.php', array( 'links' => $links, 'count' => $count ) );
		}

		public function dashboard_widget_handle() {

			
		}

	}

endif;