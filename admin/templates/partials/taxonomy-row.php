@props([ 'term' => null, 'rank' => 1, 'visual' => '', 'variant' => 'portrait', 'movie_count' => 0, 'genre_count' => null, 'avg_rating' => null, 'bar_width' => 100, 'bar_color' => '#7b1fa2' ])
@php
	// The meta line shares a single row with the term name, so it is kept to
	// two segments: the movie count, then the average rating when the term has
	// one — falling back to the distinct genre count otherwise. The complete
	// breakdown goes to the link title, which has no width constraint.
	$movies = sprintf( _n( '%s movie', '%s movies', $movie_count, 'wpmovielibrary' ), number_format_i18n( $movie_count ) );
	$genres = null === $genre_count ? '' : sprintf( _n( '%s genre', '%s genres', $genre_count, 'wpmovielibrary' ), number_format_i18n( $genre_count ) );
	/* translators: %s: average rating, e.g. "3.25". The leading character is a black star. */
	$rating = null === $avg_rating ? '' : sprintf( __( '★ %s', 'wpmovielibrary' ), number_format_i18n( $avg_rating, 2 ) );

	$meta  = implode( ' · ', array_filter( [ $movies, $rating ?: $genres ] ) );
	$title = implode( ' · ', array_filter( [ $term->name, $movies, $genres, $rating ] ) );
@endphp
<a href="{!! esc_url( get_edit_term_link( $term->term_id, $term->taxonomy ) ) !!}" class="wpmovielibrary-taxonomy-row" title="{{ $title }}" style="--wpmovielibrary-bar-width: {{ (int) $bar_width }}%; --wpmovielibrary-bar-color: {{ $bar_color }};">
	<span class="wpmovielibrary-taxonomy-row-rank">{{ number_format_i18n( $rank ) }}</span>
	<span class="wpmovielibrary-taxonomy-row-visual is-{{ $variant }}">{!! $visual !!}</span>
	<span class="wpmovielibrary-taxonomy-row-body">
		<span class="wpmovielibrary-taxonomy-row-head">
			<span class="wpmovielibrary-taxonomy-row-name">{{ $term->name }}</span>
			<span class="wpmovielibrary-taxonomy-row-meta">{{ $meta }}</span>
		</span>
		<span class="wpmovielibrary-taxonomy-row-bar"><span class="wpmovielibrary-taxonomy-row-bar-fill"></span></span>
	</span>
</a>
