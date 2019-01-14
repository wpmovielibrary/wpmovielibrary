
							<input type="hidden" data-value="tmdb-id" value="{{ data.tmdb_id }}" />
							<p class="description">{{ wpmolyEditorL10n.about_tmdb_id }}</p>
							<div class="field">
								<label for="new-tmdb-id">{{ wpmolyEditorL10n.tmdb_id }}</label>
								<input type="text" id="new-tmdb-id" data-value="new-tmdb-id" value="{{ data.tmdb_id }}" />
							</div>

							<button id="fetch-person" type="button" class="button submit" data-action="fetch-person"<# if ( ! _.isNumber( data.tmdb_id ) ) { #> disabled="disabled"<# } #>>{{ wpmolyEditorL10n.fetch_person }}</button>
