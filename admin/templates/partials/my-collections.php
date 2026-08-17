<div id="wpmovielibrary-my-collections" class="wpmovielibrary-panel wpmovielibrary-panel-leaderboard col-span-4">
	<div class="wpmovielibrary-panel-header">{{ esc_html__( 'Most Popular Collections', 'wpmovielibrary' ) }}</div>
	<div class="wpmovielibrary-panel-content">
		@if ( ! $top_collections )
			<p class="wpmovielibrary-panel-empty">{{ esc_html__( 'No collections yet.', 'wpmovielibrary' ) }}</p>
		@else
			@foreach ( $top_collections as $index => $term )
				@php
					// Initials: first letter of the first two words, skipping
					// leading articles; first two letters if there is only one
					// word. They sit on the shared hatched disc — the stand-in
					// for the TMDb photo — so no per-term color is involved.
					$words = preg_split( '/\s+/', trim( (string) $term->name ) );
					$meaningful = array_values( array_filter( $words, fn( $word ) => ! in_array( mb_strtolower( $word ), [ 'the', 'a', 'an', 'le', 'la', 'les' ], true ) ) ) ?: $words;
					$initials = 1 < count( $meaningful ) ? mb_substr( $meaningful[0], 0, 1 ) . mb_substr( $meaningful[1], 0, 1 ) : mb_substr( $meaningful[0], 0, 2 );
					$letters = '<span class="wpmovielibrary-taxonomy-row-initials" aria-hidden="true">' . esc_html( mb_strtoupper( $initials ) ) . '</span>';
					$stats = $taxonomy_stats[ $term->taxonomy . '_' . $term->term_id ] ?? [];
					$bar_width = 0 < $max_collection_count ? round( (int) $term->count / $max_collection_count * 100 ) : 100;
				@endphp
				@include( 'partials.taxonomy-row', [ 'term' => $term, 'rank' => $index + 1, 'visual' => $letters, 'variant' => 'portrait', 'movie_count' => $stats['movie_count'] ?? 0, 'genre_count' => $stats['genre_count'] ?? null, 'avg_rating' => $stats['avg_rating'] ?? null, 'bar_width' => $bar_width, 'bar_color' => $term_colors[ $term->term_id ] ?? '#7b1fa2' ] )
			@endforeach
		@endif
	</div>
	@if ( $top_collections )
		<div class="wpmovielibrary-panel-footer">
			<a href="{!! esc_url( admin_url( 'edit-tags.php?taxonomy=wpmoly_movie_collection&post_type=movie' ) ) !!}" class="wpmovielibrary-panel-view-all">{{ sprintf( _n( 'View all %s collection', 'View all %s collections', $total_collections, 'wpmovielibrary' ), number_format_i18n( $total_collections ) ) }} &rarr;</a>
		</div>
	@endif
</div>
