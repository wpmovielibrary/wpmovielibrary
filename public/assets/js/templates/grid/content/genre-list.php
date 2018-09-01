<?php
/**
 * Default Genre List JavaScript template.
 *
 * @since 3.0.0
 */

?>

					<div class="item-title genre-title"><a href="{{ data.link }}">{{ data.name }}</a></div>
					<div class="item-count genre-count">{{ wpmoly._n( wpmolyL10n.n_movie_found, data.count ) }}</div>
