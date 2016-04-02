<?php
/**
 * Define the options.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) || ! isset( $wpmoly_loading_config ) ) {
	wp_die();
}

require_once WPMOLY_PATH . 'includes/config/wpmoly-languages.php';
require_once WPMOLY_PATH . 'includes/config/wpmoly-options.php';
require_once WPMOLY_PATH . 'includes/config/wpmoly-movies.php';
require_once WPMOLY_PATH . 'includes/config/wpmoly-admin-bar-menu.php';

if ( is_admin() ) {
	require_once WPMOLY_PATH . 'includes/config/wpmoly-admin-menu.php';
	require_once WPMOLY_PATH . 'includes/config/wpmoly-admin-dashboard.php';
}
$wpmoly_loading_config = false;
