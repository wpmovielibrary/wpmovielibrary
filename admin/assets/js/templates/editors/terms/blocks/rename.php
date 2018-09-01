
							<input type="hidden" data-value="name" value="{{ data.name }}" />
							<p class="description">{{ wpmolyEditorL10n.about_new_name }}</p>
							<div class="field">
								<label for="new-name">{{ wpmolyEditorL10n.new_name }}</label>
								<input type="text" id="new-name" data-value="new-name" value="" placeholder="{{ data.name }}" />
							</div>
							<button id="update-name" type="button" class="button submit" data-action="update-name" disabled="disabled">{{ wpmolyEditorL10n.update_name }}</button>
