<?php
/**
 * Default Persons Grid JavaScript template.
 *
 * @since 3.0.0
 */

?>

				<div id="{{ data.uid }}" class="item-thumbnail item-picture post-picture person-picture" style="background-image:url({{ data.picture.sizes.medium.url || '' }})">
					<a href="{{ data.permalink || '' }}" title="{{ data.name }}"></a>
				</div>
				<div class="item-title post-title person-name"><a href="{{ data.permalink || '' }}">{{{ data.name || '' }}}</a></div>
