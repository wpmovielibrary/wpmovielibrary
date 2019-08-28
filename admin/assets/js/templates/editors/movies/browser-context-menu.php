<?php
/**
 * Movies Editor browser context menu Template
 *
 * @since 3.0.0
 */

use wpmoly\utils;

$meta = utils\get_registered_movie_meta();
?>

			<# console.log( data ); #>
			<div class="context-menu-content">
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
							<div class="context-menu-item<# if ( _.contains( data.media, '<?php echo esc_attr( $key ); ?>' ) ) { #> active<# } #>" data-action="update" data-field="media" data-value="<?php echo esc_attr( $key ); ?>">
								<div class="context-menu-icon"><# if ( _.contains( data.media, '<?php echo esc_attr( $key ); ?>' ) ) { #><span class="wpmolicon icon-yes"></span><# } #></div>
								<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
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
							<div class="context-menu-item<# if ( '<?php echo esc_attr( $key ); ?>' === data.status ) { #> active<# } #>" data-action="update" data-field="status" data-value="<?php echo esc_attr( $key ); ?>">
								<div class="context-menu-icon"><# if ( '<?php echo esc_attr( $key ); ?>' === data.status ) { #><span class="wpmolicon icon-yes"></span><# } #></div>
								<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
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
							<div class="context-menu-item<# if ( '<?php echo esc_attr( $key ); ?>' === data.rating ) { #> active<# } #>" data-action="update" data-field="rating" data-value="<?php echo esc_attr( $key ); ?>">
								<div class="context-menu-icon"><# if ( '<?php echo esc_attr( $key ); ?>' === data.rating ) { #><span class="wpmolicon icon-yes"></span><# } #></div>
								<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
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
							<div class="context-menu-item<# if ( _.contains( data.format, '<?php echo esc_attr( $key ); ?>' ) ) { #> active<# } #>" data-action="update" data-field="format" data-value="<?php echo esc_attr( $key ); ?>">
								<div class="context-menu-icon"><# if ( _.contains( data.format, '<?php echo esc_attr( $key ); ?>' ) ) { #><span class="wpmolicon icon-yes"></span><# } #></div>
								<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
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
							<div class="context-menu-item<# if ( _.contains( data.subtitles, '<?php echo esc_attr( $key ); ?>' ) ) { #> active<# } #>" data-action="update" data-field="subtitles" data-value="<?php echo esc_attr( $key ); ?>">
								<div class="context-menu-icon"><# if ( _.contains( data.subtitles, '<?php echo esc_attr( $key ); ?>' ) ) { #><span class="wpmolicon icon-yes"></span><# } #></div>
								<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
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
							<div class="context-menu-item<# if ( _.contains( data.language, '<?php echo esc_attr( $key ); ?>' ) ) { #> active<# } #>" data-action="update" data-field="language" data-value="<?php echo esc_attr( $key ); ?>">
								<div class="context-menu-icon"><# if ( _.contains( data.language, '<?php echo esc_attr( $key ); ?>' ) ) { #><span class="wpmolicon icon-yes"></span><# } #></div>
								<div class="context-menu-text"><?php echo esc_html( $value ); ?></div>
							</div>
<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="context-menu-item context-menu-separator"></div>
				<div class="context-menu-item unpublish-item" data-action="unpublish">
					<div class="context-menu-icon"><span class="wpmolicon icon-unpublish"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Unpublish', 'wpmovielibray' ); ?></div>
				</div>
				<div class="context-menu-item trash-item" data-action="trash">
					<div class="context-menu-icon"><span class="wpmolicon icon-trash"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Delete', 'wpmovielibray' ); ?></div>
				</div>
			</div>
