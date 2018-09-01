<?php
/**
* Movies Editor Actors Block Template
 *
 * @since 3.0.0
 */

?>

							<p class="description"><?php esc_html_e( 'A list of terms for the Actors Taxonomy that can be used independantly from the \'cast\' metadata.', 'wpmovielibrary' ); ?></p>
						  <div class="block-menu">
                <button type="button" class="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
              </div>
              <div class="term-list">
								<# if ( ! _.isEmpty( data.terms ) ) { #>
								<div class="term-list-inner">
									<# _.each( data.terms, function( actor ) { #>
	                <div class="term-item actor-item">
	                  <div class="term-thumbnail actor-thumbnail" style="background-image:url({{ actor.thumbnail }})"></div>
	                  <a href="{{ actor.edit_link }}" target="_blank" class="term-name actor-name">{{ actor.name }}</a>
	                </div>
									<# } ); #>
								</div>
								<# } else { #>
								<p class="description">{{ wpmolyEditorL10n.no_actor_found }}</p>
								<# } #>
              </div>
							<div class="term-field">
                <div id="movie-actors-field" class="field select-field">
                  <div class="field-label"><?php esc_html_e( 'Actors', 'wpmovielibrary' ); ?></div>
                  <div class="field-value">
                    <div class="field-control">
                      <select id="movie-actors" data-field="actors" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
                      <# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( actor ) { #>
                        <option value="{{ actor.name  }}" selected="selected">{{ actor.name }}</option>
                      <# } ); } #>
                      </select>
                    </div>
										<button class="button empty" type="button" data-action="clear-terms"><?php esc_html_e( 'Clear all', 'wpmovielibrary' ); ?></button>
                  </div>
                </div>
              </div>
							<button id="synchronize-actors" type="button" class="button submit" data-action="synchronize">{{ wpmolyEditorL10n.synchronize_actors }}</button>
