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
	 * Single instance.
	 *
	 * @var    Frontend
	 */
	private static $instance = null;

	/**
	 * Public stylesheets.
	 *
	 * @var    array
	 */
	private $styles = array();

	/**
	 * Public scripts.
	 *
	 * @var    array
	 */
	private $scripts = array();

	/**
	 * Initialize the class and set its properties.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function __construct() {

		$styles = array(

			// Plugin-wide normalize
			'normalize' => array( 'file' => WPMOLY_URL . 'public/css/wpmoly-normalize-min.css' ),

			// Main stylesheet
			''          => array( 'file' => WPMOLY_URL . 'public/css/wpmoly.css', 'deps' => array( WPMOLY_SLUG . '-normalize' ) ),

			// Common stylesheets
			'common'    => array( 'file' => WPMOLY_URL . 'public/css/common.css' ),
			'headboxes' => array( 'file' => WPMOLY_URL . 'public/css/wpmoly-headboxes.css' ),
			'grids'     => array( 'file' => WPMOLY_URL . 'public/css/wpmoly-grids.css' ),
			'flags'     => array( 'file' => WPMOLY_URL . 'public/css/wpmoly-flags.css' ),

			// Plugin icon font
			'font'      => array( 'file' => WPMOLY_URL . 'public/fonts/wpmovielibrary/style.css' )
		);

		/**
		 * Filter the default styles to register.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $styles
		 */
		$this->styles = apply_filters( 'wpmoly/filter/default/public/styles', $styles );

		$scripts = array(
			'' => array( 'file' => WPMOLY_URL . 'public/js/wpmoly.js', 'deps' => array( 'jquery', 'underscore', 'backbone' ) )
		);

		/**
		 * Filter the default scripts to register.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $scripts
		 */
		$this->scripts = apply_filters( 'wpmoly/filter/default/public/scripts', $scripts );
	}

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   Singleton
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Register frontend stylesheets.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function register_styles() {

		foreach ( $this->styles as $id => $style ) {

			if ( ! empty( $id ) ) {
				$id = '-' . $id;
			}
			$id = WPMOLY_SLUG . $id;

			$style = wp_parse_args( $style, array(
				'file'    => '',
				'deps'    => array(),
				'version' => WPMOLY_VERSION,
				'media'   => 'all'
			) );

			wp_register_style( $id, $style['file'], $style['deps'], $style['version'], $style['media'] );
		}
	}

	/**
	 * Enqueue a specific style.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $id Script ID.
	 * 
	 * @return   void
	 */
	private function enqueue_style( $id = '' ) {

		if ( ! empty( $id ) ) {
			$id = '-' . $id;
		}
		$id = WPMOLY_SLUG . $id;

		wp_enqueue_style( $id );
	}

	/**
	 * Register frontend JavaScript.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function register_scripts() {

		foreach ( $this->scripts as $id => $script ) {

			if ( ! empty( $id ) ) {
				$id = '-' . $id;
			}
			$id = WPMOLY_SLUG . $id;

			$script = wp_parse_args( $script, array(
				'file'    => '',
				'deps'    => array(),
				'version' => WPMOLY_VERSION,
				'footer'  => true
			) );

			wp_register_script( $id, $script['file'], $script['deps'], $script['version'], $script['footer'] );
		}
	}

	/**
	 * Enqueue a specific script.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $id Style ID.
	 * 
	 * @return   void
	 */
	private function enqueue_script( $id = '' ) {

		if ( ! empty( $id ) ) {
			$id = '-' . $id;
		}
		$id = WPMOLY_SLUG . $id;

		wp_enqueue_script( $id );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function enqueue_styles() {

		$this->register_styles();

		$this->enqueue_style();
		$this->enqueue_style( 'common' );
		$this->enqueue_style( 'headboxes' );
		$this->enqueue_style( 'grids' );
		$this->enqueue_style( 'flags' );
		$this->enqueue_style( 'font' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function enqueue_scripts() {

		$this->register_scripts();

		//$this->enqueue_style();
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
		$loader->add_filter( 'wpmoly/shortcode/format/director/value',             '', array( 'wpmoly\Helpers\Formatting', 'director' ),           15, 1 );
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
			'\wpmoly\Shortcodes\Movie',
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

	/**
	 * Display the movie Headbox along with movie content.
	 * 
	 * If we're in search or archive templates, show the default, minimal
	 * Headbox; if we're in single template, show the default full Headbox.
	 * 
	 * TODO implement other Headbox themes
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $content Post content.
	 * 
	 * @return   string
	 */
	public function the_headbox( $content ) {

		if ( 'movie' != get_post_type() ) {
			return $content;
		}

		$headbox = get_movie_headbox();
		if ( is_single() ) {
			$headbox->set( 'theme', 'default' );
		} elseif ( is_archive() || is_search() ) {
			$headbox->set( 'theme', 'default' );
		}

		return $headbox->output() . $content;
	}

}
