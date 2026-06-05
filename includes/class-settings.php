<?php
/**
 * Define the plugin settings class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

/**
 * Manage the plugin's settings.
 *
 * Stores a single nested option (`wpmoly_settings`) and exposes a dot-notation
 * accessor. Form submissions are handled directly through `admin-post.php`
 * rather than the WordPress Settings API.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Settings {

	/**
	 * Option name in the WordPress options table.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private string $option_name;

	/**
	 * Action name handled through admin-post.php.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private string $save_action;

	/**
	 * Static instance.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Settings
	 */
	private static $_instance = null;

	/**
	 * Constructor.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function __construct() {

		$this->option_name = 'wpmoly_settings';
		$this->save_action = 'wpmoly_save_settings';
	}

	/**
	 * Get the instance of this class, instantiating it if it doesn't exist
	 * yet.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Settings
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

		add_action( 'admin_post_' . $this->save_action, [ $this, 'save' ] );
	}

	/**
	 * Handle the settings form submission.
	 *
	 * Verifies nonce and capability, sanitizes the payload, persists the
	 * option and redirects back to the settings screen.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function save() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage settings.', 'wpmovielibrary' ), '', [ 'response' => 403 ] );
		}

		check_admin_referer( $this->save_action );

		$raw       = isset( $_POST[ $this->option_name ] ) && is_array( $_POST[ $this->option_name ] ) ? wp_unslash( $_POST[ $this->option_name ] ) : [];
		$sanitized = $this->sanitize( $raw );

		update_option( $this->option_name, $sanitized );

		wp_safe_redirect( add_query_arg(
			[
				'page'    => 'wpmovielibrary-settings',
				'updated' => 'true',
			],
			admin_url( 'admin.php' )
		) );
		exit;
	}

	/**
	 * Sanitize the settings payload.
	 *
	 * Ensures the final shape always matches the documented schema even when
	 * partial or malformed input is submitted.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @param mixed $input Raw input.
	 *
	 * @return array Sanitized settings.
	 */
	private function sanitize( $input ) {

		$defaults = $this->defaults();

		if ( ! is_array( $input ) ) {
			return $defaults;
		}

		$tmdb = is_array( $input['tmdb'] ?? null ) ? $input['tmdb'] : [];

		return [
			'tmdb' => [
				'api_key'  => isset( $tmdb['api_key'] )  ? sanitize_text_field( (string) $tmdb['api_key'] )  : $defaults['tmdb']['api_key'],
				'language' => isset( $tmdb['language'] ) ? sanitize_text_field( (string) $tmdb['language'] ) : $defaults['tmdb']['language'],
				'country'  => isset( $tmdb['country'] )  ? sanitize_text_field( (string) $tmdb['country'] )  : $defaults['tmdb']['country'],
			],
		];
	}

	/**
	 * Read a settings value using dot notation.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 *
	 * @param string $key     Dot-notation key, e.g. `tmdb.api_key`.
	 * @param mixed  $default Value returned when the key is missing.
	 *
	 * @return mixed The setting value, or `$default` if not set.
	 */
	public function get( string $key, mixed $default = null ) {

		$value = get_option( $this->option_name, [] );

		foreach ( explode( '.', $key ) as $segment ) {
			if ( ! is_array( $value ) || ! array_key_exists( $segment, $value ) ) {
				return $default;
			}
			$value = $value[ $segment ];
		}

		return $value;
	}

	/**
	 * Default settings values.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function defaults() {

		return [
			'tmdb' => [
				'api_key'  => '',
				'language' => 'en',
				'country'  => 'US',
			],
		];
	}
}
