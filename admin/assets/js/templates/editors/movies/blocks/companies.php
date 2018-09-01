<?php
/**
* Movies Editor Companies Block Template
 *
 * @since 3.0.0
 */

?>

							<p class="description"><?php esc_html_e( 'A list of the companies that produced this movie.', 'wpmovielibrary' ); ?></p>
						  <div class="block-menu">
                <button class="button" type="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
              </div>
              <div class="term-list">
								<# if ( ! _.isEmpty( data.terms ) ) { _.each( _.filter( data.terms ), function( company ) { #>
                <div class="term-item company-item">
									<# if ( company.logo_path ) { #>
                  <div class="term-thumbnail company-thumbnail" style="background-image:url({{ 'https://image.tmdb.org/t/p/w45' + company.logo_path }})" title="{{ company.name }}"></div>
									<# } else { #>
                  <div class="term-thumbnail company-logo" title="{{ company.name }}"><span class="dashicons dashicons-store"></span></div>
									<# } #>
                </div>
								<# } ); } else { #>
								<p class="description">{{ wpmolyEditorL10n.no_company_found }}</p>
								<# } #>
              </div>
							<div class="term-field">
                <div id="movie-companies-field" class="field select-field">
                  <div class="field-label"><?php esc_html_e( 'Companies', 'wpmovielibrary' ); ?></div>
                  <div class="field-value">
                    <div class="field-control">
                      <select id="movie-companies" data-field="production_companies" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
                        <option value=""></option>
                      	<# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( company ) { #>
                        <option value="{{ company.name }}" selected="selected">{{ company.name }}</option>
                      	<# } ); } #>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
