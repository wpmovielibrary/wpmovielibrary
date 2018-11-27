<?php
/**
 * Movie Editor 'Submit' Block Template
 *
 * @since 3.0.0
 */
?>

						<div class="confirm">
							<span>{{ wpmolyEditorL10n.move_post_to_trash }}</span>
							<button type="button" class="button" data-action="confirm-trash">{{ wpmolyL10n.yes }}</button>
							<button type="button" class="button" data-action="dismiss">{{ wpmolyL10n.no }}</button>
						</div>
						<div class="content">
							<button type="button" class="button menu" data-action="menu">
<# if ( 'preview' === data.mode ) { #>
								{{ 'svg:icon:preview' }}{{ 'svg:icon:caret' }}
<# } else if ( 'download' === data.mode ) { #>
								{{ 'svg:icon:download' }}{{ 'svg:icon:caret' }}
<# } else if ( 'snapshot' === data.mode ) { #>
								{{ 'svg:icon:snapshot' }}{{ 'svg:icon:caret' }}
<# } else if ( 'edit-post' === data.mode ) { #>
								{{ 'svg:icon:edit-post' }}{{ 'svg:icon:caret' }}
<# } else { #>
								{{ 'svg:icon:menu' }}
<# } #>
							</button>
							<div class="dropdown-menu">
								<button type="button" class="button preview" data-mode="preview">{{ 'svg:icon:preview' }}{{ wpmolyEditorL10n.preview_label }}</button>
								<button type="button" class="button download" data-mode="download">{{ 'svg:icon:download' }}{{ wpmolyEditorL10n.download_label }}</button>
								<button type="button" class="button snapshot" data-mode="snapshot">{{ 'svg:icon:snapshot' }}{{ wpmolyEditorL10n.snapshot_label }}</button>
								<div class="separator"></div>
								<button type="button" class="button editor" data-mode="editor">{{ 'svg:icon:edit-post' }}{{ wpmolyEditorL10n.old_editor_label }}</button>
							</div>
							<button type="button" class="button trash" data-action="trash">{{ 'svg:icon:trash' }}<span class="spinner"></span></button>
							<button type="button" class="button save" data-action="save">{{ 'svg:icon:save' }}<span class="spinner"></span></button>
						</div>
