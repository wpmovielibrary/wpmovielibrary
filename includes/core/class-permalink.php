<?php
/**
 * 
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */

namespace wpmoly;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Permalink {

	/**
	 * URL rewrite ID.
	 * 
	 * @var    string
	 */
	protected $id;

	/**
	 * URL rewrite value.
	 * 
	 * @var    string
	 */
	protected $content;

	/**
	 * Permalink title.
	 * 
	 * @var    string
	 */
	protected $title;

	/**
	 * Permalink title attribute.
	 * 
	 * @var    string
	 */
	protected $attr_title;

	/**
	 * Permalink URL.
	 * 
	 * @var    string
	 */
	private $url;

	/**
	 * Permalink HTML result.
	 * 
	 * @var    string
	 */
	private $permalink;

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $params {
	 *     @type    string    $id
	 *     @type    string    $content
	 *     @type    string    $title
	 *     @type    string    $attr_title
	 * }
	 * 
	 * @return   \wpmoly\Permalink
	 */
	public function __construct( $params = array() ) {

		$params = wp_parse_args( $params, array(
			'id'         => '',
			'content'    => '',
			'title'      => '',
			'attr_title' => ''
		) );

		$this->setID( $params['id'] );
		$this->setContent( $params['content'] );
		$this->setTitle( $params['title'] );
		$this->setTitleAttr( $params['attr_title'] );
	}

	/**
	 * Set Permalink ID.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $id
	 * 
	 * @return   string
	 */
	public function setID( $id ) {

		return $this->id = sanitize_key( $id );
	}

	/**
	 * Set Permalink Content.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $content
	 * 
	 * @return   string
	 */
	public function setContent( $content ) {

		return $this->content = $content;
	}

	/**
	 * Set Permalink Title.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $title
	 * 
	 * @return   string
	 */
	public function setTitle( $title ) {

		return $this->title = esc_html( $title );
	}

	/**
	 * Set Permalink Title attribute.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $title
	 * 
	 * @return   string
	 */
	public function setTitleAttr( $title ) {

		return $this->attr_title = esc_attr( $title );
	}

	/**
	 * Output Permalink HTML result.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function toHTML() {

		if ( ! is_null( $this->permalink ) ) {
			return $this->permalink;
		}

		if ( empty( $this->toString() ) ) {
			return $this->toString();
		}

		return $this->permalink = '<a href="' . esc_url( $this->toString() ) . '" title="' . $this->attr_title . '">' . $this->title . '</a>';
	}

	/**
	 * Output Permalink URL.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function toString() {

		if ( ! is_null( $this->url ) ) {
			return $this->url;
		}

		return $this->url = $this->build();
	}

	/**
	 * Generate Permalink URL.
	 * 
	 * TODO use settings to generate base URL
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	private function build() {

		$base = home_url( '/movies' );

		return $this->url = $base . '/' . $this->id . '/' . $this->content;
	}

}
