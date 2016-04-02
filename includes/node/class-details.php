<?php
/**
 * Define the details class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Node;

use wpmoly\Node\Meta;

/**
 * Details are very similar to Meta, just extend that class with a different
 * init to set the details validators, escapers and default values.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Details extends Meta {

	/**
	 * Initialize the Node.
	 * 
	 * Set the validators and defaults values if available.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function init() {

		$allowed = wpmoly_o( 'default_details' );
		foreach ( $allowed as $slug => $detail ) {
			$this->validates[ $slug ] = ! empty( $detail['validate'] ) ? $detail['validate'] : 'esc_attr';
			$this->escapes[ $slug ]   = ! empty( $detail['escape'] )   ? $detail['escape']   : 'esc_attr';
			$this->defaults[ $slug ]  = ! empty( $detail['defaults'] ) ? $detail['defaults'] : '';
		}
	}
}