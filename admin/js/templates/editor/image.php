
						<a class="context-menu-toggle<# if ( data.menu ) { #> active<# } #>" data-action="toggle-menu" data-id="{{ data.id }}" href="{{ data.edit_link }}" target="_blank"><span class="wpmolicon icon-ellipsis-h"></span></a>
						<div class="context-menu<# if ( data.menu ) { #> active<# } #>">
							<div class="context-menu-title"><?php _e( 'Options', 'wpmovielibrary' ); ?></div>
							<div class="context-menu-content">
								<a class="context-menu-item" data-action="edit" href="{{ data.edit_link }}"><span class="wpmolicon icon-edit-page"></span>&nbsp; <?php _e( 'Edit', 'wpmovielibrary' ); ?></a>
								<a class="context-menu-item" data-action="remove" href="#"><span class="wpmolicon icon-trash"></span>&nbsp; <?php _e( 'Remove', 'wpmovielibrary' ); ?></a>
								<a class="context-menu-item" data-action="featured" href="#"><span class="wpmolicon icon-poster"></span>&nbsp; <?php _e( 'Featured', 'wpmovielibrary' ); ?></a>
							</div>
						</div>
						<div class="thumbnail" style="background-image:url({{ data.sizes.medium.url }})"></div>
