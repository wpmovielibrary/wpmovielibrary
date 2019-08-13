<?php
/**
 * Default Movie List JavaScript template.
 *
 * @since 3.0.0
 */

?>

					<div class="item-thumbnail item-picture post-picture person-picture" style="background-image:url({{ data.picture.sizes.thumbnail.url }})">
						<a href="{{ data.permalink }}" title="{{ data.name }}"></a>
					</div>
					<div class="item-content">
						<div class="item-title person-name"><a href="{{ data.permalink }}">{{ data.name }}</a></div>
					</div>
