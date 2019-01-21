
                <# if ( 'attachment' === data.type ) { #>
                <div class="backdrop-thumbnail" style="background-image:url({{ data.url || '' }})">
                  <div class="open-backdrop"><button type="button" class="button empty" data-action="open" title="<?php esc_html_e( 'Open Backdrop', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-export"></span></button></div>
                  <div class="set-as-backdrop"><button type="button" class="button empty" data-action="set-as" title="<?php esc_html_e( 'Set as backdrop', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-backdrop"></span></button></div>
                  <div class="remove-backdrop"><button type="button" class="button empty" data-action="remove" title="<?php esc_html_e( 'Remove backdrop', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-trash"></span></button></div>
                  <# if ( 1 >= data.ratio ) { #><button class="button empty bad-ratio" title="<?php esc_html_e( 'Warning: Bad Ratio', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-warning"></span></button><# } #>
                </div>
                <div class="backdrop-size">{{{ ( data.width || '' ) + '&times;' + ( data.height || '' ) }}}</div>
                <# } else { #>
                <div class="backdrop-thumbnail" style="background-image:url({{ data.url || '' }})">
                  <div class="download-backdrop"><button type="button" class="button empty" data-action="download" title="<?php esc_html_e( 'Download Backdrop', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-download"></span></button></div>
                  <div class="download-progress"><div class="progress-bar"></div></div>
                  <div class="upload-progress"><div class="progress-bar"></div></div>
                  <div class="upload-dropzone"></div>
                </div>
                <div class="backdrop-language">{{ wpmolyEditorL10n.native_languages[ data.lang ] || data.lang || '<?php esc_html_e( 'No Language.', 'wpmovielibrary' ); ?>' }}</div>
                <div class="backdrop-size">{{{ ( data.width || '' ) + '&times;' + ( data.height || '' ) }}}</div>
                <# } #>
