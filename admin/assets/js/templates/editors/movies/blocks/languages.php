<?php
/**
* Movies Editor Languages Block Template
 *
 * @since 3.0.0
 */

?>

						  <p class="description"><?php esc_html_e( 'A list of the languages spoken in this movie.', 'wpmovielibrary' ); ?></p>
							<div class="block-menu">
                <button class="button" type="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
              </div>
              <div class="term-list">
								<# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( language ) { #>
                <div class="term-item language-item">
                  <div class="term-thumbnail language-thumbnail" title="{{ language.name }}">[{{ ( language.iso_639_1 || '' ).toUpperCase() }}]</div>
                </div>
								<# } ); } else { #>
								<p class="description">{{ wpmolyEditorL10n.no_language_found }}</p>
								<# } #>
              </div>
							<div class="term-field">
                <div id="movie-languages-field" class="field select-field">
                  <div class="field-label"><?php esc_html_e( 'Languages', 'wpmovielibrary' ); ?></div>
                  <div class="field-value">
                    <div class="field-control">
                      <select id="movie-languages" data-field="spoken_languages" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
                        <option value=""></option>
												<# _.each( wpmolyEditorL10n.standard_languages, function( language, code ) { #>
                        <option value="{{ code }}"<# if ( _.where( data.terms || {}, { iso_639_1 : code } ).length ) { #> selected="selected"<# } #>>{{ language }}</option>
                      	<# } ); #>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
