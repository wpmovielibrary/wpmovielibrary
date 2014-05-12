<?php
/**
 * WPMovieLibrary Stats Class extension.
 * 
 * Basic statistics about the movie library
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Stats' ) ) :

	class WPML_Stats extends WPML_Module {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 */
		public function __construct() {

			$this->register_hook_callbacks();
		}

		/**
		 * Get total number of queued movies
		 * Alias for get_movies_count()
		 * 
		 * @since    1.0.0
		 * 
		 * @return   int    Total number of movies
		 */
		public static function get_queued_movies_count() {
			return self::get_movies_count( 'import-queued' );
		}

		/**
		 * Get total number of imported movies
		 * Alias for get_movies_count()
		 * 
		 * @since    1.0.0
		 * 
		 * @return   int    Total number of movies
		 */
		public static function get_imported_movies_count() {
			return self::get_movies_count( 'import-draft' );
		}

		/**
		 * Get total number of movies
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    Query arguments
		 * 
		 * @return    int    Total number of movies
		 */
		public static function get_movies_count( $status = 'publish' ) {

			if ( ! in_array( $status, array( 'import-draft', 'import-queued', 'publish', 'draft' ) ) )
				$status = 'publish';

			$count = wp_count_posts( 'movie' );
			$count = is_object( $count ) ? (array) $count : $count;
			$count = isset( $count[ $status ] ) ? $count[ $status ] : 0;

			return $count;
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;