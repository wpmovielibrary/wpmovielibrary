
					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-date"></span></span>
						<span class="setting-text"><label><?php _e( 'Search a specific year:', 'wpmovielibrary' ); ?>&nbsp; <input id="wpmoly-search-year" type="text" data-set-setting="search-year" data-value="{{ data.search_year }}" size="4" maxlength="4" value="{{ data.search_year }}" /></label></span>
					</div>
					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-date"></span></span>
						<span class="setting-text"><label><?php _e( 'Search a specific primary year:', 'wpmovielibrary' ); ?>&nbsp; <input id="wpmoly-search-pyear" type="text" data-set-setting="search-pyear" data-value="{{ data.search_pyear }}" size="4" maxlength="4" value="{{ data.search_pyear }}" /></label></span>
					</div>
					<div class="wpmoly-search-setting full">
						<span class="setting-icon"><span class="wpmolicon icon-language"></span></span>
						<span class="setting-text">
							<span><?php _e( 'Select a language:', 'wpmovielibrary' ) ?></span>
							<div class="setting-list"><?php foreach ( wpmoly_o( 'supported_languages' ) as $code => $lang ) : ?><button type="button" class="<# if ( '<?php echo $code ?>' == data.api_language ) { #>active<# } #>" data-set-setting="api-language" data-value="<?php echo $code ?>"><?php echo $lang ?></button><?php endforeach; ?></div>
						</span>
					</div>

					<hr/>

					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-heart"></span></span>
						<span class="setting-text"><a id="wpmoly-api-adult" data-switch-setting="api-adult" data-value="{{ data.api_adult }}"><span class="wpmolicon icon-<# if (  data.api_adult ) { #>yes<# } else { #>no<# } #>"></span>&nbsp; <?php _e( 'Include adult movies', 'wpmovielibrary' ); ?></a></span>
					</div>
					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-ellipsis-h"></span></span>
						<span class="setting-text"><a id="wpmoly-api-paginate" data-switch-setting="api-paginate" data-value="{{ data.api_paginate }}"><span class="wpmolicon icon-<# if (  data.api_paginate ) { #>yes<# } else { #>no<# } #>"></span>&nbsp; <?php _e( 'Enable paginated results', 'wpmovielibrary' ); ?></a></span>
					</div>

					<hr/>

					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-collection"></span></span>
						<span class="setting-text"><a id="wpmoly-search-collection-autocomplete" data-switch-setting="collection-autocomplete" data-value="{{ data.collection_autocomplete }}"><span class="wpmolicon icon-<# if (  data.collection_autocomplete ) { #>yes<# } else { #>no<# } #>"></span>&nbsp; <?php _e( 'Autocomplete collections', 'wpmovielibrary' ); ?></a></span>
					</div>

					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-actor"></span></span>
						<span class="setting-text"><a id="wpmoly-search-actor-autocomplete" data-switch-setting="actor-autocomplete" data-value="{{ data.actor_autocomplete }}"><span class="wpmolicon icon-<# if (  data.actor_autocomplete ) { #>yes<# } else { #>no<# } #>"></span>&nbsp; <?php _e( 'Autocomplete actors', 'wpmovielibrary' ); ?></a></span>
					</div>

					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-tags"></span></span>
						<span class="setting-text"><a id="wpmoly-search-genre-autocomplete" data-switch-setting="genre-autocomplete" data-value="{{ data.genre_autocomplete }}"><span class="wpmolicon icon-<# if (  data.genre_autocomplete ) { #>yes<# } else { #>no<# } #>"></span>&nbsp; <?php _e( 'Autocomplete genres', 'wpmovielibrary' ); ?></a></span>
					</div>
					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-actor"></span></span>
						<span class="setting-text"><label><?php _e( 'Maximum number of actors:', 'wpmovielibrary' ); ?>&nbsp; <input id="wpmoly-actor-limit" type="text" data-set-setting="actor-limit" data-value="{{ data.actor_limit }}" size="4" maxlength="4" value="{{ data.actor_limit }}" /></label></span>
					</div>

					<hr/>

					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-images-alt"></span></span>
						<span class="setting-text"><a id="wpmoly-search-hide-existing-backdrops" data-switch-setting="hide-existing-backdrops" data-value="{{ data.hide_existing_backdrops }}"><span class="wpmolicon icon-<# if (  data.hide_existing_backdrops ) { #>yes<# } else { #>no<# } #>"></span>&nbsp; <?php _e( 'Hide previoulsy imported backdrops', 'wpmovielibrary' ); ?></a></span>
					</div>
					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-poster"></span></span>
						<span class="setting-text"><a id="wpmoly-search-hide-existing-posters" data-switch-setting="hide-existing-posters" data-value="{{ data.hide_existing_posters }}"><span class="wpmolicon icon-<# if (  data.hide_existing_posters ) { #>yes<# } else { #>no<# } #>"></span>&nbsp; <?php _e( 'Hide previoulsy imported posters', 'wpmovielibrary' ); ?></a></span>
					</div>
					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-images-alt"></span></span>
						<span class="setting-text"><a id="wpmoly-search-backdrops-autoimport" data-switch-setting="backdrops-autoimport" data-value="{{ data.backdrops_autoimport }}"><span class="wpmolicon icon-<# if (  data.backdrops_autoimport ) { #>yes<# } else { #>no<# } #>"></span>&nbsp; <?php _e( 'Automatically import backdrops', 'wpmovielibrary' ); ?></a></span>
					</div>
					<div class="wpmoly-search-setting hidden">
						<span class="setting-icon"><span class="wpmolicon icon-poster"></span></span>
						<span class="setting-text"><a id="wpmoly-search-posters-autoimport" data-switch-setting="posters-autoimport" data-value="{{ data.posters_autoimport }}"><span class="wpmolicon icon-<# if (  data.posters_autoimport ) { #>yes<# } else { #>no<# } #>"></span>&nbsp; <?php _e( 'Automatically import posters', 'wpmovielibrary' ); ?></a></span>
					</div>
					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-images-alt"></span></span>
						<span class="setting-text"><label><?php _e( 'Number of backdrops to import:', 'wpmovielibrary' ); ?>&nbsp; <input id="wpmoly-search-backdrops-limit" type="text" data-set-setting="backdrops-limit" data-value="{{ data.backdrops_limit }}" size="4" maxlength="4" value="{{ data.backdrops_limit }}" /></label></span>
					</div>
					<div class="wpmoly-search-setting hidden">
						<span class="setting-icon"><span class="wpmolicon icon-poster"></span></span>
						<span class="setting-text"><label><?php _e( 'Number of posters to import:', 'wpmovielibrary' ); ?>&nbsp; <input id="wpmoly-search-posters-limit" type="text" data-set-setting="posters-limit" data-value="{{ data.posters_limit }}" size="4" maxlength="4" value="{{ data.posters_limit }}" /></label></span>
					</div>
					<div class="wpmoly-search-setting hidden">
					</div>
					<div class="wpmoly-search-setting">
						<span class="setting-icon"><span class="wpmolicon icon-poster"></span></span>
						<span class="setting-text"><a id="wpmoly-search-posters-featured" data-set-setting="posters-featured" data-value="{{ data.posters_featured }}"><span class="wpmolicon icon-<# if (  data.posters_featured ) { #>yes<# } else { #>no<# } #>"></span>&nbsp; <?php _e( 'Set default poster as featured image', 'wpmovielibrary' ); ?></a></span>
					</div>

					<a id="wpmoly-save-search-settings" data-action="save-settings"><span class="wpmolicon icon-yes"></span>&nbsp; <?php _e( 'Save current settings', 'wpmovielibrary' ); ?></a>
