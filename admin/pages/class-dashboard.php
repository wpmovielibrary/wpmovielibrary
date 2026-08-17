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
	 * Transient key holding the aggregated taxonomy statistics.
	 *
	 * Unlike `wpmovielibrary_dashboard_totals` — a short-lived (one minute),
	 * expiry-based cache of cheap term counts — this transient stores the
	 * expensive per-term aggregation (movie count, distinct genre count,
	 * average rating) and never expires. It is busted explicitly through the
	 * hooks registered by {@see Dashboard::register_cache_invalidation()}.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var string
	 */
	private static string $stats_transient = 'wpmovielibrary_dashboard_taxonomy_stats';

	/**
	 * Progress bar shades.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var string[]
	 */
	private static array $bar_shades = [
		'#c9b8dd',
		'#bea2d5',
		'#b38ccc',
		'#a876c4',
		'#9c61bb',
		'#914bb3',
		'#8635aa',
		'#7b1fa2',
	];

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

		// Only genuinely rated movies take part in the best/worst rankings. A
		// stored 0.0 is the "not rated" sentinel — not a score of zero — and an
		// empty meta value casts to 0 as well, so a single numeric `> 0` clause
		// rules out both. Without it the "Most Hated" panel, which sorts
		// ascending, would be filled with unrated movies.
		$rated = [
			'meta_key'   => '_wpmoly_movie_rating',
			'orderby'    => 'meta_value_num',
			'meta_query' => [
				[
					'key'     => '_wpmoly_movie_rating',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC',
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
		$top_terms  = [];
		$max_counts = [];
		foreach ( [ 'genre', 'collection', 'actor' ] as $taxonomy ) {
			$terms = get_terms( [
				'taxonomy'   => "wpmoly_movie_{$taxonomy}",
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => 12,
				'hide_empty' => true,
			] );
			$top_terms[ $taxonomy ] = is_wp_error( $terms ) ? [] : $terms;

			// Terms come back ordered by descending count, so the leader — the
			// reference the progress bars are scaled against — is the first one.
			$max_counts[ $taxonomy ] = empty( $top_terms[ $taxonomy ] ) ? 0 : (int) $top_terms[ $taxonomy ][0]->count;
		}

		$taxonomy_stats = $this->get_taxonomy_stats( array_merge(
			$top_terms['genre'],
			$top_terms['collection'],
			$top_terms['actor']
		) );

		// Bar colors, flattened into a single map: term IDs are unique across
		// taxonomies, so one array indexed by term ID serves the three panels.
		$term_colors = [];
		foreach ( $top_terms as $taxonomy => $terms ) {
			$term_colors += $this->get_term_colors( $terms, $max_counts[ $taxonomy ] );
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
			'max_genre_count'      => $max_counts['genre'],
			'max_collection_count' => $max_counts['collection'],
			'max_actor_count'      => $max_counts['actor'],
			// Library-wide term counts, reused from the totals transient rather
			// than counted again: they are the very same wp_count_terms() calls.
			'total_genres'      => $totals['genres'],
			'total_collections' => $totals['collections'],
			'total_actors'      => $totals['actors'],
			'taxonomy_stats'  => $taxonomy_stats,
			'term_colors'     => $term_colors,
			'genre_icons'     => $genre_icons,
		] );
	}

	/**
	 * Map taxonomy terms to progress bar color.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @param \WP_Term[] $terms     Terms to pick a color for.
	 * @param int        $max_count Count of the leading term of the ranking.
	 *
	 * @return array<int,string> Hexadecimal colors indexed by term ID.
	 */
	private function get_term_colors( array $terms, int $max_count ) : array {

		$last   = count( self::$bar_shades ) - 1;
		$colors = [];

		foreach ( $terms as $term ) {
			$ratio = 0 < $max_count ? (int) $term->count / $max_count : 1.0;
			$index = (int) round( $ratio * $last );

			$colors[ $term->term_id ] = self::$bar_shades[ max( 0, min( $last, $index ) ) ];
		}

		return $colors;
	}

	/**
	 * Get the aggregated statistics for a set of taxonomy terms.
	 *
	 * Reads the {@see Dashboard::$stats_transient} cache and computes — then
	 * stores — the statistics of any term not yet cached. The cache is a flat
	 * array indexed by `{taxonomy}_{term_id}`, each entry holding `movie_count`,
	 * `genre_count` (null for `genre` terms) and `avg_rating` (null when no
	 * associated movie carries a rating). Because the aggregation is costly, the
	 * transient never expires and is only invalidated explicitly through
	 * {@see Dashboard::register_cache_invalidation()}.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @param \WP_Term[] $terms Terms to collect statistics for.
	 *
	 * @return array Statistics indexed by `{taxonomy}_{term_id}`.
	 */
	private function get_taxonomy_stats( array $terms ) : array {

		$cache = get_transient( self::$stats_transient );
		if ( ! is_array( $cache ) ) {
			$cache = [];
		}

		$dirty = false;
		foreach ( $terms as $term ) {
			$key = $term->taxonomy . '_' . $term->term_id;
			if ( ! isset( $cache[ $key ] ) ) {
				$cache[ $key ] = $this->compute_term_stats( $term );
				$dirty = true;
			}
		}

		if ( $dirty ) {
			// No expiration — invalidation is manual (see register_cache_invalidation()).
			set_transient( self::$stats_transient, $cache, 0 );
		}

		return $cache;
	}

	/**
	 * Compute the aggregated statistics for a single taxonomy term.
	 *
	 * Collects the published movies associated with the term, then counts the
	 * distinct genres among them (skipped — left null — when the term is itself
	 * a genre) and averages their non-empty ratings, rounded to two decimals.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @param \WP_Term $term Term to aggregate.
	 *
	 * @return array {
	 *     @type int        $movie_count Number of associated published movies.
	 *     @type int|null   $genre_count Distinct genres among them, null for genres.
	 *     @type float|null $avg_rating  Average rating, null when none is set.
	 * }
	 */
	private function compute_term_stats( \WP_Term $term ) : array {

		$query = new \WP_Query( [
			'post_type'      => 'movie',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'no_found_rows'  => true,
			'fields'         => 'ids',
			'tax_query'      => [
				[
					'taxonomy' => $term->taxonomy,
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				],
			],
		] );

		$movie_ids = $query->posts;

		// Distinct genres among the associated movies. Meaningless — and skipped —
		// when the term is itself a genre.
		$genre_count = null;
		if ( 'wpmoly_movie_genre' !== $term->taxonomy ) {
			$genre_count = 0;
			if ( ! empty( $movie_ids ) ) {
				$genres = wp_get_object_terms( $movie_ids, 'wpmoly_movie_genre', [ 'fields' => 'ids' ] );
				$genre_count = is_wp_error( $genres ) ? 0 : count( $genres );
			}
		}

		// Average of the actual ratings. A movie counts towards the average only
		// when its rating is a real value: absent or empty meta is excluded, and
		// — following the same convention as the movie card — a stored 0.0 means
		// "not rated", not a genuine score of zero, so it is excluded too. When no
		// associated movie carries a real rating the average stays null and is
		// rendered as "—" rather than a misleading 0.00.
		$avg_rating = null;
		if ( ! empty( $movie_ids ) ) {
			update_postmeta_cache( $movie_ids );

			$ratings = [];
			foreach ( $movie_ids as $movie_id ) {
				$rating = get_post_meta( $movie_id, '_wpmoly_movie_rating', true );
				if ( ! is_numeric( $rating ) ) {
					continue;
				}

				$rating = (float) $rating;
				if ( 0.0 === $rating ) {
					continue;
				}

				$ratings[] = $rating;
			}

			if ( ! empty( $ratings ) ) {
				$avg_rating = round( array_sum( $ratings ) / count( $ratings ), 2 );
			}
		}

		return [
			'movie_count' => count( $movie_ids ),
			'genre_count' => $genre_count,
			'avg_rating'  => $avg_rating,
		];
	}

	/**
	 * Register the hooks that invalidate the taxonomy statistics cache.
	 *
	 * Called once from the plugin bootstrap rather than instantiating the page
	 * class on every request: the callbacks are static and only delete the
	 * transient. The cache is dropped whenever a movie is saved or deleted, or
	 * whenever the genre, collection or actor terms of any object change.
	 *
	 * Note: each individual mutation drops the whole transient. Bulk imports
	 * therefore thrash the cache — a known, accepted limitation for now; no
	 * suspension mechanism is provided.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access public
	 */
	public static function register_cache_invalidation() {

		add_action( 'save_post_movie', [ __CLASS__, 'flush_stats_cache' ] );
		add_action( 'deleted_post', [ __CLASS__, 'flush_stats_cache_on_delete' ], 10, 2 );
		add_action( 'set_object_terms', [ __CLASS__, 'flush_stats_cache_on_terms' ], 10, 4 );
	}

	/**
	 * Delete the taxonomy statistics transient.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access public
	 */
	public static function flush_stats_cache() {

		delete_transient( self::$stats_transient );
	}

	/**
	 * Invalidate the statistics cache when a movie is deleted.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @param int          $post_id Deleted post ID.
	 * @param \WP_Post|null $post   Deleted post object, when available.
	 */
	public static function flush_stats_cache_on_delete( $post_id, $post = null ) {

		$post_type = $post instanceof \WP_Post ? $post->post_type : get_post_type( $post_id );
		if ( 'movie' === $post_type ) {
			self::flush_stats_cache();
		}
	}

	/**
	 * Invalidate the statistics cache when relevant terms change.
	 *
	 * Only the three taxonomies surfaced by the dashboard panels — genre,
	 * collection and actor — trigger a flush.
	 *
	 * @since 6.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @param int    $object_id Object the terms were set for.
	 * @param array  $terms     Terms passed to wp_set_object_terms().
	 * @param array  $tt_ids    Term taxonomy IDs.
	 * @param string $taxonomy  Taxonomy slug.
	 */
	public static function flush_stats_cache_on_terms( $object_id, $terms, $tt_ids, $taxonomy ) {

		if ( in_array( $taxonomy, [ 'wpmoly_movie_genre', 'wpmoly_movie_collection', 'wpmoly_movie_actor' ], true ) ) {
			self::flush_stats_cache();
		}
	}
}
