<?php
/**
 * Define the custom post meta plugin class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary\Registrars;

use function WPMovieLibrary\Support\Helpers\config;

/**
 * Register the plugin's custom post meta.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Post_Meta {

	/**
	 * Static instance.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Post_Meta
	 */
	private static $_instance = null;

	/**
	 * Post Meta parameters.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var array
	 */
	private array $post_meta = [];

	/**
	 * Constructor.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Get the instance of this class, insantiating it if it doesn't exist
	 * yet.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Post_Meta
	 */
	public static function get_instance() {

		if ( ! is_object( self::$_instance ) ) {
			self::$_instance = new self;
			self::$_instance->init();
		}

		return self::$_instance;
	}

	/**
	 * Initialize.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function init() {

		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Register post meta.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function register() {

		$this->post_meta = config( 'post-meta', [] );
		foreach ( $this->post_meta as $post_type => $metas ) {
			foreach ( $metas as $key => $args ) {
				$meta_key = "_wpmoly_{$post_type}_{$key}";
				$args['post_type'] = [ $post_type ];
				register_post_meta( $post_type, $meta_key, $args );
			}
		}
	}
}
