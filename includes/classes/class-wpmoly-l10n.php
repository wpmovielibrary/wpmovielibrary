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

			add_filter( 'wpmoly_filter_rewrites', __CLASS__ . '::filter_rewrites', 10, 1 );
			add_filter( 'wpmoly_filter_value_rewrites', __CLASS__ . '::filter_value_rewrites', 10, 3 );
		}

		/**
		 * Get rewrites list
		 * 
		 * @since    2.0
		 * 
		 * @return   array     Translated rewrites
		 */
		public static function get_l10n_rewrite() {

			$l10n_rewrite = get_option( 'wpmoly_l10n_rewrite' );
			if ( false === $l10n_rewrite )
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
				if ( ! is_null( $m['rewrite'] ) ) {
					if ( wpmoly_o( 'rewrite-enable' ) )
						$l10n_rewrite['meta'][ $slug ] = array_pop( $m['rewrite'] );
					else
						$l10n_rewrite['meta'][ $slug ] = key( $m['rewrite'] );
				}
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
			add_option( 'wpmoly_l10n_rewrite', $l10n_rewrite );

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

			$l10n_rewrite_rules = get_option( 'wpmoly_l10n_rewrite_rules' );
			if ( false === $l10n_rewrite_rules )
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

			$l10n_rules['movies'] = ( $translate && '' != $movies ? $movies : 'movies' );
			$l10n_rules['collection'] = ( $translate && '' != $collection ? $collection : 'collection' );
			$l10n_rules['genre'] = ( $translate && '' != $genre ? $genre : 'genre' );
			$l10n_rules['actor'] = ( $translate && '' != $actor ? $actor : 'actor' );

			$details = WPMOLY_Settings::get_supported_movie_details();
			$meta    = WPMOLY_Settings::get_supported_movie_meta();

			foreach ( $details as $slug => $detail ) {
				if ( $translate )
					$l10n_rules['detail'][ $slug ] = array_pop( $detail['rewrite'] );
				else
					$l10n_rules['detail'][ $slug ] = key( $detail['rewrite'] );
			}

			foreach ( $meta as $slug => $m ) {
				if ( ! is_null( $m['rewrite'] ) ) {
					if ( $translate )
						$l10n_rules['meta'][ $slug ] = array_pop( $m['rewrite'] );
					else
						$l10n_rules['meta'][ $slug ] = key( $m['rewrite'] );
				}
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
			add_option( 'wpmoly_l10n_rewrite_rules', $l10n_rules );

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

		/**
		 * Simple filter for rewrites to get rid of %xx%xx-like accented
		 * letters in URLs.
		 *
		 * @since    2.0
		 * 
		 * @param    string    $rewrite
		 *
		 * @return   string    Filtered $rewrite
		*/
		public static function filter_rewrites( $rewrite ) {

			if ( 1 == strpos( $rewrite, '.' ) )
				return $rewrite;

			$rewrite = remove_accents( $rewrite );
			$rewrite = sanitize_title_with_dashes( $rewrite );

			return $rewrite;
		}

		public static function translate_meta( $type, $meta ) {

			$rewrites = self::get_l10n_rewrite();

			if ( ! isset( $rewrites[ $type ][ $meta ] ) )
				return $meta;

			$meta = $rewrites[ $type ][ $meta ];

			return $meta;
		}

		public static function translate_value( $type, $meta, $value ) {

			if ( 'rating' != $meta )
				$value = self::filter_rewrites( __( ucwords( $value ), 'wpmovielibrary' ) );

			return $value;
		}

		/**
		 * Filter a meta value to match a translation, if any.
		 * 
		 * @since    2.1.1
		 * 
		 * @param    string    $meta Meta type
		 * @param    string    $key Meta key
		 * @param    string    $value Meta value
		 * 
		 * @return   string    Un-rewrite meta value if any, original meta value else
		 */
		public static function untranslate_value( $meta, $key, $value ) {

			$rewrites = self::get_l10n_rewrite();
			$_value   = apply_filters( 'wpmoly_filter_rewrites', $value );
			$_value   = array_search( $_value, $rewrites[ $meta ] );

			if ( ! $_value )
				$_value = array_search( remove_accents( rawurldecode( $value ) ), $rewrites[ $meta ] );

			if ( false != $_value )
				$value = $_value;

			return $value;
		}

		/**
		 * Generate Custom Movie Meta permalinks
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $args Permalink parameters
		 * 
		 * @return   string    HTML href of raw URL
		 */
		public static function get_meta_permalink( $args ) {

			$defaults = array(
				'key'     => null,
				'value'   => null,
				'type'    => null,
				'format'  => null,
				'baseurl' => null,
			);
			$args = wp_parse_args( $args, $defaults );
			extract( $args );

			if ( ! in_array( $type, array( 'meta', 'detail' ) ) )
				return null;

			if ( 'raw' !== $format )
				$format = 'html';

			$name  = $value;
			$key   = self::translate_meta( $type, $key );
			$value = self::translate_value( $type, $key, $value );

			$args = array(
				$type     => $key,
				'value'   => $value,
				'baseurl' => $baseurl
			);

			$url = self::build_meta_permalink( $args );

			if ( 'raw' == $format )
				return $url;

			$permalink = sprintf( '<a href="%s" title="%s">%s</a>', $url, $value, $name );

			return $permalink;
		}

		/**
		 * Build Meta URL. Use an array of parameter to build a custom
		 * URLs for meta queries.
		 * 
		 * @since    2.1.1
		 * 
		 * @param    array     $args URL parameters to use
		 * 
		 * @return   string    Custom URL
		 */
		public static function build_meta_permalink( $args ) {

			global $wp_rewrite;
			$rewrite = ( '' != $wp_rewrite->permalink_structure );

			$defaults = array(
				'baseurl' => get_permalink(),
				'number'  => null,
				'columns' => null,
				'rows'    => null,
				'order'   => null,
				'orderby' => null,
				'paged'   => null,
				'meta'    => null,
				'detail'  => null,
				'value'   => null,
				'letter'  => null,
				'l10n'    => true
			);
			$args = wp_parse_args( $args, $defaults );

			// 
			$args['type'] = '';
			if ( '' != $args['meta'] && '' != $args['value'] ) {
				$args['type'] = 'meta';
			}
			else if ( '' != $args['detail'] && '' != $args['value'] ) {
				$args['type'] = 'detail';
				$args['meta'] = $args['detail'];
			}

			// Meta should already be translated, but won't if the
			// method is called directly, so we have to have a way
			// translate meta if needed
			if ( false === $args['l10n'] ) {
				$args['meta']  = self::translate_meta( $args['type'], $args['meta'] );
				$args['value'] = self::translate_value( $args['type'], $args['meta'], $args['value'] );
			}

			unset( $args['l10n'] );
			if ( 'rating' != $args['meta'] )
				$args['value'] = sanitize_title( $args['value'] );

			$url = '';
			if ( $rewrite )
				$url = self::build_custom_meta_permalink( $args );
			else
				$url = self::build_default_meta_permalink( $args );

			return $url;
		}

		/**
		 * Build a custom meta permalink for custom permalinks settings
		 * 
		 * This generate a user-friendly URL to access meta-based archive
		 * pages.
		 * 
		 * @since    2.1.1
		 * 
		 * @param    array    $args URL parameters
		 * 
		 * @return   string   Generated URL
		 */
		private static function build_custom_meta_permalink( $args ) {

			extract( $args );

			$movies = wpmoly_o( 'rewrite-movie' );
			if ( ! $movies )
				$movies = 'movies';

			$url = array();

			if ( '' != $meta && '' != $value ) {
				$url[] = $meta;
				$url[] = $value;
			}

			if ( '1' == wpmoly_o( 'rewrite-enable' ) )
				$grid = __( 'grid', 'wpmovielibrary' );
			else
				$grid = 'grid';

			$url[] = $grid;

			if ( '' != $letter )
				$url[] = $letter;

			if ( '' != $columns && '' != $rows )
				$url[] = "$columns:$rows";
			else if ( '' != $number )
				$url[] = $number;

			if ( '' != $orderby )
				$url[] = $orderby;

			if ( '' != $order )
				$url[] = $order;

			if ( 1 < $paged )
				$url[] = "page/$paged";

			if ( $grid == end( $url ) )
				$grid = array_pop( $url );

			$url = implode( '/', $url );
			$url = $baseurl . $url;

			return $url;
		}

		/**
		 * Build a custom meta permalink for default permalinks settings
		 * 
		 * This generate a meta URL with raw URL parameters instead of
		 * nice user-friendly URLs if the user chose not to use WordPress
		 * permalinks.
		 * 
		 * @since    2.1.1
		 * 
		 * @param    array    $args URL parameters
		 * 
		 * @return   string   Generated URL
		 */
		private static function build_default_meta_permalink( $args ) {

			$movie = intval( wpmoly_o( 'movie-archives' ) );
			$base  = 'index.php?';
			if ( $movie )
				$base .= 'page_id=' . $movie . '&';

			$url = array();

			if ( '' != $args['type'] && '' != $args['meta'] && '' != $args['value'] ) {
				$url[ $args['type'] ] = $args['meta'];
				$url['value'] = $args['value'];
			}

			unset( $args['type'], $args['meta'], $args['value'], $args['baseurl'] );

			foreach ( $args as $slug => $arg )
				if ( '' != $arg )
					$url[ $slug ] = $arg;

			$url = add_query_arg( $url, home_url( "/${base}" ) );

			return $url;
		}

		/**
		 * Generate Custom Taxonomies permalinks
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $taxonomy Taxonomy name
		 * @param    string    $value Text for the link
		 * 
		 * @return   string    HTML href of raw URL
		 */
		public static function get_taxonomy_permalink( $taxonomy, $value ) {

			$page_id = intval( wpmoly_o( "{$taxonomy}-archives" ) );
			if ( ! $page_id || ! get_post( $page_id ) )
				return $value;

			if ( '1' != wpmoly_o( 'rewrite-enable' ) )
				$url = home_url( sanitize_title( get_the_title( $page_id ) ) );
			else
				$url = get_permalink( $page_id );

			if ( false === $value )
				return $url;

			$permalink = sprintf( '<a href="%s" title="%s">%s</a>', $url, strip_tags( $value ), $value );

			return $permalink;
		}

		/**
		 * Localization for scripts
		 * 
		 * Adds a translation object to the plugin's JavaScript object
		 * containing localized texts.
		 * 
		 * @since    1.0
		 * 
		 * @return   array    Localization array
		 */
		public static function localize_script() {

			$localize = array();
			$localize['language'] = wpmoly_o( 'api-language' );

			$lang = array(
				'available'		=> __( 'Available', 'wpmovielibrary' ),
				'deleted_movie'		=> __( 'One movie successfully deleted.', 'wpmovielibrary' ),
				'deleted_movies'	=> __( '%s movies successfully deleted.', 'wpmovielibrary' ),
				'dequeued_movie'	=> __( 'One movie removed from the queue.', 'wpmovielibrary' ),
				'dequeued_movies'	=> __( '%s movies removed from the queue.', 'wpmovielibrary' ),
				'done'			=> __( 'Done!', 'wpmovielibrary' ),
				'enqueued_movie'	=> __( 'One movie added to the queue.', 'wpmovielibrary' ),
				'enqueued_movies'	=> __( '%s movies added to the queue.', 'wpmovielibrary' ),
				'images_added'		=> __( 'Images added!', 'wpmovielibrary' ),
				'image_from'		=> __( 'Image from', 'wpmovielibrary' ),
				'images_uploaded'	=> __( 'Images uploaded!', 'wpmovielibrary' ),
				'import_images'		=> __( 'Import Images', 'wpmovielibrary' ),
				'import_images_title'	=> __( 'Import Images for "%s"', 'wpmovielibrary' ),
				'import_images_wait'	=> __( 'Please wait while the images are uploaded...', 'wpmovielibrary' ),
				'import_poster'		=> __( 'Import Poster', 'wpmovielibrary' ),
				'import_poster_title'	=> __( 'Select a poster for "%s"', 'wpmovielibrary' ),
				'import_poster_wait'	=> __( 'Please wait while the poster is uploaded...', 'wpmovielibrary' ),
				'imported'		=> __( 'Imported', 'wpmovielibrary' ),
				'imported_movie'	=> __( 'One movie successfully imported!', 'wpmovielibrary' ),
				'imported_movies'	=> __( '%s movies successfully imported!', 'wpmovielibrary' ),
				'in_progress'		=> __( 'Progressing', 'wpmovielibrary' ),
				'length_key'		=> __( 'Invalid key: it should be 32 characters long.', 'wpmovielibrary' ),
				'load_images'		=> __( 'Load Images', 'wpmovielibrary' ),
				'load_more'		=> __( 'Load More', 'wpmovielibrary' ),
				'loading_images'	=> __( 'Loading Images…', 'wpmovielibrary' ),
				'media_no_movie'	=> __( 'No movie could be found. You need to select a movie before importing images or posters.', 'wpmovielibrary' ),
				'metadata_saved'	=> __( 'Metadata saved!', 'wpmovielibrary' ),
				'missing_meta'		=> __( 'No metadata could be found, please import metadata before queuing.', 'wpmovielibrary' ),
				'movie'			=> __( 'Movie', 'wpmovielibrary' ),
				'movie_updated'		=> _n( 'movie updated', 'movies updated', 0, 'wpmovielibrary' ),
				'movies_updated'	=> _n( 'movie updated', 'movies updated', 2, 'wpmovielibrary' ),
				'not_updated'		=> __( 'not updated', 'wpmovielibrary' ),
				'oops'			=> __( 'Oops… Did something went wrong?', 'wpmovielibrary' ),
				'poster'		=> __( 'Poster', 'wpmovielibrary' ),
				'save_image'		=> __( 'Saving Images…', 'wpmovielibrary' ),
				'search_movie_title'	=> __( 'Searching movie', 'wpmovielibrary' ),
				'search_movie'		=> __( 'Fetching movie data', 'wpmovielibrary' ),
				'see_less'		=> __( 'see no more', 'wpmovielibrary' ),
				'see_more'		=> __( 'see more', 'wpmovielibrary' ),
				'selected'		=> _n( 'selected', 'selected', 0, 'wpmovielibrary' ),
				'set_featured'		=> __( 'Setting featured image…', 'wpmovielibrary' ),
				'updated'		=> __( 'updated successfully', 'wpmovielibrary' ),
				'used'			=> __( 'Used', 'wpmovielibrary' ),
				'updating'		=> __( 'updating movies...', 'wpmovielibrary' ),
				'x_selected'		=> _n( 'selected', 'selected', 2, 'wpmovielibrary' )
			);

			$localize = array_merge( $localize, $lang );

			return $localize;
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
