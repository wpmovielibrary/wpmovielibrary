<?php
/**
 * Define the Movie Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
 */

namespace wpmoly\Shortcodes;

use wpmoly\Node\Grid;
use wpmoly\Node\Headbox;
use wpmoly\Core\PublicTemplate as Template;

/**
 * General Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Movie extends Shortcode {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 * 
	 * @var    string
	 */
	public static $name = 'movie';

	/**
	 * Shortcode attributes sanitizers
	 * 
	 * @var    array
	 */
	protected $validates = array(
		'id' => array(
			'default' => '',
			'values'  => null,
			'filter'  => 'intval'
		),
		'type' => array(
			'default' => 'movie',
			'values'  => null,
			'filter'  => 'esc_attr'
		),
		'theme' => array(
			'default' => 'default',
			'values'  => null,
			'filter'  => 'esc_attr'
		)
	);

	/**
	 * Shortcode aliases
	 * 
	 * @var    array
	 */
	protected static $aliases = array(
		'movie_headbox'
	);

	/**
	 * Build the Shortcode.
	 * 
	 * Prepare Shortcode parameters.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function make() {

		$this->headbox = new Headbox( $this->attributes['id'] );
		$this->headbox->set( $this->attributes );
		$this->headbox->build();

		$template = 'headboxes/' . $this->headbox->get( 'type' ) . '-' . $this->headbox->get( 'theme' ) . '.php';

		// Set Template
		$this->template = new Template( $template );
	}

	/**
	 * Run the Shortcode.
	 * 
	 * Perform all needed Shortcode stuff.
	 * 
	 * @since    3.0
	 * 
	 * @return   Shortcode
	 */
	public function run() {

		$movie = $this->headbox->node;
		if ( $movie->is_empty() ) {
			$this->template = new Template( 'notice.php' );
				$this->template->set_data( array(
					'type'    => 'info',
					'icon'    => 'wpmolicon icon-info',
					'message' => sprintf( __( 'It seems this movie does not have any metadata available yet; %s?', 'wpmovielibrary' ), sprintf( '<a href="%s">%s</a>', get_edit_post_link(), __( 'care to add some', 'wpmovielibrary' ) ) ),
					'note'    => __( 'This notice is private; only you and other administrators can see it.', 'wpmovielibrary' )
				) );
			return $this;
		}

		$this->template->set_data( array(
			'movie' => $movie
		) );

		return $this;
	}

	/**
	 * Initialize the Shortcode.
	 * 
	 * Run things before doing anything.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function init() {}
}
