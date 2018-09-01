<?php
/**
 * Post Browser 'Trash' Block Template
 *
 * @since 3.0.0
 */
?>

<# if ( _.isEmpty( data.posts ) ) { #>
							<p class="description">{{ wpmolyEditorL10n.no_post_in_trash }}</p>
<# } else { #>
							<p class="description">{{ wpmoly._n( wpmolyEditorL10n.n_post_in_trash, data.posts.length ) }} {{ wpmolyEditorL10n.trash_post_warning }}</p>
							<ul class="posts-list"></ul>
							<div class="confirm">
								<p class="description">{{ wpmolyEditorL10n.about_delete_posts }}</p>
								<input id="trash-posts-confirmed" type="checkbox" value="1" /><label for="trash-posts-confirmed">{{ wpmolyL10n.i_know }}</label>
								<button id="confirm-trash-posts" class="delete" type="button" data-action="confirm-trash-posts" disabled="disabled">{{ wpmolyEditorL10n.empty_trash }}</button>
								<button type="button" class="button dismiss" data-action="dismiss" title="{{ wpmolyL10n.dismiss }}"><span class="wpmolicon icon-no"></span></button>
							</div>
							<button type="button" class="button delete" data-action="trash-posts">{{ wpmolyEditorL10n.empty_trash }}</button>
<# } #>
