<?php
/**
 * Toast template.
 *
 * @since 3.0.0
 */
?>

				<div class="toast-icon"><span class="dashicons dashicons-{{ data.icon }}"></span></div>
				<div class="toast-content">{{{ data.content }}}</div>
				<div class="toast-buttons">
					<# if ( data.callback ) { #><button type="button" class="toast-cancel-button" data-action="cancel"><?php esc_html_e( 'Cancel' ); ?></button><# } #>
					<button type="button" class="toast-dismiss-button" data-action="dismiss" title="<?php esc_html_e( 'Dismiss' ); ?>"><span class="dashicons dashicons-no-alt"></span></button>
				</div>
