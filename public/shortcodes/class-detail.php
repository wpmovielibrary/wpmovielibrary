<?php
/**
 * Define the Detail Shortcode class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\shortcodes;

use wpmoly\templates\Front as Template;
use wpmoly\helpers;

/**
 * Detail Shortcode class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Detail extends Metadata {

	/**
	 * Shortcode name, used for declaring the Shortcode.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @var string
	 */
	public static $name = 'movie_detail';

	/**
	 * Shortcode attributes sanitizers.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $validates = array(
		'id' => array(
			'default' => false,
			'values'  => null,
			'filter'  => 'intval',
		),
		'title' => array(
			'default' => null,
			'values'  => null,
			'filter'  => 'esc_attr',
		),
		'label' => array(
			'default' => true,
			'values'  => null,
			'filter'  => 'boolval',
		),
		'key' => array(
			'default' => false,
			'values'  => null,
			'filter'  => 'esc_attr',
		),
		'format' => array(
			'default' => 'display',
			'values'  => null,
			'filter'  => 'esc_attr',
		),
		'icon' => array(
			'default' => true,
			'values'  => null,
			'filter'  => '_is_boolval',
		),
		'text' => array(
			'default' => true,
			'values'  => null,
			'filter'  => '_is_boolval',
		),
	);

	/**
	 * Shortcode aliases.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access protected
	 *
	 * @var array
	 */
	protected static $aliases = array(
		'my_movie_rating'    => 'rating',
		'my_movie_status'    => 'status',
		'my_movie_media'     => 'media',
		'my_movie_language'  => 'language',
		'my_movie_format'    => 'format',
		'my_movie_subtitles' => 'subtitles',
	);

	/**
	 * Build the Shortcode.
	 *
	 * Prepare Shortcode parameters.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
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
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @return mixed
	 */
	protected function get_detail_value() {

		$key    = $this->attributes['key'];
		$format = isset( $this->attributes['format'] ) ? $this->attributes['format'] : 'raw';

		// Get Movie ID
		$post_id = $this->get_movie_id();

		// Get value
		$value = get_movie_meta( $post_id, $key, true );
		if ( empty( $value ) ) {
			/**
			 * Filter empty detail value.
			 *
			 * @since 3.0.0
			 *
			 * @param string $value
			 * @param string $format
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
			 * @since 3.0.0
			 *
			 * @param string $value
			 */
			return apply_filters( "wpmoly/shortcode/format/{$key}/raw/value", $value );
		}

		$options = array(
			'show_icon' => _is_bool( $this->attributes['icon'] ),
			'show_text' => _is_bool( $this->attributes['text'] ),
		);

		/**
		 * Filter detail value.
		 *
		 * @since 3.0.0
		 *
		 * @param string $value
		 * @param string $format
		 */
		return apply_filters( "wpmoly/shortcode/format/{$key}/value", $value, $options );
	}

	/**
	 * Run the Shortcode.
	 *
	 * Perform all needed Shortcode stuff.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return Shortcode
	 */
	public function run() {

		// Get value
		$detail = $this->get_detail_value();
		$text   = $this->attributes['text'];
		$icon   = $this->attributes['icon'];
		$key    = $this->attributes['key'];

		// Get label
		$metadata = helpers\get_registered_movie_meta( $key );
		if ( ! empty( $metadata['description'] ) ) {
			$label = $metadata['description'];
		}

		// Set template data
		$this->template->set_data( compact( 'detail', 'label', 'text', 'icon', 'key' ) );

		return $this;
	}

	/**
	 * Initialize the Shortcode.
	 *
	 * Run things before doing anything.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function init() {}
}
