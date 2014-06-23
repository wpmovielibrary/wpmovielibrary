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
 * Plugin URI:  http://www.caercam.org/wpmovielibrary
 * Description: A WordPress Plugin to manage a personnal library of movies.
 * Version:     1.0.0
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
define( 'WPML_VERSION',                '1.0.0' );
define( 'WPML_SLUG',                   'wpml' );
define( 'WPML_URL',                    plugins_url( basename( __DIR__ ) ) );
define( 'WPML_PATH',                   plugin_dir_path( __FILE__ ) );
define( 'WPML_REQUIRED_PHP_VERSION',   '5.3' );
define( 'WPML_REQUIRED_WP_VERSION',    '3.6' );
define( 'WPML_SETTINGS_SLUG',          'wpml_settings' );
define( 'WPML_SETTINGS_REVISION_NAME', 'settings_revision' );
define( 'WPML_SETTINGS_REVISION',      10 );
define( 'WPML_DEFAULT_POSTER_URL',     plugins_url( basename( __DIR__ ) ) . '/assets/img/no_poster.png' );
define( 'WPML_DEFAULT_POSTER_PATH',    plugin_dir_path( __FILE__ ) . '/assets/img/no_poster.png' );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-module.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/class-wpmovielibrary.php' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wpml-settings.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wpml-utils.php' );

require_once( plugin_dir_path( __FILE__ ) . 'public/movies/class-wpml-movies.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/collections/class-wpml-collections.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/genres/class-wpml-genres.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/actors/class-wpml-actors.php' );

/* Widgets */

require_once( plugin_dir_path( __FILE__ ) . 'public/movies/class-media-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/movies/class-most-rated-movies-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/movies/class-recent-movies-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/movies/class-status-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/collections/class-collections-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/genres/class-genres-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/actors/class-actors-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/statistics/class-statistics-widget.php' );

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

	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wpml-ajax.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-stats.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/dashboard/class-dashboard.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/dashboard/class-dashboard-stats-widget.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/dashboard/class-dashboard-latest-movies-widget.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/dashboard/class-dashboard-most-rated-movies-widget.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/dashboard/class-dashboard-quickaction-widget.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/dashboard/class-dashboard-helper-widget.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/api/class-tmdb.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/api/class-wpml-tmdb.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wpmovielibrary-admin.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/edit-movies/class-wpml-edit-movies.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/media/class-wpml-media.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/import/class-wpml-import-table.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/import/class-wpml-import.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/import/class-wpml-queue.php' );

	add_action( 'plugins_loaded', array( 'WPMovieLibrary_Admin', 'get_instance' ) );

}
