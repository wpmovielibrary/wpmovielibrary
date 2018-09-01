
					 <div class="panel-title"><?php esc_html_e( 'Backdrops', 'wpmovielibrary' ); ?></div>
             <div class="field filter-field">
               <div class="field-label"><?php esc_html_e( 'Language', 'wpmovielibrary' ); ?></div>
               <div class="field-value">
                 <div class="field-control">
                   <select id="backdrops-language-filter" data-filter="language" data-selectize="1">
                     <option value=""></option>
                     <# _.each( wpmolyEditorL10n.standard_languages, function( name, code ) { #>
                     <option value="{{ code }}"<# if ( ! _.contains( data.languages, code ) ) { #> disabled="true"<# } #>>{{ name }}</option>
                     <# } ); #>
                   </select>
                 </div>
               </div>
             </div>
             <div class="field filter-field">
               <div class="field-label"><?php esc_html_e( 'Size', 'wpmovielibrary' ); ?></div>
               <div class="field-value">
                 <div class="field-control">
                   <select id="backdrops-size-filter" data-filter="size" data-selectize="1">
                     <option value=""></option>
                     <option value="small"><?php esc_html_e( 'Small', 'wpmovielibrary' ); ?></option>
                     <option value="medium"><?php esc_html_e( 'Medium', 'wpmovielibrary' ); ?></option>
                     <option value="large"><?php esc_html_e( 'Large', 'wpmovielibrary' ); ?></option>
                     <option value="huge"><?php esc_html_e( 'Huge', 'wpmovielibrary' ); ?></option>
                   </select>
                 </div>
               </div>
             </div>
             <div class="field filter-field">
               <div class="field-label"><?php esc_html_e( 'Ratio', 'wpmovielibrary' ); ?></div>
               <div class="field-value">
                 <div class="field-control">
                   <select id="backdrops-ratio-filter" data-filter="ratio" data-selectize="1">
                     <option value=""></option>
                     <option value="portrait"><?php esc_html_e( 'Portrait', 'wpmovielibrary' ); ?></option>
                     <option value="landscape"><?php esc_html_e( 'Landscape', 'wpmovielibrary' ); ?></option>
                   </select>
                 </div>
               </div>
             </div>
