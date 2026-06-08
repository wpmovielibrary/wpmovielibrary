<div id="wpmovielibrary-my-genres" class="wpmovielibrary-panel col-span-4">
	<div class="wpmovielibrary-panel-header">{{ esc_html__( 'Most Popular Genres', 'wpmovielibrary' ) }}</div>
	<div class="wpmovielibrary-panel-content">
		@if ( ! $top_genres )
			<p class="wpmovielibrary-panel-empty">{{ esc_html__( 'No genres yet.', 'wpmovielibrary' ) }}</p>
		@else
			@foreach ( $top_genres as $term )
				@php
					$icon = $genre_icons[ $term->slug ] ?? $genre_icons['default'];
				@endphp
				@include( 'partials.taxonomy-card', [ 'term' => $term, 'visual' => $icon ] )
			@endforeach
		@endif
	</div>
</div>