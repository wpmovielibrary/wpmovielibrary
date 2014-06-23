<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0+
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-module.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/class-wpmovielibrary.php' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wpml-settings.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wpml-utils.php' );

require_once( plugin_dir_path( __FILE__ ) . 'public/movies/class-wpml-movies.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/collections/class-wpml-collections.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/genres/class-wpml-genres.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/actors/class-wpml-actors.php' );

WPMovieLibrary::uninstall();
