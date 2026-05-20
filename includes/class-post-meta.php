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

		add_action( 'init',            [ $this, 'register' ] );
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
	 * Forms that submit `meta_input` must include a nonce field generated with
	 * `wp_nonce_field( 'wpmoly_save_meta_input', '_wpmoly_meta_input_nonce' )`.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 *
	 * @param int $post_ID Post ID.
	 */
	public function save_meta_input( $post_ID ) {

		// Bail on autosaves and revisions — meta_input is only meaningful on real saves.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_ID ) ) {
			return;
		}

		// Bail if no meta_input was submitted.
		if ( empty( $_POST['meta_input'] ) || ! is_array( $_POST['meta_input'] ) ) {
			return;
		}

		// Verify nonce.
		if ( ! isset( $_POST['_wpmoly_meta_input_nonce'] ) ||
		     ! wp_verify_nonce( sanitize_key( $_POST['_wpmoly_meta_input_nonce'] ), 'wpmoly_save_meta_input' ) ) {
			return;
		}

		// Capability check — must be allowed to edit this specific post.
		if ( ! current_user_can( 'edit_post', $post_ID ) ) {
			return;
		}

		$post_type = get_post_type( $post_ID );

		$data = wp_unslash( $_POST['meta_input'] );
		foreach ( $data as $key => $value ) {

			$args = $this->get_registered_meta( $key );
			if ( null === $args ) {
				continue;
			}

			// Only persist meta that belongs to this post type.
			if ( ! in_array( $post_type, (array) ( $args['post_type'] ?? [] ), true ) ) {
				continue;
			}

			if ( ! empty( $args['prepare_callback'] ) && is_callable( $args['prepare_callback'] ) ) {
				$value = call_user_func_array( $args['prepare_callback'], [ $post_ID, $key, $value ] );
			}

			update_post_meta( $post_ID, $key, $value );
		}
	}

	/**
	 * Get registered meta.
	 *
	 * Lookup is keyed by the full, prefixed meta key (e.g. `_wpmoly_movie_director`),
	 * matching the structure `register()` builds.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 *
	 * @param string $key Meta key. Optional.
	 *
	 * @return array|null
	 */
	public function get_registered_meta( $key = null ) {

		if ( ! is_null( $key ) ) {
			return $this->post_meta[ $key ] ?? null;
		}

		return $this->post_meta;
	}

	/**
	 * Register post meta.
	 *
	 * Stores the registered meta as a flat map keyed by the full prefixed
	 * meta key so `save_meta_input()` can look up args in O(1).
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function register() {

		$this->post_meta = [];
		foreach ( config( 'post-meta', [] ) as $post_type => $metas ) {
			foreach ( $metas as $key => $args ) {

				$meta_key = "_wpmoly_{$post_type}_{$key}";
				$args['post_type'] = [ $post_type ];

				$this->post_meta[ $meta_key ] = $args;

				register_post_meta( $post_type, $meta_key, $args );
			}
		}
	}
}
