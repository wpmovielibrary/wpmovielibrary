
			<div class="wpmoly-images-editor-preview wpmoly-{{ data.type }}s-editor-preview">
				<a href="#" id="wpmoly-edit-{{ data.type }}" data-action="edit" class="button-link wpmoly-edit-image wpmoly-edit-{{ data.type }}" title="<?php _e( 'Edit Image', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-edit"></span></a>
				<a href="#" id="wpmoly-close-{{ data.type }}" data-action="close" class="button-link wpmoly-close-image wpmoly-close-{{ data.type }}" title="<?php _e( 'Close Editor', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-no-alt"></span></a>
				<img src="{{ ( data.sizes.large || data.sizes.full ).url }}" alt="" />
				<div class="wpmoly-images-editor-details">
					<div class="detail-item filename"><strong><?php _e( 'File name:' ) ?></strong> {{ data.filename }}</div>
					<div class="detail-item filename"><strong><?php _e( 'File type:' ); ?></strong> {{ data.mime }}</div>
					<div class="detail-item uploaded"><strong><?php _e( 'Uploaded on:' ); ?></strong> {{ data.dateFormatted }}</div>
					<div class="detail-item file-size"><strong><?php _e( 'File size:' ); ?></strong> {{ data.filesizeHumanReadable || '−' }}</div>
					<# if ( data.width && data.height ) { #>
						<div class="detail-item dimensions"><strong><?php _e( 'Dimensions:' ); ?></strong> {{ data.width }} &times; {{ data.height }}</div>
					<# } #>
					<hr />
					<div class="detail-item">
						<a href="{{ data.delete_link }}" class="delete" data-action="delete"><?php _e( 'Delete Permanently' ) ?></a>
					</div>
				</div>
			</div>

			<div class="wpmoly-images-editor-form wpmoly-{{ data.type }}s-editor-form">
				<div class="editor-form-item">
					<label for="image_url"><h4><?php _e( 'URL' ); ?></h4></label>
					<div class="editor-form-item-value">
						<input type="text" value="{{ data.url }}" readonly />
					</div>
				</div>
				<div class="editor-form-item">
					<label for="image_title"><h4><?php _e( 'Title' ); ?></h4></label>
					<div class="editor-form-item-value">
						<input type="text" id="image_title" data-image-data="title" value="{{ data.title }}" />
					</div>
				</div>
				<div class="editor-form-item">
					<label for="image_excerpt"><h4><?php _e( 'Caption' ); ?></h4></label>
					<div class="editor-form-item-value">
						<textarea id="image_excerpt" data-image-data="caption">{{ data.excerpt }}</textarea>
					</div>
				</div>
				<div class="editor-form-item">
					<label for="image_image_alt"><h4><?php _e( 'Alternative Text' ); ?></h4></label>
					<div class="editor-form-item-value">
						<input type="text" id="image_image_alt" data-image-data="alt" value="{{ data.image_alt }}" />
					</div>
				</div>
				<div class="editor-form-item">
					<label for="image_description"><h4><?php _e( 'Description' ); ?></h4></label>
					<div class="editor-form-item-value">
						<textarea id="image_description" data-image-data="description" rows="5">{{ data.description }}</textarea>
					</div>
				</div>
			</div>

			<div style="clear:both"></div>
