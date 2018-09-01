<?php
/**
 * Define the Term Headbox class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\headboxes;

use \wpmoly\nodes\Node;
use \wpmoly\nodes\taxonomies;

/**
 * General Term Headbox class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 * @author Charlie Merland <charlie@caercam.org>
 */
class Term extends Headbox {

	/**
	 * Class Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param int|Node|WP_Term $node Node ID, node instance or term object
	 */
	public function __construct( $node = null ) {

		if ( is_numeric( $node ) ) {
			$this->id   = absint( $node );
			$this->term = get_term( $this->id );
		} elseif ( $node instanceof Node ) {
			if ( $node instanceof taxonomies\Actor ) {
				$this->id    = absint( $node->id );
				$this->actor = $node;
				$this->type  = 'actor';
			} elseif ( $node instanceof taxonomies\Collection ) {
				$this->id         = absint( $node->id );
				$this->collection = $node;
				$this->type       = 'collection';
			} elseif ( $node instanceof taxonomies\Genre ) {
				$this->id    = absint( $node->id );
				$this->genre = $node;
				$this->type  = 'genre';
			} else {
				$this->id   = absint( $node->id );
				$this->term = $node->term;
			}
		} elseif ( isset( $node->term_id ) ) {
			$this->id   = absint( $node->term_id );
			$this->term = $node;
			$this->type = $node->taxonomy;
		}

		$this->init();
	}

	/**
	 * Initialize the Headbox.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function init() {

		$headbox_types = array(
			'collection' => array(
				'label'  => __( 'Collection', 'wpmovielibrary' ),
				'themes' => array(
					'default'  => __( 'Default', 'wpmovielibrary' ),
					'extended' => __( 'Extended', 'wpmovielibrary' ),
				),
			),
			'actor' => array(
				'label'  => __( 'Actor', 'wpmovielibrary' ),
				'themes' => array(
					'default'  => __( 'Default', 'wpmovielibrary' ),
					'extended' => __( 'Extended', 'wpmovielibrary' ),
				),
			),
			'genre' => array(
				'label'  => __( 'Genre', 'wpmovielibrary' ),
				'themes' => array(
					'default'  => __( 'Default', 'wpmovielibrary' ),
					'extended' => __( 'Extended', 'wpmovielibrary' ),
				),
			),
		);

		/**
		 * Filter the supported Headbox types.
		 *
		 * @since 3.0.0
		 *
		 * @param array $headbox_types
		 */
		$this->supported_types = apply_filters( 'wpmoly/filter/headbox/supported/types', $headbox_types );

		foreach ( $this->supported_types as $type_id => $type ) {

			/**
			 * Filter the supported Headbox themes.
			 *
			 * @since 3.0.0
			 *
			 * @param array $default_modes
			 */
			$this->supported_themes[ $type_id ] = apply_filters( "wpmoly/filter/headbox/supported/{$type_id}/themes", $type['themes'] );
		}

		$this->build();
	}

	/**
	 * Build the Headbox.
	 *
	 * Load items depending on presets or custom settings.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function build() {

		$type = $this->get( 'type' );
		if ( is_null( $type ) ) {
			return false;
		}

		$function = "get_$type";
		if ( function_exists( $function ) ) {
			$this->$type = $function( $this->id );
		}
	}

	/**
	 * Retrieve current headbox type.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_type() {

		/**
		 * Filter headbox default type.
		 *
		 * @since 3.0.0
		 *
		 * @param string $default_type
		 */
		$default_type = apply_filters( 'wpmoly/filter/headbox/default/type', '' );

		if ( is_null( $this->type ) ) {
			$this->type = $default_type;
		}

		return $this->type;
	}

	/**
	 * Set headbox type.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public function set_type( $type ) {

		if ( ! isset( $this->supported_types[ $type ] ) ) {
			$type = '';
		}

		$this->type = $type;

		return $this->type;
	}

	/**
	 * Retrieve current headbox theme.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_theme() {

		if ( is_null( $this->theme ) ) {
			$this->theme = 'default';
		}

		return $this->theme;
	}

	/**
	 * Set headbox theme.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $theme
	 *
	 * @return string
	 */
	public function set_theme( $theme ) {

		if ( ! isset( $this->supported_themes[ $this->type ][ $theme ] ) ) {
			$theme = 'default';
		}

		$this->theme = $theme;

		return $this->theme;
	}

}
