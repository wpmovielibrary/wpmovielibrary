<?php
/**
 * Movies Backdrops Editor Template
 *
 * @since 3.0.0
 */

?>

				<div class="editor-menu">
					<button type="button" class="button download" data-action="download"><span class="wpmolicon icon-download"></span></button>
					<button type="button" class="button upload" data-action="upload"><span class="wpmolicon icon-upload"></span></button>
					<button type="button" class="button toggle" data-action="toggle"><span class="wpmolicon icon-up-open"></span></button>
				</div>
				<div class="editor-content active">
					<div class="editor-subcontent editor-content-download clearfix">
						<div class="panel left"></div>
						<div class="panel right"></div>
					</div>
					<div class="editor-subcontent editor-content-upload clearfix">
						<div class="panel left">
							<div class="panel-title"><?php esc_html_e( 'Custom Backdrops', 'wpmovielibrary' ); ?></div>
							<div class="panel-description"><?php esc_html_e( 'You can select existing images or upload new images to be used as backdrops. For better consistancy try using landscape images (3/2 or 1.5 ratio).', 'wpmovielibrary' ); ?></div>
						</div>
						<div class="panel right"></div>
					</div>
				</div>
