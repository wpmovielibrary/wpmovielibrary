<?php
/**
 * Post Browser 'Add-New' Block Template
 *
 * @since 3.0.0
 */
?>

							<p class="description">{{ wpmolyEditorL10n.about_new_post }}</p>
							<div class="field">
								<label for="new-post-title">{{ wpmolyEditorL10n.post_title }}</label>
								<input type="text" id="new-post-title" data-value="new-post-title" value="" />
							</div>
							<button id="add-new-post" class="button submit" type="button" data-action="add-new-post" disabled="disabled"><span class="wpmolicon icon-plus"></span> {{ wpmolyEditorL10n.create_post }}</button>
