<?php
/**
 * Define the Template classes.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\templates;

use WP_Error;

/**
 * General Template class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
abstract class Template {

	/**
	 * Template path.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Template data.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Template content.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $template = '';

	/**
	 * Set the Template data.
	 *
	 * If $name is an array or an object, use it as a set of data; if not,
	 * use $name and $value as key and value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param mixed $name Data name.
	 * @param mixed $value Data value.
	 *
	 * @return Template
	 */
	public function set_data( $name, $value = '' ) {

		if ( is_object( $name ) ) {
			$name = get_object_vars( $name );
		}

		if ( is_array( $name ) ) {
			foreach ( $name as $key => $data ) {
				$this->set_data( $key, $data );
			}
		} else {
			$this->data[ (string) $name ] = $value;
		}

		return $this;
	}

	/**
	 * Add SVG Icons support.
	 *
	 * Detect every occurrences of {{ 'svg:icon:icon-name' }} markers and replace
	 * with the corresponding registered SVG, if any.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function iconify() {

		// Don't waste ressources if no icon is requested.
		if ( false === stripos( $this->template, 'svg:icon:' ) ) {
			return false;
		}

		$template = preg_replace_callback( "/{{\ ?'svg:icon:([a-z\-]+)'\ ?}}/", array( &$this, 'svgify' ), $this->template );

		if ( ! is_null( $template ) ) {
			$this->template = $template;
		}
	}

	/**
	 * Generate an inline SVG element.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $icon Icon slug.
	 *
	 * @return string
	 */
	public function svgify( $icon ) {

		if ( empty( $icon ) || ! is_array( $icon ) || 2 > count( $icon ) ) {
			return null;
		}

		$icons = get_template_default_icons();

		$icon = array_pop( $icon );
		if ( empty( $icons[ $icon ] ) || empty( $icons[ $icon ]['paths'] ) ) {
			return null;
		}

		$fill = '#000';
		if ( ! empty( $icons[ $icon ]['fill'] ) ) {
			$fill = $icons[ $icon ]['fill'];
		}

		$paths = $icons[ $icon ]['paths'];
		if ( is_string( $paths ) ) {
			$paths = (array) $paths;
		}

		$svg  = '<svg version="1.1" x="0px" y="0px" width="20" height="20" viewBox="0 0 20 20" class="svg-icon">';
		foreach ( $paths as $path ) {
			if ( ! empty( $path['path'] ) ) {
				$p = $path['path'];
				if ( ! empty( $path['fill'] ) ) {
					$f = $path['fill'];
				} else {
					$f = $fill;
				}
			} else {
				$p = (string) $path;
				$f = (string) $fill;
			}
			$svg .= '<path class="svg-icon-path" d="' . $p . '" fill="' . $f . '" />';
		}
		$svg .= '</svg>';

		/**
		 * Filter rendered SVG icon.
		 *
		 * @since 3.0.0
		 *
		 * @param string $svg  SVG content.
		 * @param string $icon Icon slug.
		 * @param array  $data Icon data.
		 */
		return apply_filters( "wpmoly/filter/template/{$icon}/icon", $svg, $icon, $icons[ $icon ] );
	}

	/**
	 * Prepare the Template.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param string $require 'once' to use require_once(), 'always' to use require()
	 *
	 * @return string
	 */
	abstract protected function prepare( $require = 'once' );

	/**
	 * Render the Template.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $require Use 'once' to use require_once(), 'always' to use require()
	 * @param boolean $echo Use true to display, false to return
	 *
	 * @return null
	 */
	public function render( $require = 'once', $echo = true ) {

		$this->prepare( $require );

		$this->iconify();

		if ( true !== $echo ) {
			return $this->template;
		}

		echo $this->template;
	}

}
