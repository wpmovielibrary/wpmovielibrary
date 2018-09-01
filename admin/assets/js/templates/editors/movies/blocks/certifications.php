<?php
/**
 * Movies Editor Certifications Block Template
 *
 * @since 3.0.0
 */

 use \wpmoly\core\L10n;
?>

              <p class="description"><?php esc_html_e( 'Depending on your country, certifications and release dates may differ from the original release date. Some movies also have more than one release date available. Pick the one you prefer to use as your local release date and certification.', 'wpmovielibrary' ); ?></p>
							<div class="block-menu">
                <button class="button" type="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
              </div>
              <div class="term-list">
                <p class="description"><?php esc_html_e( 'Listed release dates correspond to theatrical release unless mentioned otherwise.', 'wpmovielibrary' ); ?></p>
                <select data-selectize="1" placeholder="<?php esc_html_e( 'Find Release Date', 'wpmovielibrary' ); ?>">
                  <option value=""><?php esc_html_e( 'Find Release Date', 'wpmovielibrary' ); ?></option>
                  <# _.each( data.release_dates, function( date ) { #>
                    <# _.each( date.release_dates, function( release ) { var d = ( new Date( release.release_date ) ).toISOString().substring( 0, 10 ); #>
                  <option data-value="{{ d || '' }}" data-country="{{ date.iso_3166_1.toLowerCase() || '' }}" data-certification="{{ release.certification || '' }}" data-release-date="{{ release.release_date || '' }}" data-release-type="{{ release.type || '' }}"<# if ( data.local_release_date === d && data.certification === release.certification ) { #> selected="selected"<# } #>>{{ wpmolyEditorL10n.standard_countries[ date.iso_3166_1 ] || '' }}{{ new Date( release.release_date || '' ).toLocaleDateString() }}{{ release.certification || '−' }}</option>
                    <# } ) ; #>
                  <# } ) ; #>
                </select>
              </div>
              <div class="term-field">
                <div id="movie-certification-field" class="field text-field">
                  <div class="field-label"><?php esc_html_e( 'Certification', 'wpmovielibrary' ); ?></div>
                  <div class="field-value"><input type="text" data-field="certification" value="{{ data.certification }}" /></label></div>
                </div>
              </div>
              <div class="term-field">
                <div id="movie-local-release-date-field" class="field text-field">
                  <div class="field-label"><?php esc_html_e( 'Local Release Date', 'wpmovielibrary' ); ?></div>
                  <div class="field-value"><input type="date" id="local-release-date" data-field="local_release_date" value="{{ data.local_release_date }}" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" /></div>
                </div>
              </div>
