<?php
/**
 * Default Movie List JavaScript template.
 *
 * @since 3.0.0
 */

?>

					<div class="item-thumbnail item-poster post-poster movie-poster" style="background-image:url({{ data.poster.sizes.thumbnail.url }})">
						<a href="{{ data.permalink }}" title="{{ data.title }}"></a>
					</div>
					<div class="item-content">
						<div class="item-title movie-title"><a href="{{ data.permalink }}">{{ data.title }}</a></div>
						<div class="item-meta movie-meta"><span class="movie-genres">{{{ data.genres }}}</span></div>
						<div class="item-meta movie-meta"><span class="movie-runtime">{{{ data.runtime }}}</span></div>
					</div>
