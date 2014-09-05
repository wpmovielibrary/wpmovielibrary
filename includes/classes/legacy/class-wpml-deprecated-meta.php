<?php
/**
 * WPMovieLibrary Deprecated Meta Class.
 * 
 * This class handles deprecated WPMovieLibrary Movie Metadata. Prior to WPML
 * version 1.3 movie metadata were stored in a unique post meta value which
 * blocked a lot of features and improvement. Current class handles the migration
 * from obsolete to new data format.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Deprecated_Meta' ) ) :

	class WPML_Deprecated_Meta extends WPML_Module {

		/**
		 * Constructor
		 *
		 * @since    1.3
		 */
		public function __construct() {

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.3
		 */
		public function register_hook_callbacks() {}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.3
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.3
		 */
		public function deactivate() {}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    1.3
		 */
		public static function uninstall() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.3
		 */
		public function init() {}

	}

endif;