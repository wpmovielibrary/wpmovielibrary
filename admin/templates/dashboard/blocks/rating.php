<?php
/**
 * Dashboard Rating Block Template
 *
 * @since 3.0.0
 *
 * @uses $id
 * @uses $title
 * @uses $testimony_url
 */
?>

					<div id="dashboard-<?php echo esc_attr( $id ); ?>-block" class="dashboard-block rating-block">
						<button type="button" class="button arrow" data-action="collapse"><span class="wpmolicon icon-up-chevron"></span></button>
						<h3 class="block-title"><?php echo esc_html( $title ); ?></h3>
						<div class="block-content">
							<p><?php printf( __( 'You love your library? Share your <a href="%s" target="_blank">testimonial</a>!', 'wpmovielibrary' ), esc_url( $testimony_url ) ); ?></p>
						</div>
					</div>
