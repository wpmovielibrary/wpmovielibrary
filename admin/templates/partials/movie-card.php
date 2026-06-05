@props([ 'movie_id' => 0, 'show_rating' => false ])
<a href="{!! esc_url( get_edit_post_link( $movie_id ) ) !!}" class="wpmovielibrary-movie-card" title="{{ get_post_meta( $movie_id, '_wpmoly_movie_title', true ) ?: get_the_title( $movie_id ) }}">
	{!! get_the_post_thumbnail( $movie_id, 'medium', [ 'class' => 'wpmovielibrary-movie-card-poster' ] ) ?: '<span class="wpmovielibrary-movie-card-poster is-placeholder"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Pro 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2024 Fonticons, Inc.--><path d="M361.9 32l-1 1-127 127h92.1l1-1 127-127H361.9zM512 160V41.9L393.9 160H512zM294.1 32H201.9l-1 1L73.9 160h92.1l1-1 127-127zM0 32V160H6.1l1-1 127-127H0zM512 192H0V480H512V192z"/></svg></span>' !!}
	@if ( $show_rating )
		@php
			$rating = get_post_meta( $movie_id, '_wpmoly_movie_rating', true );
			if ( 0.0 === (float) $rating ) {
				$rating = null;
			}
		@endphp
		<span class="wpmovielibrary-movie-card-rating">★ {{ $rating ?: 'N/A' }}</span>
	@endif
</a>