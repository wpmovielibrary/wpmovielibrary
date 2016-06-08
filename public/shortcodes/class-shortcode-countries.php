<?php
/**
 * Define the Local Release Date Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcode
 */

namespace wpmoly\Shortcodes;

use wpmoly\Core\PublicTemplate as Template;

/**
 * Local Release Date Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcode
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Countries extends Metadata {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 * 
	 * @var    string
	 */
	public static $name = 'movie_countries';

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
		'format' => array(
			'default' => '',
			'values'  => null,
			'filter'  => 'esc_attr'
		)
	);

	/**
	 * Shortcode aliases
	 * 
	 * @var    array
	 */
	protected static $aliases = array(
		'movie_country' => 'production_countries'
	);

	/**
	 * Build the Shortcode.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function make() {

		parent::make();

		// Hard set key
		$this->attributes['key'] = 'production_countries';
	}
}
