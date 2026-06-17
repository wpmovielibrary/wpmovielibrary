<div id="wpmovielibrary-my-actors" class="wpmovielibrary-panel col-span-4">
	<div class="wpmovielibrary-panel-header">{{ esc_html__( 'Most Popular Actors', 'wpmovielibrary' ) }}</div>
	<div class="wpmovielibrary-panel-content">
		@if ( ! $top_actors )
			<p class="wpmovielibrary-panel-empty">{{ esc_html__( 'No actors yet.', 'wpmovielibrary' ) }}</p>
		@else
			@php
				// Generic human silhouette, shared by all actor cards.
				$silhouette = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><circle cx="24" cy="15" r="9"/><path d="M7 42c0-9.5 7.5-15 17-15s17 5.5 17 15z"/></svg>';
			@endphp
			@foreach ( $top_actors as $term )
				@php
					$stats = $taxonomy_stats[ $term->taxonomy . '_' . $term->term_id ] ?? [];
				@endphp
				@include( 'partials.taxonomy-card', [ 'term' => $term, 'visual' => $silhouette, 'movie_count' => $stats['movie_count'] ?? 0, 'genre_count' => $stats['genre_count'] ?? null, 'avg_rating' => $stats['avg_rating'] ?? null ] )
			@endforeach
		@endif
	</div>
</div>