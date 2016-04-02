<?php
/**
 * Define the Metabox class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Metabox;

/**
 * Create a set of metaboxes for the plugin to display data in a nicer way
 * than standard WP Metaboxes.
 * 
 * Also handle the Post Convertor Metabox, if needed.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Metabox {

	/**
	 * Metabox ID
	 * 
	 * @since    3.0
	 * 
	 * @var      string
	 */
	public $id;

	/**
	 * Metabox Title
	 * 
	 * @since    3.0
	 * 
	 * @var      string
	 */
	public $title;

	/**
	 * Metabox Callback
	 * 
	 * @since    3.0
	 * 
	 * @var      callback
	 */
	public $callback;

	/**
	 * Metabox Callback parameters
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	public $callback_args;

	/**
	 * Metabox Screen
	 * 
	 * @since    3.0
	 * 
	 * @var      string
	 */
	public $screen;

	/**
	 * Metabox Context
	 * 
	 * @since    3.0
	 * 
	 * @var      string
	 */
	public $context;

	/**
	 * Metabox Priority
	 * 
	 * @since    3.0
	 * 
	 * @var      string
	 */
	public $priority;

	/**
	 * Metabox Panels
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	public $panels = array();

	/**
	 * Metabox template
	 * 
	 * @since    3.0
	 * 
	 * @var      Template
	 */
	protected $template = '';

	/**
	 * Metabox Actions hooks
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	public $actions = array();

	/**
	 * Metabox Filters hooks
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	public $filters = array();

	/**
	 * Class constructor.
	 *
	 * @since    3.0
	 */
	public function __construct( $params = array() ) {

		if ( ! is_admin() ) {
			return;
		}

		$this->init();

		$defaults = array(
			'id'        => '',
			'title'     => __( 'WordPress Movie Library', 'wpmovielibrary' ),
			'callback'  => '',
			'screen'    => 'movie',
			'context'   => 'normal',
			'priority'  => 'high',
			'panels'    => array(),
			'condition' => null
		);

		foreach ( $defaults as $key => $value ) {
			if ( isset( $params[ $key ] ) ) {
				$this->$key = $params[ $key ];
			} else {
				$this->$key = $value;
			}
		}

		$this->make();
	}

	/**
	 * Add the Metabox to WordPress.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function create() {

		add_meta_box( $this->id . '-metabox', $this->title, $this->callback, $this->screen, $this->context, $this->priority, $this->callback_args );
	}

	/**
	 * Generate an HTML tag for to be used in Metaboxes.
	 * 
	 * Supported tags: <input>, <textarea>, <select>.
	 * 
	 * Note that $value is expected to be escaped.
	 * 
	 * @since    3.0
	 * 
	 * @param    array     $field Field parameters
	 * @param    string    $slug Field ID
	 * @param    string    $value Field current value
	 * @param    string    $data_type Data type, 'meta' or 'detail'
	 * @param    string    $format Field output format, 'html' or 'json'
	 * 
	 * @return   string    HTML output
	 */
	public function get_field( $field, $slug, $value = '', $data_type = 'meta', $format = 'html' ) {

		if ( ! is_array( $field ) || empty( $field['type'] ) ) {
			return false;
		}

		if ( 'detail' != $data_type ) {
			$data_type = 'meta';
		}

		$field = parse_args_strict(
			$field,
			array(
				'type'     => '',
				'title'    => '',
				'desc'     => '',
				'size'     => '',
				'icon'     => '',
				'multi'    => false,
				'options'  => array()
			)
		);

		switch ( $field['type'] ) {
			case 'select':
				if ( true === $field['multi'] ) {
					$form = $this->get_multiple_select_field( $field, $slug, $value, $data_type, $format );
				} else {
					$form = $this->get_select_field( $field, $slug, $value, $data_type, $format );
				}
				break;
			case 'text':
				$form = $this->get_text_field( $field, $slug, $value, $data_type, $format );
				break;
			case 'hidden':
				$form = $this->get_text_field( $field, $slug, $value, $data_type, $format );
				break;
			case 'textarea':
				$form = $this->get_textarea_field( $field, $slug, $value, $data_type, $format );
				break;
			default:
				$form = '';
		}

		if ( 'detail' == $data_type ) {
			$html = '<h4 class="wpmoly-' . $data_type . '-item-title"><span class="' . $field['icon'] . '"></span>&nbsp; ' . esc_attr__( $field['title'], 'wpmovielibrary' ) . '</h4><div class="wpmoly-meta-value">' . $form . '</div>';
		} else {
			$html  = '<div class="wpmoly-meta-label"><label for="meta_' . $slug . '">' . esc_attr__( $field['title'], 'wpmovielibrary' ) . '</label></div>';
			$html .= '<div class="wpmoly-meta-value">' . $form . '</div>';
		}

		return $html;
	}

	/**
	 * Generate a SELECT HTML tag for to be used in Metaboxes.
	 * 
	 * This method is private and should only be accessed by calling for
	 * Metabox::get_field(). This means $value is expected to be escaped and
	 * will not be at this point.
	 * 
	 * @since    3.0
	 * 
	 * @param    array     $field Field parameters
	 * @param    string    $slug Field ID
	 * @param    string    $value Field current value
	 * @param    string    $data_type Data type, 'meta' or 'detail'
	 * @param    string    $format Field output format, 'html' or 'json'
	 * 
	 * @return   string    HTML output
	 */
	private function get_select_field( $field, $slug, $value, $data_type, $format ) {

		$name = "wpmoly[$data_type][$slug]";

		// Backbone data attributes
		$data = ' data-meta-type="' . $data_type . '" data-meta-key="' . $slug . '"';

		$html  = '<select id="' . $data_type . '_' . $slug . '" name="' . $name . '" placeholder="' . esc_attr__( $field['desc'], 'wpmovielibrary' ) . '"' . $data . '>';
		foreach ( $field['options'] as $option_value => $option_text ) {
			if ( 'json' == $format ) {
				$selected = '<# if ( "' . $option_value . '" == data.' . $slug . ' ) { #> selected="selected"<# } #>';
			} else {
				$selected = selected( $option_value, $value, $echo = false );
			}
			$html .= '<option value="' . esc_attr( $option_value ) . '"' . $selected . '>' . esc_attr__( $option_text, 'wpmovielibrary' ) . '</option>';
		}
		$html .= '</select>';

		return $html;
	}

	/**
	 * Generate a SELECT HTML tag for to be used in Metaboxes.
	 * 
	 * This method is private and should only be accessed by calling for
	 * Metabox::get_field(). This means $value is expected to be escaped and
	 * will not be at this point.
	 * 
	 * @since    3.0
	 * 
	 * @param    array     $field Field parameters
	 * @param    string    $slug Field ID
	 * @param    string    $value Field current value
	 * @param    string    $data_type Data type, 'meta' or 'detail'
	 * @param    string    $format Field output format, 'html' or 'json'
	 * 
	 * @return   string    HTML output
	 */
	private function get_multiple_select_field( $field, $slug, $values, $data_type, $format ) {

		$name = "wpmoly[$data_type][$slug][]";

		// Backbone data attributes
		$data = ' data-meta-type="' . $data_type . '" data-meta-key="' . $slug . '"';

		if ( ! is_array( $values ) ) {
			$values = (array) $values;
		}

		$html  = '<select id="' . $data_type . '_' . $slug . '" name="' . $name . '" multiple="multiple" placeholder="' . esc_attr__( $field['desc'], 'wpmovielibrary' ) . '"' . $data . '>';
		foreach ( $field['options'] as $option_value => $option_text ) {
			if ( 'json' == $format ) {
				$selected = '<# if ( _.contains( data.' . $slug . ', "' . $option_value . '" ) ) { #> selected="selected"<# } #>';
			} else {
				$selected = in_array( $option_value, $values ) ? ' selected="selected"' : '';
			}
			$html .= '<option value="' . esc_attr( $option_value ) . '"' . $selected . '>' . esc_attr__( $option_text, 'wpmovielibrary' ) . '</option>';
		}
		$html .= '</select>';

		return $html;
	}

	/**
	 * Generate an INPUT HTML tag for to be used in Metaboxes.
	 * 
	 * This method is private and should only be accessed by calling for
	 * Metabox::get_field(). This means $value is expected to be escaped and
	 * will not be at this point.
	 * 
	 * @since    3.0
	 * 
	 * @param    array      $field Field parameters
	 * @param    string     $slug Field ID
	 * @param    string     $value Field current value
	 * @param    string     $data_type Data type, 'meta' or 'detail'
	 * @param    string     $format Field output format, 'html' or 'json'
	 * @param    boolean    $format Hidden input?
	 * 
	 * @return   string    HTML output
	 */
	private function get_text_field( $field, $slug, $value, $data_type, $format, $hidden = false ) {

		// Backbone data attributes
		$data = ' data-meta-type="' . $data_type . '" data-meta-key="' . $slug . '"';
		$type = true === $hidden ? 'hidden' : 'text';

		$html = '<input type="' . $type . '" id="meta_' . $slug . '" name="wpmoly[' . $data_type . '][' . $slug . ']" value="' . $value . '"' . $data . ' />';

		return $html;
	}

	/**
	 * Generate a TEXTAREA HTML tag for to be used in Metaboxes.
	 * 
	 * This method is private and should only be accessed by calling for
	 * Metabox::get_field(). This means $value is expected to be escaped and
	 * will not be at this point.
	 * 
	 * @since    3.0
	 * 
	 * @param    array     $field Field parameters
	 * @param    string    $slug Field ID
	 * @param    string    $value Field current value
	 * @param    string    $data_type Data type, 'meta' or 'detail'
	 * @param    string    $format Field output format, 'html' or 'json'
	 * 
	 * @return   string    HTML output
	 */
	private function get_textarea_field( $field, $slug, $value, $data_type, $format ) {

		// Backbone data attributes
		$data = ' data-meta-type="' . $data_type . '" data-meta-key="' . $slug . '"';
		
		$html = '<textarea id="meta_' . $slug . '" name="wpmoly[' . $data_type . '][' . $slug . ']"' . $data . '>' . $value . '</textarea>';

		return $html;
	}

	/**
	 * Initialize the Metabox.
	 * 
	 * This is run priori to Metabox construction.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	abstract public function init();

	/**
	 * Build the Metabox.
	 * 
	 * This is run after Metabox construction.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	abstract public function make();
}
