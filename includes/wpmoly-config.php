<?php
/**
 * WPMovieLibrary Default Config
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
	wp_die();

require_once WPML_PATH . '/includes/config/wpmoly-settings.php';
require_once WPML_PATH . '/includes/config/wpmoly-movies.php';
require_once WPML_PATH . '/includes/config/wpmoly-shortcodes.php';
require_once WPML_PATH . '/includes/config/wpmoly-admin-menu.php';
require_once WPML_PATH . '/includes/config/wpmoly-admin-dashboard.php';

