<?php
/**
 * Person Editor Menu Template
 *
 * @since 3.0.0
 */
?>

		<button type="button" class="button preview<# if ( 'preview' === data.mode ) { #> active<# } #>" data-mode="preview" title="<?php esc_html_e( 'Preview person', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-screen"></span></button>
		<button type="button" class="button download<# if ( 'download' === data.mode ) { #> active<# } #>" data-mode="download" title="<?php esc_html_e( 'Import person data and images', 'wpmovielibrary' ); ?>">{{ 'svg:icon:download' }}</button>
		<# if ( ! data.snapshot_date ) { #>
		<button type="button" class="button refresh" data-mode="refresh" title="<?php esc_html_e( 'Snapshot', 'wpmovielibrary' ); ?>"<# if ( ! data.refresh ) { #> disabled="disabled"<# } #>>{{ 'svg:icon:snapshot' }}</button>
		<# } else { #>
		<button type="button" class="button snapshot<# if ( 'snapshot' === data.mode ) { #> active<# } #>" data-mode="snapshot" title="<?php esc_html_e( 'Snapshot', 'wpmovielibrary' ); ?>">{{ 'svg:icon:snapshot' }}</button>
		<# } #>
