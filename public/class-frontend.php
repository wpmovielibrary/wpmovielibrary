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

		wp_enqueue_style( $this->plugin_name, WPMOLY_URL . 'public/css/wpmoly.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name . '-flags', WPMOLY_URL . 'public/css/wpmoly-flags.css', array(), $this->version, 'all' );

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

		$loader = Core\Loader::get_instance();

		// Meta/Details Formatting
		$loader->add_filter( 'wpmoly/shortcode/format/adult/value',                '', array( 'wpmoly\Helpers\Formatting', 'adult' ),              15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/author/value',               '', array( 'wpmoly\Helpers\Formatting', 'author' ),             15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/budget/value',               '', array( 'wpmoly\Helpers\Formatting', 'budget' ),             15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/certification/value',        '', array( 'wpmoly\Helpers\Formatting', 'certification' ),      15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/composer/value',             '', array( 'wpmoly\Helpers\Formatting', 'composer' ),           15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/homepage/value',             '', array( 'wpmoly\Helpers\Formatting', 'homepage' ),           15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/cast/value',                 '', array( 'wpmoly\Helpers\Formatting', 'cast' ),               15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/format/value',               '', array( 'wpmoly\Helpers\Formatting', 'format' ),             15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/genres/value',               '', array( 'wpmoly\Helpers\Formatting', 'genres' ),             15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/language/value',             '', array( 'wpmoly\Helpers\Formatting', 'language' ),           15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/local_release_date/value',   '', array( 'wpmoly\Helpers\Formatting', 'local_release_date' ), 15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/media/value',                '', array( 'wpmoly\Helpers\Formatting', 'media' ),              15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/photography/value',          '', array( 'wpmoly\Helpers\Formatting', 'photography' ),        15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/production_countries/value', '', array( 'wpmoly\Helpers\Formatting', 'countries' ),          15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/production_companies/value', '', array( 'wpmoly\Helpers\Formatting', 'production' ),         15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/producer/value',             '', array( 'wpmoly\Helpers\Formatting', 'producer' ),           15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/rating/value',               '', array( 'wpmoly\Helpers\Formatting', 'rating' ),             15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/release_date/value',         '', array( 'wpmoly\Helpers\Formatting', 'release_date' ),       15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/revenue/value',              '', array( 'wpmoly\Helpers\Formatting', 'revenue' ),            15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/runtime/value',              '', array( 'wpmoly\Helpers\Formatting', 'runtime' ),            15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/spoken_languages/value',     '', array( 'wpmoly\Helpers\Formatting', 'languages' ),          15, 1 );
		$loader->add_filter( 'wpmoly/shortcode/format/status/value',               '', array( 'wpmoly\Helpers\Formatting', 'status' ),             15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/subtitles/value',            '', array( 'wpmoly\Helpers\Formatting', 'languages' ),          15, 3 );
		$loader->add_filter( 'wpmoly/shortcode/format/writer/value',               '', array( 'wpmoly\Helpers\Formatting', 'writer' ),             15, 1 );

		// Meta Permalinks
		$loader->add_filter( 'wpmoly/filter/meta/adult',              '', array( 'wpmoly\Helpers\Permalinks', 'adult' ),         15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/author/single',      '', array( 'wpmoly\Helpers\Permalinks', 'author' ),        15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/certification',      '', array( 'wpmoly\Helpers\Permalinks', 'certification' ), 15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/composer/single',    '', array( 'wpmoly\Helpers\Permalinks', 'composer' ),      15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/local_release_date', '', array( 'wpmoly\Helpers\Permalinks', 'release_date' ),  15, 6 );
		$loader->add_filter( 'wpmoly/filter/meta/photography/single', '', array( 'wpmoly\Helpers\Permalinks', 'photographer' ),  15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/producer/single',    '', array( 'wpmoly\Helpers\Permalinks', 'producer' ),      15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/production/single',  '', array( 'wpmoly\Helpers\Permalinks', 'production' ),    15, 1 );
		$loader->add_filter( 'wpmoly/filter/meta/country/single',     '', array( 'wpmoly\Helpers\Permalinks', 'country' ),       15, 3 );
		$loader->add_filter( 'wpmoly/filter/meta/release_date',       '', array( 'wpmoly\Helpers\Permalinks', 'release_date' ),  15, 6 );
		$loader->add_filter( 'wpmoly/filter/meta/language/single',    '', array( 'wpmoly\Helpers\Permalinks', 'language' ),      15, 4 );
		$loader->add_filter( 'wpmoly/filter/meta/writer/single',      '', array( 'wpmoly\Helpers\Permalinks', 'writer' ),        15, 1 );

		// Details Permalinks
		$loader->add_filter( 'wpmoly/filter/detail/format/single',    '', array( 'wpmoly\Helpers\Permalinks', 'format' ),    15, 4 );
		$loader->add_filter( 'wpmoly/filter/detail/language/single',  '', array( 'wpmoly\Helpers\Permalinks', 'language' ),  15, 4 );
		$loader->add_filter( 'wpmoly/filter/detail/media/single',     '', array( 'wpmoly\Helpers\Permalinks', 'media' ),     15, 4 );
		$loader->add_filter( 'wpmoly/filter/detail/rating',           '', array( 'wpmoly\Helpers\Permalinks', 'rating' ),    15, 5 );
		$loader->add_filter( 'wpmoly/filter/detail/status/single',    '', array( 'wpmoly\Helpers\Permalinks', 'status' ),    15, 4 );
		//$loader->add_filter( 'wpmoly/filter/detail/subtitles/single', '', array( 'wpmoly\Helpers\Permalinks', 'subtitles' ), 15, 3 );
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
			'\wpmoly\Shortcodes\Images',
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
