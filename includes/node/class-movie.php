<?php
/**
 * Define the Movie Node.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */

namespace wpmoly\Node;

/**
 * Define the most important class of the plugin: Movie.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 * 
 * @property    int        $tmdb_id Movie TMDb ID.
 * @property    string     $title Movie title.
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
class Movie {

	/**
	 * Movie ID.
	 * 
	 * @var    int
	 */
	public $id;

	/**
	 * Movie Post object
	 * 
	 * @var    WP_Post
	 */
	public $post;

	/**
	 * Movie poster.
	 * 
	 * @var    Poster
	 */
	protected $poster;

	/**
	 * Movie posters collection.
	 * 
	 * @var    Collection
	 */
	protected $posters;

	/**
	 * Movie backdrops collection.
	 * 
	 * @var    Collection
	 */
	protected $backdrops;

	/**
	 * Class Constructor.
	 * 
	 * @since    3.0
	 *
	 * @param    int|Movie|WP_Post    $product Movie ID, movie instance or post object
	 */
	public function __construct( $movie = null ) {

		if ( is_numeric( $movie ) ) {
			$this->id   = absint( $movie );
			$this->post = get_post( $this->id );
		} elseif ( $movie instanceof Movie ) {
			$this->id   = absint( $movie->id );
			$this->post = $movie->post;
		} elseif ( isset( $movie->ID ) ) {
			$this->id   = absint( $movie->ID );
			$this->post = $movie;
		}

		$this->backdrops = new Collection;
		$this->posters   = new Collection;
	}

	/**
	 * __isset()
	 * 
	 * @since    3.0
	 * 
	 * @param    mixed    $name
	 * 
	 * @return   boolean
	 */
	public function __isset( $name ) {

		return metadata_exists( 'post', $this->id, '_wpmoly_movie_' . $name );
	}

	/**
	 * __get().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name
	 * 
	 * @return   mixed
	 */
	public function __get( $name ) {

		$value = get_post_meta( $this->id, '_wpmoly_movie_' . $name, $single = true );

		if ( false !== $value ) {
			$this->$name = $value;
		}

		return $value;
	}

	/**
	 * __set().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name
	 * @param    mixed     $value
	 * 
	 * @return   mixed
	 */
	public function __set( $name, $value ) {

		if ( ! isset( $this->name ) || $value !== $this->$name ) {
			return $this->$name = $value;
		}

		return $value;
	}

	/**
	 * Property accessor.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name Property name
	 * @param    mixed     $default Default value
	 * 
	 * @return   mixed
	 */
	public function get( $name, $default = null ) {

		return $this->__isset( $name ) ? $this->$name : $default;
	}

	/**
	 * Property set.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name Property name
	 * @param    mixed     $value Property value
	 * 
	 * @return   mixed
	 */
	public function set( $name, $value = null ) {

		return $this->__set( $name, $value );
	}

	/**
	 * Load backdrops for the current Movie.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $language Language to filter images
	 * @param    int       $number Number of images to fetch
	 * 
	 * @return   Backdrops
	 */
	public function load_backdrops( $language = '', $number = -1 ) {

		global $wpdb;

		$attachments = get_posts( array(
			'post_type'   => 'attachment',
			'numberposts' => -1,
			'post_status' => null,
			'post_parent' => $this->id,
			'meta_key'    => '_wpmoly_image_related_tmdb_id'
		) );

		foreach ( $attachments as $attachment ) {
			$this->backdrops->add( new Image( $attachment ) );
		}

		return $this->backdrops;
	}

	/**
	 * Load posters for the current Movie.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $language Language to filter images
	 * @param    int       $number Number of images to fetch
	 * 
	 * @return   null
	 */
	public function load_posters( $language = '', $number = -1 ) {

		global $wpdb;

		$attachments = get_posts( array(
			'post_type'   => 'attachment',
			'numberposts' => -1,
			'post_status' => null,
			'post_parent' => $this->id,
			'meta_key'    => '_wpmoly_poster_related_tmdb_id'
		) );

		foreach ( $attachments as $attachment ) {
			$this->posters->add( new Image( $attachment ) );
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
	 * @since    3.0
	 * 
	 * @param    string    $variant Backdrop variant.
	 * 
	 * @return   Backdrop|DefaultBackdrop
	 */
	public function get_backdrop( $variant = 'featured' ) {

		if ( 'featured' == $variant && ! has_post_thumbnail( $this->id ) ) {
			$variant = 'default';
		}

		switch ( $variant ) {
			case 'featured' :
				$image_id = get_post_thumbnail_id( $this->id );
				$backdrop = new Image( $image_id );
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
				$backdrop = DefaultBackdrop::get_instance();
				break;
		}

		if ( ! $backdrop instanceof Image ) {
			$backdrop = DefaultBackdrop::get_instance();
		}

		return $backdrop;
	}

	/**
	 * Simple accessor for Movie's Poster.
	 * 
	 * Different variant can be used. 'featured' will use the featured image
	 * if available, default poster if no featured image is defined. 'first',
	 * 'last' and 'random' are self-explanatory and will fall back to the
	 * default poster if no poster is available.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $variant Poster variant.
	 * 
	 * @return   Poster|DefaultPoster
	 */
	public function get_poster( $variant = 'featured' ) {

		if ( 'featured' == $variant && ! has_post_thumbnail( $this->id ) ) {
			$variant = 'default';
		}

		switch ( $variant ) {
			case 'featured' :
				$image_id = get_post_thumbnail_id( $this->id );
				$poster = new Image( $image_id );
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
				$poster = DefaultPoster::get_instance();
				break;
		}

		if ( ! $poster instanceof Image ) {
			$poster = DefaultPoster::get_instance();
		}

		return $poster;
	}

	/**
	 * Simple accessor for Backdrops collection.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $language Filter backdrops by language
	 * @param    int       $number Limit the number of backdrops
	 * 
	 * @return   Posters
	 */
	public function get_backdrops( $language = '', $number = -1 ) {

		if ( ! $this->backdrops->has_items() ) {
			$this->load_backdrops( $language, $number );
		}

		if ( -1 == $number ) {
			return $this->backdrops;
		}

		$backdrops = new Collection;
		while ( $this->backdrops->key() < $number - 1 ) {
			$backdrops->add( $this->backdrops->next() );
		}

		$this->backdrops->rewind();

		return $backdrops;
	}

	/**
	 * Simple accessor for Posters collection.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $language Filter posters by language
	 * @param    int       $number Limit the number of posters
	 * 
	 * @return   Posters
	 */
	public function get_posters( $language = '', $number = -1 ) {

		if ( ! $this->posters->has_items() ) {
			$this->load_posters( $language, $number );
		}

		if ( -1 == $number ) {
			return $this->posters;
		}

		$posters = new Collection;
		while ( $this->posters->key() < $number - 1 ) {
			$posters->add( $this->posters->next() );
		}

		$this->posters->rewind();

		return $posters;
	}

	/**
	 * Save movie.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function save() {

		$this->save_meta();
		$this->save_details();
	}

	/**
	 * Save movie metadata.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function save_meta() {

		
	}

	/**
	 * Save movie details.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function save_details() {

		
	}
}