
		<div class="no-js-alert hide-if-js"><?php _e( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', 'wpmovielibrary' ); ?></div>

		<?php /*echo wpmoly_nonce_field( 'upload-movie-poster', $referer = false ); ?>
		<?php echo wpmoly_nonce_field( 'load-movie-posters', $referer = false );*/ ?>

		<div id="wpmoly-posters-preview">
			<ul id="wpmoly-imported-posters" class="wpmoly-imported-images wpmoly-imported-posters clearfix">
<?php foreach ( $posters as $poster ) : ?>
				<li class="wpmoly-imported-image wpmoly-imported-poster">
					<a class="context-menu-toggle" href="<?php echo get_edit_post_link( $poster->id ) ?>" data-id="<?php $poster->the( 'id' ) ?>"><span class="wpmolicon icon-ellipsis-h"></span></a>
					<div class="context-menu">
						<div class="context-menu-title"><?php _e( 'Options', 'wpmovielibrary' ); ?></div>
						<div class="context-menu-content">
							<a class="context-menu-item" href="<?php echo get_edit_post_link( $poster->id ) ?>"><span class="wpmolicon icon-edit-page"></span>&nbsp; <?php _e( 'Edit', 'wpmovielibrary' ); ?></a>
							<a class="context-menu-item" href="<?php echo get_delete_post_link( $poster->id ) ?>"><span class="wpmolicon icon-trash"></span>&nbsp; <?php _e( 'Delete', 'wpmovielibrary' ); ?></a>
							<a class="context-menu-item" href="#"><span class="wpmolicon icon-poster"></span>&nbsp; <?php _e( 'Featured', 'wpmovielibrary' ); ?></a>
						</div>
					</div>
					<div class="thumbnail"><img src="<?php $poster->render( 'medium' ) ?>" alt="<?php $poster->the( 'image_alt' ) ?>" /></div>
				</li>
<?php endforeach; ?>
				<li class="wpmoly-image wpmoly-poster hide-if-no-js">
					<a href="#" id="wpmoly-import-posters" class="wpmoly-import-images"><span class="wpmolicon icon-import"></span><span class="label"><?php _e( 'Import Posters', 'wpmovielibrary' ); ?></span></a>
					<a href="#" id="wpmoly-upload-posters" class="wpmoly-upload-images"><span class="wpmolicon icon-export"></span><span class="label"><?php _e( 'Upload Posters', 'wpmovielibrary' ); ?></span></a>
					<div id="wpmoly-load-posters" class="wpmoly-load-images wpmoly-load-posters"><span class="wpmolicon icon-plus"></span></div>
				</li>
			</ul>
		</div>
