
							<div class="main">
								<p><?php _e( 'Here\'s some statistics about your movie library:', WPML_SLUG ) ?></p>
								<ul>
									<?php echo $links ?>
								</ul>
								<p><?php
								//TODO: <strong> only for numbers
								printf(
									__( 'All combined you have a total of %s in your library, regrouped in %s, %s and %s.', WPML_SLUG ),
									sprintf( '<strong>%s</strong>', sprintf( _n( 'one movie', '%d movies', $count['total'], WPML_SLUG ), $count['total'] ) ),
									sprintf( '<strong>%s</strong>', sprintf( _n( 'one collection', '%d collections', $count['collections'], WPML_SLUG ), $count['collections'] ) ),
									sprintf( '<strong>%s</strong>', sprintf( _n( 'one ', '%d genres', $count['genres'], WPML_SLUG ), $count['genres'] ) ),
									sprintf( '<strong>%s</strong>', sprintf( _n( 'one ', '%d actors', $count['actors'], WPML_SLUG ), $count['actors'] ) )
								) ?></p>
							</div>
