<div id="wpmovielibrary-recently-added-movies" class="wpmovielibrary-panel">
	<div class="wpmovielibrary-panel-header">{{ esc_html__( 'Recently Added Movies', 'wpmovielibrary' ) }}</div>
	<div class="wpmovielibrary-panel-content">
		@if ( ! $recently_added )
			<p class="wpmovielibrary-panel-empty">{{ esc_html__( 'No movies in your library yet.', 'wpmovielibrary' ) }}</p>
		@else
			<div class="wpmovielibrary-panel-scroll-indicator wpmovielibrary-panel-scroll-indicator-left">
				<button><span class="dashicons dashicons-arrow-left-alt2"></span></button>
			</div>
			<div class="wpmovielibrary-panel-scroll-indicator wpmovielibrary-panel-scroll-indicator-right">
				<button><span class="dashicons dashicons-arrow-right-alt2"></span></button>
			</div>
			<div class="wpmovielibrary-panel-scroll-wrapper">
				@foreach ( $recently_added as $movie_id )
					@include( 'partials.movie-card', [ 'movie_id' => $movie_id ] )
				@endforeach
			</div>
		@endif
	</div>
</div>