<?php
/**
 * Define the custom term meta plugin class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary\Registrars;

use function WPMovieLibrary\Support\Helpers\config;

/**
 * Register the plugin's custom term meta.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Term_Meta {

	/**
	 * Static instance.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Term_Meta
	 */
	private static $_instance = null;

	/**
	 * Term Meta parameters.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var array
	 */
	private array $term_meta = [];

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
	 * @return Term_Meta
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
	 * Register term meta.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function register() {

		$this->term_meta = config( 'term-meta', [] );
		foreach ( $this->term_meta as $slug => $args ) {
			$taxonomies = (array) $args['taxonomy'];
			foreach ( $taxonomies as $taxonomy ) {
				register_term_meta( $taxonomy, $slug, $args );
			}
		}
	}
}
