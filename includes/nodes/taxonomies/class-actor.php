<?php
/**
 * Define the Actor Taxonomy class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\taxonomies;

/**
 * Actors are terms from the 'actor' taxonomy.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 * @author Charlie Merland <charlie@caercam.org>
 *
 * @property    string     $name Actor name.
 * @property    int        $person_id Actor related Person ID.
 */
class Actor extends Taxonomy {

	/**
	 * Taxonomy name.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $taxonomy = 'actor';

	/**
	 * Simple accessor for Actor thumbnail.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $variant Image variant.
	 * @param string $size Image size.
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
		 * Filter default actor thumbnail
		 *
		 * @since 3.0.0
		 *
		 * @param string $thumbnail
		 */
		$variants = apply_filters( 'wpmoly/filter/default/actor/thumbnail/variants', array( 'neutral', 'female', 'male' ) );
		if ( ! in_array( $variant, $variants ) ) {
			$variant = 'neutral';
		}

		/**
		 * Filter default actor thumbnail
		 *
		 * @since 3.0.0
		 *
		 * @param string $thumbnail
		 */
		$sizes = apply_filters( 'wpmoly/filter/default/actor/thumbnail/sizes', array( 'original', 'full', 'medium', 'small', 'thumb', 'thumbnail', 'tiny' ) );
		if ( ! in_array( $size, $sizes ) ) {
			$size = 'medium';
		}

		if ( 'original' !== $size ) {
			$size = '-' . $size;
		}

		/**
		 * Filter default actor thumbnail
		 *
		 * @since 3.0.0
		 *
		 * @param string $thumbnail
		 */
		$thumbnail = apply_filters( 'wpmoly/filter/default/actor/thumbnail', WPMOLY_URL . "public/assets/img/actor-{$variant}{$size}.png" );

		$this->thumbnail = $thumbnail;

		return $this->thumbnail;
	}

	/**
	 * Retrieve the Actor custom thumbnail, if any.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $size Image size.
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
