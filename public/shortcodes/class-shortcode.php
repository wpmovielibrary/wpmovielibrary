<?php
/**
 * Define the Shortcode class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\shortcodes;

/**
 * General Shortcode class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
abstract class Shortcode {

	/**
	 * Shortcode Tag.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var string
	 */
	public static $name;

	/**
	 * Shortcode real Tag, used for aliases.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $tag;

	/**
	 * Shortcode attributes.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $attributes;

	/**
	 * Shortcode aliases.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access protected
	 *
	 * @var array
	 */
	protected static $aliases = array();

	/**
	 * Shortcode attributes sanitizers.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $validates = array();

	/**
	 * Shortcode attributes escapers.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $escapes = array();

	/**
	 * Shortcode template.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $template;

	/**
	 * Shortcode content.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * Class constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $atts Shortcode parameters
	 * @param string $content Shortcode content
	 * @param string $tag Shortcode tag
	 *
	 * @return Shortcode
	 */
	public function __construct( $atts = array(), $content = null, $tag = null ) {

		// Run some things before actually construct anything
		$this->init();

		// Set tag
		$this->tag = (string) $tag;

		// Set content
		$this->content = (string) $content;
		$this->set_attributes( (array) $atts );

		// Run some things after construction
		$this->make();

		return $this;
	}

	/**
	 * Set the Shortcode's attributes.
	 *
	 * Attributes not found in the validate list are simply ignored.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param array $attributes Shortcode attributes
	 *
	 * @return Node      Return itself to allow chaining
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
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $key Attribute name
	 * @param mixed $value Attribute value
	 *
	 * @return mixed
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
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $key Attribute name
	 * @param mixed $value Attribute value
	 */
	public function set( $key, $value ) {

		// unknown attribute, exit
		if ( ! isset( $this->validates[ $key ] ) ) {
			return false;
		}

		// Validate the attribute
		$value = $this->validate( $key, $value );

		$this->attributes[ $key ] = $value ? $value : $this->validates[ $key ]['default'];
	}

	/**
	 * Register the Shortcode.
	 *
	 * Add hook for the current Shortcode and its optional aliases.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public static function register() {

		// Get Shortcode Class name
		$class = get_called_class();

		// Register main Shortcode
		add_shortcode( $class::$name, array( $class, 'shortcode' ) );

		// Register aliases
		if ( ! empty( $class::$aliases ) ) {
			$aliases = array_keys( $class::$aliases );
			foreach ( $aliases as $alias ) {
				add_shortcode( $alias, array( $class, 'shortcode' ) );
			}
		}
	}

	/**
	 * Run the Shortcode.
	 *
	 * Create a new instance of Shortcode, run the Shortcode and build the
	 * Template for return.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $atts Shortcode parameters
	 * @param string $content Shortcode content
	 * @param string $tag Shortcode tag
	 *
	 * @return string
	 */
	public static function shortcode( $atts = array(), $content = null, $tag = null ) {

		$shortcode = new static( $atts, $content, $tag );
		$shortcode->run();

		return $shortcode->output();
	}

	/**
	 * Output the Shortcode.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function output() {

		return $this->template->render( 'always', false );
	}

	/**
	 * Initialize the Shortcode.
	 *
	 * Run things before doing anything.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	abstract protected function init();

	/**
	 * Build the Shortcode.
	 *
	 * Prepare Shortcode parameters.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	abstract protected function make();

	/**
	 * Run the Shortcode.
	 *
	 * Perform all needed Shortcode stuff.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	abstract protected function run();
}
