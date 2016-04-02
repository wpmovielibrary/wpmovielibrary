
		<div class="attachment-details {{ data.type }}-details save-ready" data-id="{{ data.id }}" tabindex="{{ data.tabindex }}">
<# if ( ! _.isEmpty( data ) ) { #>
		<h2><# if ( 'poster' == data.type ) { #><?php _e( 'Poster preview', 'wpmovielibrary' ) ?><# } else if ( 'backdrop' == data.type ) { #><?php _e( 'Backdrop preview', 'wpmovielibrary' ) ?><# } #></h2>
		<div class="attachment-info">
			<div class="thumbnail thumbnail-image">
				<img src="{{ data.thumbnail }}" draggable="false" alt="">
			</div>
			<div class="details">
<# if ( data.existing ) { #>
				<div class="existing"><span class="dashicons dashicons-info"></span>&nbsp; <?php _e( 'This image is already in your media library.', 'wpmovielibrary' ) ?></div>
<# } #>
				<div class="filename">{{ data.name }}</div>
				<div class="dimensions">{{ data.width }} × {{ data.height }}</div>
			</div>
		</div>
<# } #>
