<?php

if ( ! class_exists( 'WPMOLY_Module' ) ) {

	/**
	 * Abstract class to define/implement base methods for all module classes
	 */
	abstract class WPMOLY_Module {

		private static $instances = array();

		/**
		 * Provides access to a single instance of a module using the singleton pattern
		 *
		 * @return   object
		 */
		public static function get_instance() {

			$module = get_called_class();

			if ( ! isset( self::$instances[ $module ] ) ) {
				self::$instances[ $module ] = new $module();
			}

			return self::$instances[ $module ];
		}

		/**
		 * Render an admin template
		 * 
		 * Simple alias of WPMOLY_Module::render_template() adding admin
		 * views path and hooks.
		 *
		 * @since    2.0
		 * 
		 * @param    string    $default_template_path The path to the template, relative to the plugin's `views` folder
		 * @param    array     $variables An array of variables to pass into the template's scope, indexed with the variable name so that it can be extract()-ed
		 * @param    string    $require 'once' to use require_once() | 'always' to use require()
		 * 
		 * @return   string
		 */
		public static function render_admin_template( $default_template_path = false, $variables = array(), $require = 'once' ) {

			return self::render_template( 'admin/' . $default_template_path, $variables, $require, $admin = true );
		}

		/**
		 * Render a template
		 *
		 * Allows parent/child themes to override the markup by placing 
		 * the a file named basename( $default_template_path ) in their 
		 * root folder, and also allows plugins or themes to override the 
		 * markup by a filter. Themes might prefer that method if they 
		 * place their templates in sub-directories to avoid cluttering 
		 * the root folder. In both cases, the theme/plugin will have 
		 * access to the variables so they can fully customize the output.
		 *
		 * @since    1.2
		 * 
		 * @param    string    $default_template_path The path to the template, relative to the plugin's `views` folder
		 * @param    array     $variables An array of variables to pass into the template's scope, indexed with the variable name so that it can be extract()-ed
		 * @param    string    $require 'once' to use require_once() | 'always' to use require()
		 * 
		 * @return   string
		 */
		public static function render_template( $default_template_path = false, $variables = array(), $require = 'once', $admin = false ) {

			$admin = ( true === $admin ? 'admin_' : '' );

			do_action( "wpmoly_render_{$admin}template_pre", $default_template_path, $variables );

			$template_path = locate_template( 'wpmovielibrary/' . $default_template_path, false, false );
			if ( ! $template_path )
				$template_path = WPMOLY_PATH . '/views/' . $default_template_path;

			$template_path = apply_filters( "wpmoly_{$admin}template_path", $template_path );

			if ( is_file( $template_path ) ) {

				extract( $variables );
				ob_start();

				if ( 'always' == $require )
					require( $template_path );
				else
					require_once( $template_path );

				$template_content = apply_filters( "wpmoly_{$admin}template_content", ob_get_clean(), $default_template_path, $template_path, $variables );
			}
			else
				$template_content = '';

			do_action( "wpmoly_render_{$admin}template_after", $default_template_path, $variables, $template_path, $template_content );

			return $template_content;
		}

		/**
		 * Set the uninstallation instructions
		 */
		public static function uninstall() {}

		/**
		 * Constructor
		 */
		abstract protected function __construct();

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @param    bool    $network_wide
		 */
		abstract public function activate( $network_wide );

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 */
		abstract public function deactivate();

		/**
		 * Register callbacks for actions and filters
		 */
		abstract public function register_hook_callbacks();

		/**
		 * Initializes variables
		 */
		abstract public function init();
	} // end WPMOLY_Module
}