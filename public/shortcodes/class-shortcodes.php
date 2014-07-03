<?php
/**
 * WPMovieLibrary Shortcodes Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

if ( ! class_exists( 'WPML_Shortcodes' ) ) :

	class WPML_Shortcodes extends WPML_Module {

		/**
		 * Shortcodes
		 * 
		 * @since    1.1.0
		 * @var      array
		 */
		protected $shortcodes;

		/**
		 * Constructor
		 *
		 * @since    1.1.0
		 */
		public function __construct() {

			$this->init();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.1.0
		 */
		public function init() {

			$this->shortcodes = WPML_Settings::get_available_shortcodes();

			$this->register_shortcodes();
		}

		/**
		 * Register all shortcodes
		 *
		 * @since    1.1.0
		 */
		private function register_shortcodes() {

			if ( ! is_array( $this->shortcodes ) || empty( $this->shortcodes ) )
				return false;

			foreach ( $this->shortcodes as $slug => $shortcode ) {

				$callback = array( $this, 'default_shortcode' );

				if ( ! is_null( $shortcode['callback'] ) && method_exists( $this, $shortcode['callback'] ) )
					$callback = array( $this, $shortcode['callback'] );

				add_shortcode( $slug, $callback );
			}
		}

		/**
		 * Default shortcodes' callback method
		 *
		 * @since    1.1.0
		 * 
		 * @param    array     Shortcode attributes
		 * @param    string    Shortcode content
		 * 
		 * @return   string    Shortcode display
		 */
		public function default_shortcode( $atts = array(), $content = null ) {

			return $content;
		}

		public function movies_shortcode( $atts = array(), $content = null ) {

			$atts = apply_filters( 'wpml_filter_shortcode_atts', 'movies', $atts );

			return $content;
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.1.0
		 */
		public function register_hook_callbacks() {}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.1.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.1.0
		 */
		public function deactivate() {}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.1.0
		 */
		public static function uninstall() {}

	}

endif;