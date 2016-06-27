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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpmoly.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name . '-font', WPMOLY_URL . 'public/fonts/wpmovielibrary/style.css', array(), $this->version, 'all' );
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

	/**
	 * Register default filters for the plugin.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function set_default_filters() {

		$loader = Loader::get_instance();

		// Meta/Details Formatting
		$loader->add_filter( 'wpmoly/shortcode/format/adult/value',                '', 'wpmoly\Helpers\Formatting\adult',          15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/budget/value',               '', 'wpmoly\Helpers\Formatting\budget',         15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/homepage/value',             '', 'wpmoly\Helpers\Formatting\homepage',       15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/cast/value',                 '', 'wpmoly\Helpers\Formatting\cast',           15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/format/value',               '', 'wpmoly\Helpers\Formatting\format',         15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/genres/value',               '', 'wpmoly\Helpers\Formatting\genres',         15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/language/value',             '', 'wpmoly\Helpers\Formatting\languages',      15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/local_release_date/value',   '', 'wpmoly\Helpers\Formatting\release_date',   15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/media/value',                '', 'wpmoly\Helpers\Formatting\media',          15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/production_countries/value', '', 'wpmoly\Helpers\Formatting\countries',      15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/rating/value',               '', 'wpmoly\Helpers\Formatting\rating',         15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/release_date/value',         '', 'wpmoly\Helpers\Formatting\release_date',   15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/revenue/value',              '', 'wpmoly\Helpers\Formatting\revenue',        15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/runtime/value',              '', 'wpmoly\Helpers\Formatting\runtime',        15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/spoken_languages/value',     '', 'wpmoly\Helpers\Formatting\languages',      15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/status/value',               '', 'wpmoly\Helpers\Formatting\status',         15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/subtitles/value',            '', 'wpmoly\Helpers\Formatting\languages',      15, 3 );

		// Meta Permalinks
		$loader->add_filter( 'wpmoly/filter/meta/adult/url',                '', 'wpmoly\Helpers\Permalinks\adult',              15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/author/url',               '', 'wpmoly\Helpers\Permalinks\author',             15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/composer/url',             '', 'wpmoly\Helpers\Permalinks\composer',           15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/local_release_date/url',   '', 'wpmoly\Helpers\Permalinks\local_release_date', 15, 5 );
		$loader->add_filter( 'wpmoly/filter/meta/photography/url',          '', 'wpmoly\Helpers\Permalinks\photography',        15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/producer/url',             '', 'wpmoly\Helpers\Formatting\producer',           15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/production_countries/url', '', 'wpmoly\Helpers\Permalinks\countries',          15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/release_date/url',         '', 'wpmoly\Helpers\Permalinks\release_date',       15, 5 );
		$loader->add_filter( 'wpmoly/filter/meta/spoken_languages/url',     '', 'wpmoly\Helpers\Permalinks\languages',          15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/writer/url',               '', 'wpmoly\Helpers\Permalinks\writer',             15, 1 );

		// Details Permalinks
		$loader->add_filter( 'wpmoly/filter/detail/format/url',    '', 'wpmoly\Helpers\Permalinks\format',    15, 3 );
		$loader->add_filter( 'wpmoly/filter/detail/language/url',  '', 'wpmoly\Helpers\Permalinks\language',  15, 3 );
		$loader->add_filter( 'wpmoly/filter/detail/media/url',     '', 'wpmoly\Helpers\Permalinks\media',     15, 3 );
		$loader->add_filter( 'wpmoly/filter/detail/rating/url',    '', 'wpmoly\Helpers\Permalinks\rating',    15, 3 );
		$loader->add_filter( 'wpmoly/filter/detail/status/url',    '', 'wpmoly\Helpers\Permalinks\status',    15, 3 );
		$loader->add_filter( 'wpmoly/filter/detail/subtitles/url', '', 'wpmoly\Helpers\Permalinks\subtitles', 15, 3 );
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
			'\wpmoly\Shortcodes\Detail',
			'\wpmoly\Shortcodes\Countries',
			'\wpmoly\Shortcodes\Languages',
			'\wpmoly\Shortcodes\LocalReleaseDate',
			'\wpmoly\Shortcodes\ReleaseDate',
			'\wpmoly\Shortcodes\Runtime'
		);

		foreach ( $shortcodes as $shortcode ) {
			$shortcode::register();
		}
	}

}
