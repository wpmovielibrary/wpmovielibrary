
		<div class="wpml-inline-edit-details wpml-inline-edit-status">
			<?php wpml_nonce_field( 'status-inline-edit', $referer = false ) ?>

			<ul>
<?php foreach ( $default_movie_status as $slug => $title ) : ?>
				<li><span class="dashicons dashicons-arrow-right-alt2"></span> <a href="#" onclick="wpml_edit_details.inline_edit( 'status', this ); return false;" data-status="<?php echo $slug ?>" data-status-title="<?php _e( $title, 'wpmovielibrary' ) ?>"><?php _e( $title, 'wpmovielibrary' ) ?></a></li>
<?php endforeach; ?>
			</ul>
		</div>

		<div class="wpml-inline-edit-details wpml-inline-edit-media">
			<?php wpml_nonce_field( 'media-inline-edit', $referer = false ) ?>

			<ul>
<?php foreach ( $default_movie_media as $slug => $title ) : ?>
				<li><span class="dashicons dashicons-arrow-right-alt2"></span> <a href="#" onclick="wpml_edit_details.inline_edit( 'media', this ); return false;" data-media="<?php echo $slug ?>" data-media-title="<?php _e( $title, 'wpmovielibrary' ) ?>"><?php _e( $title, 'wpmovielibrary' ) ?></a></li>
<?php endforeach; ?>
			</ul>
		</div>

		<div class="wpml-inline-edit-details wpml-inline-edit-rating">
			<?php wpml_nonce_field( 'rating-inline-edit', $referer = false ) ?>

			<a href="#" class="wpml-inline-edit-rating-update" onclick="wpml_edit_details.inline_edit( 'rating', this ); return false;" data-rating="0.0"><div id="stars" data-default-rating="0.0" data-rating="0.0" data-rated="false" class="stars"></div></a>
		</div>
