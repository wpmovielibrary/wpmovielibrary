<?php
/**
* Movies Editor Countries Block Template
 *
 * @since 3.0.0
 */

?>

						  <p class="description"><?php esc_html_e( 'A list of the countries this movie was produced in.', 'wpmovielibrary' ); ?></p>
							<div class="block-menu">
                <button class="button" type="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
              </div>
              <div class="term-list">
								<# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( country ) { #>
                <div class="term-item country-item">
                  <div class="term-thumbnail country-thumbnail" title="{{ country.name }}"><span class="flag flag-{{ ( country.iso_3166_1 || '' ).toLowerCase() }}"></span></div>
                </div>
								<# } ); } else { #>
								<p class="description">{{ wpmolyEditorL10n.no_country_found }}</p>
								<# } #>
              </div>
							<div class="term-field">
                <div id="movie-countries-field" class="field select-field">
                  <div class="field-label"><?php esc_html_e( 'Countries', 'wpmovielibrary' ); ?></div>
                  <div class="field-value">
                    <div class="field-control">
                      <select id="movie-countries" data-field="production_countries" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
                        <option value=""></option>
												<# _.each( wpmolyEditorL10n.standard_countries, function( country, code ) { #>
                        <option value="{{ code }}"<# if ( _.where( data.terms || {}, { iso_3166_1 : code } ).length ) { #> selected="selected"<# } #>>{{ country }}</option>
                      	<# } ); #>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
