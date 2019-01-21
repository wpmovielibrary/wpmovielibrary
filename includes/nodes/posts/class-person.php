<?php
/**
 * Define the Person Node.
 *
 * @link https://wppersonlibrary.com
 * @since 3.0.0
 *
 * @package wpPersonLibrary
 */

namespace wpmoly\nodes\posts;

use wpmoly\utils;
use wpmoly\nodes\Node;
use wpmoly\nodes\Nodes;
use wpmoly\nodes\images;

/**
 * Define the most important class of the plugin: Person.
 *
 * Give easy access to metadata, details, pictures and images.
 *
 * Person::get( $meta )
 * Person::the( $meta )
 * Person::get_the( $meta )
 *
 * Person::get_{$meta}()
 * Person::the_{$meta}()
 * Person::get_the_{$meta}()
 *
 * Person::get_picture()
 * Person::get_backdrop()
 * Person::get_pictures()
 * Person::get_backdrops()
 *
 * @since 3.0.0
 * @package wpPersonLibrary
 * @author Charlie Merland <charlie@caercam.org>
 *
 * @property    int        $tmdb_id Person TMDb ID.
 */
class Person extends Node {

	/**
	 * Person Post object
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var WP_Post
	 */
	public $post;

	/**
	 * Person picture.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var Picture
	 */
	protected $picture;

	/**
	 * Person pictures list.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var Nodes
	 */
	protected $pictures;

	/**
	 * Person backdrops list.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var Nodes
	 */
	protected $backdrops;

	/**
	 * Initialize the Person.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function init() {

		$this->backdrops = new Nodes;
		$this->pictures  = new Nodes;
		$this->backdrops->loaded = false;
		$this->pictures->loaded  = false;

		$this->prefix = utils\person\prefix( '' );
	}

	/**
	 * Property accessor.
	 *
	 * Override Node::get() to add support for additional data like 'year'.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Property name
	 * @param mixed $default Default value
	 *
	 * @return mixed
	 */
	public function get( $name, $default = null ) {

		switch ( $name ) {
			/*case 'year':
				$value = date_i18n( 'Y', strtotime( $this->get( 'release_date' ) ) );
				break;*/
			case 'url':
			case 'link':
			case 'permalink':
				$value = $this->get_permalink();
				break;
			default:
				$value = parent::get( $name, $default );
				break;
		}

		return $value;
	}

	/**
	 * Enhanced property accessor. Unlike Node::get() this method automatically
	 * escapes the property requested and therefore should be used when the
	 * property is meant for display.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Property name
	 *
	 * @return mixed
	 */
	public function get_the( $name ) {

		/**
		 * Filter properties for display.
		 *
		 * @since 3.0.0
		 *
		 * @param string $name  Meta name.
		 * @param mixed  $value Meta value.
		 * @param Node   $node  Person object.
		 */
		return apply_filters( 'wpmoly/filter/the/person/' . sanitize_key( $name ), $this->get( $name ), $this );
	}

	/**
	 * Get the filtered person permalink.
	 *
	 * Wrapper for get_permalink().
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_permalink() {

		$url = get_permalink( $this->id );

		return esc_url( $url );
	}

	/**
	 * Echo the filtered person permalink.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function the_permalink() {

		echo $this->get_permalink();
	}

	/**
	 * Does this person have metadata?
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_empty() {

		$tmdb_id = $this->get( 'tmdb_id' );

		return empty( $tmdb_id );
	}

	/**
	 * Load backdrops for the current Person.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $language Language to filter images
	 * @param int $number Number of images to fetch
	 *
	 * @return Backdrops
	 */
	public function load_backdrops( $language = '', $number = -1 ) {

		global $wpdb;

		$attachments = new \WP_Query( array(
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => 'inherit',
			'post_parent'    => $this->id,
			'meta_query'     => array(
				'relation'     => 'OR',
				array(
					'key'        => '_wpmoly_backdrop_related_tmdb_id',
				),
				array(
					'key'        => '_wpmoly_image_related_tmdb_id',
				),
			),
		) );

		if ( $attachments->have_posts() ) {
			foreach ( $attachments->posts as $attachment ) {
				$this->backdrops->add( new images\Backdrop( $attachment ) );
			}
		}

		return $this->backdrops;
	}

	/**
	 * Load pictures for the current Person.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $language Language to filter images
	 * @param int $number Number of images to fetch
	 *
	 * @return Pictures
	 */
	public function load_pictures( $language = '', $number = -1 ) {

		global $wpdb;

		$attachments = get_posts( array(
			'post_type'   => 'attachment',
			'numberposts' => -1,
			'post_status' => null,
			'post_parent' => $this->id,
			'meta_key'    => '_wpmoly_picture_related_tmdb_id',
		) );

		foreach ( $attachments as $attachment ) {
			$this->pictures->add( new images\Picture( $attachment ) );
		}

		return $this->pictures;
	}

	/**
	 * Simple accessor for Person's Backdrop.
	 *
	 * Different variant can be used. 'featured' will use the featured image
	 * if available, default backdrop if no featured image is defined. 'first',
	 * 'last' and 'random' are self-explanatory and will fall back to the
	 * default backdrop if no backdrop is available.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $variant Backdrop variant.
	 *
	 * @return Backdrop|Default_Backdrop
	 */
	public function get_backdrop( $variant = 'selected' ) {

		if ( ! $this->backdrops->has_items() && ! $this->backdrops->loaded ) {
			$this->load_backdrops();
		}

		if ( 'selected' === $variant ) {
			$selected = utils\person\get_meta( $this->id, 'backdrop_id' );
			if ( empty( $selected ) ) {
				$variant = 'first';
			}
		}

		switch ( $variant ) {
			case 'selected' :
				$backdrop = new images\Backdrop( $selected );
				break;
			case 'first' :
				$backdrop = $this->backdrops->first();
				break;
			case 'last' :
				$backdrop = $this->backdrops->last();
				break;
			case 'random' :
				$backdrop = $this->backdrops->random();
				break;
			case 'default' :
			default :
				$backdrop = images\Default_Backdrop::get_instance();
				break;
		}

		if ( ! $backdrop instanceof images\Backdrop ) {
			$backdrop = images\Default_Backdrop::get_instance();
		}

		return $backdrop;
	}

	/**
	 * Simple accessor for Person's Picture.
	 *
	 * Different variant can be used. 'selected' will use the picture selected as
	 * such in the person editor, 'featured' will use the featured image
	 * if available, default picture if no featured image is defined. 'first',
	 * 'last' and 'random' are self-explanatory and will fall back to the
	 * default picture if no picture is available.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $variant Picture variant.
	 *
	 * @return Picture|Default_Picture
	 */
	public function get_picture( $variant = 'selected' ) {

		if ( ! $this->pictures->has_items() && ! $this->pictures->loaded ) {
			$this->load_pictures();
		}

		if ( 'selected' === $variant ) {
			$selected = utils\person\get_meta( $this->id, 'picture_id' );
			if ( empty( $selected ) ) {
				$variant = 'featured';
			}
		}

		if ( 'featured' === $variant && ! has_post_thumbnail( $this->id ) ) {
			$variant = 'default';
		}

		switch ( $variant ) {
			case 'featured' :
				$image_id = get_post_thumbnail_id( $this->id );
				$picture = new images\Picture( $image_id );
				break;
			case 'selected' :
				$picture = new images\Picture( $selected );
				break;
			case 'first' :
				$picture = $this->pictures->first();
				break;
			case 'last' :
				$picture = $this->pictures->last();
				break;
			case 'random' :
				$picture = $this->pictures->random();
				break;
			case 'default' :
			default :
				$picture = images\Default_Picture::get_instance();
				break;
		}

		if ( ! $picture instanceof images\Picture ) {
			$picture = images\Default_Picture::get_instance();
		}

		return $picture;
	}

	/**
	 * Simple accessor for Backdrops list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $language Filter backdrops by language
	 * @param int    $number   Limit the number of backdrops
	 *
	 * @return Pictures
	 */
	public function get_backdrops( $language = '', $number = -1 ) {

		if ( ! $this->backdrops->has_items() ) {
			$this->load_backdrops( $language, $number );
		}

		if ( -1 == $number ) {
			return $this->backdrops;
		}

		$backdrops = new Nodes;
		while ( $this->backdrops->key() < $number - 1 ) {
			$backdrops->add( $this->backdrops->next() );
		}

		$this->backdrops->rewind();

		return $backdrops;
	}

	/**
	 * Simple accessor for Pictures list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $language Filter pictures by language
	 * @param int    $number   Limit the number of pictures
	 *
	 * @return Pictures
	 */
	public function get_pictures( $language = '', $number = -1 ) {

		if ( ! $this->pictures->has_items() ) {
			$this->load_pictures( $language, $number );
		}

		if ( -1 == $number ) {
			return $this->pictures;
		}

		$pictures = new Nodes;
		while ( $this->pictures->key() < $number - 1 ) {
			$pictures->add( $this->pictures->next() );
		}

		$this->pictures->rewind();

		return $pictures;
	}

}
