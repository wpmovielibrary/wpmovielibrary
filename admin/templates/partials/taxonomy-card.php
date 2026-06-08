@props([ 'term' => null, 'visual' => '' ])
<a href="{!! esc_url( get_edit_term_link( $term->term_id, $term->taxonomy ) ) !!}" class="wpmovielibrary-taxonomy-card" title="{{ $term->name }}">
	<span class="wpmovielibrary-taxonomy-card-visual">{!! $visual !!}</span>
	<span class="wpmovielibrary-taxonomy-card-name">{{ $term->name }}</span>
	<span class="wpmovielibrary-taxonomy-card-count">{!! esc_html( sprintf( _n( '%s movie', '%s movies', $term->count, 'wpmovielibrary' ), $term->count ) ) !!}</span>
</a>