<?php
/**
 * Movies Editor Genres Block Template
 *
 * @since 3.0.0
 */

?>

						  <p class="description"><?php esc_html_e( 'A list of terms for the Genres Taxonomy that can be used independantly from the \'genres\' metadata.', 'wpmovielibrary' ); ?></p>
							<div class="block-menu">
                <button class="button" type="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
              </div>
              <div class="term-list">
								<# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( genre ) { #>
                <div class="term-item genre-item">
                  <div class="term-thumbnail genre-thumbnail" style="background-image:url({{ genre.thumbnail }})"></div>
                  <a href="{{ genre.edit_link }}" target="_blank" class="term-name genre-name">{{ genre.name }}</a>
                </div>
								<# } ); } else { #>
								<p class="description">{{ wpmolyEditorL10n.no_genre_found }}</p>
								<# } #>
              </div>
							<div class="term-field">
                <div id="movie-genres-field" class="field select-field">
                  <div class="field-label"><?php esc_html_e( 'Genres', 'wpmovielibrary' ); ?></div>
                  <div class="field-value">
                    <div class="field-control">
                      <select id="movie-genres" data-field="genres" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
                        <option value=""></option>
                      <# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( genre ) { #>
                        <option value="{{ genre.name }}" selected="selected">{{ genre.name }}</option>
                      <# } ); } #>
                      </select>
                    </div>
										<button class="button empty" type="button" data-action="clear-terms"><?php esc_html_e( 'Clear all', 'wpmovielibrary' ); ?></button>
                  </div>
                </div>
              </div>
							<button id="synchronize-genres" type="button" class="button submit" data-action="synchronize">{{ wpmolyEditorL10n.synchronize_genres }}</button>
