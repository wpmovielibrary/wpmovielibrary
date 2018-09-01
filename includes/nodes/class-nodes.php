<?php

/**
 * Define the Nodes class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes;

/**
 * Manipulate lists of multiple nodes.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Nodes implements \Iterator, \SeekableIterator, \ArrayAccess {

	/**
	 * Nodes Loop status
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var boolean
	 */
	protected $looping;

	/**
	 * Nodes current position
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var int
	 */
	protected $position = -1;

	/**
	 * Nodes current Node
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var Node
	 */
	public $item;

	/**
	 * Nodes Nodes
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $items = array();

	/**
	 * Nodes Previous Node
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var Node
	 */
	protected $previous;

	/**
	 * Nodes Next Node
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var Node
	 */
	protected $next;

	/**
	 * Nodes size
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var int
	 */
	public $length;

	/**
	 * Initialize Nodes.
	 *
	 * @since 3.0.0
	 *
	 * @access public
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
	 * Add a Node to the list.
	 *
	 * If no position is specified the Node will added at the end of the list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param Node $item     Node instance.
	 * @param int  $position Node position.
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
	 * Remove a Node to the list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $key Item key.
	 */
	public function remove( $key ) {

		if ( isset( $this->items[ $key ] ) ) {
			unset( $this->items[ $key ] );
		}
	}

	/**
	 * Filter Nodes by given key-value pair.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string  $key         Item key.
	 * @param mixed   $value       Item value.
	 * @param boolean $strict      Use strict comparison.
	 * @param boolean $force_array Force result to be an array.
	 *
	 * @return array
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
	 * Run a callback function on each item of the list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $callback
	 *
	 * @return array
	 */
	public function filter( $callback = null ) {

		if ( $callback ) {
			return array_filter( $this->items, $callback );
		}

		return array_filter( $this->items );
	}

	/**
	 * Retrieve a Node from a specific position in the list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param int $position Node position in the list.
	 *
	 * @return Node|null
	 */
	public function at( $position ) {

		if ( isset( $this->items[ $position ] ) ) {
			return $this->items[ $position ];
		}

		return null;
	}

	/**
	 * Retrieve the first Node of the list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return Node|null
	 */
	public function first() {

		return $this->at( 0 );
	}

	/**
	 * Return the last item in the list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return Node|null
	 */
	public function last() {

		return $this->at( $this->count() );
	}

	/**
	 * Return a random item in the list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return Node
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
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return int
	 */
	public function key() {

		return $this->position;
	}

	/**
	 * Return current item.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return Node
	 */
	public function current() {

		return $this->item;
	}

	/**
	 * Decrement position and set current item.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return Node
	 */
	public function prev() {

		$this->position--;

		$this->item = $this->items[ $this->position ];

		return $this->item;
	}

	/**
	 * Increment position and set current item.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function next() {

		$this->position++;

		if ( ! empty( $this->items[ $this->position ] ) ) {
			$this->item = $this->items[ $this->position ];
			return $this->item;
		}

		return false;
	}

	/**
	 * Check if current position is valid, ie. if there is an item at that
	 * position.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function valid() {

		return isset( $this->items[ $this->position ] );
	}

	/**
	 * Reset position and current item.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function rewind() {

		$this->position = -1;

		$this->item = $this->first();
	}

	/**
	 * Jump to a specific position.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param int $position Item position (key).
	 *
	 * @return Node
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
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param int $key Item key.
	 *
	 * @return boolean
	 */
	public function offsetExists( $key ) {

		return isset( $this->items[ $key ] );
	}

	/**
	 * Get an item from a specific position.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param int $key Item key.
	 *
	 * @return Node|null
	 */
	public function offsetGet( $key ) {

		return $this->items[ $key ];
	}

	/**
	 * Set an item at a specific position.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param int   $key   Item key.
	 * @param mixed $value Item.
	 */
	public function offsetSet( $key, $value ) {

		$this->items[ $key ] = $value;
	}

	/**
	 * Unset an item at a specific position.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param int $key Item key.
	 */
	public function offsetUnset( $key ) {

		unset( $this->items[ $key ] );
	}

	/**
	 * Check the existence of a Node in the list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param mixed $key   Item position.
	 * @param mixed $value Item.
	 *
	 * @return boolean
	 */
	public function contains( $key, $value = null ) {

		if ( 2 == func_num_args() ) {
			return $this->contains( function ( $k, $item ) use ( $key, $value ) {
				return data_get( $item, $key ) == $value;
			});
		}

		return in_array( $key, $this->items );
	}

	/**
	 * Check if the list contains at least one item.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_empty() {

		return ! $this->count();
	}

	/**
	 * Return the number of Nodes in the list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return int
	 */
	public function count() {

		$this->length = count( $this->items );

		return $this->length;
	}

	/**
	 * Check if current item is the first of the list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_first() {

		return ! $this->key();
	}

	/**
	 * Check if current item is the last of the list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_last() {

		return $this->key() == $this->count() - 1;
	}

	/**
	 * Are we done looping?
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function has_items() {

		if ( $this->position + 1 < $this->length ) {
			return true;
		} elseif ( $this->length && $this->position + 1 == $this->length ) {
			$this->rewind();
		}

		$this->looping = false;

		return $this->looping;
	}

	/**
	 * Loop: jump to the next item and set it has the current item.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return Node
	 */
	public function the_item() {

		$this->looping = true;

		$this->next();

		return $this->item;
	}

}
