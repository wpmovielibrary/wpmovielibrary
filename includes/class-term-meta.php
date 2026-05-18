<?php
/**
 * Define the custom term meta plugin class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

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

		add_action( 'edited_term', [ &$this, 'save_meta_input' ] );

		$this->term_meta = [
			//
		];
	}

	/**
	 * Save registered metadata.
	 *
	 * WordPress 5.0.1 broke backward compatibility on metadata saving for all
	 * version since 3.7. Prior to this update metadata could be saved automatically
	 * by providing a `meta_input` to $_POST data. That row is now explicitely
	 * ignored and meta have to be saved explicitely by plugins/themes.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 *
	 * @param int $term_ID Term ID.
	 *
	 * @return array
	 */
	public function save_meta_input( $term_ID ) {

		if ( ! isset( $_POST['meta_input'] ) ) {
			return false;
		}

		foreach ( $_POST['meta_input'] as $key => $value ) {
			if ( ! is_null( $this->get_registered_meta( $key ) ) ) {
				update_term_meta( $term_ID, $key, $value );
			}
		}
	}

	/**
	 * Get registered meta.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 *
	 * @param string $key Meta key. Optional.
	 *
	 * @return array
	 */
	public function get_registered_meta( $key = null ) {

		if ( ! is_null( $key ) ) {
			$meta = isset( $this->term_meta[ $key ] ) ? $this->term_meta[ $key ] : null;
		} else {
			$meta = $this->term_meta;
		}

		return $meta;
	}

	/**
	 * Register term meta.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function register() {

		foreach ( $this->term_meta as $slug => $args ) {
			$taxonomies = (array) $args['taxonomy'];
			foreach ( $taxonomies as $taxonomy ) {
				register_term_meta( $taxonomy, $slug, $args );
			}
		}
	}
}
