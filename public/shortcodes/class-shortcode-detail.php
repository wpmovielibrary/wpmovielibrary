<?php
/**
 * Define the Detail Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
 */

namespace wpmoly\Shortcodes;

use wpmoly\Core\PublicTemplate as Template;

/**
 * Detail Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Detail extends Metadata {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 * 
	 * @var    string
	 */
	public static $name = 'movie_detail';

	/**
	 * Shortcode attributes sanitizers
	 * 
	 * @var    array
	 */
	protected $validates = array(
		'id' => array(
			'default' => false,
			'values'  => null,
			'filter'  => 'intval'
		),
		'title' => array(
			'default' => null,
			'values'  => null,
			'filter'  => 'esc_attr'
		),
		'label' => array(
			'default' => true,
			'values'  => null,
			'filter'  => 'boolval'
		),
		'key' => array(
			'default' => false,
			'values'  => null,
			'filter'  => 'esc_attr'
		),
		'format' => array(
			'default' => 'display',
			'values'  => null,
			'filter'  => 'esc_attr'
		),
		'icon' => array(
			'default' => true,
			'values'  => null,
			'filter'  => '_is_boolval'
		),
		'text' => array(
			'default' => true,
			'values'  => null,
			'filter'  => '_is_boolval'
		)
	);

	/**
	 * Shortcode aliases
	 * 
	 * @var    array
	 */
	protected static $aliases = array(
		'my_movie_rating'    => 'rating',
		'my_movie_status'    => 'status',
		'my_movie_media'     => 'media',
		'my_movie_language'  => 'language',
		'my_movie_format'    => 'format',
		'my_movie_subtitles' => 'subtitles'
	);

	/**
	 * Build the Shortcode.
	 * 
	 * Prepare Shortcode parameters.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function make() {

		if ( ! is_null( $this->tag ) && isset( self::$aliases[ $this->tag ] ) ) {
			$this->set( 'key', self::$aliases[ $this->tag ] );
		}

		// Set Template
		if ( false !== $this->attributes['label'] ) {
			$template = 'shortcodes/detail-label.php';
		} else {
			$template = 'shortcodes/detail.php';
		}

		$this->template = new Template( $template );
	}

	/**
	 * Get the detail value.
	 * 
	 * @since    3.0
	 * 
	 * @return   mixed
	 */
	protected function get_detail_value() {

		$key    = $this->attributes['key'];
		$format = isset( $this->attributes['format'] ) ? $this->attributes['format'] : 'raw';

		// Get Movie ID
		$post_id = $this->get_movie_id();

		// Get value
		$value = get_movie_meta( $post_id, $key, $single = true );
		if ( empty( $value ) ) {
			/**
			 * Filter empty detail value.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $value
			 * @param    string    $format
			 */
			$value = apply_filters( "wpmoly/shortcode/format/{$key}/empty/value", $value, $format );
			if ( empty( $value ) ) {
				/** This filter is documented in includes/helpers/formatting.php **/
				$value = apply_filters( 'wpmoly/filter/detail/empty/value', '&mdash;' );
			}

			return $value;
		}

		// Raw value requested
		if ( 'raw' == $format ) {
			/**
			 * Filter raw detail value.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $value
			 */
			return apply_filters( "wpmoly/shortcode/format/{$key}/raw/value", $value );
		}

		/**
		 * Filter detail value.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $value
		 * @param    string    $format
		 */
		return apply_filters( "wpmoly/shortcode/format/{$key}/value", $value, $this->attributes['text'], $this->attributes['icon'] );
	}

	/**
	 * Run the Shortcode.
	 * 
	 * Perform all needed Shortcode stuff.
	 * 
	 * @since    3.0
	 * 
	 * @return   Shortcode
	 */
	public function run() {

		// Get value
		$detail = $this->get_detail_value();
		$text   = $this->attributes['text'];
		$icon   = $this->attributes['icon'];
		$key    = $this->attributes['key'];

		// Get label
		$label = wpmoly_o( 'default_details' );
		$label = isset( $label[ $key ]['title'] ) ? $label[ $key ]['title'] : '';

		// Set template data
		$this->template->set_data( compact( 'detail', 'label', 'text', 'icon', 'key' ) );

		return $this;
	}

	/**
	 * Initialize the Shortcode.
	 * 
	 * Run things before doing anything.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function init() {}
}
