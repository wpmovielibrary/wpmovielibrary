<?php
/**
 * Define the collection class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Collection;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Collection implements \Iterator, \SeekableIterator, \ArrayAccess {

	/**
	 * Collection Current Node
	 * 
	 * @var    string
	 */
	protected $position;

	/**
	 * Collection Nodes
	 * 
	 * @var    array
	 */
	public $items = array();

	/**
	 * Collection Current Node
	 * 
	 * @var    \Node
	 */
	protected $current;

	/**
	 * Collection Previous Node
	 * 
	 * @var    \Node
	 */
	protected $previous;

	/**
	 * Collection Next Node
	 * 
	 * @var    \Node
	 */
	protected $next;

	/**
	 * Collection Node type
	 * 
	 * @var    boolean
	 */
	public $has_items;

	/**
	 * Collection size
	 * 
	 * @var    int
	 */
	public $length;

	/**
	 * Initialize Collection.
	 * 
	 * @since    3.0
	 * 
	 * @return   Images    Return itself to allow chaining
	 */
	public function __construct() {

		if ( $this->count() ) {
			$this->current = &$this->items[0];
		}

		if ( 1 < $this->count() ) {
			$this->next = &$this->items[1];
		}

		return $this;
	}

	/**
	 * Add a Node to the collection.
	 * 
	 * If no position is specified the Node will added at the end of the list.
	 * 
	 * @since    3.0
	 * 
	 * @param    \Node    $item Node instance
	 * @param    int      $position Node position
	 * 
	 * @return   void
	 */
	public function add( $item, $position = null, $append = false ) {

		if ( ! is_null( $position ) ) {
			if ( isset( $this->items[ $position ] ) ) {
				if ( false === $append ) {
					$this->items[ $position ] = $item;
				}/* else {
					$this->items[ $position ] = $item;
				}*/
			} else {
				$this->items[ $position ] = $item;
			}
		} else {
			$this->items[] = $item;
		}

		++$this->length;
	}

	/**
	 * Remove a Node to the collection.
	 * 
	 * @since    3.0
	 * 
	 * @param    \Node    $item Node instance
	 * 
	 * @return   void
	 */
	public function remove( $item ) {

		
	}

	/**
	 * Filter Nodes by given key-value pair.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $key Item key
	 * @param    mixed      $value Item value
	 * @param    boolean    $strict Use strict comparison
	 * @param    boolean    $force_array Force result to be an array
	 * 
	 * @return   array
	 */
	public function where( $key, $value, $strict = false, $force_array = false ) {

		$array = $this->filter( function( $item ) use ( $key, $value, $strict ) {
			return $strict ? $value === $item->get( $key ) : $value == $item->get( $key );
		} );

		if ( 1 == count( $array ) && false === $force_array ) {
			return array_shift( $array );
		}

		return $array;
	}

	/**
	 * Run a callback function on each item of the collection.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $callback
	 * 
	 * @return   array
	 */
	public function filter( $callback = null ) {

		if ( $callback ) {
			return array_filter( $this->items, $callback );
		}

		return array_filter( $this->items );
	}

	/**
	 * Retrieve a Node from a specific position in the collection.
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $position Node position in the list
	 * 
	 * @return   Node|null
	 */
	public function at( $position ) {

		if ( isset( $this->items[ $position ] ) ) {
			return $this->items[ $position ];
		}

		return null;
	}

	public function first() {

		return reset( $this->items );
	}

	public function last() {

		return end( $this->items );
	}

	/**
	 * Return current item.
	 * 
	 * @since    3.0
	 * 
	 * @return   Node
	 */
	public function current() {

		return $this->items[ $this->position ];
	}

	/**
	 * Decrement position.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function prev() {

		$this->position--;
	}

	/**
	 * Increment position.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function next() {

		$this->position++;
	}

	/**
	 * Get current Key.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function key() {

		return $this->position;
	}

	/**
	 * Check if current position is valid, ie. if there is an item at that
	 * position.
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function valid() {

		return isset( $this->items[ $this->position ] );
	}

	/**
	 * Reset the position to the first item.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function rewind() {

		$this->position = 0;
	}

	/**
	 * Jump to a specific position.
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $position Item position (key)
	 * 
	 * @return   Node
	 */
	public function seek( $position ) {

		$previous_position = $this->position;
		$this->position = $position;

		if ( ! $this->valid() ) {
			$this->position = $previous_position;
		}
	}

	/**
	 * Check if given key exists.
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $key Item key
	 * 
	 * @return   boolean
	 */
	public function offsetExists( $key ) {

		return isset( $this->items[ $key ] );
	}

	/**
	 * Get an item from a specific position.
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $key Item key
	 * 
	 * @return   Node|null
	 */
	public function offsetGet( $key ) {

		return $this->items[ $key ];
	}

	/**
	 * Set an item at a specific position.
	 * 
	 * @since    3.0
	 * 
	 * @param    int      $key Item key
	 * @param    mixed    $value Item
	 * 
	 * @return   void
	 */
	public function offsetSet( $key, $value ) {

		$this->items[ $key ] = $value;
	}

	/**
	 * Unset an item at a specific position.
	 * 
	 * @since    3.0
	 * 
	 * @param    int      $key Item key
	 * 
	 * @return   void
	 */
	public function offsetUnset( $key ) {

		unset( $this->items[ $key ] );
	}

	/**
	 * Check the existence of a Node in the collection.
	 * 
	 * @since    3.0
	 * 
	 * @param    mixed    $key Item position
	 * @param    mixed    $value Item
	 * 
	 * @return   boolean
	 */
	public function contains( $key, $value = null ) {

		if ( 2 == func_num_args() ) {
			return $this->contains( function ( $k, $item ) use ( $key, $value ) {
				return $value == data_get( $item, $key );
			});
		}

		return in_array( $key, $this->items );
	}

	/**
	 * Check if the collection contains at least one item.
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function has_items() {

		return (boolean) $this->count();
	}

	/**
	 * Return the number of Nodes in the collection.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function count() {

		return count( $this->items );
	}
}