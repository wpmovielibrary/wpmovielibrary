<?php
/**
 * WordPress Movie Library Plugin
 *
 * WPMOLY is a WordPress Plugin designed to handle a personnal library of
 * movies. Using Custom Post Type you can add new movies, manage collections,
 * write reviews and share your latest-seen/favorite movies.
 * WPMOLY uses TMDb to gather movies' informations.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 *
 * @wordpress-plugin
 * Plugin Name: WPMovieLibrary
 * Plugin URI:  http://wpmovielibrary.com
 * Description: A WordPress Plugin to manage a personnal library of movies.
 * Version:     2.1.4.7
 * Author:      Charlie MERLAND
 * Author URI:  http://www.caercam.org/
 * Text Domain: wpmovielibrary
 * License:     GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/wpmovielibrary/wpmovielibrary
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPMOLY_PLUGIN',                 plugin_basename( __FILE__ ) );
define( 'WPMOLY_NAME',                   'WPMovieLibrary' );
define( 'WPMOLY_VERSION',                '2.1.4.7' );
define( 'WPMOLY_SLUG',                   'wpmoly' );
define( 'WPMOLY_URL',                    plugins_url( basename( __DIR__ ) ) );
define( 'WPMOLY_PATH',                   plugin_dir_path( __FILE__ ) );
define( 'WPMOLY_REQUIRED_PHP_VERSION',   '5.3' );
define( 'WPMOLY_REQUIRED_WP_VERSION',    '4.2' );
define( 'WPMOLY_DEFAULT_POSTER_URL',     plugins_url( basename( __DIR__ ) ) . '/assets/img/no_poster{size}.jpg' );
define( 'WPMOLY_DEFAULT_POSTER_PATH',    WPMOLY_PATH . '/assets/img/no_poster{size}.jpg' );
define( 'WPMOLY_MAX_TAXONOMY_LIST',      50 );



/**
 * Checks if the system requirements are met
 * 
 * @since    1.0.1
 * 
 * @return   bool    True if system requirements are met, false if not
 */
function wpmoly_requirements_met() {

	global $wp_version;

	if ( version_compare( PHP_VERSION, WPMOLY_REQUIRED_PHP_VERSION, '<' ) )
		return false;

	if ( version_compare( $wp_version, WPMOLY_REQUIRED_WP_VERSION, '<' ) )
		return false;

	return true;
}

/**
 * Prints an error that the system requirements weren't met.
 * 
 * @since    1.0.1
 */
function wpmoly_requirements_error() {
	global $wp_version;

	require_once WPMOLY_PATH . 'views/admin/requirements-error.php';
}

/**
 * Prints an error that the system requirements weren't met.
 * 
 * @since    1.0.1
 */
function wpmoly_l10n() {

	$domain = 'wpmovielibrary';
	$domain_iso = 'wpmovielibrary-iso';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	$locale_iso = apply_filters( 'plugin_locale', get_locale(), $domain_iso );

	load_textdomain( $domain, WPMOLY_PATH . $domain . '/languages/'. $domain . '-' . $locale . '.mo' );
	load_textdomain( $domain_iso, WPMOLY_PATH . $domain . '/languages/'. $domain_iso . '-' . $locale . '.mo' );
	load_plugin_textdomain( $domain, FALSE, basename( __DIR__ ) . '/languages/' );
	load_plugin_textdomain( $domain_iso, FALSE, basename( __DIR__ ) . '/languages/' );
}

wpmoly_l10n();

/*
 * Check requirements and load main class
 * The main program needs to be in a separate file that only gets loaded if the
 * plugin requirements are met. Otherwise older PHP installations could crash
 * when trying to parse it.
 */
if ( wpmoly_requirements_met() ) {

	/*----------------------------------------------------------------------------*
	 * Public-Facing Functionality
	 *----------------------------------------------------------------------------*/

	// Core
	require_once( WPMOLY_PATH . 'includes/functions/wpmoly-core-functions.php' );
	require_once( WPMOLY_PATH . 'includes/classes/class-module.php' );
	require_once( WPMOLY_PATH . 'public/class-wpmovielibrary.php' );

	// Basics
	require_once( WPMOLY_PATH . 'includes/classes/class-wpmoly-settings.php' );
	if ( ! class_exists( 'ReduxFramework' ) )
		require_once( WPMOLY_PATH . 'includes/framework/redux/ReduxCore/framework.php' );
	if ( ! isset( $wpmoly_settings ) )
		require_once( WPMOLY_PATH . 'includes/classes/class-wpmoly-redux.php' );
	//require_once( WPMOLY_PATH . 'includes/framework/redux/sample-config.php' );
	require_once( WPMOLY_PATH . 'includes/classes/class-wpmoly-cache.php' );
	require_once( WPMOLY_PATH . 'includes/classes/class-wpmoly-l10n.php' );
	require_once( WPMOLY_PATH . 'includes/classes/class-wpmoly-utils.php' );

	// CPT and Taxo
	require_once( WPMOLY_PATH . 'public/class-wpmoly-movies.php' );
	require_once( WPMOLY_PATH . 'public/class-wpmoly-headbox.php' );
	require_once( WPMOLY_PATH . 'public/class-wpmoly-grid.php' );
	require_once( WPMOLY_PATH . 'public/class-wpmoly-search.php' );
	require_once( WPMOLY_PATH . 'public/class-wpmoly-collections.php' );
	require_once( WPMOLY_PATH . 'public/class-wpmoly-genres.php' );
	require_once( WPMOLY_PATH . 'public/class-wpmoly-actors.php' );
	require_once( WPMOLY_PATH . 'public/class-wpmoly-archives.php' );

	// Self-speaking
	require_once( WPMOLY_PATH . 'public/class-wpmoly-shortcodes.php' );

	// Widgets
	require_once( WPMOLY_PATH . 'includes/classes/class-wpmoly-widget.php' );
	require_once( WPMOLY_PATH . 'includes/widgets/class-statistics-widget.php' );
	require_once( WPMOLY_PATH . 'includes/widgets/class-taxonomies-widget.php' );
	require_once( WPMOLY_PATH . 'includes/widgets/class-details-widget.php' );
	require_once( WPMOLY_PATH . 'includes/widgets/class-movies-widget.php' );

	// Legacy
	require_once( WPMOLY_PATH . 'includes/classes/legacy/class-wpmoly-legacy.php' );

	/*
	 * Register hooks that are fired when the plugin is activated or deactivated.
	 * When the plugin is deleted, the uninstall.php file is loaded.
	 */
	if ( class_exists( 'WPMovieLibrary' ) ) {
		$GLOBALS['wpmoly'] = WPMovieLibrary::get_instance();
		register_activation_hook(   __FILE__, array( $GLOBALS['wpmoly'], 'activate' ) );
		register_deactivation_hook( __FILE__, array( $GLOBALS['wpmoly'], 'deactivate' ) );
	}


	/*----------------------------------------------------------------------------*
	 * Dashboard and Administrative Functionality
	 *----------------------------------------------------------------------------*/

	/*
	 * The code below is intended to to give the lightest footprint possible.
	 */
	if ( is_admin() ) {

		require_once( WPMOLY_PATH . 'includes/classes/class-wpmoly-ajax.php' );
		require_once( WPMOLY_PATH . 'admin/class-wpmoly-admin.php' );
		require_once( WPMOLY_PATH . 'admin/class-dashboard.php' );
		require_once( WPMOLY_PATH . 'admin/class-dashboard-stats-widget.php' );
		require_once( WPMOLY_PATH . 'admin/class-dashboard-latest-movies-widget.php' );
		require_once( WPMOLY_PATH . 'admin/class-dashboard-most-rated-movies-widget.php' );
		require_once( WPMOLY_PATH . 'admin/class-dashboard-quickaction-widget.php' );
		require_once( WPMOLY_PATH . 'admin/class-dashboard-helper-widget.php' );
		require_once( WPMOLY_PATH . 'admin/class-dashboard-vendor-widget.php' );
		require_once( WPMOLY_PATH . 'admin/class-wpmoly-api.php' );
		require_once( WPMOLY_PATH . 'admin/class-wpmoly-api-wrapper.php' );
		require_once( WPMOLY_PATH . 'admin/class-wpmoly-diagnose.php' );
		require_once( WPMOLY_PATH . 'admin/class-wpmoly-metaboxes.php' );
		require_once( WPMOLY_PATH . 'admin/class-wpmoly-edit-movies.php' );
		require_once( WPMOLY_PATH . 'admin/class-wpmoly-media.php' );
		require_once( WPMOLY_PATH . 'admin/class-wpmoly-list-table.php' );
		require_once( WPMOLY_PATH . 'admin/class-wpmoly-import-table.php' );
		require_once( WPMOLY_PATH . 'admin/class-wpmoly-import-queue.php' );
		require_once( WPMOLY_PATH . 'admin/class-wpmoly-import.php' );

		add_action( 'plugins_loaded', array( 'WPMovieLibrary_Admin', 'get_instance' ) );

	}
}
else {
	add_action( 'admin_notices', 'wpmoly_requirements_error' );
}
