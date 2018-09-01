<?php
/**
 * Post Editor 'Submit' Block Template
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
							<a href="{{ data.old_edit_link }}" class="button settings" title="{{ wpmolyEditorL10n.old_editor_label }}">{{ 'svg:icon:edit-post' }}</a>
							<button type="button" class="button trash" data-action="trash">{{ 'svg:icon:trash' }}<span class="spinner"></span></button>
							<button type="button" class="button save" data-action="save">{{ 'svg:icon:save' }}<span class="spinner"></span>
							</button>
						</div>
