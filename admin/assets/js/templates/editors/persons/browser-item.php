<?php
/**
 * Persons Editor browser item Template
 *
 * @since 3.0.0
 */
?>

			<div class="post-thumbnail person-picture" style="background-image:url({{ data.picture.sizes.medium.url }})">
			<div class="edit-menu">
			<# if ( 'publish' === data.post_status ) { #>
				<a href="{{ data.edit_link }}"></a>
				<button type="button" class="button trash" data-action="trash-post" title="<?php esc_html_e( 'Move to the trash', 'wppersonlibrary' ); ?>"><span class="wpmolicon icon-trash"></span></button>
				<div class="confirm">
					<p><?php esc_html_e( 'Move to trash?', 'wppersonlibrary' ); ?></p>
					<button type="button" class="button delete" data-action="confirm-trash-post"><?php esc_html_e( 'Yes' ); ?></button>
					<button type="button" class="button dismiss" data-action="dismiss" title="<?php esc_html_e( 'Dismiss' ); ?>"><span class="wpmolicon icon-no"></span></button>
				</div>
				<!--<a href="{{ data.edit_link }}" class="button edit"><span class="wpmolicon icon-edit"></span></a>-->
				<!--<button type="button" class="button preview" data-action="preview-person"><span class="wpmolicon icon-series"></span></button>-->
			<# } else if ( 'draft' === data.post_status ) { #>
				<button type="button" class="button restore" data-action="restore-post" title="<?php esc_html_e( 'Publish person', 'wppersonlibrary' ); ?>"><span class="wpmolicon icon-publish"></span></button>
				<button type="button" class="button trash" data-action="trash-post" title="<?php esc_html_e( 'Move to the trash', 'wppersonlibrary' ); ?>"><span class="wpmolicon icon-trash"></span></button>
				<div class="confirm">
					<p><?php esc_html_e( 'Move to trash?', 'wppersonlibrary' ); ?></p>
					<button type="button" class="button delete" data-action="confirm-trash-post"><?php esc_html_e( 'Yes' ); ?></button>
					<button type="button" class="button dismiss" data-action="dismiss" title="<?php esc_html_e( 'Dismiss' ); ?>"><span class="wpmolicon icon-no"></span></button>
				</div>
			<# } else if ( 'trash' === data.post_status ) { #>
				<button type="button" class="button restore" data-action="restore-post" title="<?php esc_html_e( 'Restore person', 'wppersonlibrary' ); ?>"><span class="wpmolicon icon-restore"></span></button>
				<button type="button" class="button delete" data-action="delete-post" title="<?php esc_html_e( 'Delete permanently', 'wppersonlibrary' ); ?>"><span class="wpmolicon icon-trash"></span></button>
				<div class="confirm">
					<p><?php esc_html_e( 'Delete permanently?', 'wppersonlibrary' ); ?></p>
					<button type="button" class="button delete" data-action="confirm-delete-post"><?php esc_html_e( 'Yes' ); ?></button>
					<button type="button" class="button dismiss" data-action="dismiss" title="<?php esc_html_e( 'Dismiss' ); ?>"><span class="wpmolicon icon-no"></span></button>
				</div>
			<# } #>
			</div>
		</div>
		<div class="post-title person-title"><a href="{{ data.edit_link }}">{{{ data.title }}}</a></div>
