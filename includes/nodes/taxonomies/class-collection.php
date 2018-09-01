<?php
/**
 * Define the Collection Taxonomy class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\nodes\taxonomies;

/**
 * Collections are terms from the 'collection' taxonomy.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 * @author Charlie Merland <charlie@caercam.org>
 */
class Collection extends Taxonomy {

	/**
	 * Taxonomy name.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $taxonomy = 'collection';

	/**
	 * Simple accessor for Collection thumbnail.
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
			if ( empty( $variant ) ) {
				$variant = strtoupper( substr( $this->get( 'name' ), 0, 1 ) );
			}
		}

		/**
		 * Filter default collection thumbnail variants
		 *
		 * @since 3.0.0
		 *
		 * @param string $variants
		 */
		$variants = apply_filters( 'wpmoly/filter/default/collection/thumbnail/variants', array_merge( range( 'A', 'Z' ), array( 'default' ) ) );
		if ( ! in_array( $variant, $variants ) ) {
			$variant = 'default';
		}

		/**
		 * Filter default collection thumbnail
		 *
		 * @since 3.0.0
		 *
		 * @param string $thumbnail
		 */
		$sizes = apply_filters( 'wpmoly/filter/default/collection/thumbnail/sizes', array( 'original', 'full', 'medium', 'small', 'thumb', 'thumbnail', 'tiny' ) );
		if ( ! in_array( $size, $sizes ) ) {
			$size = 'medium';
		}

		if ( 'original' !== $size ) {
			$size = '-' . $size;
		}

		/**
		 * Filter default collection thumbnail
		 *
		 * @since 3.0.0
		 *
		 * @param string $thumbnail
		 */
		$thumbnail = apply_filters( 'wpmoly/filter/default/collection/thumbnail', WPMOLY_URL . "public/assets/img/collection-{$variant}{$size}.png" );

		$this->thumbnail = $thumbnail;

		return $this->thumbnail;
	}

	/**
	 * Retrieve the Collection custom thumbnail, if any.
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
