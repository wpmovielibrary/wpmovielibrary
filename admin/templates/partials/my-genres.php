<div id="wpmovielibrary-my-genres" class="wpmovielibrary-panel wpmovielibrary-panel-leaderboard col-span-4">
	<div class="wpmovielibrary-panel-header">{{ esc_html__( 'Most Popular Genres', 'wpmovielibrary' ) }}</div>
	<div class="wpmovielibrary-panel-content">
		@if ( ! $top_genres )
			<p class="wpmovielibrary-panel-empty">{{ esc_html__( 'No genres yet.', 'wpmovielibrary' ) }}</p>
		@else
			@foreach ( $top_genres as $index => $term )
				@php
					$icon = $genre_icons[ $term->slug ] ?? $genre_icons['default'];
					$stats = $taxonomy_stats[ $term->taxonomy . '_' . $term->term_id ] ?? [];
					$bar_width = 0 < $max_genre_count ? round( (int) $term->count / $max_genre_count * 100 ) : 100;
				@endphp
				@include( 'partials.taxonomy-row', [ 'term' => $term, 'rank' => $index + 1, 'visual' => $icon, 'variant' => 'icon', 'movie_count' => $stats['movie_count'] ?? 0, 'genre_count' => $stats['genre_count'] ?? null, 'avg_rating' => $stats['avg_rating'] ?? null, 'bar_width' => $bar_width, 'bar_color' => $term_colors[ $term->term_id ] ?? '#7b1fa2' ] )
			@endforeach
		@endif
	</div>
	@if ( $top_genres )
		<div class="wpmovielibrary-panel-footer">
			<a href="{!! esc_url( admin_url( 'edit-tags.php?taxonomy=wpmoly_movie_genre&post_type=movie' ) ) !!}" class="wpmovielibrary-panel-view-all">{{ sprintf( _n( 'View all %s genre', 'View all %s genres', $total_genres, 'wpmovielibrary' ), number_format_i18n( $total_genres ) ) }} &rarr;</a>
		</div>
	@endif
</div>
