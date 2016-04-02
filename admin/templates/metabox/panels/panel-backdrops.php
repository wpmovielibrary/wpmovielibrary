
		<div class="no-js-alert hide-if-js"><?php _e( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', 'wpmovielibrary' ); ?></div>

		<?php /*echo wpmoly_nonce_field( 'upload-movie-backdrop', $referer = false ); ?>
		<?php echo wpmoly_nonce_field( 'load-movie-backdrops', $referer = false );*/ ?>

		<div id="wpmoly-backdrops-preview">
			<ul id="wpmoly-imported-backdrops" class="wpmoly-imported-images wpmoly-imported-backdrops clearfix">
<?php foreach ( $backdrops as $backdrop ) : ?>
				<li class="wpmoly-imported-image wpmoly-imported-backdrop">
					<a class="context-menu-toggle" href="<?php echo get_edit_post_link( $backdrop->id ) ?>" data-id="<?php $backdrop->the( 'id' ) ?>" target="_blank"><span class="wpmolicon icon-ellipsis-h"></span></a>
					<div class="context-menu">
						<div class="context-menu-title"><?php _e( 'Options', 'wpmovielibrary' ); ?></div>
						<div class="context-menu-content">
							<a class="context-menu-item" href="<?php echo get_edit_post_link( $backdrop->id ) ?>"><span class="wpmolicon icon-edit-page"></span>&nbsp; <?php _e( 'Edit', 'wpmovielibrary' ); ?></a>
							<a class="context-menu-item" href="<?php echo get_delete_post_link( $backdrop->id ) ?>"><span class="wpmolicon icon-trash"></span>&nbsp; <?php _e( 'Delete', 'wpmovielibrary' ); ?></a>
						</div>
					</div>
					<div class="thumbnail" style="background-image:url(<?php $backdrop->render( 'medium' ) ?>)"></div>
				</li>
<?php endforeach; ?>
				<li class="wpmoly-image wpmoly-backdrop hide-if-no-js">
					<a href="#" id="wpmoly-import-backdrops" class="wpmoly-import-images" data-action="import"><span class="wpmolicon icon-import"></span><span class="label"><?php _e( 'Import Backdrops', 'wpmovielibrary' ); ?></span></a>
					<a href="#" id="wpmoly-upload-backdrops" class="wpmoly-upload-images" data-action="upload"><span class="wpmolicon icon-export"></span><span class="label"><?php _e( 'Upload Backdrops', 'wpmovielibrary' ); ?></span></a>
					<div id="wpmoly-load-backdrops" class="wpmoly-load-images wpmoly-load-backdrops"><span class="wpmolicon icon-plus"></span></div>
				</li>
			</ul>
		</div>
