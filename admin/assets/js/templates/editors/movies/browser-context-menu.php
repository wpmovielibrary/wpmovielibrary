<?php
/**
 * Movies Editor browser context menu Template
 *
 * @since 3.0.0
 */

use wpmoly\utils;

$meta = utils\get_registered_movie_meta();
?>

			<div class="context-menu-content">
<# if ( 'publish' === data.post.status ) { #>
				<div class="context-menu-item" data-action="preview">
					<div class="context-menu-icon"><span class="wpmolicon icon-movie"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Preview', 'wpmovielibray' ); ?></div>
				</div>
				<div class="context-menu-item" data-action="edit">
					<div class="context-menu-icon"><span class="wpmolicon icon-edit"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Edit', 'wpmovielibray' ); ?></div>
				</div>
				<div class="context-menu-item context-menu-separator"></div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-media"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Media', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
<?php foreach ( $meta['media']['show_in_rest']['enum'] as $key => $value ) : ?>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="media-<?php echo esc_attr( $key ); ?>" data-field="media" name="media[]" value="<?php echo esc_attr( $key ); ?>"<# if ( _.contains( data.media, '<?php echo esc_attr( $key ); ?>' ) ) { #> checked="checked"<# } #> />
								<label for="media-<?php echo esc_attr( $key ); ?>">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
								</label>
							</div>
<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-circle-thin"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Status', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
<?php foreach ( $meta['status']['show_in_rest']['enum'] as $key => $value ) : ?>
							<div class="context-menu-item">
								<input type="radio" id="status-<?php echo esc_attr( $key ); ?>" data-field="status" name="status[]" value="<?php echo esc_attr( $key ); ?>"<# if ( '<?php echo esc_attr( $key ); ?>' === data.status ) { #> checked="checked"<# } #> />
								<label for="status-<?php echo esc_attr( $key ); ?>">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
								</label>
							</div>
<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-star"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Rating', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
<?php foreach ( $meta['rating']['show_in_rest']['enum'] as $key => $value ) : ?>
							<div class="context-menu-item">
								<input type="radio" id="rating-<?php echo esc_attr( $key ); ?>" data-field="rating" name="rating[]" value="<?php echo esc_attr( $key ); ?>"<# if ( '<?php echo esc_attr( $key ); ?>' === data.rating ) { #> checked="checked"<# } #> />
								<label for="rating-<?php echo esc_attr( $key ); ?>">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
								</label>
							</div>
<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-format"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Format', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
<?php foreach ( $meta['format']['show_in_rest']['enum'] as $key => $value ) : ?>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="format-<?php echo esc_attr( $key ); ?>" data-field="format" name="format[]" value="<?php echo esc_attr( $key ); ?>"<# if ( _.contains( data.format, '<?php echo esc_attr( $key ); ?>' ) ) { #> checked="checked"<# } #> />
								<label for="format-<?php echo esc_attr( $key ); ?>">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
								</label>
							</div>
<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-subtitle"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Subtitles', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
<?php foreach ( $meta['subtitles']['show_in_rest']['enum'] as $key => $value ) : ?>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-<?php echo esc_attr( $key ); ?>" data-field="subtitles" name="subtitles[]" value="<?php echo esc_attr( $key ); ?>"<# if ( _.contains( data.subtitles, '<?php echo esc_attr( $key ); ?>' ) ) { #> checked="checked"<# } #> />
								<label for="subtitles-<?php echo esc_attr( $key ); ?>">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
								</label>
							</div>
<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-language"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Languages', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
<?php foreach ( $meta['language']['show_in_rest']['enum'] as $key => $value ) : ?>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-<?php echo esc_attr( $key ); ?>" data-field="language" name="language[]" value="<?php echo esc_attr( $key ); ?>"<# if ( _.contains( data.language, '<?php echo esc_attr( $key ); ?>' ) ) { #> checked="checked"<# } #> />
								<label for="language-<?php echo esc_attr( $key ); ?>">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
								</label>
							</div>
<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="context-menu-item context-menu-separator"></div>
				<div class="context-menu-item draft-item" data-action="draft">
					<div class="context-menu-icon"><span class="wpmolicon icon-unpublish"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Unpublish', 'wpmovielibray' ); ?></div>
				</div>
				<div class="context-menu-item trash-item" data-action="trash">
					<div class="context-menu-icon"><span class="wpmolicon icon-trash"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Delete', 'wpmovielibray' ); ?></div>
				</div>
<# } else if ( 'draft' === data.post.status ) { #>
				<div class="context-menu-item restore-item" data-action="restore">
					<div class="context-menu-icon"><span class="wpmolicon icon-publish"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Restore', 'wpmovielibray' ); ?></div>
				</div>
				<div class="context-menu-item trash-item" data-action="trash">
					<div class="context-menu-icon"><span class="wpmolicon icon-trash"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Delete', 'wpmovielibray' ); ?></div>
				</div>
<# } else if ( 'trash' === data.post.status ) { #>
				<div class="context-menu-item restore-item" data-action="restore">
					<div class="context-menu-icon"><span class="wpmolicon icon-restore"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Restore', 'wpmovielibray' ); ?></div>
				</div>
				<div class="context-menu-item restore-item" data-action="delete">
					<div class="context-menu-icon"><span class="wpmolicon icon-trash"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Delete', 'wpmovielibray' ); ?></div>
				</div>
<# } #>
			</div>
