<?php
/**
 * Define the Template class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Template {

	/**
	 * Template ID.
	 * 
	 * @since    3.0
	 * 
	 * @var      int
	 */
	public $id;

	/**
	 * Template path.
	 * 
	 * @since    3.0
	 * 
	 * @var      string
	 */
	public $path;

	/**
	 * Template data.
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	public $data = array();

	/**
	 * Template content.
	 * 
	 * @since    3.0
	 * 
	 * @var      string
	 */
	public $template = '';

	/**
	 * Class Constructor.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $path Template file path
	 * @param    array     $data Template data
	 * 
	 * @return   null
	 */
	public function __construct( $path, $data = array() ) {

		$path = (string) $path;
		if ( is_admin() ) {
			$path = 'admin/templates/' . $path;
		} else {
			$path = 'public/templates/' . $path;
		}

		if ( ! file_exists( WPMOLY_PATH . $path ) ) {
			return $this->template;
		}

		$this->path = $path;
	}

	/**
	 * Prepare template
	 *
	 * Allows parent/child themes to override the markup by placing a file
	 * named basename( $default_template_path ) in their root folder, and
	 * also allows plugins or themes to override the markup by a filter.
	 * 
	 * Themes might prefer that method if they place their templates in
	 * sub-directories to avoid cluttering the root folder. In both cases,
	 * the theme/plugin will have access to the variables so they can fully
	 * customize the output.
	 *
	 * @since    3.0
	 * 
	 * @param    string     $require 'once' to use require_once(), 'always' to use require()
	 * @param    boolean    $admin Admin side?
	 * 
	 * @return   string
	 */
	public function prepare( $require = 'once', $admin = false ) {

		$admin = '';
		if ( true === $admin ) {
			$admin = 'admin/';
		}

		do_action( "wpmoly/render/{$admin}template/pre", $this->path, $this->data );

		$template_path = locate_template( 'wpmovielibrary/' . $this->path, false, false );
		if ( ! $template_path ) {
			if ( true === $admin ) {
				$template_path = WPMOLY_PATH . $this->path;
			} else {
				$template_path = WPMOLY_PATH . $this->path;
			}
		}

		$template_path = apply_filters( "wpmoly/filter/{$admin}template/path", $template_path );

		if ( is_file( $template_path ) ) {

			extract( $this->data );
			ob_start();

			if ( 'always' == $require ) {
				require( $template_path );
			} else {
				require_once( $template_path );
			}

			$this->template = apply_filters( "wpmoly/filter/{$admin}template/content", ob_get_clean(), $this->path, $template_path, $this->data );
		} else {
			$this->template = '';
		}

		do_action( "wpmoly/render/{$admin}template/after", $this->path, $this->data, $template_path, $this->template );

		return $this->template;
	}

	/**
	 * Render template.
	 *
	 * @since    3.0
	 * 
	 * @param    string     $require 'once' to use require_once(), 'always' to use require()
	 * @param    boolean    $admin Admin side?
	 * 
	 * @return   null
	 */
	public function render( $require = 'once', $admin = false ) {

		$this->prepare( $require, $admin );

		echo $this->template;
	}
}