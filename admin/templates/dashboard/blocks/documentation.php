<?php
/**
 * Dashboard Documentation Block Template
 *
 * @since 3.0.0
 *
 * @uses $id
 * @uses $title
 */
?>

					<div id="dashboard-<?php echo esc_attr( $id ); ?>-block" class="dashboard-block documentation-block">
						<button type="button" class="button arrow" data-action="close"><span class="wpmolicon icon-up-chevron"></span></button>
						<h3 class="block-title"><?php echo esc_html( $title ); ?></h3>
						<div class="block-content">
							<p><?php _e( 'Learn how to use your library to its full extend in the <a href="https://wpmovielibrary.com/documentation">documentation</a>.', 'wpmovielibrary' ); ?></p>
							<p><?php _e( 'Plugin/Theme developer? Take a look at the <a href="https://wpmovielibrary.com/developer-guide">developer\'s guide</a>.', 'wpmovielibrary' ); ?></p>
						</div>
					</div>
