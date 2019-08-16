<?php
/**
 * Movies Editor browser item Template
 *
 * @since 3.0.0
 */
?>

			<div class="post-thumbnail movie-poster" style="background-image:url({{ data.poster.sizes.medium.url }})">
			<div class="edit-menu">
			<# if ( 'publish' === data.post_status ) { #>
				<a href="{{ data.edit_link }}"></a>
				<button type="button" class="button trash" data-action="trash-post" title="<?php esc_html_e( 'Move to the trash', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-trash"></span></button>
				<div class="confirm">
					<p><?php esc_html_e( 'Move to trash?', 'wpmovielibrary' ); ?></p>
					<button type="button" class="button delete" data-action="confirm-trash-post"><?php esc_html_e( 'Yes' ); ?></button>
					<button type="button" class="button dismiss" data-action="dismiss" title="<?php esc_html_e( 'Dismiss' ); ?>"><span class="wpmolicon icon-no"></span></button>
				</div>
				<button type="button" class="button edit" data-action="preview-movie"><span class="wpmolicon icon-edit"></span></button>
				<!--<button type="button" class="button preview" data-action="preview-movie"><span class="wpmolicon icon-series"></span></button>-->
				<button type="button" class="button status {{ data.status }}" data-action="edit-status"><span class="wpmolicon icon-circle-thin"></span></button>
				<button type="button" class="button rating" data-action="edit-rating"><span class="wpmolicon icon-star-empty"></span></button>
			<# } else if ( 'draft' === data.post_status ) { #>
				<button type="button" class="button restore" data-action="restore-post" title="<?php esc_html_e( 'Publish movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-publish"></span></button>
				<button type="button" class="button trash" data-action="trash-post" title="<?php esc_html_e( 'Move to the trash', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-trash"></span></button>
				<div class="confirm">
					<p><?php esc_html_e( 'Move to trash?', 'wpmovielibrary' ); ?></p>
					<button type="button" class="button delete" data-action="confirm-trash-post"><?php esc_html_e( 'Yes' ); ?></button>
					<button type="button" class="button dismiss" data-action="dismiss" title="<?php esc_html_e( 'Dismiss' ); ?>"><span class="wpmolicon icon-no"></span></button>
				</div>
			<# } else if ( 'trash' === data.post_status ) { #>
				<button type="button" class="button restore" data-action="restore-post" title="<?php esc_html_e( 'Restore movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-restore"></span></button>
				<button type="button" class="button delete" data-action="delete-post" title="<?php esc_html_e( 'Delete permanently', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-trash"></span></button>
				<div class="confirm">
					<p><?php esc_html_e( 'Delete permanently?', 'wpmovielibrary' ); ?></p>
					<button type="button" class="button delete" data-action="confirm-delete-post"><?php esc_html_e( 'Yes' ); ?></button>
					<button type="button" class="button dismiss" data-action="dismiss" title="<?php esc_html_e( 'Dismiss' ); ?>"><span class="wpmolicon icon-no"></span></button>
				</div>
			<# } #>
			</div>
			<div class="edit-rating">
				<input type="number" min="0.0" max="5.0" step="0.5" value="{{ data.rating }}" data-value="rating" />
				<button type="button" class="button dismiss" data-action="dismiss" title="<?php esc_html_e( 'Dismiss', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-no"></span></button>
				<button type="button" class="button apply" data-action="update-rating"><?php esc_html_e( 'Apply', 'wpmovielibrary' ); ?></button>
			</div>
			<div class="edit-status">
				<select data-value="status" data-selectize="1">
					<option value=""<# if ( '' === data.status ) { #> selected="selected"<# } #>><?php esc_html_e( 'Empty', 'wpmovielibrary' ); ?></option>
					<option value="unavailable"<# if ( 'unavailable' === data.status ) { #> selected="selected"<# } #>><?php esc_html_e( 'Unavailable', 'wpmovielibrary' ); ?></option>
					<option value="available"<# if ( 'available' === data.status ) { #> selected="selected"<# } #>><?php esc_html_e( 'Available', 'wpmovielibrary' ); ?></option>
					<option value="scheduled"<# if ( 'scheduled' === data.status ) { #> selected="selected"<# } #>><?php esc_html_e( 'Scheduled', 'wpmovielibrary' ); ?></option>
					<option value="loaned"<# if ( 'loaned' === data.status ) { #> selected="selected"<# } #>><?php esc_html_e( 'Loaned', 'wpmovielibrary' ); ?></option>
				</select>
				<button type="button" class="button dismiss" data-action="dismiss" title="<?php esc_html_e( 'Dismiss', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-no"></span></button>
				<button type="button" class="button apply" data-action="update-status"><?php esc_html_e( 'Apply', 'wpmovielibrary' ); ?></button>
			</div>
		</div>
		<div class="movie-year">{{ data.year }}</div>
		<div class="post-title movie-title"><a href="{{ data.edit_link }}">{{{ data.title }}}</a></div>
		<!--<div class="movie-genres">{{ data.genres }}</div>
		<div class="movie-runtime"><# if ( data.runtime ) { #>{{ data.runtime }}min<# } #></div>-->
