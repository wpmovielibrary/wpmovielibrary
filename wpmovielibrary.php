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
 * @license   GPL-3.0+
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
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/Askelon/WPMovieLibrary
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-wpmovielibrary.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'WPMovieLibrary', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WPMovieLibrary', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'WPMovieLibrary', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wpmovielibrary-admin.php' );

	add_action( 'plugins_loaded', array( 'WPMovieLibrary_Admin', 'get_instance' ) );

}
