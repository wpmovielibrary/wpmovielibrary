<?php
/**
 * Genre Editor Main Template
 *
 * @since 3.0.0
 */
?>

		<div data-headbox="{{ data.id }}" data-theme="default" class="wpmoly term-headbox genre-headbox theme-default">
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
						<div class="term-title genre-title">{{ data.name }}</div>
					</div>
					<div class="headbox-subtitle">
						<div class="term-count genre-count">{{{ wpmoly._n( wpmolyEditorL10n.n_total_post, data.count ) }}}</div>
					</div>
				</div>
				<div class="headbox-metadata">
					<div class="headbox-description">
						<div class="term-description genre-description">{{ data.description || s.sprintf( wpmolyEditorL10n.no_description, data.name ) }}</div>
					</div>
				</div>
			</div>
		</div>
