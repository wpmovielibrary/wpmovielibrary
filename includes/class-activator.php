<?php
/**
 * Define the plugin activator class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

/**
 * Run the plugin activation tasks.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Activator {

    /**
     * Run the activation tasks.
     * 
     * @since 6.0.0
     * 
     * @static
     * @access public
     */
    public static function activate() {

        self::seed_default_terms();
        self::update_version();
    }

    /**
     * Seed the default terms.
     * 
     * @since 6.0.0
     * 
     * @static
     * @access private
     */
    private static function seed_default_terms() {
        
        //
    }

    /**
     * Update the plugin version.
     * 
     * @since 6.0.0
     * 
     * @static
     * @access private
     */
    private static function update_version() {

        update_option( 'wpmoly_version', WPMOLY_VERSION );
    }
}