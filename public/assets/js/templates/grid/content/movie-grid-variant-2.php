<?php
/**
 * Variant-2 Movies Grid JavaScript template.
 *
 * @since 3.0.0
 */

?>

				<div class="item-thumbnail item-poster post-poster movie-poster" style="background-image:url({{ data.poster.sizes.medium.url }})">
					<a href="{{ data.permalink }}" title="{{ data.title }}"></a>
				</div>
				<div class="item-content clearfix">
					<div class="movie-title"><a href="{{ data.permalink }}">{{{ data.title }}}</a></div>
					<div class="movie-year">{{{ data.year }}}</div>
					<div class="movie-rating">{{{ data.rating }}}</div>
				</div>
