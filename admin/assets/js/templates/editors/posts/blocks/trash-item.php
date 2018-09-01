<?php
/**
 * Post Browser 'Trash' Block Item Template
 *
 * @since 3.0.0
 */
?>

		<a>{{{ data.title.rendered }}}</a>
		<button type="button" class="button restore" data-action="restore-post" data-item-id="{{ data.id }}" title="{{ wpmolyEditorL10n.restore_post }}"><span class="wpmolicon icon-restore"></span></button>
		<button type="button" class="button remove" data-action="trash-post" data-item-id="{{ data.id }}" title="{{ wpmolyEditorL10n.delete_post }}"><span class="wpmolicon icon-trash-alt"></span></button>
		<div class="confirm">
			<button type="button" class="button dismiss" data-action="dismiss" title="{{ wpmolyL10n.dismiss }}">{{ wpmolyL10n.no }}</button>
			<button id="confirm-trash-post" type="button" class="button accept" data-action="confirm-trash-post">{{ wpmolyL10n.yes }}</button>
			<p>{{ wpmolyEditorL10n.delete_permanently }}</p>
		</div>
