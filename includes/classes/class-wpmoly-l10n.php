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
		 * Register callbacks for actions and filters
		 * 
		 * @since    2.0
		 */
		public function register_hook_callbacks() {

			add_action( 'wp_head', __CLASS__ . '::dev4press_debug_page_request' );

			add_filter( 'wpmoly_filter_rewrites', __CLASS__ . '::filter_rewrites', 10, 1 );
		}

		/**
		 * Get rewrites list
		 * 
		 * @since    2.0
		 * 
		 * @return   array     Translated rewrites
		 */
		public static function get_l10n_rewrite() {

			/*$l10n_rewrite = get_option( 'wpmoly_l10n_rewrite' );
			if ( false === $l10n_rewrite )*/
				$l10n_rewrite = self::set_l10n_rewrite();

			return $l10n_rewrite;
		}

		public static function dev4press_debug_page_request() {

		    global $wp;
		    
		    echo '<!-- Request: ' . $wp->request . ' -->'."\n";
		    echo '<!-- Matched Rewrite Rule: ' . $wp->matched_rule . ' -->'."\n";
		    echo '<!-- Matched Rewrite Query: ' . $wp->matched_query . ' -->'."\n";
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

				if ( wpmoly_o( 'rewrite-enable' ) )
					$l10n_rewrite['detail'][ $slug ] = array_pop( $detail['rewrite'] );
				else
					$l10n_rewrite['detail'][ $slug ] = key( $detail['rewrite'] );

				foreach ( $detail['options'] as $_slug => $option )
					if ( 'rating' == $slug )
						$l10n_rewrite['detail'][ $_slug ] = $_slug;
					else
						$l10n_rewrite['detail'][ $_slug ] = __( $option, 'wpmovielibrary' );
			}

			foreach ( $meta as $slug => $m ) {
				if ( wpmoly_o( 'rewrite-enable' ) )
					$l10n_rewrite['meta'][ $slug ] = array_pop( $m['rewrite'] );
				else
					$l10n_rewrite['meta'][ $slug ] = key( $m['rewrite'] );
			}

			foreach ( $languages as $language ) {
				if ( wpmoly_o( 'rewrite-enable' ) )
					$l10n_rewrite['languages'][ $language['native'] ] = $language['name'];
				else
					$l10n_rewrite['languages'][ $language['native'] ] = $language['standard'];
			}

			foreach ( $countries as $country ) {
				if ( wpmoly_o( 'rewrite-enable' ) )
					$l10n_rewrite['countries'][ $country['native'] ] = $country['name'];
				else
					$l10n_rewrite['countries'][ $country['native'] ] = $country['native'];
			}

			foreach ( $l10n_rewrite as $id => $rewrite )
				$l10n_rewrite[ $id ] = array_map( __CLASS__ . '::filter_rewrites', $rewrite );

			/**
			 * Filter the rewrites list
			 *
			 * @since    2.0
			 *
			 * @param    array    $l10n_rewrite Existing rewrites
			 */
			$l10n_rewrite = apply_filters( 'wpmoly_filter_l10n_rewrite', $l10n_rewrite );

			self::delete_l10n_rewrite();
			//add_option( 'wpmoly_l10n_rewrite', $l10n_rewrite );

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

			$delete = delete_option( 'wpmoly_l10n_rewrite' );

			return $delete;
		}

		/**
		 * Get rewrite rules list
		 * 
		 * @since    2.0
		 * 
		 * @return   array     Translated rewrite rules
		 */
		public static function get_l10n_rewrite_rules() {

			/*$l10n_rewrite_rules = get_option( 'wpmoly_l10n_rewrite_rules' );
			if ( false === $l10n_rewrite_rules )*/
				$l10n_rewrite_rules = self::set_l10n_rewrite_rules();

			return $l10n_rewrite_rules;
		}

		/**
		 * Generate a list of possible translated rewrite rules
		 * 
		 * Rewrite rules are more limited than rewrites as we only need
		 * to adapt structures.
		 * 
		 * @since    2.0
		 * 
		 * @return   array     Translated rewrite rules
		 */
		public static function set_l10n_rewrite_rules() {

			$l10n_rules = array();

			$translate  = wpmoly_o( 'rewrite-enable' );
			$movies     = wpmoly_o( 'rewrite-movie' );
			$collection = wpmoly_o( 'rewrite-collection' );
			$genre      = wpmoly_o( 'rewrite-genre' );
			$actor      = wpmoly_o( 'rewrite-actor' );

			$l10n_rules['movies']                   = ( $translate && '' != $movies ? $movies : 'movies' );
			$l10n_rules['taxonomies']['collection'] = ( $translate && '' != $collection ? $collection : 'collection' );
			$l10n_rules['taxonomies']['genre']      = ( $translate && '' != $genre ? $genre : 'genre' );
			$l10n_rules['taxonomies']['actor']      = ( $translate && '' != $actor ? $actor : 'actor' );

			$details    = WPMOLY_Settings::get_supported_movie_details();
			$meta       = WPMOLY_Settings::get_supported_movie_meta();

			foreach ( $details as $slug => $detail ) {
				if ( $translate )
					$l10n_rules['detail'][ $slug ] = array_pop( $detail['rewrite'] );
				else
					$l10n_rules['detail'][ $slug ] = key( $detail['rewrite'] );
			}

			foreach ( $meta as $slug => $m ) {
				if ( $translate )
					$l10n_rules['meta'][ $slug ] = array_pop( $m['rewrite'] );
				else
					$l10n_rules['meta'][ $slug ] = key( $m['rewrite'] );
			}

			/**
			 * Filter the rewrite rules list
			 *
			 * @since    2.0
			 *
			 * @param    array    $l10n_rules Existing rewrite rules
			 */
			$l10n_rules = apply_filters( 'wpmoly_filter_l10n_rewrite_rules', $l10n_rules );

			self::delete_l10n_rewrite_rules();
			//add_option( 'wpmoly_l10n_rewrite_rules', $l10n_rules );

			return $l10n_rules;
		}

		/**
		 * Delete cached rewrite rules list
		 * 
		 * @since    2.0
		 * 
		 * @return   boolean    Deletion status
		 */
		public static function delete_l10n_rewrite_rules() {

			$delete = delete_option( 'wpmoly_rewrite_rules' );

			return $delete;
		}

		public static function filter_rewrites( $rewrite ) {

			$rewrite = remove_accents( $rewrite );
			$rewrite = sanitize_title_with_dashes( $rewrite );

			return $rewrite;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    2.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {

			self::delete_l10n_rewrite();
			self::delete_l10n_rewrite_rules();
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    2.0
		 */
		public function deactivate() {

			self::delete_l10n_rewrite();
			self::delete_l10n_rewrite_rules();
		}

		/**
		 * Set the uninstallation instructions
		 *
		 * @since    2.0
		 */
		public static function uninstall() {

			self::delete_l10n_rewrite();
			self::delete_l10n_rewrite_rules();
		}

		/**
		 * Initializes variables
		 *
		 * @since    2.0
		 */
		public function init() {}

	}

endif;
