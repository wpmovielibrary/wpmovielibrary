<?php
/**
 * Define the Template classes.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\templates;

use WP_Error;

/**
 * Admin-side Template class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Admin extends Template {

	/**
	 * Class Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $path Template file path
	 * @param array $data Template data
	 *
	 * @return Template|WP_Error
	 */
	public function __construct( $path, $data = array(), $params = array() ) {

		$path = 'admin/templates/' . (string) $path;
		if ( ! file_exists( WPMOLY_PATH . $path ) ) {
			return new WP_Error( 'missing_template_path', sprintf( __( 'Error: "%s" does not exists.', 'wpmovielibrary' ), esc_attr( WPMOLY_PATH . $path ) ) );
		}

		$this->path = $path;

		return $this;
	}

	/**
	 * Prepare the Template.
	 *
	 * Unlike \wpmoly\templates\Front::prepare() we don't allow themes or
	 * plugins to replace admin templates, only to act before and after the
	 * template is prepared.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param string $require 'once' to use require_once(), 'always' to use require()
	 *
	 * @return string
	 */
	protected function prepare( $require = 'once' ) {

		/**
		 * Fired before starting to prepare the template.
		 *
		 * @since 3.0.0
		 *
		 * @param string $path Plugin-relative file path
		 * @param array $data Template data
		 */
		do_action( 'wpmoly/render/admin/template/pre', $this->path, $this->data );

		$template = $this->locate_template();
		if ( is_file( $template ) ) {

			/**
			 * Filter the template data.
			 *
			 * @since 3.0.0
			 *
			 * @param array $data Template data
			 * @param string $template WordPress-relative file path
			 * @param string $path Plugin-relative file path
			 */
			$this->data = apply_filters( 'wpmoly/filter/template/data', $this->data, $template, $this->path );

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
		 * @since 3.0.0
		 *
		 * @param string $template Template content
		 * @param string $path Plugin-relative file path
		 * @param string $template WordPress-relative file path
		 * @param array $data Template data
		 */
		do_action( 'wpmoly/render/admin/template/after', $this->template, $this->path, $template, $this->data );

		return $this->template;
	}

	/**
	 * Admin Templates should always be in the plugins directory.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @return string
	 */
	private function locate_template() {

		$template = WPMOLY_PATH . $this->path;

		return $template;
	}

}
