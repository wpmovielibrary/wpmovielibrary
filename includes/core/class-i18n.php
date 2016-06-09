<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */

namespace wpmoly;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 * @author     Charlie Merland <charlie@caercam.org>
 */
class i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    3.0
	 */
	public function load_plugin_textdomain() {

		$domain      = 'wpmovielibrary';
		$rel_path    = false;
		$plugin_path = dirname( dirname( dirname( plugin_basename( __FILE__ ) ) ) ) . '/languages/';

		load_plugin_textdomain( $domain, $rel_path, $plugin_path );
	}

	/**
	 * Load additional text domains for translation.
	 *
	 * @since    3.0
	 */
	public function load_additional_textdomains() {

		$domain      = 'wpmovielibrary-iso';
		$rel_path    = false;
		$plugin_path = dirname( dirname( dirname( plugin_basename( __FILE__ ) ) ) ) . '/languages/';

		load_plugin_textdomain( $domain, $rel_path, $plugin_path );
	}

	/**
	 * Localize JS scripts.
	 *
	 * @since    3.0
	 */
	public function localize_scripts() {

		$localized = array(
			'_locale'            => get_locale(),
			'_language'          => wpmoly_o( 'api-language' ),
			'_country'           => wpmoly_o( 'api-country' ),
			'_country_alt'       => wpmoly_o( 'api-country-alt' ),

			'actors'             => __( 'actors', 'wpmovielibrary' ),
			'autocomplete'       => __( 'add automatically', 'wpmovielibrary' ),
			'availableBackdrops' => __( 'Available Backdrops', 'wpmovielibrary' ),
			'availablePosters'   => __( 'Available Posters', 'wpmovielibrary' ),
			'backdrop'           => array(
				__( 'Backdrop', 'wpmovielibrary' ),
				__( 'Backdrops', 'wpmovielibrary' ),
			),
			'backdropAlt'        => wpmoly_o( 'backdrops-title', sprintf( '%s "{title}"', __( 'Image from the movie', 'wpmovielibrary' ) ) ),
			'backdropCaption'    => wpmoly_o( 'backdrops-description', sprintf( '© {release_date} {production_companies} − %s', __( 'All right reserved.', 'wpmovielibrary' ) ) ),
			'collections'        => __( 'collections', 'wpmovielibrary' ),
			'featuredImageSet'   => __( 'Featured image set!', 'wpmovielibrary' ),
			'genres'             => __( 'genres', 'wpmovielibrary' ),
			'importImages'       => __( 'Import selected images', 'wpmovielibrary' ),
			'importingMovie'     => __( 'Importing movie %s…', 'wpmovielibrary' ),
			'importingPoster'    => __( 'Importing poster…', 'wpmovielibrary' ),
			'nMoviesFound'       => array(
				__( 'No movies found!', 'wpmovielibrary' ),
				__( 'One movie found!', 'wpmovielibrary' ),
				__( '%d movies found!', 'wpmovielibrary' )
			),
			'metaSaved'          => __( 'Metadata saved!', 'wpmovielibrary' ),
			'modalTabTitle'      => __( 'Backdrops and Posters', 'wpmovielibrary' ),
			'movieImported'      => __( 'Movie %s imported successfully!', 'wpmovielibrary' ),
			'termsAutocomplete'  => __( 'or %s using the %s list.', 'wpmovielibrary' ),
			'poster'             => array(
				__( 'Poster', 'wpmovielibrary' ),
				__( 'Posters', 'wpmovielibrary' ),
			),
			'posterAlt'          => wpmoly_o( 'posters-title', sprintf( '%s "{title}"', __( 'Poster from the movie', 'wpmovielibrary' ) ) ),
			'posterCaption'      => wpmoly_o( 'posters-description', sprintf( '© {release_date} {production_companies} − %s', __( 'All right reserved.', 'wpmovielibrary' ) ) ),
			'posterImported'     => __( 'Poster successfully imported!', 'wpmovielibrary' ),
			'ready'              => __( 'Ready!', 'wpmovielibrary' ),
			'reload'             => __( 'Reload', 'wpmovielibrary' ),
			'replaceSettings'    => __( 'You’re about to save the current settings. This will erase the existing settings and apply to every search, including multiple imports.<br /><br />Are you sure you want to do that?', 'wpmovielibrary' ),
			'run'                => __( 'Run Forrest, run!', 'wpmovielibrary' ),
			'savingSettings'     => __( 'Saving settings…', 'wpmovielibrary' ),
			'searchingMovie'     => __( 'Searching movie %s…', 'wpmovielibrary' ),
			'selectedImages'     => array(
				__( 'one selected image', 'wpmovielibrary' ),
				__( '%d selected images', 'wpmovielibrary' )
			),
			'setImagesAs'        => __( 'You’re about to set %s as %s for the movie %s.<br /><br />Are you sure you want to do that?', 'wpmovielibrary' ),
			'settingFeatured'    => __( 'Setting featured image…', 'wpmovielibrary' ),
			'settingsError'      => __( 'Error: settings were not saved.', 'wpmovielibrary' ),
			'settingsSaved'      => __( 'Settings saved!', 'wpmovielibrary' ),
		);

		wp_localize_script( 'wpmoly', 'wpmolyL10n', $localized );

		$poster   = Node\DefaultPoster::get_instance();
		$backdrop = Node\DefaultBackdrop::get_instance();
		$localized = array(
			'poster'   => $poster->__get( 'sizes' ),
			'backdrop' => $backdrop->__get( 'sizes' )
		);

		wp_localize_script( 'wpmoly', 'wpmolyDefaultImages', $localized );
	}

}
