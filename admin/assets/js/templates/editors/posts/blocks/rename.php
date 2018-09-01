
							<input type="hidden" data-value="original-title" value="{{ data.title.raw }}" />
							<p class="description">{{ wpmolyEditorL10n.about_new_title }}</p>
							<div class="field">
								<label for="new-title">{{ wpmolyEditorL10n.new_title }}</label>
								<input type="text" id="new-title" data-value="new-title" value="" placeholder="{{ data.title.raw }}" />
							</div>
							<button id="update-title" type="button" class="button submit" data-action="update-title" disabled="disabled">{{ wpmolyEditorL10n.update_title }}</button>
