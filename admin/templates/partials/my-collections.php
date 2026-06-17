<div id="wpmovielibrary-my-collections" class="wpmovielibrary-panel col-span-4">
	<div class="wpmovielibrary-panel-header">{{ esc_html__( 'Most Popular Collections', 'wpmovielibrary' ) }}</div>
	<div class="wpmovielibrary-panel-content">
		@if ( ! $top_collections )
			<p class="wpmovielibrary-panel-empty">{{ esc_html__( 'No collections yet.', 'wpmovielibrary' ) }}</p>
		@else
			@foreach ( $top_collections as $term )
				@php
					// Initials: first letter of the first two words, skipping
					// leading articles; first two letters if there is only one
					// word. Background hue derives from the slug so each
					// collection keeps a stable color.
					$words = preg_split( '/\s+/', trim( (string) $term->name ) );
					$meaningful = array_values( array_filter( $words, fn( $word ) => ! in_array( mb_strtolower( $word ), [ 'the', 'a', 'an', 'le', 'la', 'les' ], true ) ) ) ?: $words;
					$initials = 1 < count( $meaningful ) ? mb_substr( $meaningful[0], 0, 1 ) . mb_substr( $meaningful[1], 0, 1 ) : mb_substr( $meaningful[0], 0, 2 );
					$initials = mb_strtoupper( $initials );
					$hue = ( crc32( $term->slug ) % 8 ) * 45;
					$letters = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" class="wpmovielibrary-taxonomy-card-letters"><rect width="48" height="48" fill="hsl(' . $hue . ', 45%, 42%)"/><text x="24" y="25" text-anchor="middle" dominant-baseline="central" font-size="19" font-weight="600" fill="#fff">' . esc_html( $initials ) . '</text></svg>';
					$stats = $taxonomy_stats[ $term->taxonomy . '_' . $term->term_id ] ?? [];
				@endphp
				@include( 'partials.taxonomy-card', [ 'term' => $term, 'visual' => $letters, 'movie_count' => $stats['movie_count'] ?? 0, 'genre_count' => $stats['genre_count'] ?? null, 'avg_rating' => $stats['avg_rating'] ?? null ] )
			@endforeach
		@endif
	</div>
</div>