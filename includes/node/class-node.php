<?php
/**
 * Define the Node class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Node;

/**
 * Define a generic Node class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Node {

	/**
	 * Node ID.
	 * 
	 * @var      int
	 */
	public $id;

	/**
	 * Node Post object
	 * 
	 * @var    WP_Post
	 */
	public $post;

	/**
	 * Node meta suffix.
	 * 
	 * @var    string
	 */
	private $suffix;

	/**
	 * Class Constructor.
	 * 
	 * @since    3.0
	 *
	 * @param    int|Node|WP_Post    $node Node ID, node instance or post object
	 */
	public function __construct( $node = null ) {

		if ( is_numeric( $node ) ) {
			$this->id   = absint( $node );
			$this->post = get_post( $this->id );
		} elseif ( $node instanceof Movie ) {
			$this->id   = absint( $node->id );
			$this->post = $node->post;
		} elseif ( isset( $node->ID ) ) {
			$this->id   = absint( $node->ID );
			$this->post = $node;
		}

		$this->init();
	}

	/**
	 * __isset().
	 * 
	 * @since    3.0
	 * 
	 * @param    mixed    $name
	 * 
	 * @return   boolean
	 */
	public function __isset( $name ) {

		return metadata_exists( 'post', $this->id, $this->suffix . $name );
	}

	/**
	 * __get().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name
	 * 
	 * @return   mixed
	 */
	public function __get( $name ) {

		if ( 'suffix' == $name ) {
			return $this->suffix;
		}

		// Load metadata
		$value = get_post_meta( $this->id, $this->suffix . $name, $single = true );

		// Validate before setting
		$value = $this->__validate( $name, $value );
		if ( false !== $value ) {
			$this->$name = $value;
		}

		return $value;
	}

	/**
	 * __set().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name
	 * @param    mixed     $value
	 * 
	 * @return   mixed
	 */
	public function __set( $name, $value ) {

		if ( is_object( $name ) ) {
			$name = get_object_vars();
		}

		if ( is_array( $name ) ) {
			foreach ( $name as $key => $value ) {
				$this->__set( $key, $value );
			}
			return true;
		}

		// Validate before setting
		$value = $this->__validate( $name, $value );

		if ( ! isset( $this->$name ) ) {
			return $this->$name = $value;
		}

		return $this->$name = $value;
	}

	/**
	 * Validate properties before setting if need be.
	 * 
	 * Node child classes can define their own validate_$name methods to
	 * check on the values passed.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name
	 * @param    mixed     $value
	 * 
	 * @return   mixed
	 */
	private function __validate( $name, $value ) {

		$method_name = "validate_$name";
		if ( method_exists( $this, $method_name ) ) {
			$value = $this->$method_name( $value );
		}

		return $value;
	}

	/**
	 * Property set.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name Property name
	 * @param    mixed     $value Property value
	 * 
	 * @return   mixed
	 */
	public function set( $name, $value = null ) {

		return $this->__set( $name, $value );
	}

	/**
	 * Property accessor.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name Property name
	 * @param    mixed     $default Default value
	 * 
	 * @return   mixed
	 */
	public function get( $name, $default = null ) {

		return false !== $this->__get( $name ) ? $this->$name : $default;
	}
}