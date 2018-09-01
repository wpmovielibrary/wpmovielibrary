<?php
/**
 * Define the Statistics Widget class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\widgets;

/**
 * Statistics Widget class.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Statistics extends Widget {

	/**
	 * Register the Widget.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 */
	public static function register() {

		register_widget( __CLASS__ );
	}

	/**
	 * Set default properties.
	 *
	 * @since 3.0.0
	 */
	protected function make() {

		$this->id_base = 'statistics';
		$this->name = __( 'wpMovieLibrary Statistics', 'wpmovielibrary' );
		$this->description = __( 'Show some statistics about your movie library.', 'wpmovielibrary' );
	}

	/**
	 * Build Widget content.
	 *
	 * @since 3.0.0
	 */
	protected function build() {

		$count = (array) wp_count_posts( 'movie' );
		$count = array(
			'movies'      => $count['publish'],
			//'imported'    => $count['import-draft'],
			//'queued'      => $count['import-queued'],
			'draft'       => $count['draft'],
			'total'       => 0,
		);

		$count['total'] = array_sum( $count );
		$count['collections'] = wp_count_terms( 'collection' );
		$count['genres'] = wp_count_terms( 'genre' );
		$count['actors'] = wp_count_terms( 'actor' );

		$count = array_map( 'intval', $count );

		// Get archive urls
		$urls = array(
			'total'       => get_movie_archive_link(),
			'collections' => get_collection_archive_link(),
			'genres'      => get_genre_archive_link(),
			'actors'      => get_actor_archive_link(),
		);

		// Prepare links
		$links = array(
			'{total}'       => sprintf( _n( '<strong>%d</strong> movie', '<strong>%d</strong> movies', $count['movies'], 'wpmovielibrary' ), $count['movies'] ),
			'{collections}' => sprintf( _n( '<strong>%d</strong> collection', '<strong>%d</strong> collections', $count['collections'], 'wpmovielibrary' ), $count['collections'] ),
			'{genres}'      => sprintf( _n( '<strong>%d</strong> genre', '<strong>%d</strong> genres', $count['genres'], 'wpmovielibrary' ), $count['genres'] ),
			'{actors}'      => sprintf( _n( '<strong>%d</strong> actor', '<strong>%d</strong> actors', $count['actors'], 'wpmovielibrary' ), $count['actors'] ),
		);

		// Replace
		foreach ( $urls as $id => $url ) {
			if ( false !== $url ) {
				$links[ '{' . $id . '}' ] = sprintf( '<a href="%s">%s</a>', esc_url( $url ), $links[ '{' . $id . '}' ] );
			}
		}

		$this->data['title'] = $this->get_arg( 'before_title' ) . apply_filters( 'widget_title', $this->get_attr( 'title' ) ) . $this->get_arg( 'after_title' );
		$this->data['description'] = $this->get_attr( 'description' );
		$this->data['content'] = wpautop( wp_kses( $this->get_attr( 'content' ), array( 'ul', 'ol', 'li', 'p', 'span', 'em', 'i', 'p', 'strong', 'b', 'br' ) ) );
		$this->data['content'] = str_replace( array_keys( $links ), array_values( $links ), $this->data['content'] );
	}

	/**
	 * Build Widget form content.
	 *
	 * @since 3.0.0
	 */
	protected function build_form() {

		if ( empty( $this->get_attr( 'title' ) ) ) {
			$this->set_attr( 'title', __( 'Statistics', 'wpmovielibrary' ) );
		}

		if ( empty( $this->get_attr( 'description' ) ) ) {
			$this->set_attr( 'description', '' );
		}

		if ( empty( $this->get_attr( 'content' ) ) ) {
			$this->set_attr( 'content', __( 'All combined you have a total of {total} in your library, regrouped in {collections}, {genres} and {actors}.', 'wpmovielibrary' ) );
		}
	}
}
