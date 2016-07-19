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

namespace wpmoly\Collections;

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
	 * Collection Loop status
	 * 
	 * @var    boolean
	 */
	protected $looping;

	/**
	 * Collection current position
	 * 
	 * @var    int
	 */
	protected $position = -1;

	/**
	 * Collection current Node
	 * 
	 * @var    Node
	 */
	public $item;

	/**
	 * Collection Nodes
	 * 
	 * @var    array
	 */
	public $items = array();

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
				}
			} else {
				$this->items[ $position ] = $item;
			}
		} else {
			$this->items[] = $item;
			++$this->length;
		}
	}

	/**
	 * Remove a Node to the collection.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $key Item key
	 * 
	 * @return   void
	 */
	public function remove( $key ) {

		if ( isset( $this->items[ $key ] ) ) {
			unset( $this->items[ $key ] );
		}
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
	public function where( $key, $value, $strict = false, $force_array = true ) {

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

	/**
	 * Retrieve the first Node of the collection.
	 * 
	 * @since    3.0
	 * 
	 * @return   Node|null
	 */
	public function first() {

		return $this->at(0);
	}

	/**
	 * Return the last item in the collection.
	 * 
	 * @since    3.0
	 * 
	 * @return   Node|null
	 */
	public function last() {

		return $this->at( $this->count() );
	}

	/**
	 * Return a random item in the collection.
	 * 
	 * @since    3.0
	 * 
	 * @return   Node
	 */
	public function random() {

		if ( 1 == $this->count() ) {
			return $this->first();
		}

		$position = rand( 0, $this->count() - 1 );

		return $this->at( $position );
	}

	/**
	 * Get current Key.
	 * 
	 * @since    0.1
	 * 
	 * @return   int
	 */
	public function key() {

		return $this->position;
	}

	/**
	 * Return current item.
	 * 
	 * @since    3.0
	 * 
	 * @return   Node
	 */
	public function current() {

		return $this->item;
	}

	/**
	 * Decrement position and set current item.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function prev() {

		$this->position--;

		return $this->item = $this->items[ $this->position ];
	}

	/**
	 * Increment position and set current item.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function next() {

		$this->position++;

		return $this->item = $this->items[ $this->position ];
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
	 * Reset position and current item.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function rewind() {

		$this->position = -1;

		$this->item = $this->first();
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
	 * @since    0.1
	 * 
	 * @return   boolean
	 */
	public function is_empty() {

		return ! $this->count();
	}

	/**
	 * Return the number of Nodes in the collection.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function count() {

		return $this->length = count( $this->items );
	}

	/**
	 * Check if current item is the first of the collection.
	 * 
	 * @since    0.1
	 * 
	 * @return   boolean
	 */
	public function is_first() {

		return ! $this->key();
	}

	/**
	 * Check if current item is the last of the collection.
	 * 
	 * @since    0.1
	 * 
	 * @return   boolean
	 */
	public function is_last() {

		return $this->key() == $this->count() - 1;
	}

	/**
	 * Are we done looping?
	 * 
	 * @since    0.1
	 * 
	 * @return   boolean
	 */
	public function has_items() {

		if ( $this->position + 1 < $this->length ) {
			return true;
		} elseif ( $this->length && $this->position + 1 == $this->length ) {
			$this->rewind();
		}

		return $this->looping = false;
	}

	/**
	 * Loop: jump to the next item and set it has the current item.
	 * 
	 * @since    0.1
	 * 
	 * @return   boolean
	 */
	public function the_item() {

		$this->looping = true;

		$this->next();

		return $this->item;
	}

}
