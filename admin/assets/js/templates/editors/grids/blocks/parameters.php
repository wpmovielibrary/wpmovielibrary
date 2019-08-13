<?php
/**
 * Grid Editor Parameters Block Template
 *
 * @since 3.0.0
 */
?>

						<div class="grid-type<# if ( 'movie' === data.type ) { #> active<# if ( 'movie' === data.defaults.type ) { #> default<# } } #>">
							<div class="type-name">
								<a data-action="set-type" data-value="movie"><?php esc_html_e( 'Movies', 'wpmovielibrary' ); ?></a>
								<# if ( 'movie' === data.type ) { #><span class="wpmolicon icon-yes-alt2"></span><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
							</div>
							<div class="grid-modes">
								<div class="grid-mode<# if ( 'grid' === data.mode ) { #> active<# if ( 'grid' === data.defaults.mode ) { #> default<# } } #>">
									<div class="mode-name">
										<a data-action="set-mode" data-value="grid"><?php esc_html_e( 'Grid', 'wpmovielibrary' ); ?></a>
										<# if ( 'grid' === data.mode ) { #><span class="wpmolicon icon-yes-alt2"></span><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
									</div>
									<div class="grid-themes">
										<div class="grid-theme<# if ( 'default' === data.theme ) { #> active<# if ( 'default' === data.defaults.theme ) { #> default<# } } #>">
											<div class="theme-name">
												<a data-action="set-theme" data-value="default"><?php esc_html_e( 'Default', 'wpmovielibrary' ); ?></a>
												<# if ( 'default' === data.theme ) { #><span class="wpmolicon icon-yes-alt2"></span><# } #>
											</div>
										</div>
										<div class="grid-theme<# if ( 'variant-1' === data.theme ) { #> active<# if ( 'variant-1' === data.defaults.theme ) { #> default<# } } #>">
											<div class="theme-name">
												<a data-action="set-theme" data-value="variant-1"><?php esc_html_e( 'Variant #1', 'wpmovielibrary' ); ?></a>
												<# if ( 'variant-1' === data.theme ) { #><span class="wpmolicon icon-yes-alt2"></span><# } #>
											</div>
										</div>
										<div class="grid-theme<# if ( 'variant-2' === data.theme ) { #> active<# if ( 'variant-2' === data.defaults.theme ) { #> default<# } } #>">
											<div class="theme-name">
												<a data-action="set-theme" data-value="variant-2"><?php esc_html_e( 'Variant #2', 'wpmovielibrary' ); ?></a>
												<# if ( 'variant-2' === data.theme ) { #><span class="wpmolicon icon-yes-alt2"></span><# } #>
											</div>
										</div>
									</div>
								</div>
								<div class="grid-mode<# if ( 'list' === data.mode ) { #> active<# if ( 'list' === data.defaults.mode ) { #> default<# } } #>">
									<div class="mode-name">
										<a data-action="set-mode" data-value="list"><?php esc_html_e( 'List', 'wpmovielibrary' ); ?></a>
										<# if ( 'list' === data.mode ) { #><span class="wpmolicon icon-yes-alt2"></span><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
									</div>
									<div class="grid-themes">
										<div class="grid-theme<# if ( 'default' === data.theme ) { #> active<# if ( 'default' === data.defaults.theme ) { #> default<# } } #>">
											<div class="theme-name">
												<a data-action="set-theme" data-value="default"><?php esc_html_e( 'Default', 'wpmovielibrary' ); ?></a>
												<# if ( 'default' === data.theme ) { #><span class="wpmolicon icon-yes-alt2"></span><# } #>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="grid-type<# if ( 'person' === data.type ) { #> active<# if ( 'person' === data.defaults.type ) { #> default<# } } #>">
							<div class="type-name">
								<a data-action="set-type" data-value="person"><?php esc_html_e( 'Persons', 'wpmovielibrary' ); ?></a>
								<# if ( 'person' === data.type ) { #><span class="wpmolicon icon-yes-alt2"></span><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
							</div>
							<div class="grid-modes">
								<div class="grid-mode<# if ( 'grid' === data.mode ) { #> active<# if ( 'grid' === data.defaults.mode ) { #> default<# } } #>">
									<div class="mode-name">
										<a data-action="set-mode" data-value="grid"><?php esc_html_e( 'Grid', 'wpmovielibrary' ); ?></a>
										<# if ( 'grid' === data.mode ) { #><span class="wpmolicon icon-yes-alt2"></span><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
									</div>
									<div class="grid-themes">
										<div class="grid-theme<# if ( 'default' === data.theme ) { #> active<# if ( 'default' === data.defaults.theme ) { #> default<# } } #>">
											<div class="theme-name">
												<a data-action="set-theme" data-value="default"><?php esc_html_e( 'Default', 'wpmovielibrary' ); ?></a>
												<# if ( 'default' === data.theme ) { #><span class="wpmolicon icon-yes-alt2"></span><# } #>
											</div>
										</div>
									</div>
								</div>
								<div class="grid-mode<# if ( 'list' === data.mode ) { #> active<# if ( 'list' === data.defaults.mode ) { #> default<# } } #>">
									<div class="mode-name">
										<a data-action="set-mode" data-value="list"><?php esc_html_e( 'List', 'wpmovielibrary' ); ?></a>
										<# if ( 'list' === data.mode ) { #><span class="wpmolicon icon-yes-alt2"></span><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
									</div>
									<div class="grid-themes">
										<div class="grid-theme<# if ( 'default' === data.theme ) { #> active<# if ( 'default' === data.defaults.theme ) { #> default<# } } #>">
											<div class="theme-name">
												<a data-action="set-theme" data-value="default"><?php esc_html_e( 'Default', 'wpmovielibrary' ); ?></a>
												<# if ( 'default' === data.theme ) { #><span class="wpmolicon icon-yes-alt2"></span><# } #>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="grid-type<# if ( 'actor' === data.type ) { #> active<# if ( 'actor' === data.defaults.type ) { #> default<# } } #>">
							<div class="type-name">
								<a data-action="set-type" data-value="actor"><?php esc_html_e( 'Actors', 'wpmovielibrary' ); ?></a>
								<# if ( 'actor' === data.type ) { #><span class="wpmolicon icon-yes-alt2"></span><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
							</div>
							<div class="grid-modes">
								<div class="grid-mode<# if ( 'grid' === data.mode ) { #> active<# if ( 'grid' === data.defaults.mode ) { #> default<# } } #>">
									<div class="mode-name">
										<a data-action="set-mode" data-value="grid"><?php esc_html_e( 'Grid', 'wpmovielibrary' ); ?></a>
										<# if ( 'grid' === data.mode ) { #><span class="wpmolicon icon-yes-alt2"></span><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
									</div>
									<div class="grid-themes">
										<div class="grid-theme<# if ( 'default' === data.theme ) { #> active<# if ( 'default' === data.defaults.theme ) { #> default<# } } #>">
											<div class="theme-name">
												<a data-action="set-theme" data-value="default"><?php esc_html_e( 'Default', 'wpmovielibrary' ); ?></a>
												<# if ( 'default' === data.theme ) { #><span class="wpmolicon icon-yes-alt2"></span><# } #>
											</div>
										</div>
									</div>
								</div>
								<div class="grid-mode<# if ( 'list' === data.mode ) { #> active<# if ( 'list' === data.defaults.mode ) { #> default<# } } #>">
									<div class="mode-name">
										<a data-action="set-mode" data-value="list"><?php esc_html_e( 'List', 'wpmovielibrary' ); ?></a>
										<# if ( 'list' === data.mode ) { #><span class="wpmolicon icon-yes-alt2"></span><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
									</div>
									<div class="grid-themes">
										<div class="grid-theme<# if ( 'default' === data.theme ) { #> active<# if ( 'default' === data.defaults.theme ) { #> default<# } } #>">
											<div class="theme-name">
												<a data-action="set-theme" data-value="default"><?php esc_html_e( 'Default', 'wpmovielibrary' ); ?></a>
												<# if ( 'default' === data.theme ) { #><span class="wpmolicon icon-yes-alt2"></span><# } #>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="grid-type<# if ( 'genre' === data.type ) { #> active<# if ( 'genre' === data.defaults.type ) { #> default<# } } #>">
							<div class="type-name">
								<a data-action="set-type" data-value="genre"><?php esc_html_e( 'Genres', 'wpmovielibrary' ); ?></a>
								<# if ( 'genre' === data.type ) { #><span class="wpmolicon icon-yes-alt2"></span><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
							</div>
							<div class="grid-modes">
								<div class="grid-mode<# if ( 'grid' === data.mode ) { #> active<# if ( 'grid' === data.defaults.mode ) { #> default<# } } #>">
									<div class="mode-name">
										<a data-action="set-mode" data-value="grid"><?php esc_html_e( 'Grid', 'wpmovielibrary' ); ?></a>
										<# if ( 'grid' === data.mode ) { #><span class="wpmolicon icon-yes-alt2"><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
									</div>
									<div class="grid-themes">
										<div class="grid-theme<# if ( 'default' === data.theme ) { #> active<# if ( 'default' === data.defaults.theme ) { #> default<# } } #>">
											<div class="theme-name">
												<a data-action="set-theme" data-value="default"><?php esc_html_e( 'Default', 'wpmovielibrary' ); ?></a>
												<# if ( 'default' === data.theme ) { #><span class="wpmolicon icon-yes-alt2"></span><# } #>
											</div>
										</div>
									</div>
								</div>
								<div class="grid-mode<# if ( 'list' === data.mode ) { #> active<# if ( 'list' === data.defaults.mode ) { #> default<# } } #>">
									<div class="mode-name">
										<a data-action="set-mode" data-value="list"><?php esc_html_e( 'List', 'wpmovielibrary' ); ?></a>
										<# if ( 'list' === data.mode ) { #><span class="wpmolicon icon-yes-alt2"><# } else { #><span class="wpmolicon icon-down-chevron"></span><# } #>
									</div>
									<div class="grid-themes">
										<div class="grid-theme<# if ( 'default' === data.theme ) { #> active<# if ( 'default' === data.defaults.theme ) { #> default<# } } #>">
											<div class="theme-name">
												<a data-action="set-theme" data-value="default"><?php esc_html_e( 'Default', 'wpmovielibrary' ); ?></a>
												<# if ( 'default' === data.theme ) { #><span class="wpmolicon icon-yes-alt2"></span><# } #>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
