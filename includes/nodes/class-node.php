<?php
/**
 * Define the Node class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes;

/**
 * Define a generic Node class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Node {

	/**
	 * Node ID.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Node meta prefix.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Class Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param int|Node|WP_Post $node Node ID, node instance or post object.
	 */
	public function __construct( $node = null ) {

		if ( is_numeric( $node ) ) {
			$this->id   = absint( $node );
			$this->post = get_post( $this->id );
		} elseif ( $node instanceof Node ) {
			$this->id   = absint( $node->id );
			$this->post = $node->post;
		} elseif ( isset( $node->ID ) ) {
			$this->id   = absint( $node->ID );
			$this->post = $node;
		}

		$this->init();
	}

	/**
	 * Magic.
	 *
	 * Add support for Movie::get_{$property}() and Movie::the_{$property}()
	 * methods.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $method Method name.
	 * @param array $arguments Method arguments.
	 *
	 * @return mixed
	 */
	public function __call( $method, $arguments ) {

		if ( preg_match( '/get_the_[a-z_]+/i', $method ) ) {
			$name = str_replace( 'get_the_', '', $method );
			return $this->get_the( $name );
		} elseif ( preg_match( '/get_[a-z_]+/i', $method ) ) {
			$name = str_replace( 'get_', '', $method );
			return $this->get( $name );
		} elseif ( preg_match( '/the_[a-z_]+/i', $method ) ) {
			$name = str_replace( 'the_', '', $method );
			$this->the( $name );
		}
	}

	/**
	 * Load metadata.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param string $name Property name
	 *
	 * @return mixed
	 */
	protected function get_property( $name ) {

		// Load metadata
		$value = get_post_meta( $this->id, $this->prefix . $name, true );

		return $value;
	}

	/**
	 * Property accessor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Property name
	 * @param mixed $default Default value
	 *
	 * @return mixed
	 */
	public function get( $name, $default = null ) {

		if ( isset( $this->$name ) && ! is_null( $this->$name ) ) {
			return $this->$name;
		}

		$value = $this->get_property( $name );
		if ( false === $value ) {
			$value = $default;
		}

		return $value;
	}

	/**
	 * Set Property.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function set( $name, $value = null ) {

		if ( is_object( $name ) ) {
			$name = get_object_vars();
		}

		if ( is_array( $name ) ) {
			foreach ( $name as $key => $value ) {
				$this->set( $key, $value );
			}
			return true;
		}

		$this->$name = $value;

		return $value;
	}

	/**
	 * Simple property echoer. Use Node::get_the() to automatically escape
	 * the requested property.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Property name
	 */
	public function the( $name ) {

		echo $this->get_the( $name );
	}

}
