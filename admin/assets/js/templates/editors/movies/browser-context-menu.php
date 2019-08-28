<?php
/**
 * Movies Editor browser context menu Template
 *
 * @since 3.0.0
 */
?>

			<# console.log( data ); #>
			<div class="context-menu-content">
				<div class="context-menu-item" data-action="">
					<div class="context-menu-icon"><span class="wpmolicon icon-movie"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Preview', 'wpmovielibray' ); ?></div>
				</div>
				<div class="context-menu-item" data-action="">
					<div class="context-menu-icon"><span class="wpmolicon icon-edit"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Edit', 'wpmovielibray' ); ?></div>
				</div>
				<div class="context-menu-item context-menu-separator"></div>
				<div class="context-menu-item" data-action="">
					<div class="context-menu-icon"><span class="wpmolicon icon-media"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Media', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
							<div class="context-menu-item active" data-action="" data-value="">
								<div class="context-menu-icon"><span class="wpmolicon icon-yes"></span></div>
								<div class="context-menu-text"><?php esc_html_e( 'DVD', 'wpmovielibray' ); ?></div>
							</div>
							<div class="context-menu-item" data-action="" data-value="">
								<div class="context-menu-icon"></div>
								<div class="context-menu-text"><?php esc_html_e( 'Blu-ray', 'wpmovielibray' ); ?></div>
							</div>
							<div class="context-menu-item" data-action="" data-value="">
								<div class="context-menu-icon"></div>
								<div class="context-menu-text"><?php esc_html_e( 'VoD', 'wpmovielibray' ); ?></div>
							</div>
							<div class="context-menu-item" data-action="" data-value="">
								<div class="context-menu-icon"></div>
								<div class="context-menu-text"><?php esc_html_e( 'DivX', 'wpmovielibray' ); ?></div>
							</div>
							<div class="context-menu-item" data-action="" data-value="">
								<div class="context-menu-icon"></div>
								<div class="context-menu-text"><?php esc_html_e( 'VHS', 'wpmovielibray' ); ?></div>
							</div>
							<div class="context-menu-item active" data-action="" data-value="">
								<div class="context-menu-icon"><span class="wpmolicon icon-yes"></span></div>
								<div class="context-menu-text"><?php esc_html_e( 'Cinema', 'wpmovielibray' ); ?></div>
							</div>
							<div class="context-menu-item" data-action="" data-value="">
								<div class="context-menu-icon"></div>
								<div class="context-menu-text"><?php esc_html_e( 'Other', 'wpmovielibray' ); ?></div>
							</div>
						</div>
					</div>
				</div>
				<div class="context-menu-item" data-action="">
					<div class="context-menu-icon"><span class="wpmolicon icon-circle-thin"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Status', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
				</div>
				<div class="context-menu-item" data-action="">
					<div class="context-menu-icon"><span class="wpmolicon icon-star"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Rating', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
				</div>
				<div class="context-menu-item" data-action="">
					<div class="context-menu-icon"><span class="wpmolicon icon-format"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Format', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
				</div>
				<div class="context-menu-item" data-action="">
					<div class="context-menu-icon"><span class="wpmolicon icon-subtitle"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Subtitles', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
				</div>
				<div class="context-menu-item" data-action="">
					<div class="context-menu-icon"><span class="wpmolicon icon-language"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Languages', 'wpmovielibray' ); ?></div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
				</div>
				<div class="context-menu-item context-menu-separator"></div>
				<div class="context-menu-item" data-action="">
					<div class="context-menu-icon"><span class="wpmolicon icon-unpublish"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Unpublish', 'wpmovielibray' ); ?></div>
				</div>
				<div class="context-menu-item" data-action="">
					<div class="context-menu-icon"><span class="wpmolicon icon-trash"></span></div>
					<div class="context-menu-text"><?php esc_html_e( 'Delete', 'wpmovielibray' ); ?></div>
				</div>
			</div>
