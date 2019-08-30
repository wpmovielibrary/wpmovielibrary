<?php
/**
 * Posts Browser Context Menu Template
 *
 * @since 3.0.0
 */
?>

			<div class="context-menu-content">
<# if ( 'publish' === data.post.status ) { #>
				<div class="context-menu-item trash-item" data-action="trash">
					<div class="context-menu-icon"><span class="wpmolicon icon-trash"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Delete', 'wpmovielibray' ); ?></div>
				</div>
<# } else if ( 'draft' === data.post.status ) { #>
				<div class="context-menu-item restore-item" data-action="restore">
					<div class="context-menu-icon"><span class="wpmolicon icon-publish"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Restore', 'wpmovielibray' ); ?></div>
				</div>
				<div class="context-menu-item trash-item" data-action="trash">
					<div class="context-menu-icon"><span class="wpmolicon icon-trash"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Delete', 'wpmovielibray' ); ?></div>
				</div>
<# } else if ( 'trash' === data.post.status ) { #>
				<div class="context-menu-item restore-item" data-action="restore">
					<div class="context-menu-icon"><span class="wpmolicon icon-restore"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Restore', 'wpmovielibray' ); ?></div>
				</div>
				<div class="context-menu-item restore-item" data-action="delete">
					<div class="context-menu-icon"><span class="wpmolicon icon-trash"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Delete permanently', 'wpmovielibray' ); ?></div>
				</div>
<# } #>
			</div>
