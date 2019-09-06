<?php
/**
 * Grid Browser Item Template
 *
 * @since 3.0.0
 */
?>

			<div class="post-thumbnail grid-thumbnail">
				<div class="edit-menu">
<# if ( 'publish' === data.status ) { #>
					<a href="{{ data.edit_link }}"></a>
<# } else if ( 'draft' === data.status ) { #>
					<button type="button" class="button restore" data-action="restore-post" title="<?php esc_html_e( 'Publish grid', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-publish"></span></button>
					<button type="button" class="button trash" data-action="trash-post" title="<?php esc_html_e( 'Move to the trash', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-trash"></span></button>
					<div class="confirm">
						<p><?php esc_html_e( 'Move to trash?', 'wpmovielibrary' ); ?></p>
						<button type="button" class="button" data-action="confirm-trash-post"><?php esc_html_e( 'Yes' ); ?></button>
						<button type="button" class="button dismiss" data-action="dismiss" title="<?php esc_html_e( 'Dismiss' ); ?>"><span class="wpmolicon icon-no"></span></button>
					</div>
<# } else if ( 'trash' === data.status ) { #>
					<button type="button" class="button restore" data-action="restore-post" title="<?php esc_html_e( 'Restore grid', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-restore"></span></button>
					<button type="button" class="button delete" data-action="delete-post" title="<?php esc_html_e( 'Delete permanently', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-trash"></span></button>
					<div class="confirm">
						<p><?php esc_html_e( 'Delete permanently?', 'wpmovielibrary' ); ?></p>
						<button type="button" class="button" data-action="confirm-delete-post"><?php esc_html_e( 'Yes' ); ?></button>
						<button type="button" class="button dismiss" data-action="dismiss" title="<?php esc_html_e( 'Dismiss' ); ?>"><span class="wpmolicon icon-no"></span></button>
					</div>
<# } #>
				</div>
			</div>
			<div class="post-title grid-title">
<# if ( 'publish' === data.status ) { #>
				<a href="{{ data.edit_link }}">{{ data.title.rendered || 'Unknown Title' }}</a>
<# } else { #>
				{{ data.title.rendered || 'Unknown Title' }}
<# } #>
			</div>
