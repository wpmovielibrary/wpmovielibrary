<?php
/**
 * Define the custom post meta plugin class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

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

		add_action( 'save_post',       [ $this, 'save_meta_input' ] );
		add_action( 'add_attachment',  [ $this, 'save_meta_input' ] );
		add_action( 'edit_attachment', [ $this, 'save_meta_input' ] );
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
	 * @param int $post_ID Post ID.
	 *
	 * @return array
	 */
	public function save_meta_input( $post_ID ) {

		if ( ! isset( $_POST['meta_input'] ) ) {
			return false;
		}

		foreach ( $_POST['meta_input'] as $key => $value ) {
			$args = $this->get_registered_meta( $key );
			if ( ! is_null( $args ) ) {
				if ( ! empty( $args['prepare_callback'] ) && is_callable( $args['prepare_callback'] ) ) {
					$value = call_user_func_array( $args['prepare_callback'], [ $post_ID, $key, $value ] );
				}
				update_post_meta( $post_ID, $key, $value );
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
			$meta = isset( $this->post_meta[ $key ] ) ? $this->post_meta[ $key ] : null;
		} else {
			$meta = $this->post_meta;
		}

		return $meta;
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
