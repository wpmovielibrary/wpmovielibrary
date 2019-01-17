<?php
/**
 * Define the Headbox Template classes.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\templates;

use WP_Error;
use wpmoly\utils;

/**
 * Headbox Template class.
 *
 * This class acts as a controller for headbox templates, determining which template
 * file to use and preseting data for it.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Headbox extends Front {

	/**
	 * Headbox instance.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var Headbox
	 */
	private $headbox;

	/**
	 * Class Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param mixed $headbox Headbox instance or ID.
	 *
	 * @return Template|WP_Error
	 */
	public function __construct( $headbox ) {

		if ( is_int( $headbox ) ) {
			$headbox = utils\get_headbox( $headbox );
			if ( empty( $headbox->post ) || empty( $headbox->term ) ) {
				return null;
			}
			$this->headbox = $headbox;
		} elseif ( is_object( $headbox ) ) {
			$this->headbox = $headbox;
		} else {
			return null;
		}

		$this->set_path();

		return $this;
	}

	/**
	 * __call().
	 *
	 * Allows access to $this->headbox methods through this class.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __call( $method, $arguments ) {

		if ( method_exists( $this->headbox, $method ) ) {
			return call_user_func_array( array( $this->headbox, $method ), $arguments );
		}

		return null;
	}

	/**
	 * __get().
	 *
	 * Allows access to $this->headbox properties through this class.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get( $name ) {

		if ( ! isset( $this->$name ) ) {
			$method = "get_$name";
			if ( method_exists( $this->headbox, $method ) ) {
				return $this->headbox->$method();
			} elseif ( $this->headbox->get( $name ) ) {
				return $this->headbox->get( $name );
			}
		}

		return null;
	}

	/**
	 * Determine the headbox template path based on the headbox's type and mode.
	 *
	 * @TODO make use of that WP_Error.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @return string
	 */
	private function set_path() {

		$path = 'public/templates/headboxes/' . $this->headbox->get_type() . '-' . $this->headbox->get_theme() . '.php';
		if ( ! file_exists( WPMOLY_PATH . $path ) ) {
			return new WP_Error( 'missing_template_path', sprintf( __( 'Error: "%s" does not exists.', 'wpmovielibrary' ), esc_attr( WPMOLY_PATH . $path ) ) );
		}

		$this->path = $path;

		return $path;
	}

	/**
	 * Render the Template.
	 *
	 * Default parameters are the opposite of Template::render(): always
	 * require and never echo.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $require Use 'once' to use require_once(), 'always' to use require()
	 * @param boolean $echo Use true to display, false to return
	 *
	 * @return string
	 */
	public function render( $require = 'always', $echo = false ) {

		if ( empty( $this->data ) ) {

			$data = array(
				'headbox' => $this,
			);

			// include current type Node
			$types = array_keys( $this->headbox->get_supported_types() );
			foreach ( $types as $type ) {
				if ( isset( $this->headbox->$type ) ) {
					$data[ $type ] = $this->headbox->$type;
				}
			}

			$this->set_data( $data );
		}

		$this->prepare( $require );

		$this->iconify();

		if ( true !== $echo ) {
			return $this->template;
		}

		echo $this->template;
	}

}
