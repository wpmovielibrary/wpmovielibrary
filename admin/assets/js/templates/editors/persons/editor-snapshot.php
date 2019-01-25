<?php
/**
 * Movies Editor Snapshot Template
 *
 * @since 3.0.0
 */
?>

		<div class="snapshot-details">
			<p class="description"><?php esc_html_e( 'When you import a new movie a snapshot of the retrieved metadata is stored and kept for later use.', 'wpmovielibrary' ); ?></p>
			<div class="snapshot-details-tabs">
				<button type="button" data-tab="summary" class="tab summary-tab active"><?php esc_html_e( 'Summary', 'wpmovielibrary' ); ?></button>
				<button type="button" data-tab="formatted" class="tab formatted-tab"><?php esc_html_e( 'Formatted', 'wpmovielibrary' ); ?></button>
				<button type="button" data-tab="raw" class="tab raw-tab"><?php esc_html_e( 'Raw', 'wpmovielibrary' ); ?></button>
			</div>
			<div class="snapshot-details-panels">
				<div class="snapshot-details-panel summary-panel active">
					<ul>
						<li><?php esc_html_e( 'Last snapshot update:', 'wpmovielibrary' ); ?> <strong>{{ data.date }}</strong><# if ( data.days ) { if ( -1 !== data.days ) { #> ({{{ wpmoly._n( wpmolyEditorL10n.n_days_ago, data.days ) }}})<# } else { #> ({{ wpmolyEditorL10n.moments_ago }})<# } } #> − <button type="button" data-action="update-snapshot" class="button empty"><?php esc_html_e( 'Update', 'wpmovielibrary' ); ?></button></li>
						<li><?php esc_html_e( 'Total size:', 'wpmovielibrary' ); ?> <strong>{{ Math.round( data.size / 100 ) / 10 }}KiB</strong></li>
						<li><?php esc_html_e( 'Available Backdrops:', 'wpmovielibrary' ); ?> <strong><# if ( _.has( data.snapshot.images || {}, 'backdrops' ) ) { #>{{ data.snapshot.images.backdrops.length }}<# } #></strong></li>
						<li><?php esc_html_e( 'Available Posters:', 'wpmovielibrary' ); ?> <strong><# if ( _.has( data.snapshot.images || {}, 'posters' ) ) { #>{{ data.snapshot.images.posters.length }}<# } #></strong></li>
					</ul>
				</div>
				<div class="snapshot-details-panel formatted-panel">
					<pre>{{{ JSON.stringify( data.snapshot ) }}}</pre>
				</div>
				<div class="snapshot-details-panel raw-panel">
					<pre>{{ data.snapshot.toSource() }}</pre>
				</div>
			</div>
		</div>
