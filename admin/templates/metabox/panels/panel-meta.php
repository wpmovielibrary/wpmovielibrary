
		<div class="no-js-alert hide-if-js"><?php _e( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', 'wpmovielibrary' ); ?></div>

		<div id="wpmoly-movie-meta" class="wpmoly-meta">
<?php foreach ( $fields as $slug => $field ) : ?>
			<div class="wpmoly-meta-edit wpmoly-meta-edit-<?php echo $slug; ?> <?php echo $field['size'] ?>">
				<?php echo $field['html'] ?>
			</div>
<?php endforeach; ?>

		</div>
