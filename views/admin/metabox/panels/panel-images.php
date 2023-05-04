
		<div id="wpmoly-images" class="wpmoly-images">

			<div class="no-js-alert hide-if-js"><?php _e( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', 'wpmovielibrary' ); ?></div>

			<?php echo $nonce; ?>
			<input type="hidden" id="wp-version" value="<?php echo $version ?>" />

			<div id="tmdb_images_preview" class="hide-if-no-js">
				<ul id="__attachments-view" class="attachments ui-sortable ui-sortable-disabled" tabindex="-1">

<?php foreach ( $images as $image ) : ?>
					<li class="tmdb_movie_images tmdb_movie_imported_image">
						<a href="<?php echo $image['link'] ?>" data-id="<?php echo $image['id'] ?>">
							<div class="js--select-attachment type-image <?php echo $image['type'] . $image['format'] ?>">
								<div class="thumbnail">
									<div class="centered"><img src="<?php echo $image['image'][0] ?>" draggable="false" alt=""></div>
								</div>
							</div>
						</a>
					</li>

<?php endforeach; ?>

					<li class="tmdb_movie_images tmdb_movie_imported_image"><a href="#" id="tmdb_load_images"><?php _e( 'Load Images', 'wpmovielibrary' ); ?></a></li>

				</ul>
			</div>
			<div style="clear:both"></div>

		</div>
