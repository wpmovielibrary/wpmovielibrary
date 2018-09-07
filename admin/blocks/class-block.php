<?php
/**
 * The file that defines the dashboard block class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\dashboard;

use wpmoly\templates\Admin as Template;

/**
 * The dashboard block class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Block {

	/**
	 * Block ID.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Block Data.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Block Template.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var \wpmoly\templates\Admin
	 */
	protected $template = null;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $id   Block ID.
	 * @param array  $args Block parameters.
	 */
	public function __construct( $id, $args = array() ) {

		// Set Block ID.
		$this->id = $id;

		// Prepare parameters.
		$args = wp_parse_args( $args, array(
			'name'        => '',
			'title'       => '',
			'description' => '',
			'controller'  => '',
			'template'    => 'editors/blocks/block.php',
		) );

		// Set Block parameters.
		$this->set_args( $args );

		// Set Block template.
		$this->template = new Template( $this->get_arg( 'template' ) );
	}

	/**
	 * Set Block parameters.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $args Block parameters.
	 *
	 * @return array
	 */
	public function set_args( $args = array() ) {

		$this->args = (array) $args;

		return $this->args;
	}

	/**
	 * Retrieve Block parameters.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function get_args() {

		return $this->args;
	}

	/**
	 * Set Block parameter.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name  Block parameter name.
	 * @param mixed  $value Block parameter value.
	 *
	 * @return mixed
	 */
	public function set_arg( $name, $value ) {

		if ( ! is_string( $name ) || empty( $name ) ) {
			return false;
		}

		$this->args[ $name ] = $value;

		return $this->args[ $name ];
	}

	/**
	 * Retrieve Block parameter.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Block parameter name.
	 *
	 * @return mixed
	 */
	public function get_arg( $name ) {

		$arg = null;
		if ( isset( $this->args[ $name ] ) ) {
			$arg = $this->args[ $name ];
		}

		return $arg;
	}

	/**
	 * Set Block data.
	 *
	 * Used to store additional data passed to the template.
	 *
	 * @since 3.0.0
	 *
	 * @param array $data Additional Block data.
	 *
	 * @return array
	 */
	public function set_data( $data ) {

		$this->data = $data;

		return $this->data;
	}

	/**
	 * Render the Block.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function render() {

		/**
		 * Fires before building the Block.
		 *
		 * @since 3.0.0
		 *
		 * @param Block &$this The Block instance (passed by reference).
		 */
		do_action_ref_array( "wpmoly/dashboard/block/{$this->id}/build", array( &$this ) );

		$this->template->set_data( 'id',          $this->id );
		$this->template->set_data( 'title',       $this->get_arg( 'title' ) );
		$this->template->set_data( 'description', $this->get_arg( 'description' ) );
		$this->template->set_data( 'controller',  $this->get_arg( 'controller' ) );

		$this->template->set_data( $this->data );

		$classes = array();
		$object_type    = $this->get_arg( 'object_type' );
		$object_subtype = $this->get_arg( 'object_subtype' );
		if ( ! empty( $object_type ) ) {
			$classes[] = 'editor-block';
			$classes[] = $this->get_arg( 'object_type' ) . '-editor-block';
			if ( ! empty( $object_subtype ) ) {
				$classes[] = $this->get_arg( 'object_subtype' ) . '-editor-block';
				$classes[] = str_replace( $this->get_arg( 'object_subtype' ), $this->get_arg( 'object_type' ), $this->id ) . '-editor-block';
			}
			$classes[] = $this->id . '-editor-block';
		} else {
			$classes[] = 'dashboard-block';
		}

		$this->template->set_data( 'class', implode( ' ', $classes ) );

		/**
		 * Fires before rendering the Block.
		 *
		 * @since 3.0.0
		 *
		 * @param Block &$this The Block instance (passed by reference).
		 */
		do_action_ref_array( "wpmoly/dashboard/block/{$this->id}/render", array( &$this ) );

		$this->template->render();

		/**
		 * Fires after the Block is rendered.
		 *
		 * @since 3.0.0
		 *
		 * @param Block &$this The Block instance (passed by reference).
		 */
		do_action_ref_array( "wpmoly/dashboard/block/{$this->id}/rendered", array( &$this ) );
	}

}
