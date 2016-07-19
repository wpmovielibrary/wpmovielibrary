<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class i18n extends Singleton {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    3.0
	 */
	public function load_plugin_textdomain() {

		$domain      = 'wpmovielibrary';
		$rel_path    = false;
		$plugin_path = dirname( dirname( dirname( plugin_basename( __FILE__ ) ) ) ) . '/languages/';

		load_plugin_textdomain( $domain, $rel_path, $plugin_path );
	}

	/**
	 * Load additional text domains for translation.
	 *
	 * @since    3.0
	 */
	public function load_additional_textdomains() {

		$domain      = 'wpmovielibrary-iso';
		$rel_path    = false;
		$plugin_path = dirname( dirname( dirname( plugin_basename( __FILE__ ) ) ) ) . '/languages/';

		load_plugin_textdomain( $domain, $rel_path, $plugin_path );
	}

}
