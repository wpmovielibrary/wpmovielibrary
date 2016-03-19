<?php
/**
 * WPMovieLibrary Movie Headbox Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Headbox_IMDb' ) ) :

	class WPMOLY_Headbox_IMDb extends WPMOLY_Headbox {

		/**
		 * Render IMDb styled Headbox.
		 *
		 * @since    2.1.4
		 * 
		 * @param    string    $content The original post content
		 *
		 * @return   string    Filtered content
		 */
		public function render( $content = null ) {

			$theme = wp_get_theme();
			if ( ! is_null( $theme->stylesheet ) ) {
				$theme = 'theme-' . $theme->stylesheet;
			} else {
				$theme = '';
			}

			if ( 'bottom' == wpmoly_o( 'headbox-position' ) ) {
				$theme .= ' position-bottom';
			} else {
				$theme .= ' position-top';
			}

			$id = get_the_ID();

			$meta = self::get_movie_meta( $id, 'meta' );
			$details = self::get_movie_meta( $id, 'details' );
			
			$poster = get_the_post_thumbnail( $id, 'medium' );

			$images = $this->get_imdb_headbox_images( $id );

			$collections = get_the_terms( $id, 'collection' );
			if ( $collections && ! is_wp_error( $collections ) ) {
				foreach ( $collections as $i => $c ) {
					$collections[ $i ] = $c->name;
				}
			}

			if ( is_array( $collections ) )
				$collections = implode( ',', $collections );

			$meta['collections'] = WPMOLY_Utils::format_movie_terms_list( $collections, 'collection' );

			$meta['year']  = apply_filters( 'wpmoly_format_movie_year', $meta['release_date'], 'Y' );
			$meta['_year'] = date_i18n( 'Y', strtotime( $meta['release_date'] ) );
			$meta['_runtime'] = $meta['runtime'];

			$_meta = array( 'title', 'director', 'writer', 'certification', 'runtime', 'genres', 'cast', 'release_date', 'overview', 'tagline', 'genres', 'homepage', 'production_countries', 'spoken_languages', 'budget', 'revenue', 'production_companies' );
			foreach ( $_meta as $m )
				$meta[ $m ] = apply_filters( "wpmoly_format_movie_$m", $meta[ $m ] );

			$details['rating_stars'] = apply_filters( 'wpmoly_movie_rating_stars', $details['rating'], $id, $base = 10 );

			$attributes = compact( 'id', 'meta', 'details', 'poster', 'images', 'theme' );

			$content = WPMovieLibrary::render_template( 'movies/movie-imdb-headbox.php', $attributes, $require = 'always' );

			return $content;
		}

		/**
		 * Render Images.
		 * 
		 * @since    2.1.4
		 * 
		 * @param    int       $post_id Current Post ID
		 *
		 * @return   string    Filtered content
		 */
		private function get_imdb_headbox_images( $post_id ) {

			$attachments = get_posts( array(
				'post_type'   => 'attachment',
				'orderby'     => 'title',
				'numberposts' => -1,
				'post_status' => null,
				'post_parent' => $post_id,
				'meta_key'    => '_wpmoly_image_related_tmdb_id',
				'exclude'     => get_post_thumbnail_id( $post_id )
			) );

			$images = array();
			$content = __( 'No images were imported for this movie.', 'wpmovielibrary' );
			
			if ( $attachments ) {

				foreach ( $attachments as $attachment )
					$images[] = array(
						'thumbnail' => wp_get_attachment_image_src( $attachment->ID, 'thumbnail' ),
						'full'      => wp_get_attachment_image_src( $attachment->ID, 'full' )
					);

				$content = WPMovieLibrary::render_template( 'shortcodes/images.php', array( 'size' => 'thumbnail', 'movie_id' => get_the_ID(), 'images' => $images ), $require = 'always' );
			}

			$attributes = array(
				'images' => $content
			);

			$content = WPMovieLibrary::render_template( 'movies/headbox/tabs/images.php', $attributes, $require = 'always' );

			return $content;
		}

	}

endif;
