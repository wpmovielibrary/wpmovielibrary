<?php
/**
 * Default Actor Grid JavaScript template.
 *
 * @since 3.0.0
 */

?>

				<div class="item-thumbnail item-thumbnail term-thumbnail actor-thumbnail" style="background-image:url({{ data.thumbnail }})">
					<a href="{{ data.link }}"></a>
				</div>
				<div class="item-name term-name actor-name"><a href="{{ data.link }}">{{ data.name }}</a></div>
				<div class="item-count term-count actor-count">{{ wpmoly._n( wpmolyL10n.n_movie_found, data.count ) }}</div>
