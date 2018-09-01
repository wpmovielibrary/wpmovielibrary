<?php
/**
 * Term Browser Item Template.
 *
 * @since 3.0.0
 */
?>

		<div class="term-thumbnail" style="background-image:url({{ data.thumbnail }})">
			<a href="{{ data.edit_link }}"></a>
			<button type="button" class="button trash" data-action="delete-term" title="{{ wpmolyEditorL10n.delete_term }}"><span class="wpmolicon icon-trash"></span></button>
			<div class="confirm">
				<p>{{ wpmolyEditorL10n.delete_term }}</p>
				<button type="button" class="button delete" data-action="confirm-delete-term">{{ wpmolyL10n.yes }}</button>
				<button type="button" class="button dismiss" data-action="dismiss" title="{{ wpmolyL10n.dismiss }}"><span class="wpmolicon icon-no"></span></button>
			</div>
		</div>
		<div class="term-name">
			<a href="{{ data.edit_link }}">{{ data.name }}</a>
		</div>
		<div class="term-count">{{ wpmoly._n( wpmolyL10n.n_movie_found, data.count ) }}</div>
