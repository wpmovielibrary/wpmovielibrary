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

require_once WPML_PATH . '/includes/config/wpml-settings.php';
require_once WPML_PATH . '/includes/config/wpml-movies.php';
require_once WPML_PATH . '/includes/config/wpml-shortcodes.php';
require_once WPML_PATH . '/includes/config/wpml-admin-menu.php';
require_once WPML_PATH . '/includes/config/wpml-admin-dashboard.php';

