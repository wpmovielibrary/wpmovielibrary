<?php
/**
 * Define the Movie Node.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\posts;

use wpmoly\utils;
use wpmoly\nodes\Node;
use wpmoly\nodes\Nodes;
use wpmoly\nodes\images;

/**
 * Define the most important class of the plugin: Movie.
 *
 * Give easy access to metadata, details, posters and images.
 *
 * Movie::get( $meta )
 * Movie::the( $meta )
 * Movie::get_the( $meta )
 *
 * Movie::get_{$meta}()
 * Movie::the_{$meta}()
 * Movie::get_the_{$meta}()
 *
 * Movie::get_poster()
 * Movie::get_backdrop()
 * Movie::get_posters()
 * Movie::get_backdrops()
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 * @author Charlie Merland <charlie@caercam.org>
 *
 * @property    int        $tmdb_id Movie TMDb ID.
 * @property    string     $title   Movie title.
 * @property    string     $original_title Movie original title.
 * @property    string     $tagline Movie tagline.
 * @property    string     $overview Movie overview.
 * @property    string     $release_date Movie release date.
 * @property    string     $local_release_date Movie local release date.
 * @property    int        $runtime Movie runtime.
 * @property    string     $production_companies Movie production companies.
 * @property    string     $production_countries Movie production countries.
 * @property    string     $spoken_languages Movie spoken languages.
 * @property    string     $genres Movie genres.
 * @property    string     $director Movie director.
 * @property    string     $producer Movie producer.
 * @property    string     $cast Movie actors.
 * @property    string     $photography Movie director of photography.
 * @property    string     $composer Movie original music composer.
 * @property    string     $author Movie author.
 * @property    string     $writer Movie writer.
 * @property    string     $certification Movie certification.
 * @property    int        $budget Movie budget.
 * @property    int        $revenue Movie revenue.
 * @property    int        $imdb_id Movie IMDb ID.
 * @property    boolean    $adult Movie adult-only.
 * @property    string     $homepage Movie official URL.
 * @property    string     $status Movie status.
 * @property    string     $media Movie media.
 * @property    float      $rating Movie rating.
 * @property    string     $language Movie language.
 * @property    string     $subtitles Movie subtitles.
 * @property    string     $format Movie format.
 */
class Movie extends Node {

	/**
	 * Movie Post object
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var WP_Post
	 */
	public $post;

	/**
	 * Movie poster.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var Poster
	 */
	protected $poster;

	/**
	 * Movie posters list.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var Nodes
	 */
	protected $posters;

	/**
	 * Movie backdrops list.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var Nodes
	 */
	protected $backdrops;

	/**
	 * Initialize the Movie.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function init() {

		$this->backdrops = new Nodes;
		$this->posters   = new Nodes;
		$this->backdrops->loaded = false;
		$this->posters->loaded   = false;

		$this->prefix = utils\movie\prefix( '' );
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
			case 'year':
				$value = date_i18n( 'Y', strtotime( $this->get( 'release_date' ) ) );
				break;
			case 'actors':
			case 'casting':
				$value = $this->get( 'cast' );
				break;
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
		 * @param Node   $node  Movie object.
		 */
		return apply_filters( 'wpmoly/filter/the/movie/' . sanitize_key( $name ), $this->get( $name ), $this );
	}

	/**
	 * Get the filtered movie permalink.
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
	 * Echo the filtered movie permalink.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function the_permalink() {

		echo $this->get_permalink();
	}

	/**
	 * Does this movie have metadata?
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
	 * Load backdrops for the current Movie.
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
	 * Load posters for the current Movie.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $language Language to filter images
	 * @param int $number Number of images to fetch
	 *
	 * @return Posters
	 */
	public function load_posters( $language = '', $number = -1 ) {

		global $wpdb;

		$attachments = get_posts( array(
			'post_type'   => 'attachment',
			'numberposts' => -1,
			'post_status' => null,
			'post_parent' => $this->id,
			'meta_key'    => '_wpmoly_poster_related_tmdb_id',
		) );

		foreach ( $attachments as $attachment ) {
			$this->posters->add( new images\Poster( $attachment ) );
		}

		return $this->posters;
	}

	/**
	 * Simple accessor for Movie's Backdrop.
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
			$selected = utils\movie\get_meta( $this->id, 'backdrop_id' );
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
	 * Simple accessor for Movie's Poster.
	 *
	 * Different variant can be used. 'selected' will use the poster selected as
	 * such in the movie editor, 'featured' will use the featured image
	 * if available, default poster if no featured image is defined. 'first',
	 * 'last' and 'random' are self-explanatory and will fall back to the
	 * default poster if no poster is available.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $variant Poster variant.
	 *
	 * @return Poster|Default_Poster
	 */
	public function get_poster( $variant = 'selected' ) {

		if ( ! $this->posters->has_items() && ! $this->posters->loaded ) {
			$this->load_posters();
		}

		if ( 'selected' === $variant ) {
			$selected = utils\movie\get_meta( $this->id, 'poster_id' );
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
				$poster = new images\Poster( $image_id );
				break;
			case 'selected' :
				$poster = new images\Poster( $selected );
				break;
			case 'first' :
				$poster = $this->posters->first();
				break;
			case 'last' :
				$poster = $this->posters->last();
				break;
			case 'random' :
				$poster = $this->posters->random();
				break;
			case 'default' :
			default :
				$poster = images\Default_Poster::get_instance();
				break;
		}

		if ( ! $poster instanceof images\Poster ) {
			$poster = images\Default_Poster::get_instance();
		}

		return $poster;
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
	 * @return Posters
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
	 * Simple accessor for Posters list.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $language Filter posters by language
	 * @param int    $number   Limit the number of posters
	 *
	 * @return Posters
	 */
	public function get_posters( $language = '', $number = -1 ) {

		if ( ! $this->posters->has_items() ) {
			$this->load_posters( $language, $number );
		}

		if ( -1 == $number ) {
			return $this->posters;
		}

		$posters = new Nodes;
		while ( $this->posters->key() < $number - 1 ) {
			$posters->add( $this->posters->next() );
		}

		$this->posters->rewind();

		return $posters;
	}

}
