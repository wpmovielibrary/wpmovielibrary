<?php
/**
 * Default Collection List JavaScript template.
 *
 * @since 3.0.0
 */

?>

					<div class="item-name collection-name"><a href="{{ data.link }}">{{ data.name }}</a></div>
					<div class="item-count collection-count">{{ wpmoly._n( wpmolyL10n.n_movie_found, data.count ) }}</div>
