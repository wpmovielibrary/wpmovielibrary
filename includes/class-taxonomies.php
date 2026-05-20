<?php
/**
 * Define the custom taxonomies plugin class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

/**
 * Register the plugin's custom taxonomies.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Taxonomies {

	/**
	 * Static instance.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Taxonomies
	 */
	private static $_instance = null;

	/**
	 * Taxonomies parameters.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var array
	 */
	private array $taxonomies = [];

	/**
	 * Constructor.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Get the instance of this class, insantiating it if it doesn't exist
	 * yet.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Taxonomies
	 */
	public static function get_instance() {

		if ( ! is_object( self::$_instance ) ) {
			self::$_instance = new self;
			self::$_instance->init();
		}

		return self::$_instance;
	}

	/**
	 * Initialize.
	 *
	 * @since 6.0.0
	 * 
	 * @access private
	 */
	private function init() {

		$this->taxonomies = config( 'taxonomies', [] );
	}

	/**
	 * Register custom taxonomies.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function register() {

		foreach ( $this->taxonomies as $post_type => $groups ) {
			foreach ( $groups as $group => $taxonomies ) {
				foreach ( $taxonomies as $slug => $args ) {
					$taxonomy_slug = "wpmoly_{$post_type}_{$slug}";
					// only general taxonomies are public by default
					if ( 'general' !== $group ) {
						$args['public']             = $args['public'] ?? false;
						$args['publicly_queryable'] = $args['publicly_queryable'] ?? false;
						$args['show_ui']            = $args['show_ui'] ?? false;
						$args['show_in_nav_menus']  = $args['show_in_nav_menus'] ?? false;
					}
					register_taxonomy( $taxonomy_slug, [ $post_type ], $args );
				}
			}
		}
	}
}
