<?php
/**
 * Term Meta Editor Main Template
 *
 * @since 3.0.0
 */
?>

		<div data-headbox="{{ data.id }}" data-theme="default" class="wpmoly term-headbox actor-headbox theme-default">
			<div class="headbox-header">
				<div class="headbox-thumbnail" style="<# if ( ! _.isEmpty( data.thumbnail ) ) { #>background-image:url({{ data.thumbnail }});<# } #>">
					<div class="thumbnail-menu">
						<button class="button edit" data-action="change-thumbnail">{{ 'svg:icon:edit' }}</button>
						<button class="button remove" data-action="remove-thumbnail">{{ 'svg:icon:trash' }}</button>
					</div>
				</div>
			</div>
			<div class="headbox-content clearfix">
				<div class="headbox-titles">
					<div class="headbox-title">
						<div class="term-title actor-title">{{ data.name }}</div>
					</div>
					<div class="headbox-subtitle">
						<div class="term-count actor-count">{{{ wpmoly._n( wpmolyEditorL10n.n_total_post, data.count ) }}}</div>
					</div>
				</div>
				<div class="headbox-metadata">
					<div class="headbox-description">
						<div class="field text-field term-description actor-description">
							<# if ( ! _.isEmpty( data.description ) ) { #>
							<div class="value">{{ data.description }} <button type="button" class="button edit empty" data-action="edit-description" title="<?php esc_html_e( 'Edit Description', 'wpmovielibrary' ); ?>">{{ 'svg:icon:edit' }}</button></div>
							<# } else { #>
							<div class="value">{{ s.sprintf( wpmolyEditorL10n.no_description, data.name ) }} <button type="button" class="button empty" data-action="edit-description"><?php esc_html_e( 'Add one?', 'wpmovielibrary' ); ?></button></div>
							<# } #>
							<div class="field-control">
								<textarea id="term-description" data-field="description">{{ data.description || '' }}</textarea>
								<button type="button" class="button close empty" data-action="close-description" title="<?php esc_html_e( 'Stop Editing', 'wpmovielibrary' ); ?>">{{ 'svg:icon:close' }}</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
