

		<div class="attachment-preview js--select-attachment type-image subtype-{{ data.mimetype }} {{ data.orientation }}<# if ( data.existing ) { #> existing<# } #>">
			<div class="existing" title="<?php _e( 'This image is already in your media library.', 'wpmovielibrary' ) ?>"><span class="dashicons dashicons-info"></span></div>
			<div class="thumbnail">
				<div class="centered">
					<img src="{{ data.thumbnail }}" draggable="false" alt="">
				</div>
			</div>
		</div>
		<button type="button" class="button-link check" tabindex="-1"><span class="media-modal-icon"></span><span class="screen-reader-text"><?php _e( 'Unselect' ) ?></span></button>
