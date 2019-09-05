<?php
/**
 * Dashboard Discover Block Template
 *
 * @since 3.0.0
 *
 * @uses $id
 * @uses $title
 * @uses $license
 * @uses $license_url
 * @uses $movies
 * @uses $movies_url
 * @uses $grids
 * @uses $grids_url
 * @uses $actors
 * @uses $actors_url
 * @uses $genres
 * @uses $genres_url
 */
?>

					<div id="dashboard-<?php echo esc_attr( $id ); ?>-block" class="dashboard-block discover-block">
						<button type="button" class="button arrow" data-action="collapse"><span class="wpmolicon icon-up-chevron"></span></button>
						<?php if ( 'missing' === $license ) : ?>
						<div class="no-license-alert license-alert">
							<p><?php printf( __( '<strong>Warning!</strong> You haven\'t have registered your license key. <a href="%s">Do it now</a>?', 'wpmovielibrary' ), esc_url( $license_url ) ); ?></p>
						</div>
						<?php elseif ( 'expired' === $license ) : ?>
						<div class="expired-license-alert license-alert">
							<p><?php printf( __( '<strong>Warning!</strong> Your license key has expired. <a href="%s">Update it</a>?', 'wpmovielibrary' ), esc_url( $license_url ) ); ?></p>
						</div>
						<?php endif; ?>
						<h3 class="block-title"><?php echo esc_html( $title ); ?></h3>
						<div class="block-content">
							<?php if ( ! $movies ) : ?>
							<p><?php printf( __( 'You don\'t have any movie yet! Would you like to <a href="%s">add some</a>?', 'wpmovielibrary' ), esc_url( admin_url( 'post-new.php?post_type=movie' ) ) ); ?></p>
							<?php else : ?>
							<p><?php printf( __( 'You have %s in your library!', 'wpmovielibrary' ), sprintf( '<a href="%s">%s</a>', esc_url( $movies_url ), sprintf( _n( '%s Movie', '%s Movies', $movies, 'wpmovielibrary' ), $movies ) ) ); ?></p>
							<?php endif; ?>
							<p class="small"><?php printf( __( 'You also have <a href="%s">%s</a>, <a href="%s">%s</a> and <a href="%s">%s</a>.', 'wpmovielibrary' ), esc_url( $grids_url ), sprintf( _n( '1 Grid', '%s Grids', $grids, 'wpmovielibrary' ), $grids ), esc_url( $actors_url ), sprintf( _n( '1 Actor', '%s Actors', $actors, 'wpmovielibrary' ), $actors ), esc_url( $genres_url ), sprintf( _n( '1 Genre', '%s Genres', $genres, 'wpmovielibrary' ), $genres ) ); ?></p>
						</div>
					</div>
