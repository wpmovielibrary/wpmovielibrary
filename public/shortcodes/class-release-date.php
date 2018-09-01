<?php
/**
 * Define the Release Date Shortcode class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\shortcodes;

// use wpmoly\templates\Front as Template;

/**
 * Release Date Shortcode class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Release_Date extends Metadata {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 *
	 * @var string
	 */
	public static $name = 'movie_release_date';

	/**
	 * Shortcode attributes sanitizers
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
		'format' => array(
			'default' => '',
			'values'  => null,
			'filter'  => 'esc_attr',
		),
	);

	/**
	 * Shortcode aliases
	 *
	 * @var array
	 */
	protected static $aliases = array();

	/**
	 * Build the Shortcode.
	 *
	 * @since 3.0.0
	 */
	protected function make() {

		parent::make();

		// Hard set key
		$this->attributes['key'] = 'release_date';
	}
}
