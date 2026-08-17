<div id="wpmovielibrary-my-actors" class="wpmovielibrary-panel wpmovielibrary-panel-leaderboard col-span-4">
	<div class="wpmovielibrary-panel-header">{{ esc_html__( 'Most Popular Actors', 'wpmovielibrary' ) }}</div>
	<div class="wpmovielibrary-panel-content">
		@if ( ! $top_actors )
			<p class="wpmovielibrary-panel-empty">{{ esc_html__( 'No actors yet.', 'wpmovielibrary' ) }}</p>
		@else
			@php
				// Generic human silhouette, shared by all actor rows.
				$silhouette = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><circle cx="24" cy="15" r="9"/><path d="M7 42c0-9.5 7.5-15 17-15s17 5.5 17 15z"/></svg>';
			@endphp
			@foreach ( $top_actors as $index => $term )
				@php
					$stats = $taxonomy_stats[ $term->taxonomy . '_' . $term->term_id ] ?? [];
					$bar_width = 0 < $max_actor_count ? round( (int) $term->count / $max_actor_count * 100 ) : 100;
				@endphp
				@include( 'partials.taxonomy-row', [ 'term' => $term, 'rank' => $index + 1, 'visual' => $silhouette, 'variant' => 'portrait', 'movie_count' => $stats['movie_count'] ?? 0, 'genre_count' => $stats['genre_count'] ?? null, 'avg_rating' => $stats['avg_rating'] ?? null, 'bar_width' => $bar_width, 'bar_color' => $term_colors[ $term->term_id ] ?? '#7b1fa2' ] )
			@endforeach
		@endif
	</div>
	@if ( $top_actors )
		<div class="wpmovielibrary-panel-footer">
			<a href="{!! esc_url( admin_url( 'edit-tags.php?taxonomy=wpmoly_movie_actor&post_type=movie' ) ) !!}" class="wpmovielibrary-panel-view-all">{{ sprintf( _n( 'View all %s actor', 'View all %s actors', $total_actors, 'wpmovielibrary' ), number_format_i18n( $total_actors ) ) }} &rarr;</a>
		</div>
	@endif
</div>
