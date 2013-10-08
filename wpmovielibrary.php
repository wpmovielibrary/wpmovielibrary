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
 * @author    Charlie MERLAND <contact@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2013 CaerCam.org
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
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-wpmovielibrary.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-wpmltmdb.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-wpmllisttable.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-wpmlwidgets.php' );

register_activation_hook( __FILE__, array( 'WPMovieLibrary', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WPMovieLibrary', 'deactivate' ) );

WPMovieLibrary::get_instance();