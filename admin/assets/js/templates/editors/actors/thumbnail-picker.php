<?php
/**
 * Actor Thumbnail Editor Picker Template
 *
 * @since 3.0.0
 */
?>

					<div class="image picture">
						<div class="picture-thumbnail" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-neutral-thumbnail.png' ); ?>)">
							<div class="download-picture">
								<button type="button" class="button empty" data-action="set-as" data-thumbnail="<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-neutral-medium.png' ); ?>" data-value="neutral" title="Set as term thumbnail">{{ 'svg:icon:picture' }}</button>
							</div>
						</div>
					</div>
					<div class="image picture">
						<div class="picture-thumbnail" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-male-thumbnail.png' ); ?>)">
							<div class="download-picture">
								<button type="button" class="button empty" data-action="set-as" data-thumbnail="<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-male-medium.png' ); ?>" data-value="male" title="Set as term thumbnail">{{ 'svg:icon:picture' }}</button>
							</div>
						</div>
					</div>
					<div class="image picture">
						<div class="picture-thumbnail" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-female-thumbnail.png' ); ?>)">
							<div class="download-picture">
								<button type="button" class="button empty" data-action="set-as" data-thumbnail="<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-female-medium.png' ); ?>" data-value="female" title="Set as term thumbnail">{{ 'svg:icon:picture' }}</button>
							</div>
						</div>
					</div>
