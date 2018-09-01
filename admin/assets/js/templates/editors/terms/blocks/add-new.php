<?php
/**
 * Term Browser 'Add New' Block Template.
 *
 * @since 3.0.0
 */
?>

							<p class="description">{{ wpmolyEditorL10n.add_new_term }}</p>
							<div class="field">
								<label for="new-term-name">{{ wpmolyEditorL10n.name }}</label>
								<input type="text" id="new-term-name" data-value="new-term-name" value="" />
							</div>
							<button id="add-new-term" class="button submit" type="button" data-action="add-new-term" disabled="disabled">{{ wpmolyEditorL10n.create_term }}</button>
