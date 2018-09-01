<?php
/**
 * Variant-1 Movies Grid JavaScript template.
 *
 * @since 3.0.0
 */

?>

				<div class="item-thumbnail item-poster post-poster movie-poster" style="background-image:url({{ data.poster.sizes.medium.url }})">
					<a href="{{ data.permalink }}" title="{{ data.title }}"></a>
				</div>
				<div class="item-content">
					<div class="item-title post-title movie-title"><a href="{{ data.permalink }}">{{{ data.title }}}</a></div>
					<div class="item-genres post-genres movie-genres">{{{ data.genres }}}</div>
					<div class="item-runtime post-runtime movie-runtime">{{{ data.rating }}}</div>
					<div class="item-readmore"><a href="{{ data.permalink }}">+</a></div>
				</div>
