<?php
/**
 * Define the Headbox class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\headboxes;

use wpmoly\nodes\Node;

/**
 * General Headbox class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 * @author Charlie Merland <charlie@caercam.org>
 */
abstract class Headbox extends Node {

	/**
	 * Headbox type.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Headbox theme.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $theme;

	/**
	 * Supported Headbox types.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $supported_types = array();

	/**
	 * Supported Headbox themes.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $supported_themes = array();

	/**
	 * Initialize the Headbox.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	abstract public function init();

	/**
	 * Build the Headbox.
	 *
	 * Load items depending on presets or custom settings.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	abstract public function build();

	/**
	 * Retrieve supported headbox types.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_supported_types() {

		return $this->supported_types;
	}

	/**
	 * Retrieve supported headbox themes.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_supported_themes() {

		return $this->supported_themes;
	}

	/**
	 * Retrieve current headbox type.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	abstract public function get_type();

	/**
	 * Set headbox type.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	abstract public function set_type( $type );

	/**
	 * Retrieve current headbox theme.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	abstract public function get_theme();

	/**
	 * Set headbox theme.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $theme
	 *
	 * @return string
	 */
	abstract public function set_theme( $theme );

	/**
	 * Is this a posts headbox?
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_post() {

		return post_type_exists( $this->get_type() );
	}

	/**
	 * Is this a terms headbox?
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_taxonomy() {

		return taxonomy_exists( $this->get_type() );
	}

}
