@props([ 'term' => null, 'visual' => '', 'movie_count' => 0, 'genre_count' => null, 'avg_rating' => null ])
<a href="{!! esc_url( get_edit_term_link( $term->term_id, $term->taxonomy ) ) !!}" class="wpmovielibrary-taxonomy-card" title="{{ $term->name }}">
	<span class="wpmovielibrary-taxonomy-card-visual">{!! $visual !!}</span>
	<span class="wpmovielibrary-taxonomy-card-body">
		<span class="wpmovielibrary-taxonomy-card-name">{{ $term->name }}</span>
		<span class="wpmovielibrary-taxonomy-card-count">{!! esc_html( sprintf( _n( '%s movie', '%s movies', $movie_count, 'wpmovielibrary' ), number_format_i18n( $movie_count ) ) ) !!}@if ( null !== $genre_count ) <span class="wpmovielibrary-taxonomy-card-sep">&middot;</span> {!! esc_html( sprintf( _n( '%s genre', '%s genres', $genre_count, 'wpmovielibrary' ), number_format_i18n( $genre_count ) ) ) !!}@endif</span>
		<span class="wpmovielibrary-taxonomy-card-rating">@if ( null === $avg_rating )&mdash;@else{!! esc_html( sprintf( __( '%s avg rating', 'wpmovielibrary' ), number_format_i18n( $avg_rating, 2 ) ) ) !!}@endif</span>
	</span>
</a>
