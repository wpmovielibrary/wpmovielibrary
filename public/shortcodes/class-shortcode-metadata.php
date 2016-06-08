<?php
/**
 * Define the Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Shortcodes;

use WP_Query;
use wpmoly\Collection;
use wpmoly\Core\PublicTemplate as Template;

/**
 * General Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Metadata extends Shortcode {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 * 
	 * @var    string
	 */
	public static $name = 'movie_meta';

	/**
	 * Shortcode attributes sanitizers
	 * 
	 * @var    array
	 */
	protected $validates = array(
		'id' => array(
			'default' => false,
			'values'  => null,
			'filter'  => 'intval'
		),
		'title' => array(
			'default' => null,
			'values'  => null,
			'filter'  => 'esc_attr'
		),
		'label' => array(
			'default' => true,
			'values'  => null,
			'filter'  => 'boolval'
		),
		'key' => array(
			'default' => false,
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
		'movie_release_date' => 'release_date'
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

		if ( ! is_null( $this->tag ) && isset( self::$aliases[ $this->tag ] ) ) {
			$this->set( 'key', self::$aliases[ $this->tag ] );
		}

		// Set Template
		if ( false !== $this->attributes['label'] ) {
			$template = 'shortcodes/metadata-label.php';
		} else {
			$template = 'shortcodes/metadata.php';
		}

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

		global $wpdb;

		// Get movie by title
		if ( ! is_null( $this->attributes['title'] ) ) {

			$like = $wpdb->esc_like( $this->attributes['title'] );
			$like = '%' . $like . '%';

			$post_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE %s",
					$like
				)
			);

			$this->attributes['id'] = $post_id;
		}

		$key     = (string) $this->attributes['key'];
		$post_id = (int) $this->attributes['id'];

		// Get value
		$meta = get_movie_meta( $post_id, $key, $single = true );

		// Get label
		$label = wpmoly_o( 'default_meta' );
		$label = isset( $label[ $key ]['title'] ) ? $label[ $key ]['title'] : '';

		// Set template data
		$this->template->set_data( array(
			'meta'  => $meta,
			'label' => $label,
			'key'   => $key
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
