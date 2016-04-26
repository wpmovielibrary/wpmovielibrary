<?php
/**
 * Define the Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Shortcodes;

/**
 * General Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Shortcode {

	/**
	 * Shortcode Tag
	 * 
	 * @var    string
	 */
	public static $name;

	/**
	 * Shortcode attributes
	 * 
	 * @var    string
	 */
	protected $attributes;

	/**
	 * Shortcode attributes sanitizers
	 * 
	 * @var    array
	 */
	protected $validates = array();

	/**
	 * Shortcode attributes escapers
	 * 
	 * @var    array
	 */
	protected $escapes = array();

	/**
	 * Shortcode template
	 * 
	 * @var    string
	 */
	protected $template;

	/**
	 * Shortcode content
	 * 
	 * @var    string
	 */
	protected $content;

	/**
	 * Class constructor
	 * 
	 * @since    3.0
	 * 
	 * @param    array     $atts Shortcode parameters
	 * @param    string    $content Shortcode content
	 * 
	 * @return   Shortcode
	 */
	public function __construct( $atts = array(), $content = null ) {

		// Run some things before actually construct anything
		$this->init();

		// Set content
		$this->content = $content;
		$this->set_attributes( $atts );

		// Run some things after construction
		$this->make();

		return $this;
	}

	/**
	 * Set the Shortcode's attributes.
	 * 
	 * Attributes not found in the validate list are simply ignored.
	 * 
	 * @since    3.0
	 * 
	 * @param    array     $attributes Shortcode attributes
	 * 
	 * @return   Node      Return itself to allow chaining
	 */
	protected function set_attributes( $attributes ) {

		foreach ( $this->validates as $key => $null ) {
			if ( isset( $attributes[ $key ] ) ) {
				$this->set( $key, $attributes[ $key ] );
			} else {
				$this->attributes[ $key ] = $this->validates[ $key ]['default'];
			}
		}

		return $this;
	}

	/**
	 * Make sure we store attributes in their expected type.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $key Attribute name
	 * @param    mixed     $value Attribute value
	 * 
	 * @return   void
	 */
	private function validate( $key, $value ) {

		if ( ! isset( $this->validates[ $key ] ) ) {
			$function = 'esc_attr';
			$values   = null;
		} else {
			$function = $this->validates[ $key ]['filter'];
			$values   = $this->validates[ $key ]['values'];
		}

		if ( ! is_null( $values ) && ! in_array( $value, $values ) ) {
			return false;
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
	 * Set a specific attribute.
	 * 
	 * Attributes not present in the validate list are simply ignored. If
	 * the attribute value doesn't match the allowed values set in the
	 * validate list, default value is used.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $key Attribute name
	 * @param    mixed     $value Attribute value
	 * 
	 * @return   void
	 */
	private function set( $key, $value ) {

		// unknown attribute, exit
		if ( ! isset( $this->validates[ $key ] ) ) {
			return false;
		}

		// Validate the attribute
		$value = $this->validate( $key, $value );

		$this->attributes[ $key ] = $value ? $value : $this->validates[ $key ]['default'];
	}

	/**
	 * Run the Shortcode.
	 * 
	 * Create a new instance of Shortcode, run the Shortcode and build the
	 * Template for return.
	 * 
	 * @since    3.0
	 * 
	 * @param    array     $atts Shortcode parameters
	 * @param    string    $content Shortcode content
	 * 
	 * @return   string
	 */
	public static function shortcode( $atts = array(), $content = null ) {

		$shortcode = new static( $atts, $content );
		$shortcode->run();

		return $shortcode->output();
	}

	/**
	 * Output the Shortcode.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function output() {

		return $this->template->render( 'once', $echo = false );
	}

	/**
	 * Initialize the Shortcode.
	 * 
	 * Run things before doing anything.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	abstract protected function init();

	/**
	 * Build the Shortcode.
	 * 
	 * Prepare Shortcode parameters.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	abstract protected function make();

	/**
	 * Run the Shortcode.
	 * 
	 * Perform all needed Shortcode stuff.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	abstract protected function run();
}
