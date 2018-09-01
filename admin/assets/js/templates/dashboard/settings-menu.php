<?php
/**
 * Dashboard Settings Menu Template
 *
 * @since 3.0.0
 */
?>

						<button class="button menu" type="button" data-action="open-menu"><span class="wpmolicon icon-settings-alt"></span><span class="wpmolicon icon-down-chevron"></span></button>
						<ul class="setting-pages">
							<# _.each( data.pages, function( page ) { #>
							<li class="setting-page<# if ( data.page === page.name ) { #> active<# } #>"><a href="#/settings/{{ page.name }}" data-action="browse" data-page="{{ page.name }}">{{ page.title }}</a></li>
							<# } ); #>
						</ul>
						<button class="button search" type="button" data-action="open-search"><span class="wpmolicon icon-search"></span></button>
						<div class="setting-search">
							<button class="button close" type="button" data-action="close-search"><span class="wpmolicon icon-no"></span></button>
							<button class="button search" type="button" data-action="start-search"><span class="wpmolicon icon-search"></span></button>
							<input class="search-input" type="text" data-value="search-query" placeholder="<?php _e( 'Find a specific setting...', 'wpmovielibrary' ); ?>" />
						</div>
