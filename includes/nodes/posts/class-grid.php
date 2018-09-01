<?php
/**
 * Define the grid class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\posts;

use wpmoly\nodes\Node;
use wpmoly\helpers;

/**
 * Handle grids.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 * @author Charlie Merland <charlie@caercam.org>
 */
class Grid extends Node {

	/**
	 * Grid type.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Grid mode.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $mode;

	/**
	 * Grid theme.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $theme;

	/**
	 * Grid preset.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $preset;

	/**
	 * Custom settings.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Supported Grid types.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $supported_types = array();

	/**
	 * Supported Grid modes.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $supported_modes = array();

	/**
	 * Supported Grid themes.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $supported_themes = array();

	/**
	 * Grid Widget.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var boolean
	 */
	public $is_widget = false;

	/**
	 * Main grid status.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var boolean
	 */
	public $is_main_grid = false;

	/**
	 * Initialize the Grid.
	 *
	 * Grid query args can be overriden by passing a custom preset
	 * value through URL parameters.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function init() {

		$registered = helpers\get_registered_grid_meta();
		foreach ( $registered as $name => $args ) {
			$value = get_grid_meta( $this->id, $name );
			if ( empty( $value ) && ! empty( $args['default'] ) ) {
				$value = $args['default'];
			}

			$this->$name = $value;
		}

		$preset = get_query_var( 'preset' );
		if ( empty( $preset ) ) {
			return false;
		}

		$meta_key = prefix_movie_meta_key( $preset );
		$meta_value = get_query_var( $meta_key );
		if ( empty( $meta_value ) ) {
			return false;
		}

		$this->set_preset( array(
			$preset => $meta_value,
		) );
	}

	/**
	 * Retrieve current grid preset.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_preset() {

		/**
		 * Filter grid default preset.
		 *
		 * @since 3.0.0
		 *
		 * @param string $default_preset
		 */
		$default_preset = apply_filters( 'wpmoly/filter/default/' . $this->get_type() . '/grid/preset', 'default_preset' );

		if ( empty( $this->preset ) ) {
			$preset = $this->get( 'preset' );
			if ( empty( $preset ) ) {
				$preset = $default_preset;
			}
			$this->preset = $preset;
		}

		return $this->preset;
	}

	/**
	 * Set grid preset.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $preset New preset.
	 *
	 * @return string
	 */
	public function set_preset( $preset ) {

		$this->preset = $preset;

		return $this->preset;
	}

	/**
	 * Retrieve current grid type.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_type() {

		/**
		 * Filter grid default type.
		 *
		 * @since 3.0.0
		 *
		 * @param string $default_type
		 */
		$default_type = apply_filters( 'wpmoly/filter/grid/default/type', 'movie' );

		if ( is_null( $this->type ) ) {
			$this->type = $this->get( 'type', $default_type );
		}

		return $this->type;
	}

	/**
	 * Set grid type.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public function set_type( $type ) {

		$this->type = $type;

		return $type;
	}

	/**
	 * Retrieve current grid mode.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_mode() {

		if ( is_null( $this->mode ) ) {
			$this->mode = $this->get( 'mode', 'grid' );
		}

		return $this->mode;
	}

	/**
	 * Set grid mode.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $mode
	 *
	 * @return string
	 */
	public function set_mode( $mode ) {

		$this->mode = $mode;

		return $mode;
	}

	/**
	 * Retrieve current grid theme.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_theme() {

		if ( is_null( $this->theme ) ) {
			$this->theme = $this->get( 'theme', 'default' );
		}

		return $this->theme;
	}

	/**
	 * Set grid theme.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $theme
	 *
	 * @return string
	 */
	public function set_theme( $theme ) {

		$this->theme = $theme;

		return $theme;
	}

	/**
	 * Is this a posts grid?
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
	 * Is this a terms grid?
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

	/**
	 * Is this a grid inside a Widget?
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_widget() {

		return true === $this->is_widget;
	}

}
