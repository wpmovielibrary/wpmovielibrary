<?php
/**
 * Grid Editor Archives Block Template
 *
 * @since 3.0.0
 *
 * @uses $id
 * @uses $title
 */
?>

					<div id="editor-<?php echo esc_attr( $id ); ?>-block" data-controller="ArchivesBlock" class="editor-block post-editor-block grid-editor-block grid-archives-block">
						<button type="button" class="button arrow" data-action="collapse"><span class="wpmolicon icon-up-chevron"></span></button>
						<h3 class="block-title"><?php echo esc_html( $title ); ?></h3>
						<div class="block-content"></div>
					</div>
