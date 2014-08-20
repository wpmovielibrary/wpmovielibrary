<?php
/**
 * WPMovieLibrary Dashboard Class extension.
 * 
 * Create a Help & Doc Widget.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Dashboard_Helper_Widget' ) ) :

	class WPML_Dashboard_Helper_Widget extends WPML_Dashboard {

		/**
		 * Widget ID
		 * 
		 * @since    1.0
		 * 
		 * @var      string
		 */
		protected $widget_id = 'wpml_dashboard_helper_widget';

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
				'support' => array(
					'url'   => 'http://wordpress.org/support/plugin/wpmovielibrary',
					'title' => __( 'Support', 'wpmovielibrary' ),
					'icon'  => 'dashicons dashicons-sos'
				),
				'report' => array(
					'url'   => 'https://github.com/CaerCam/wpmovielibrary/issues/new',
					'title' => __( 'Report a bug', 'wpmovielibrary' ),
					'icon'  => 'dashicons dashicons-flag'
				),
				'contribute' => array(
					'url'   => 'https://github.com/CaerCam/wpmovielibrary',
					'title' => __( 'Contribute', 'wpmovielibrary' ),
					'icon'  => 'dashicons dashicons-admin-tools'
				),
				'donate' => array(
					'url'   => 'http://wpmovielibrary.com/contribute/#donate',
					'title' => __( 'Donate', 'wpmovielibrary' ),
					'icon'  => 'dashicons dashicons-heart'
				),
				'documentation' => array(
					'url'   => 'http://wpmovielibrary.com/documentation/',
					'title' => __( 'Documentation', 'wpmovielibrary' ),
					'icon'  => 'dashicons dashicons-welcome-learn-more'
				),
				'homepage' => array(
					'url'   => 'http://wpmovielibrary.com/',
					'title' => __( 'Official website', 'wpmovielibrary' ),
					'icon'  => 'dashicons dashicons-admin-home'
				)
			);

			foreach ( $list as $slug => $data )
				$links[] = sprintf( '<li><a href="%s"><span class="%s"></span><span class="link">%s</span></a></li>', $data['url'], $data['icon'], $data['title'] );

			$links = implode( '', $links );

			echo self::render_template( '/dashboard-help/help.php', array( 'links' => $links ) );
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