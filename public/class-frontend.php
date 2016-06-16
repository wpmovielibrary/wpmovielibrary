<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public
 */

namespace wpmoly;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Frontend {

	/**
	 * The ID of this plugin.
	 * 
	 * @var      string
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 * 
	 * @var      string
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version    The version of this plugin.
	 * 
	 * @return   void
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function enqueue_styles() {

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpmoly.css', array(), $this->version, 'all' );

		//wp_enqueue_style( $this->plugin_name . '-font', WPMOLY_URL . 'public/fonts/wpmovielibrary/style.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function enqueue_scripts() {

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpmoly.js', array( 'jquery', 'underscore', 'backbone' ), $this->version, true );
	}

	public function set_default_filters() {

		$loader = Loader::get_instance();

		$loader->add_filter( 'wpmoly/shortcode/format/runtime/value',              '', 'wpmoly\Formatting\runtime' );
		$loader->add_filter( 'wpmoly/shortcode/format/spoken_languages/value',     '', 'wpmoly\Formatting\spoken_languages' );
		$loader->add_filter( 'wpmoly/shortcode/format/production_countries/value', '', 'wpmoly\Formatting\production_countries' );
		$loader->add_filter( 'wpmoly/shortcode/format/local_release_date/value',   '', 'wpmoly\Formatting\local_release_date' );
		$loader->add_filter( 'wpmoly/shortcode/format/release_date/value',         '', 'wpmoly\Formatting\release_date' );
	}

	/**
	 * Register the Shortcodes.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register_shortcodes() {

		$shortcodes = array(
			'\wpmoly\Shortcodes\Movies',
			'\wpmoly\Shortcodes\Metadata',
			'\wpmoly\Shortcodes\Countries',
			'\wpmoly\Shortcodes\Languages',
			'\wpmoly\Shortcodes\Runtime',
			'\wpmoly\Shortcodes\ReleaseDate',
			'\wpmoly\Shortcodes\LocalReleaseDate'
		);

		foreach ( $shortcodes as $shortcode ) {
			$shortcode::register();
		}
	}

}
