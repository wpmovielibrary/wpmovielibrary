<?php
/**
 * Post Browser 'Drafts' Block Template
 *
 * @since 3.0.0
 */
?>

<# if ( _.isEmpty( data.posts ) ) { #>
							<p class="description">{{ wpmolyEditorL10n.no_post_in_trash }}</p>
<# } else { #>
							<p class="description">{{{ wpmoly._n( wpmolyEditorL10n.n_draft_post, data.posts.length ) }}}</p>
							<ul class="posts-list"></ul>
							<div class="confirm">
								<p class="description">{{ wpmolyEditorL10n.about_trash_draft }}</p>
								<input type="checkbox" id="trash-drafts-confirmed" value="1" /><label for="trash-drafts-confirmed">{{ wpmolyL10n.i_know }}</label>
								<button type="button" class="button delete" data-action="confirm-trash-posts" disabled="disabled">{{ wpmolyEditorL10n.trash_drafts }}</button>
								<button type="button" class="button dismiss" data-action="dismiss" title="{{ wpmolyL10n.dismiss }}"><span class="wpmolicon icon-no"></span></button>
							</div>
							<button type="button" class="button delete" data-action="trash-posts">{{ wpmolyEditorL10n.trash_drafts }}</button>
<# } #>
