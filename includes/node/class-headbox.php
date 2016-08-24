<?php
/**
 * Define the Headbox class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 */

namespace wpmoly\Node;

/**
 * General Headbox class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Headbox extends Node {

	/**
	 * Headbox Node.
	 * 
	 * @var    Node
	 */
	public $node;

	/**
	 * Supported Headbox types.
	 * 
	 * @var    array
	 */
	private $supported_types = array();

	/**
	 * Supported Headbox modes.
	 * 
	 * @var    array
	 */
	private $supported_modes = array();

	/**
	 * Supported Headbox themes.
	 * 
	 * @var    array
	 */
	private $supported_themes = array();

	/**
	 * __get().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name
	 * 
	 * @return   mixed
	 */
	public function __get( $name ) {

		return isset( $this->$name ) ? $this->$name : null;
	}

	/**
	 * Initialize the Headbox.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function init() {

		$headbox_types = array(
			'collection' => array(
				'label'  => __( 'Collection', 'wpmovielibrary' ),
				'themes' => array(
					'default' =>  __( 'Default', 'wpmovielibrary' )
				)
			),
			'actor' => array(
				'label'  => __( 'Actor', 'wpmovielibrary' ),
				'themes' => array(
					'default' => __( 'Default', 'wpmovielibrary' )
				)
			),
			'genre' => array(
				'label'  => __( 'Genre', 'wpmovielibrary' ),
				'themes' => array(
					'default' => __( 'Default', 'wpmovielibrary' )
				)
			),
			'movie' => array(
				'label'  => __( 'Movie', 'wpmovielibrary' ),
				'themes' => array(
					'default'  => __( 'Default', 'wpmovielibrary' ),
					'extended' => __( 'Extended', 'wpmovielibrary' ),
					'vintage'  => __( 'Vintage', 'wpmovielibrary' ),
					'allocine' => __( 'Allocine', 'wpmovielibrary' ),
					'imdb'     => __( 'IMDb', 'wpmovielibrary' )
				)
			)
		);

		/**
		 * Filter the supported Headbox types.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $headbox_types
		 */
		$this->supported_types = apply_filters( 'wpmoly/filter/headbox/supported/types', $headbox_types );

		foreach ( $this->supported_types as $type_id => $type ) {

			/**
			 * Filter the supported Headbox themes.
			 * 
			 * @since    3.0
			 * 
			 * @param    array    $default_modes
			 */
			$this->supported_modes[ $type_id ] = apply_filters( 'wpmoly/filter/headbox/supported/' . $type_id . '/themes', $type['themes'] );
		}

		$this->build();
	}

	/**
	 * Build the Headbox.
	 * 
	 * Load items depending on presets or custom settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function build() {

		if ( is_null( $this->get( 'type' ) ) ) {
			return false;
		}

		$function = "get_" . $this->get( 'type' );
		if ( function_exists( $function ) ) {
			$this->node = $function( $this->id );
		}
	}

	/**
	 * Simple accessor for supported types.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function get_supported_types() {

		return $this->supported_types;
	}

	/**
	 * Simple accessor for supported themes.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function get_supported_themes( $type = '' ) {

		return ! empty( $type ) && ! empty( $this->supported_themes[ $type ] ) ? $this->supported_themes[ $type ] : $this->supported_themes;
	}
}