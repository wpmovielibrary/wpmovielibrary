<?php
/**
 * WordPress Movie Library Plugin
 *
 * WPML is a WordPress Plugin designed to handle a personnal library of
 * movies. Using Custom Post Type you can add new movies, manage collections,
 * write reviews and share your latest-seen/favorite movies.
 * WPML uses TMDb to gather movies' informations.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 *
 * @wordpress-plugin
 * Plugin Name: WPMovieLibrary
 * Plugin URI:  http://wpmovielibrary.com
 * Description: A WordPress Plugin to manage a personnal library of movies.
 * Version:     1.1.2
 * Author:      Charlie MERLAND
 * Author URI:  http://www.caercam.org/
 * Text Domain: wpml
 * License:     GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/Askelon/WPMovieLibrary
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPML_NAME',                   'WPMovieLibrary' );
define( 'WPML_VERSION',                '1.1.2' );
define( 'WPML_SLUG',                   'wpml' );
define( 'WPML_URL',                    plugins_url( basename( __DIR__ ) ) );
define( 'WPML_PATH',                   plugin_dir_path( __FILE__ ) );
define( 'WPML_REQUIRED_PHP_VERSION',   '5.3' );
define( 'WPML_REQUIRED_WP_VERSION',    '3.6' );
define( 'WPML_SETTINGS_SLUG',          'wpml_settings' );
define( 'WPML_SETTINGS_REVISION_NAME', 'settings_revision' );
define( 'WPML_SETTINGS_REVISION',      12 );
define( 'WPML_DEFAULT_POSTER_URL',     plugins_url( basename( __DIR__ ) ) . '/assets/img/no_poster{size}.jpg' );
define( 'WPML_DEFAULT_POSTER_PATH',    WPML_PATH . '/assets/img/no_poster{size}.jpg' );
define( 'WPML_MAX_TAXONOMY_LIST',      50 );



/**
 * Checks if the system requirements are met
 * 
 * @since    1.0.1
 * 
 * @return   bool    True if system requirements are met, false if not
 */
function wpml_requirements_met() {

	global $wp_version;

	if ( version_compare( PHP_VERSION, WPML_REQUIRED_PHP_VERSION, '<' ) )
		return false;

	if ( version_compare( $wp_version, WPML_REQUIRED_WP_VERSION, '<' ) )
		return false;

	return true;
}

/**
 * Prints an error that the system requirements weren't met.
 * 
 * @since    1.0.1
 */
function wpml_requirements_error() {
	global $wp_version;

	require_once WPML_PATH . 'admin/common/views/requirements-error.php';
}

/**
 * Prints an error that the system requirements weren't met.
 * 
 * @since    1.0.1
 */
function wpml_l10n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), WPML_SLUG );
	load_textdomain( WPML_SLUG, trailingslashit( WP_LANG_DIR ) . basename( __DIR__ ) . '/languages/' . WPML_SLUG . '-' . $locale . '.mo' );
	load_plugin_textdomain( WPML_SLUG, FALSE, basename( __DIR__ ) . '/languages/' );
}

/*
 * Check requirements and load main class
 * The main program needs to be in a separate file that only gets loaded if the
 * plugin requirements are met. Otherwise older PHP installations could crash
 * when trying to parse it.
 */
if ( wpml_requirements_met() ) {

	/*----------------------------------------------------------------------------*
	 * Public-Facing Functionality
	 *----------------------------------------------------------------------------*/

	require_once( WPML_PATH . 'includes/class-module.php' );
	require_once( WPML_PATH . 'public/class-wpmovielibrary.php' );

	require_once( WPML_PATH . 'includes/class-wpml-settings.php' );
	require_once( WPML_PATH . 'includes/class-wpml-utils.php' );

	require_once( WPML_PATH . 'public/movies/class-wpml-movies.php' );
	require_once( WPML_PATH . 'public/collections/class-wpml-collections.php' );
	require_once( WPML_PATH . 'public/genres/class-wpml-genres.php' );
	require_once( WPML_PATH . 'public/actors/class-wpml-actors.php' );

	/* Widgets */

	require_once( WPML_PATH . 'public/movies/class-media-widget.php' );
	require_once( WPML_PATH . 'public/movies/class-most-rated-movies-widget.php' );
	require_once( WPML_PATH . 'public/movies/class-recent-movies-widget.php' );
	require_once( WPML_PATH . 'public/movies/class-status-widget.php' );
	require_once( WPML_PATH . 'public/collections/class-collections-widget.php' );
	require_once( WPML_PATH . 'public/genres/class-genres-widget.php' );
	require_once( WPML_PATH . 'public/actors/class-actors-widget.php' );
	require_once( WPML_PATH . 'public/statistics/class-statistics-widget.php' );
	require_once( WPML_PATH . 'public/shortcodes/class-shortcodes.php' );

	/*
	 * Register hooks that are fired when the plugin is activated or deactivated.
	 * When the plugin is deleted, the uninstall.php file is loaded.
	 */
	if ( class_exists( 'WPMovieLibrary' ) ) {
		$GLOBALS['wpml'] = WPMovieLibrary::get_instance();
		register_activation_hook(   __FILE__, array( $GLOBALS['wpml'], 'activate' ) );
		register_deactivation_hook( __FILE__, array( $GLOBALS['wpml'], 'deactivate' ) );
	}


	/*----------------------------------------------------------------------------*
	 * Dashboard and Administrative Functionality
	 *----------------------------------------------------------------------------*/

	/*
	 * The code below is intended to to give the lightest footprint possible.
	 */
	if ( is_admin() ) {

		require_once( WPML_PATH . 'includes/class-wpml-ajax.php' );
		require_once( WPML_PATH . 'includes/class-stats.php' );
		require_once( WPML_PATH . 'admin/dashboard/class-dashboard.php' );
		require_once( WPML_PATH . 'admin/dashboard/class-dashboard-stats-widget.php' );
		require_once( WPML_PATH . 'admin/dashboard/class-dashboard-latest-movies-widget.php' );
		require_once( WPML_PATH . 'admin/dashboard/class-dashboard-most-rated-movies-widget.php' );
		require_once( WPML_PATH . 'admin/dashboard/class-dashboard-quickaction-widget.php' );
		require_once( WPML_PATH . 'admin/dashboard/class-dashboard-helper-widget.php' );
		require_once( WPML_PATH . 'admin/api/class-tmdb.php' );
		require_once( WPML_PATH . 'admin/api/class-wpml-tmdb.php' );
		require_once( WPML_PATH . 'admin/class-wpmovielibrary-admin.php' );
		require_once( WPML_PATH . 'admin/edit-movies/class-wpml-edit-movies.php' );
		require_once( WPML_PATH . 'admin/media/class-wpml-media.php' );
		require_once( WPML_PATH . 'admin/import/class-wpml-import-table.php' );
		require_once( WPML_PATH . 'admin/import/class-wpml-import.php' );
		require_once( WPML_PATH . 'admin/import/class-wpml-queue.php' );

		add_action( 'plugins_loaded', array( 'WPMovieLibrary_Admin', 'get_instance' ) );

	}
}
else {
	add_action( 'init', 'wpml_l10n' );
	add_action( 'admin_notices', 'wpml_requirements_error' );
}
