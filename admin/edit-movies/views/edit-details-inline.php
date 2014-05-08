
		<div class="wpml-inline-edit-status">
			<ul>
<?php foreach ( $default_movie_status as $slug => $title ) : ?>
				<li><a href="#" onclick="wpml_status.inline_edit( this ); return false;" data-status="<?php echo $slug ?>" data-status-title="<?php _e( $title, WPML_SLUG ) ?>"><?php _e( $title, WPML_SLUG ) ?></a></li>
<?php endforeach; ?>
			</ul>
		</div>

		<div class="wpml-inline-edit-media">
			<ul>
<?php foreach ( $default_movie_media as $slug => $title ) : ?>
				<li><a href="#" onclick="wpml_media.inline_edit( this ); return false;" data-media="<?php echo $slug ?>" data-media-title="<?php _e( $title, WPML_SLUG ) ?>"><?php _e( $title, WPML_SLUG ) ?></a></li>
<?php endforeach; ?>
			</ul>
		</div>
