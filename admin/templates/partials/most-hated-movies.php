<div id="wpmovielibrary-most-hated-movies" class="wpmovielibrary-panel col-span-6">
	<div class="wpmovielibrary-panel-header">{{ esc_html__( 'Most Hated Movies', 'wpmovielibrary' ) }}</div>
	<div class="wpmovielibrary-panel-content">
		@if ( ! $most_hated )
			<p class="wpmovielibrary-panel-empty">{{ esc_html__( 'No rated movies yet.', 'wpmovielibrary' ) }}</p>
		@else
			<div class="wpmovielibrary-panel-scroll-indicator wpmovielibrary-panel-scroll-indicator-left">
				<button><span class="dashicons dashicons-arrow-left-alt2"></span></button>
			</div>
			<div class="wpmovielibrary-panel-scroll-indicator wpmovielibrary-panel-scroll-indicator-right">
				<button><span class="dashicons dashicons-arrow-right-alt2"></span></button>
			</div>
			<div class="wpmovielibrary-panel-scroll-wrapper">
				@foreach ( $most_hated as $movie_id )
					@include( 'partials.movie-card', [ 'movie_id' => $movie_id, 'show_rating' => true ] )
				@endforeach
			</div>
		@endif
	</div>
</div>