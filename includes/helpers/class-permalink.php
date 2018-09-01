<?php
/**
 * Define the permalink class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\helpers;

/**
 * Handle custom permalinks.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Permalink {

	/**
	 * URL rewrite ID.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * URL rewrite value.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * Permalink title.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Permalink title attribute.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $attr_title;

	/**
	 * Permalink URL.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Permalink HTML result.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $permalink;

	/**
	 * Permalink title type, 'text' or 'html'.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $title_type = 'text';

	/**
	 * Class constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $params {
	 *     @type    string    $id
	 *     @type    string    $content
	 *     @type    string    $title
	 *     @type    string    $attr_title
	 * }
	 */
	public function __construct( $params = array() ) {

		$params = wp_parse_args( $params, array(
			'id'         => '',
			'content'    => '',
			'title'      => '',
			'attr_title' => '',
		) );

		$this->set_id( $params['id'] );
		$this->set_content( $params['content'] );
		$this->set_title( $params['title'] );
		$this->set_title_attr( $params['attr_title'] );
	}

	/**
	 * Set Permalink ID.
	 *
	 * Uses sanitize_key() to automatically clean value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	public function set_id( $id ) {

		$this->id = sanitize_key( $id );

		return $this->id;
	}

	/**
	 * Set Permalink Content.
	 *
	 * Content is not sanitized at this point as it will be escaped latter
	 * by esc_url().
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function set_content( $content ) {

		$this->content = $content;

		return $this->content;
	}

	/**
	 * Set Permalink Title.
	 *
	 * Uses esc_html() and wp_kses() to sanitize the title.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function set_title( $title, $format = 'text' ) {

		if ( 'html' == $format ) {

			$allowed_html = array(
				'b'    => array(),
				'i'    => array(),
				'u'    => array(),
				'em'   => array(),
				'del'  => array(),
				'ins'  => array(),
				'span' => array(
					'title' => array(),
					'class' => array(),
					'id'    => array(),
				),
				'strong' => array(),
			);

			/**
			 * Filter permalinks list of allowed HTML tags for title.
			 *
			 * @since 3.0.0
			 *
			 * @param array $allowed_html Default allowed HTML tags.
			 * @param string $title Permalink title value.
			 * @param object $permalink \wpmoly\Permalink instance.
			 */
			$allowed_html = apply_filters( 'wpmoly/filter/permalinks/title/allowed_html', $allowed_html, $title, $this );

			$this->title = wp_kses( $title, $allowed_html );
		} else {
			$this->title = esc_html( $title );
		}

		return $this->title;
	}

	/**
	 * Set Permalink Title attribute.
	 *
	 * Uses esc_attr() to sanitize the attribute.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function set_title_attr( $title ) {

		$this->attr_title = esc_attr( $title );

		return $this->attr_title;
	}

	/**
	 * Output Permalink HTML result.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function to_html() {

		if ( ! is_null( $this->permalink ) ) {
			return $this->permalink;
		}

		if ( empty( $this->to_string() ) ) {
			return $this->to_string();
		}

		$this->permalink = '<a href="' . esc_url( $this->to_string() ) . '" title="' . $this->attr_title . '">' . $this->title . '</a>';

		return $this->permalink;
	}

	/**
	 * Output Permalink URL.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function to_string() {

		if ( ! is_null( $this->url ) ) {
			return $this->url;
		}

		$this->url = $this->build();

		return $this->url;
	}

	/**
	 * Generate Permalink URL.
	 *
	 * TODO use settings to generate base URL
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @return string
	 */
	private function build() {

		$this->url = home_url( "/movies/{$this->id}/{$this->content}" );

		return $this->url;
	}

}
