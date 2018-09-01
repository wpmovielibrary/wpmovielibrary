<?php
/**
 * Define the Grid Template classes.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\templates;

use WP_Error;

/**
 * Grid Template class.
 *
 * This class acts as a controller for grid templates, determining which template
 * file to use and preseting data for it.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Grid extends Front {

	/**
	 * Grid instance.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var Grid
	 */
	private $grid;

	/**
	 * Class Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param mixed $grid Grid instance or ID.
	 *
	 * @return Template|WP_Error
	 */
	public function __construct( $grid ) {

		if ( is_int( $grid ) ) {
			$grid = get_grid( $grid );
			if ( empty( $grid->post ) ) {
				return null;
			}
			$this->grid = $grid;
		} elseif ( is_object( $grid ) ) {
			$this->grid = $grid;
		} else {
			return null;
		}

		$this->set_path();

		return $this;
	}

	/**
	 * Determine the grid template path based on the grid's type and mode.
	 *
	 * @TODO make use of that WP_Error.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @return string
	 */
	private function set_path() {

		$path = 'public/templates/grid.php';
		if ( ! file_exists( WPMOLY_PATH . $path ) ) {
			return new WP_Error( 'missing_template_path', sprintf( __( 'Error: "%s" does not exists.', 'wpmovielibrary' ), esc_attr( WPMOLY_PATH . $path ) ) );
		}

		$this->path = $path;

		return $path;
	}

	/**
	 * Determine grid preset.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param array $data Template data.
	 *
	 * @return array
	 */
	private function set_preset( $data = array() ) {

		$preset = $this->grid->get_preset();
		if ( is_array( $preset ) && ! empty( $preset ) ) {
			$data['preset-name']  = key( $preset );
			$data['preset-value'] = current( $preset );
		} else {
			$data['preset'] = $preset;
			return $data;
		}

		if ( ! in_array( $data['preset-name'], array( 'actor', 'collection', 'genre' ) ) ) {
			return $data;
		}

		$term = get_term_by( 'slug', $data['preset-value'], $data['preset-name'] );
		if ( ! $term ) {
			return $data;
		}

		$taxonomy = get_taxonomy( $data['preset-name'] );
		if ( ! $taxonomy ) {
			return $data;
		}

		$data['preset-name']  = $taxonomy->rest_base;
		$data['preset-value'] = $term->term_id;

		return $data;
	}

	/**
	 * Prepare grid attributes.
	 *
	 * Generate a list of data- attributes to add to the grid container tag.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @return string
	 */
	private function get_data_attributes() {

		$data = array(
			'grid'   => $this->grid->id,
			'type'   => $this->grid->get_type(),
			'mode'   => $this->grid->get_mode(),
			'theme'  => $this->grid->get_theme(),
		);

		if ( $this->grid->is_widget() ) {
			$data['widget'] = 1;
		}

		$data = $this->set_preset( $data );

		foreach ( $data as $key => $value ) {
			$data[ $key ] = sprintf( 'data-%s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		$data = implode( ' ', $data );

		return $data;
	}

	/**
	 * Render the Template.
	 *
	 * Default parameters are the opposite of Template::render(): always
	 * require and never echo.
	 *
	 * If the grid is not ready yet, try to build it now. If an error has
	 * occurred during the query, most likely because the query preset used
	 * is not supported, the query is empty an will result in an error notice
	 * being displayed instead of the grid.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $require Use 'once' to use require_once(), 'always' to use require()
	 * @param boolean $echo Use true to display, false to return
	 *
	 * @return string
	 */
	public function render( $require = 'always', $echo = false ) {

		$this->set_data( array(
			'grid' => $this->grid,
			'data' => $this->get_data_attributes(),
		) );

		$this->prepare( $require );

		if ( true !== $echo ) {
			return $this->template;
		}

		echo $this->template;
	}

}
