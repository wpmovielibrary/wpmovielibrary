
		<div id="wpmoly-posters" class="wpmoly-posters">

			<div class="no-js-alert hide-if-js"><?php _e( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', 'wpmovielibrary' ); ?></div>

			<input type="hidden" id="wp-version" value="<?php echo $version ?>" />

			<div id="tmdb_posters_preview" class="hide-if-no-js">
				<ul id="__attachments-view" class="attachments ui-sortable ui-sortable-disabled" tabindex="-1">

<?php foreach ( $posters as $poster ) : ?>
					<li class="tmdb_movie_images tmdb_movie_imported_image">
						<a href="<?php echo $poster['link'] ?>" data-id="<?php echo $poster['id'] ?>">
							<div class="js--select-attachment type-image <?php echo $poster['type'] . $poster['format'] ?>">
								<div class="thumbnail">
									<div class="centered"><img src="<?php echo $poster['image'][0] ?>" draggable="false" alt=""></div>
								</div>
							</div>
						</a>
					</li>

<?php endforeach; ?>

				</ul>
			</div>
			<div style="clear:both"></div>

		</div>
