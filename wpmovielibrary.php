<?php
/**
 * wpMovieLibrary - A WordPress Plugin to manage a personnal library of movies.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      https://www.caercam.org/
 * @copyright 2013-2026 Charlie MERLAND
 *
 * @wordpress-plugin
 * Plugin Name: wpMovieLibrary
 * Plugin URI:  https://wplibraries.com
 * Description: A WordPress Plugin to manage a personnal library of movies.
 * Version:     6.0.0
 * Author:      Charlie MERLAND
 * Author URI:  https://www.caercam.org/
 * Text Domain: wpmovielibrary
 * License:     GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/wpmovielibrary/wpmovielibrary
 */

namespace WPMovieLibrary;

// Make sure we don't expose any info if called directly.
if ( ! function_exists( 'add_action' ) ) {
	exit;
}

define( 'WPMOLY_VERSION', '6.0.0' );
define( 'WPMOLY_PATH',    trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WPMOLY_URL',     trailingslashit( plugin_dir_url( __FILE__ ) ) );

require_once WPMOLY_PATH . 'includes/class-library.php';
require_once WPMOLY_PATH . 'includes/support/class-activator.php';

register_activation_hook( __FILE__, [ 'WPMovieLibrary\Support\Activator', 'activate' ] );

Library::get_instance();
