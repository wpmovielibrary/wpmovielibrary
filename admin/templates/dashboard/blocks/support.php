<?php
/**
 * Dashboard Support Block Template
 *
 * @since 3.0.0
 *
 * @uses $id
 * @uses $title
 * @uses $contact_url
 * @uses $hire_url
 */
?>

					<div id="dashboard-<?php echo esc_attr( $id ); ?>-block" class="dashboard-block support-block">
						<button type="button" class="button arrow" data-action="collapse"><span class="wpmolicon icon-up-chevron"></span></button>
						<h3 class="block-title"><?php echo esc_html( $title ); ?></h3>
						<div class="block-content">
							<p><?php printf( __( 'Need some help to run your library? <a href="%s" target="_blank">Get in touch</a>!', 'wpmovielibrary' ), esc_url( $contact_url ) ); ?></p>
							<p><?php printf( __( 'You want custom features and look n\' feel? Consider <a href="%s" target="_blank">hiring us</a>!', 'wpmovielibrary' ), esc_url( $hire_url ) ); ?></p>
						</div>
					</div>
