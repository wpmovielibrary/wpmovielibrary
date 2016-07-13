<?php
/**
 * Define the Metabox class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Metabox;

use wpmoly\Core\Loader;

/**
 * Create a set of metaboxes for the plugin to display data in a nicer way
 * than standard WP Metaboxes.
 * 
 * Also handle the Post Convertor Metabox, if needed.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Metaboxes {

	/**
	 * Plugin Metaboxes
	 * 
	 * @since    3.0
	 * 
	 * @var      string
	 */
	public $metaboxes;

	/**
	 * Plugin Post Convertor Metabox
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	public $convertor;

	/**
	 * Class constructor.
	 *
	 * @since    3.0
	 */
	public function __construct( $params = array() ) {

		if ( ! is_admin() ) {
			return;
		}

		$metaboxes = array(
			array(
				'id'        => 'wpmoly',
				'title'     => __( 'WordPress Movie Library', 'wpmovielibrary' ),
				'callback'  => array( 'wpmoly\Metabox\Editor', 'editor' ),
				'screen'    => 'movie',
				'context'   => 'normal',
				'priority'  => 'high',
				'condition' => null,
				'panels'    => array(
					'meta' => array(
						'title'    => __( 'Metadata', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-meta',
						'callback' => array( 'wpmoly\Metabox\Editor', 'meta_panel' )
					),

					'details' => array(
						'title'    => __( 'Details', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-details',
						'callback' => array( 'wpmoly\Metabox\Editor', 'details_panel' )
					),

					'backdrops' => array(
						'title'    => __( 'Images', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-images-alt',
						'callback' => array( 'wpmoly\Metabox\Editor', 'backdrops_panel' )
					),

					'posters' => array(
						'title'    => __( 'Posters', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-poster',
						'callback' => array( 'wpmoly\Metabox\Editor', 'posters_panel' )
					)
				)
			)
		);

		$convertor = array(
			'id'            => 'wpmoly-convertor',
			'title'         => __( 'WordPress Movie Library', 'wpmovielibrary' ),
			'callback'      => array( $this, 'movie_convertor' ),
			'screen'        => wpmoly_o( 'convert-post-types', array() ),
			'context'       => 'side',
			'priority'      => 'high',
			'callback_args' => null,
			'condition'     => ( '1' == wpmoly_o( 'convert-enable' ) )
		);

		/**
		 * Filter the plugin metaboxes
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $metaboxes Available metaboxes parameters
		 */
		$this->metaboxes = apply_filters( 'wpmoly/filter/metaboxes', $metaboxes );

		/**
		 * Filter the plugin convertor metabox
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $convertor Convertor metabox parameters
		 */
		$this->convertor = apply_filters( 'wpmoly/filter/metabox/convertor', $convertor );

		// Instanciate metaboxes
		$this->make();
	}

	/**
	 * Register all of the metaboxes hooks.
	 *
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function define_admin_hooks() {

		$loader = Loader::get_instance();

		foreach ( $this->metaboxes as $metabox ) {

			$metabox->define_admin_hooks();

			// Add metabox
			foreach ( (array) $metabox->screen as $screen ) {
				$loader->add_action( "add_meta_boxes_{$screen}", $metabox, 'create' );
			}

			// Register hooks
			if ( ! empty( $metabox->actions ) ) {
				foreach ( $metabox->actions as $action ) {
					list( $hook, $class, $method, $priority, $arguments ) = $action;
					$loader->add_action( $hook, $class, $method, $priority, $arguments );
				}
			}

			if ( ! empty( $metabox->filters ) ) {
				foreach ( $metabox->filters as $filter ) {
					list( $hook, $class, $method, $priority, $arguments ) = $filter;
					$loader->add_filter( $hook, $class, $method, $priority, $arguments );
				}
			}
		}

		if ( isset( $this->convertor ) ) {
			$loader->add_action( 'add_meta_boxes', $this, 'add_convertor_meta_box' );
		}
	}

	/**
	 * Instanciate all defined Metaboxes.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function make() {

		foreach ( $this->metaboxes as $slug => $metabox ) {

			$callback = $metabox['callback'];
			if ( ! class_exists( $callback[0] ) || ! method_exists( $callback[0], $callback[1] ) ) {
				continue;
			}

			$callback_class  = $callback[0];
			$callback_method = $callback[1];

			$this->metaboxes[ $slug ] = new $callback_class( $metabox );
		}
	}

	/**
	 * Register WPMoly Post Convertor Metabox
	 * 
	 * @since    2.0
	 * 
	 * @return   null
	 */
	public function add_convertor_meta_box() {

		extract( $this->convertor );

		if ( ! is_array( $screen ) ) {
			$screen = array( $screen );
		}

		if ( ! is_null( $condition ) && false === $condition ) {
			return;
		}

		$callback_args['_metabox'] = $this->convertor;

		foreach ( $screen as $s ) {
			add_meta_box( 'convertor-metabox', $title, $callback, $s, $context, $priority, $callback_args );
		}

	}
}
