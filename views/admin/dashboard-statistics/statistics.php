
							<div class="main">
								<p><?php _e( 'Here\'s some statistics about your movie library:', 'wpmovielibrary' ) ?></p>
								<ul>
									<?php echo $links ?>
								</ul>
								<p><?php
								printf(
									__( 'All combined you have a total of %s in your library, regrouped in %s, %s and %s.', 'wpmovielibrary' ),
									sprintf( '<a href="%s">%s</a>', admin_url( 'edit.php?post_type=movie' ), sprintf( _n( 'one movie', '%s movies', $count['total'], 'wpmovielibrary' ), '<strong>' . $count['total'] . '</strong>' ) ),
									sprintf( '<a href="%s">%s</a>', admin_url( 'edit-tags.php?taxonomy=collection&post_type=movie' ), sprintf( _n( 'one collection', '%s collections', $count['collections'], 'wpmovielibrary' ), '<strong>' . $count['collections'] . '</strong>' ) ),
									sprintf( '<a href="%s">%s</a>', admin_url( 'edit-tags.php?taxonomy=genre&post_type=movie' ), sprintf( _n( 'one genre', '%s genres', $count['genres'], 'wpmovielibrary' ), '<strong>' . $count['genres'] . '</strong>' ) ),
									sprintf( '<a href="%s">%s</a>', admin_url( 'edit-tags.php?taxonomy=actor&post_type=movie' ), sprintf( _n( 'one actor', '%s actors', $count['actors'], 'wpmovielibrary' ), '<strong>' . $count['actors'] . '</strong>' ) )
								) ?></p>
							</div>
