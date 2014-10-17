<?php
/**
 * WPMovieLibrary L10n Class extension.
 * 
 * This class implement some of the translation processes the plugin offers.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_L10n' ) ) :

	class WPMOLY_L10n extends WPMOLY_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {

			$this->register_hook_callbacks();
		}

		/**
		 * Get rewrites list
		 * 
		 * @since    2.0
		 * 
		 * @return   array     Translated rewrites
		 */
		public static function get_l10n_rewrite() {

			/*$l10n_rewrite = get_transient( 'wpmoly_l10n_rewrite' );
			if ( false === $l10n_rewrite )*/
				$l10n_rewrite = self::set_l10n_rewrite();

			return $l10n_rewrite;
		}

		/**
		 * Generate a list of possible translated rewrites
		 * 
		 * @since    2.0
		 * 
		 * @return   array     Translated rewrites
		 */
		public static function set_l10n_rewrite() {

			$l10n_rewrite = array();

			$details   = WPMOLY_Settings::get_supported_movie_details();
			$meta      = WPMOLY_Settings::get_supported_movie_meta();
			$languages = WPMOLY_Settings::get_available_languages();
			$countries = WPMOLY_Settings::get_supported_countries();

			foreach ( $details as $slug => $detail ) {
				$l10n_rewrite['detail'][ $slug ] = $detail['rewrite'];
				foreach ( $detail['options'] as $_slug => $option )
					if ( 'rating' == $slug )
						$l10n_rewrite['detail'][ $_slug ] = $_slug;
					else
						$l10n_rewrite['detail'][ $_slug ] = __( $option, 'wpmovielibrary' );
			}

			foreach ( $meta as $slug => $m ) {
				$l10n_rewrite['meta'][ $slug ] = $m['rewrite'];
			}

			foreach ( $languages as $language )
				$l10n_rewrite['languages'][ $language['native'] ] = $language['name'];

			foreach ( $countries as $country )
				$l10n_rewrite['countries'][ $country['native'] ] = $country['name'];

			foreach ( $l10n_rewrite as $id => $rewrite )
				$l10n_rewrite[ $id ] = array_map( 'sanitize_title_for_query', $rewrite );

			/**
			 * Filter the rewrites list
			 *
			 * @since    2.0
			 *
			 * @param    array    $l10n_rewrite Existing rewrites
			 */
			$l10n_rewrite = apply_filters( 'wpmoly_filter_l10n_rewrite', $l10n_rewrite );

			//set_transient( 'wpmoly_l10n_rewrite', $l10n_rewrite );

			return $l10n_rewrite;
		}

		/**
		 * Delete cached rewrites list
		 * 
		 * @since    2.0
		 * 
		 * @return   boolean    Deletion status
		 */
		public static function delete_l10n_rewrite() {

			$delete = delete_transient( 'wpmoly_l10n_rewrite' );

			return $delete;
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    2.0
		 */
		public function register_hook_callbacks() {}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    2.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {

			
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    2.0
		 */
		public function deactivate() {

			
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    2.0
		 */
		public static function uninstall() {

			
		}

		/**
		 * Initializes variables
		 *
		 * @since    2.0
		 */
		public function init() {}

	}

endif;
