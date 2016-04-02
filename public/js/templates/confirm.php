
		<div class="wpmoly-confirm-modal">
			<button type="button" class="button-link confirm-modal-close"><span class="wpmolicon icon-no-alt"></span></button>
			<div class="confirm-modal-content">
<# if ( data.icon ) { #>
				<div class="confirm-modal-icon"><span class="{{ data.icon }}"></span></div>
<# } #>
				<div class="confirm-modal-text">{{{ data.text }}}</div>
			</div>
			<div class="confirm-modal-footer">
				<button type="button" data-action="cancel" class="button-link confirm-modal-button cancel"><?php _e( 'Cancel' ) ?></button><button type="button" data-action="confirm" class="button-link confirm-modal-button confirm"><?php _e( 'Confirm', 'wpmovielibrary' ) ?></button>
			</div>
		</div>
		<div class="wpmoly-confirm-modal-backdrop"></div>
