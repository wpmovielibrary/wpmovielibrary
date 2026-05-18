<?php
/**
 * Define the custom post types plugin class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

/**
 * Register the plugin's custom post types.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Post_Types {

	/**
	 * Static instance.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Post_Types
	 */
	private static $_instance = null;

	/**
	 * Post Types parameters.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var array
	 */
	private array $post_types = [];

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
	 * @return Post_Types
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

		$this->post_types = [
			//
		];
	}

	/**
	 * Register custom Post Types.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function register() {

		foreach ( $this->post_types as $slug => $args ) {
			register_post_type( $slug, $args );
		}
	}
}
