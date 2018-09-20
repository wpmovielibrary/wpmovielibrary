
                <# if ( 'attachment' === data.type ) { #>
                <div class="poster-thumbnail" style="background-image:url({{ data.url || '' }})">
                  <div class="open-poster"><button type="button" class="button empty" data-action="open" title="<?php esc_html_e( 'Open Poster', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-export"></span></button></div>
                  <div class="set-as-poster"><button type="button" class="button empty" data-action="set-as" title="<?php esc_html_e( 'Set as poster', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-poster"></span></button></div>
                  <div class="remove-poster"><button type="button" class="button empty" data-action="remove" title="<?php esc_html_e( 'Remove poster', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-trash"></span></button></div>
                  <# if ( 1 <= data.ratio ) { #><button class="button empty bad-ratio" title="<?php esc_html_e( 'Warning: Bad Ratio', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-warning"></span></button><# } #>
                </div>
                <div class="poster-size">{{{ ( data.width || '' ) + '&times;' + ( data.height || '' ) }}}</div>
                <# } else { #>
                <div class="poster-thumbnail" style="background-image:url({{ data.url || '' }})">
                  <div class="download-poster"><button type="button" class="button empty" data-action="download" title="<?php esc_html_e( 'Download Poster', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-download"></span></button></div>
                  <div class="download-progress"><div class="progress-bar"></div></div>
                  <div class="upload-progress"><div class="progress-bar"></div></div>
                  <div class="upload-dropzone"></div>
                </div>
                <div class="poster-language">{{ wpmolyEditorL10n.native_languages[ data.lang ] || data.lang || '<?php esc_html_e( 'No Language.', 'wpmovielibrary' ); ?>' }}</div>
                <div class="poster-size">{{{ ( data.width || '' ) + '&times;' + ( data.height || '' ) }}}</div>
                <# } #>
