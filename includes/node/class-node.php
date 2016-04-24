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
 * 
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
	 * @since    3.0
	 * 
	 * @var      int
	 */
	public $id;

	/**
	 * Node data.
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	public $data = array();

	/**
	 * Node changed properties
	 * 
	 * @since    3.0
	 * 
	 * @var    array
	 */
	protected $changed = array();

	/**
	 * Node previous properties (before change)
	 * 
	 * @since    3.0
	 * 
	 * @var    array
	 */
	protected $previous = array();

	/**
	 * Node properties sanitizers
	 * 
	 * @var    array
	 */
	protected $validates = array();

	/**
	 * Node properties escapers
	 * 
	 * @var    array
	 */
	protected $escapes = array();

	/**
	 * Node default properties values
	 * 
	 * @var    array
	 */
	protected $defaults = array();

	/**
	 * Node instances.
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	public static $instances;

	/**
	 * Class Constructor.
	 * 
	 * @since    3.0
	 * 
	 * @param    object|array    $data Node object.
	 * 
	 * @return   Node
	 */
	public function __construct( $data = array() ) {

		// Run some things before actually construct anything
		$this->init();

		// Prepare data
		$class = get_called_class();
		if ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		} else if ( ! is_array( $data ) ) {
			$data = (array) $data;
		}

		// Try to set instance ID
		if ( isset( $data['id'] ) ) {
			$this->id = (int) $data['id'];
		}

		// Try to load previously set instance
		if ( ! empty( $class::$instances[ $this->id ] ) ) {
			return $class::$instances[ $this->id ];
		}

		// Populate data
		foreach ( $this->validates as $key => $null ) {
			if ( isset( $data[ $key ] ) ) {
				$this->_set( $key, $data[ $key ] );
			} elseif ( isset( $this->defaults[ $key ] ) ) {
				$this->data[ $key ] = $this->defaults[ $key ];
			}
		}

		// Set instance
		if ( $this->id ) {
			$class::$instances[ $this->id ] = &$this;
		}

		// Run some things after construction
		$this->make();

		return $this;
	}

	/**
	 * Implement generic accessors and echoers.
	 * 
	 * This allows usage of $node->$prop() and $node->get_$prop() like
	 * methods to echo or retrieve values.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $method 
	 * @param    array     $arguments 
	 * 
	 * @return   string|null
	 */
	public function __call( $method, $arguments ) {

		$get_method = str_replace( 'get_', '', $method );
		$the_method = str_replace( 'the_', '', $method );

		// Node::$method()
		if ( isset( $this->data[ $method ] ) ) {
			$this->the( $method );
		// Node::get_$method()
		} else if ( isset( $this->data[ $get_method ] ) ) {
			$this->get( $get_method );
		// Node::the_$method()
		} else if ( isset( $this->data[ $the_method ] ) ) {
			$this->the( $the_method );
		}

		return null;
	}

	/**
	 * Magic method to access class properties.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $prop Property name
	 * 
	 * @return   mixed
	 */
	public function __get( $prop ) {

		return isset( $this->$prop ) ? $this->$prop : null;
	}

	/**
	 * Get a Node instance.
	 *
	 * @since    3.0
	 * 
	 * @param    int    $node_id Node ID.
	 * 
	 * @return   Node|bool       Node object if available, false if not.
	 */
	public static function get_instance( $node_id ) {

		$node_id = (int) $node_id;
		if ( ! $node_id ) {
			return false;
		}

		// Return an existing instance
		$class = get_called_class();
		if ( isset( $class::$instances[ $node_id ] ) ) {
			return $class::$instances[ $node_id ];
		}

		// Never loaded, fallback to the actual node
		$node = $class::find( $node_id );

		return $node;
	}

	/**
	 * Prepare the data given to the constructor.
	 *
	 * @since    3.0
	 * 
	 * @param    mixed    $data Node data
	 * 
	 * @return   array
	 */
	public function prepare( $data = array() ) {

		if ( is_object( $data ) ) {
			$data = get_object_vars();
		} elseif ( is_int( $data ) ) {
			$data = array( 'id' => $data );
		} else {
			$data = (array) $data;
		}

		return $data;
	}

	/**
	 * Affect a value to an object data.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $key Property name
	 * @param    mixed     $value Property value
	 * 
	 * @return   void
	 */
	private function _set( $key, $value ) {

		// unknown property, exit
		if ( ! isset( $this->validates[ $key ] ) ) {
			return false;
		}

		$this->data[ $key ] = $this->validate( $key, $value );
	}

	/**
	 * Make sure we store properties in their expected type.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $key Property name
	 * @param    mixed     $value Property value
	 * 
	 * @return   void
	 */
	public function validate( $key, $value ) {

		if ( ! isset( $this->validates[ $key ] ) ) {
			$function = 'esc_attr';
		} else {
			$function = $this->validates[ $key ];
		}

		if ( function_exists( $function ) ) {
			if ( is_array( $value ) ) {
				array_map( $function, $value );
			} else {
				$value = $function( $value );
			}
		}

		return $value;
	}

	/**
	 * Set a property or a group of properties.
	 * 
	 * If $key is an array or an object, assume it's a set of properties and
	 * loop through the list.
	 * 
	 * If $key is a string, set the property accordingly.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $key Property name
	 * @param    mixed     $value Property value
	 * @param    array     $options Options. Unused for now.
	 * 
	 * @return   Node      Return itself to allow chaining
	 */
	public function set( $key, $value = '', $options = array() ) {

		// Setting a set of properties
		if ( is_array( $key ) || is_object( $key ) ) {

			// reset changes
			$this->changed = array();
			$this->previous = $this->data;

			$values = is_object( $key ) ? get_object_vars( $key ) : (array) $key;
			foreach ( $values as $k => $v ) {
				$k = sanitize_key( $k );
				if ( isset( $this->data[ $k ] ) && $value !== $this->data[ $k ] ) {
					$this->previous[ $k ] = $this->data[ $k ];
					$this->changed[ $k ]  = $value;
				}
				$this->_set( $k, $v );
			}

		// Setting a single property
		} else if ( is_string( $key ) ) {

			$key = sanitize_key( $key );

			// extract object vars
			if ( is_object( $value ) ) {
				$value = get_object_vars( $value );
			}
			// always use array
			$value = (array) $value;

			// reset changes
			$this->changed = array();
			$this->previous = $this->data;

			foreach ( $value as $v ) {
				if ( isset( $this->data[ $key ] ) && $value !== $this->data[ $key ] ) {
					$this->previous[ $key ] = $this->data[ $key ];
					$this->changed[ $key ]  = $v;
				}
				$this->_set( $key, $v );
			}
		}

		return $this;
	}

	/**
	 * Simple property accessor.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $prop Property name
	 * 
	 * @return   mixed
	 */
	public function get( $prop ) {

		return isset( $this->data[ $prop ] ) ? $this->data[ $prop ] : null;
	}

	/**
	 * Enhanced property accessor. Unlike Node::get() this method automatically
	 * escapes the property requested and therefore should be used when the
	 * property is meant for display.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $prop Property name
	 * 
	 * @return   void
	 */
	public function get_the( $prop ) {

		$escaper = ! empty( $this->escapers[ $prop ] ) && is_callable( $this->escapers[ $prop ] ) ? $this->escapers[ $prop ] : 'esc_attr';

		return $escaper( $this->get( $prop ) );
	}

	/**
	 * Simple property echoer. Use Node::get_the() to automatically escape
	 * the requested property.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $prop Property name
	 * 
	 * @return   void
	 */
	public function the( $prop ) {

		echo $this->get_the( $prop );
	}

	/**
	 * Has the data changed?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function has_changed() {

		return ! empty( $this->changed );
	}

	/**
	 * Is the data empty?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function is_empty() {

		$data = array_filter( $this->data );

		return empty( $this->data ) || empty( $data );
	}
}