<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0+
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'wpmovielibrary.php' );
WPMovieLibrary::uninstall();
