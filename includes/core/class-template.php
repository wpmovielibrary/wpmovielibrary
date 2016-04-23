<?php
/**
 * Define the Template classes.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

use WP_Error;

/**
 * General Template class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Template {

	/**
	 * Template path.
	 * 
	 * @since    3.0
	 * 
	 * @var      string
	 */
	protected $path;

	/**
	 * Template data.
	 * 
	 * @since    3.0
	 * 
	 * @var      array
	 */
	protected $data = array();

	/**
	 * Template content.
	 * 
	 * @since    3.0
	 * 
	 * @var      string
	 */
	protected $template = '';

	/**
	 * Set the Template data.
	 * 
	 * If $name is an array or an object, use it as a set of data; if not,
	 * use $name and $value as key and value.
	 * 
	 * @since    3.0
	 * 
	 * @param    mixed    $name Data name.
	 * @param    mixed    $value Data value.
	 * 
	 * @return   Template
	 */
	public function set_data( $name, $value = '' ) {

		if ( is_object( $name ) ) {
			$name = get_object_vars( $name );
		}

		if ( is_array( $name ) ) {
			foreach ( $name as $key => $data ) {
				$this->set_data( $key, $data );
			}
		} else {
			$this->data[ (string) $name ] = $value;
		}

		return $this;
	}

	/**
	 * Prepare the Template.
	 *
	 * @since    3.0
	 * 
	 * @param    string     $require 'once' to use require_once(), 'always' to use require()
	 * 
	 * @return   string
	 */
	abstract protected function prepare( $require = 'once' );

	/**
	 * Render the Template.
	 *
	 * @since    3.0
	 * 
	 * @param    string     $require 'once' to use require_once(), 'always' to use require()
	 * 
	 * @return   null
	 */
	public function render( $require = 'once', $echo = true ) {

		$this->prepare( $require );

		if ( true !== $echo ) {
			return $this->template;
		}

		echo $this->template;
	}
}

/**
 * Admin-side Template class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class AdminTemplate extends Template {

	/**
	 * Class Constructor.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $path Template file path
	 * @param    array     $data Template data
	 * 
	 * @return   Template|WP_Error
	 */
	public function __construct( $path, $data = array(), $params = array() ) {

		$path = 'admin/templates/' . (string) $path;
		if ( ! file_exists( WPMOLY_PATH . $path ) ) {
			return WP_Error( 'missing_template_path', sprintf( __( 'Error: "%s" does not exists.', 'wpmovielibrary' ), esc_attr( WPMOLY_PATH . $path ) ) );
		}

		$this->path = $path;

		return $this;
	}

	/**
	 * Prepare the Template.
	 *
	 * Unlike PublicTemplate::prepare() we don't allow themes/plugins to
	 * replace admin templates, only to act before and after the template
	 * is prepared.
	 *
	 * @since    3.0
	 * 
	 * @param    string     $require 'once' to use require_once(), 'always' to use require()
	 * 
	 * @return   string
	 */
	protected function prepare( $require = 'once' ) {

		/**
		 * Fired before starting to prepare the template.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $path Plugin-relative file path
		 * @param    array     $data Template data
		 */
		do_action( "wpmoly/render/admin/template/pre", $this->path, $this->data );

		$template = $this->locate_template();
		if ( is_file( $template ) ) {

			extract( $this->data );
			ob_start();

			if ( 'always' == $require ) {
				require( $template );
			} else {
				require_once( $template );
			}

			$this->template = ob_get_clean();
		}

		/**
		 * Fired after the template preparation.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $template Template content
		 * @param    string    $path Plugin-relative file path
		 * @param    string    $template WordPress-relative file path
		 * @param    array     $data Template data
		 */
		do_action( "wpmoly/render/admin/template/after", $this->template, $this->path, $template, $this->data );

		return $this->template;
	}

	/**
	 * Admin Templates should always be in the plugins directory.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	private function locate_template() {

		return $template = WPMOLY_PATH . $this->path;
	}
}

/**
 * Public-side Template class.
 * 
 * Public Templates are allowed more customization and interaction than Admin
 * Template, including filtering and template replacement.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class PublicTemplate extends Template {

	/**
	 * Class Constructor.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $path Template file path
	 * @param    array     $data Template data
	 * 
	 * @return   Template|WP_Error
	 */
	public function __construct( $path, $data = array(), $params = array() ) {

		$path = 'public/templates/' . (string) $path;
		if ( ! file_exists( WPMOLY_PATH . $path ) ) {
			return WP_Error( 'missing_template_path', sprintf( __( 'Error: "%s" does not exists.', 'wpmovielibrary' ), esc_attr( WPMOLY_PATH . $path ) ) );
		}

		$this->path = $path;

		return $this;
	}

	/**
	 * Prepare the Template.
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
	 * 
	 * @return   string
	 */
	public function prepare( $require = 'once' ) {

		/**
		 * Fired before starting to prepare the template.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $path Plugin-relative file path
		 * @param    array     $data Template data
		 */
		do_action( "wpmoly/render/template/pre", $this->path, $this->data );

		$template = $this->locate_template();
		if ( is_file( $template ) ) {

			extract( $this->data );
			ob_start();

			if ( 'always' == $require ) {
				require( $template );
			} else {
				require_once( $template );
			}

			$content = ob_get_clean();

			/**
			 * Filter the template content.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $content Plugin-relative file path
			 * @param    string    $path Plugin-relative file path
			 * @param    string    $template WordPress-relative file path
			 * @param    array     $data Template data
			 */
			$this->template = apply_filters( "wpmoly/filter/template/content", $content, $this->path, $template, $this->data );
		}

		/**
		 * Fired after the template preparation.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $template Template content
		 * @param    string    $path Plugin-relative file path
		 * @param    string    $template WordPress-relative file path
		 * @param    array     $data Template data
		 */
		do_action( "wpmoly/render/template/after", $this->template, $this->path, $template, $this->data );

		return $this->template;
	}

	/**
	 * Public Templates can be overriden by themes.
	 * 
	 * A theme implementing its own WPMovieLibrary templates should have a
	 * 'wpmovielibrary' folders at its root with an organization conform to
	 * the plugin's templates file organization.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	private function locate_template(  ) {

		$template = locate_template( 'wpmovielibrary/' . $this->path, false, false );
		if ( ! $template ) {
			$template = WPMOLY_PATH . $this->path;
		}

		/**
		 * Filter the template filepath.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $path Plugin-relative file path
		 * @param    string    $template WordPress-relative file path
		 * @param    array     $data Template data
		 */
		return $template = apply_filters( "wpmoly/filter/template/path", $this->path, $template, $this->data );
	}
}
