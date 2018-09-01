<?php
/**
 * Define the Genre Taxonomy class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\taxonomies;

/**
 * Genres are terms from the 'genre' taxonomy.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 * @author Charlie Merland <charlie@caercam.org>
 *
 * @property    string     $name Genre name.
 * @property    int        $person_id Genre related Person ID.
 */
class Genre extends Taxonomy {

	/**
	 * Taxonomy name.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $taxonomy = 'genre';

	/**
	 * Simple accessor for Genre thumbnail.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $variant Image variant. Default none.
	 * @param string $size    Image size. Default 'thumb'.
	 *
	 * @return Image
	 */
	public function get_thumbnail( $variant = '', $size = 'thumb' ) {

		$custom_thumbnail = $this->get_custom_thumbnail( $size );
		if ( ! empty( $custom_thumbnail ) ) {
			$this->thumbnail = $custom_thumbnail;
			return $this->thumbnail;
		}

		if ( empty( $variant ) ) {
			$variant = $this->get( 'thumbnail' );
		}

		/**
		 * Filter default genre thumbnail variants
		 *
		 * @since 3.0.0
		 *
		 * @param string $variants
		 */
		$variants = apply_filters( 'wpmoly/filter/default/genre/thumbnail/variants', array(
			'28'    => 'action',
			'12'    => 'adventure',
			'16'    => 'animation',
			'35'    => 'comedy',
			'80'    => 'crime',
			'99'    => 'documentary',
			'18'    => 'drama',
			'10751' => 'family',
			'14'    => 'fantasy',
			'10769' => 'foreign',
			'36'    => 'history',
			'27'    => 'horror',
			'10402' => 'music',
			'9648'  => 'mystery',
			'10749' => 'romance',
			'878'   => 'science-fiction',
			'53'    => 'thriller',
			'10770' => 'tv-movie',
			'10752' => 'war',
			'37'    => 'western',
		) );

		$variant_id = array_search( $variant, $variants );
		if ( false !== $variant_id ) {
			$variant = $variants[ $variant_id ];
		} elseif ( ! in_array( $variant, $variants ) ) {
			$variant = 'unknown';
		}

		/**
		 * Filter default genre thumbnail
		 *
		 * @since 3.0.0
		 *
		 * @param string $thumbnail
		 */
		$sizes = apply_filters( 'wpmoly/filter/default/genre/thumbnail/sizes', array( 'original', 'full', 'medium', 'small', 'thumb', 'thumbnail', 'tiny' ) );
		if ( ! in_array( $size, $sizes ) ) {
			$size = 'medium';
		}

		if ( 'original' !== $size ) {
			$size = '-' . $size;
		}

		/**
		 * Filter default genre thumbnail
		 *
		 * @since 3.0.0
		 *
		 * @param string $thumbnail
		 */
		$thumbnail = apply_filters( 'wpmoly/filter/default/genre/thumbnail', WPMOLY_URL . "public/assets/img/genre-{$variant}{$size}.png" );

		$this->thumbnail = $thumbnail;

		return $this->thumbnail;
	}

	/**
	 * Retrieve the Genre's custom thumbnail, if any.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $size Thumbnail size. Default 'thumb'.
	 *
	 * @return string
	 */
	public function get_custom_thumbnail( $size = 'thumb' ) {

		$thumbnail = $this->get( 'custom_thumbnail' );
		if ( empty( $thumbnail ) ) {
			return $thumbnail;
		}

		$thumbnail = wp_get_attachment_image_src( $thumbnail, $size );
		if ( empty( $thumbnail[0] ) ) {
			return '';
		}

		return $thumbnail[0];
	}

}
