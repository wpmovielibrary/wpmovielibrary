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
			$this->widget_name = __( 'Your library', WPML_SLUG );
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
					// __( 'One movie', '%d movies', $movies, WPML_SLUG )
					'single' => 'One online movie',
					'plural' => '%d online movies',
					'empty'  => sprintf( '%s <a href="%s">%s</a>', __( 'No movie added yet.', WPML_SLUG ), admin_url( 'post-new.php?post_status=publish&post_type=movie' ), __( 'Add one!', WPML_SLUG ) ),
					'url'    => admin_url( 'edit.php?post_type=movie' ),
					'icon'   => 'dashicons dashicons-format-video',
					'string' => '<a href="%s">%s</a>'
				),
				'draft' => array(
					// _n( 'One drafted movie', '%d movies drafts', $movies, WPML_SLUG )
					'single' => 'One movie draft',
					'plural' => '%d movies drafts',
					'empty'  => __( 'No draft', WPML_SLUG ),
					'url'    => admin_url( 'edit.php?post_status=draft&post_type=movie' ),
					'icon'   => 'dashicons dashicons-edit',
					'string' => '<a href="%s">%s</a>'
				),
				'queued' => array(
					// _n( 'One queued movie', '%d queued movies', $movies, WPML_SLUG )
					'single' => 'One queued movie',
					'plural' => '%d queued movies',
					'empty'  => sprintf( '%s <a href="%s">%s</a>', __( 'No movie added yet.', WPML_SLUG ), admin_url( 'post-new.php?post_type=movie' ), __( 'Add one!', WPML_SLUG ) ),
					'url'    => admin_url( 'admin.php?page=wpml_import&amp;wpml_section=wpml_import_queue' ),
					'icon'   => 'dashicons dashicons-playlist-video',
					'string' => '<a href="%s">%s</a>'
				),
				'imported' => array(
					// _n( 'One imported movie', '%d imported movies', $movies, WPML_SLUG )
					'single' => 'One imported movie',
					'plural' => '%d imported movies',
					'empty'  => sprintf( '%s <a href="%s">%s</a>', __( 'No movie added yet.', WPML_SLUG ), admin_url( 'post-new.php?post_type=movie' ), __( 'Add one!', WPML_SLUG ) ),
					'url'    => admin_url( 'admin.php?page=wpml_import&amp;wpml_section=wpml_imported' ),
					'icon'   => 'dashicons dashicons-download',
					'string' => '<a href="%s">%s</a>'
				)
			);

			foreach ( $list as $status => $data ) {
				if ( isset( $count[ $status ] ) ) {
					$movies = $count[ $status ];
					if ( $movies )
						$link = sprintf( $data['string'], $data['url'], sprintf( _n( $data['single'], $data['plural'], $movies, WPML_SLUG ), $movies ) );
					else
						$link = $data['empty'];

					$links[] = '<li><span class="' . $data['icon'] . '"></span> ' . $link . '</li>';

				}
			}

			$links = implode( '', $links );

			include_once( WPML_PATH . '/admin/common/views/dashboard-stats-widget.php' );
		}

		public function dashboard_widget_handle() {

			
		}

	}

endif;