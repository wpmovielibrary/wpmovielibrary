<?php
/**
 * Define the dashboard admin page class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary\Admin;

/**
 * Render the plugin dashboard page.
 *
 * Single-action class: instantiated on demand by Backstage and invoked
 * directly. Collects the data needed by the dashboard and renders the
 * matching template.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Dashboard {

	use Renderable;

	/**
	 * Render the dashboard page.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function __invoke() {

		$totals = get_transient( 'wpmovielibrary_dashboard_totals' );

		if ( false === $totals ) {
			$movies_count = (array) wp_count_posts( 'movie' );
			$totals = [
				'movies'      => $movies_count['publish'] ?? 0,
				'imported'    => $movies_count['import-draft'] ?? 0,
				'queued'      => $movies_count['import-queued'] ?? 0,
				'drafts'      => $movies_count['draft'] ?? 0,
			];
			$totals['collections'] = wp_count_terms( 'wpmoly_movie_collection', [ 'hide_empty' => false ] );
			$totals['genres'] = wp_count_terms( 'wpmoly_movie_genre', [ 'hide_empty' => false ] );
			$totals['actors'] = wp_count_terms( 'wpmoly_movie_actor', [ 'hide_empty' => false ] );
			$totals = array_map( 'intval', $totals );

			set_transient( 'wpmovielibrary_dashboard_totals', $totals, MINUTE_IN_SECONDS );
		}

		// Movie panels data. Deliberately kept out of the totals transient:
		// these lists have their own lifecycle and are cheap enough to query
		// on each dashboard load for now.
		$defaults = [
			'post_type'      => 'movie',
			'post_status'    => 'publish',
			'posts_per_page' => 8,
			'no_found_rows'  => true,
			'fields'         => 'ids',
		];

		$rated = [
			'meta_key'   => '_wpmoly_movie_rating',
			'orderby'    => 'meta_value_num',
			'meta_query' => [
				[
					'key'     => '_wpmoly_movie_rating',
					'compare' => 'EXISTS',
				],
			],
		];

		$recently_added = new \WP_Query( array_merge( $defaults, [
			'posts_per_page' => 10,
			'orderby' => 'date',
			'order'   => 'DESC',
		] ) );

		$most_rated = new \WP_Query( array_merge( $defaults, $rated, [ 'order' => 'DESC' ] ) );
		$most_hated = new \WP_Query( array_merge( $defaults, $rated, [ 'order' => 'ASC' ] ) );

		// Taxonomy panels data. Terms have their own API — no WP_Query here.
		$top_terms = [];
		foreach ( [ 'genre', 'collection', 'actor' ] as $taxonomy ) {
			$terms = get_terms( [
				'taxonomy'   => "wpmoly_movie_{$taxonomy}",
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => 12,
				'hide_empty' => true,
			] );
			$top_terms[ $taxonomy ] = is_wp_error( $terms ) ? [] : $terms;
		}

		$genre_icons = require_once WPMOLY_PATH . 'admin/includes/genre-icons.php';

		$this->render( 'dashboard', [
			'plugin_page' => 'wpmovielibrary',
			'hook_suffix' => get_current_screen()->id,
			'post_type'   => get_current_screen()->post_type ?? '',
			'totals' => $totals,
			'recently_added' => $recently_added->posts,
			'most_rated'     => $most_rated->posts,
			'most_hated'     => $most_hated->posts,
			'top_genres'      => $top_terms['genre'],
			'top_collections' => $top_terms['collection'],
			'top_actors'      => $top_terms['actor'],
			'genre_icons'     => $genre_icons,
		] );
	}
}
