<?php
/**
 * WPMovieLibrary Dashboard Class extension.
 * 
 * Create a Quick Action Widget.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Dashboard_Quickaction_Widget' ) ) :

	class WPML_Dashboard_Quickaction_Widget extends WPML_Dashboard {

		/**
		 * Widget ID
		 * 
		 * @since    1.0
		 * 
		 * @var      string
		 */
		protected $widget_id = 'wpml_dashboard_quickaction_widget';

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 */
		public function __construct() {}

		/**
		 * The Widget content.
		 * 
		 * @since    1.0
		 */
		public function dashboard_widget() {

			$links = array();
			$list = array(
				'new_movie' => array(
					'url'   => admin_url( 'post-new.php?post_type=movie' ),
					'title' => __( 'New movie', 'wpmovielibrary' ),
					'icon'  => 'dashicons dashicons-welcome-add-page'
				),
				'import'    => array(
					'url'   => admin_url( 'admin.php?page=wpml_import' ),
					'title' => __( 'Import movies', 'wpmovielibrary' ),
					'icon'  => 'dashicons dashicons-download'
				),
				'settings'  => array(
					'url'   => admin_url( 'edit.php?post_type=movie' ),
					'title' => __( 'Manage movies', 'wpmovielibrary' ),
					'icon'  => 'dashicons dashicons-format-video'
				)
			);

			foreach ( $list as $slug => $data )
				$links[] = sprintf( '<li><a href="%s"><span class="%s"></span><span class="link">%s</span></a></li>', $data['url'], $data['icon'], $data['title'] );

			$links = implode( '', $links );

			echo self::render_template( '/dashboard-quickaction/quickaction.php', array( 'links' => $links ) );
		}

		/**
		 * Widget's configuration callback
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $context box context
		 * @param    mixed     $object gets passed to the box callback function as first parameter
		 */
		public function dashboard_widget_handle( $context, $object ) {}

	}

endif;