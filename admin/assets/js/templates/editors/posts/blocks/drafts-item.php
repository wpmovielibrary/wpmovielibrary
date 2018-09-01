<?php
/**
 * Post Browser 'Drafts' Block Item Template
 *
 * @since 3.0.0
 */
?>

		<a href="{{ data.edit_link }}">{{{ data.title.rendered }}}</a>
		<button type="button" class="button publish" data-action="restore-post" data-item-id="{{ data.id }}" title="{{ wpmolyEditorL10n.publish_post }}"><span class="wpmolicon icon-publish"></span></button>
		<button type="button" class="button remove" data-action="trash-post" data-item-id="{{ data.id }}" title="{{ wpmolyEditorL10n.move_post_to_trash }}"><span class="wpmolicon icon-trash-alt"></span></button>
