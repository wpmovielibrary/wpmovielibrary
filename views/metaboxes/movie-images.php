
	<div class="no-js-alert hide-if-js"><?php _e( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', 'wpmovielibrary' ); ?></div>

	<?php echo $nonce; ?>

	<div id="tmdb_images_preview" class="hide-if-no-js">
		<ul>
			<?php echo $images ?>
			<li class="tmdb_movie_images tmdb_movie_imported_image"><a href="#" id="tmdb_load_images"><?php _e( 'Load Images', 'wpmovielibrary' ); ?></a></li>
		</ul>
	</div>
	<div style="clear:both"></div>
