
<# if ( data.settings.get( 'custom_mode' ) ) { #>
		<div class="grid-custom-section">
			<div class="grid-custom-block">
				<span class="grid-setting-label"><?php _e( 'Mode:', 'wpmovielibrary' ); ?></span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-mode-grid" name="{{ data.grid_id }}-mode[]" type="radio" data-setting-type="mode" value="grid" <# if ( 'grid' == data.settings.get( 'mode' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-mode-grid" class="value"><?php _e( 'Grid', 'wpmovielibrary' ); ?></label>
				</span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-mode-list" name="{{ data.grid_id }}-mode[]" type="radio" data-setting-type="mode" value="list" <# if ( 'list' == data.settings.get( 'mode' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-mode-list" class="value"><?php _e( 'List', 'wpmovielibrary' ); ?></label>
				</span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-mode-archives" name="{{ data.grid_id }}-mode[]" type="radio" data-setting-type="mode" value="archives" <# if ( 'archives' == data.settings.get( 'mode' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-mode-archives" class="value"><?php _e( 'Archives', 'wpmovielibrary' ); ?></label>
				</span>
			</div>
		</div>
<# } #>

<#
if ( data.settings.get( 'custom_content' ) ) {
	if ( 'grid' == data.settings.get( 'mode' ) ) {
#>
		<div class="grid-custom-section">
			<div class="grid-custom-block half-col">
				<span class="grid-setting-label"><?php _e( 'Number of columns:', 'wpmovielibrary' ); ?></span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-columns" name="{{ data.grid_id }}-columns" type="number" size="2" data-setting-type="columns" value="{{ data.settings.get( 'columns' ) }}" />
				</span>
			</div>
			<div class="grid-custom-block half-col">
				<span class="grid-setting-label"><?php _e( 'Number of rows:', 'wpmovielibrary' ); ?></span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-rows" name="{{ data.grid_id }}-rows" type="number" size="2" data-setting-type="rows" value="{{ data.settings.get( 'rows' ) }}" />
				</span>
			</div>
			<div class="grid-custom-block half-col">
				<span class="grid-setting-label"><?php _e( 'Ideal column width:', 'wpmovielibrary' ); ?></span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-column-width" name="{{ data.grid_id }}-column-width" type="number" size="2" data-setting-type="column-width" value="{{ data.settings.get( 'column_width' ) }}" />
				</span>
			</div>
			<div class="grid-custom-block half-col">
				<span class="grid-setting-label"><?php _e( 'Ideal row height:', 'wpmovielibrary' ); ?></span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-rows-height" name="{{ data.grid_id }}-rows-height" type="number" size="2" data-setting-type="rows-height" value="{{ data.settings.get( 'row_height' ) }}" />
				</span>
			</div>
		</div>
<# } else if ( 'list' == data.settings.get( 'mode' ) ) { #>
		<div class="grid-custom-section">
			<div class="grid-custom-block half-col">
				<span class="grid-setting-label"><?php _e( 'Number of columns:', 'wpmovielibrary' ); ?></span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-list-columns" name="{{ data.grid_id }}-list-columns" type="number" size="2" data-setting-type="list-columns" value="{{ data.settings.get( 'list_columns' ) }}" />
				</span>
			</div>
		</div>
		<div class="grid-custom-section">
			<div class="grid-custom-block half-col">
				<span class="grid-setting-label"><?php _e( 'Number of rows:', 'wpmovielibrary' ); ?></span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-list-rows" name="{{ data.grid_id }}-list-rows" type="number" size="2" data-setting-type="list-rows" value="{{ data.settings.get( 'list_rows' ) }}" />
				</span>
			</div>
		</div>
<#
	}
}
#>

<# if ( data.settings.get( 'custom_display' ) ) { #>
		<div class="grid-custom-section">
<#
	if ( 'grid' == data.settings.get( 'mode' ) ) {
		if ( 'movie' == data.settings.get( 'type' ) ) {
#>
			<div class="grid-custom-block half-col">
				<span class="grid-setting-label"><?php _e( 'Display:', 'wpmovielibrary' ); ?></span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-display-poster" name="{{ data.grid_id }}-display[]" type="checkbox" data-setting-type="display" value="poster" <# if ( _.contains( data.settings.get( 'display' ), 'poster' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-display-poster" class="value"><?php _e( 'Poster', 'wpmovielibrary' ); ?></label>
				</span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-display-title" name="{{ data.grid_id }}-display[]" type="checkbox" data-setting-type="display" value="" <# if ( _.contains( data.settings.get( 'display' ), 'title' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-display-title" class="value"><?php _e( 'Title', 'wpmovielibrary' ); ?></label>
				</span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-display-year" name="{{ data.grid_id }}-display[]" type="checkbox" data-setting-type="display" value="" <# if ( _.contains( data.settings.get( 'display' ), 'year' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-display-year" class="value"><?php _e( 'Year', 'wpmovielibrary' ); ?></label>
				</span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-display-genres" name="{{ data.grid_id }}-display[]" type="checkbox" data-setting-type="display" value="" <# if ( _.contains( data.settings.get( 'display' ), 'genres' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-display-genres" class="value"><?php _e( 'Genres', 'wpmovielibrary' ); ?></label>
				</span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-display-runtime" name="{{ data.grid_id }}-display[]" type="checkbox" data-setting-type="display" value="" <# if ( _.contains( data.settings.get( 'display' ), 'runtime' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-display-runtime" class="value"><?php _e( 'Runtime', 'wpmovielibrary' ); ?></label>
				</span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-display-rating" name="{{ data.grid_id }}-display[]" type="checkbox" data-setting-type="display" value="" <# if ( _.contains( data.settings.get( 'display' ), 'rating' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-display-rating" class="value"><?php _e( 'Rating', 'wpmovielibrary' ); ?></label>
				</span>
			</div>
<#
		} else if ( _.contains( [ 'actors', 'genres' ], data.settings.get( 'type' ) ) ) {
#>
			<div class="grid-custom-block half-col">
				<span class="grid-setting-label"><?php _e( 'Display:', 'wpmovielibrary' ); ?></span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-display-poster" name="{{ data.grid_id }}-display[]" type="checkbox" data-setting-type="display" value="poster" <# if ( _.contains( data.settings.get( 'display' ), 'poster' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-display-poster" class="value"><?php _e( 'Poster', 'wpmovielibrary' ); ?></label>
				</span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-display-title" name="{{ data.grid_id }}-display[]" type="checkbox" data-setting-type="display" value="" <# if ( _.contains( data.settings.get( 'display' ), 'title' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-display-title" class="value"><?php _e( 'Title', 'wpmovielibrary' ); ?></label>
				</span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-display-number" name="{{ data.grid_id }}-display[]" type="checkbox" data-setting-type="display" value="" <# if ( _.contains( data.settings.get( 'display' ), 'number' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-display-number" class="value"><?php _e( 'Number of items', 'wpmovielibrary' ); ?></label>
				</span>
			</div>
<#
		}
	} else if ( 'list' == data.settings.get( 'mode' ) ) {
#>
			<div class="grid-custom-block">
				<span class="grid-setting-label"><?php _e( 'Display:', 'wpmovielibrary' ); ?></span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-display-title" name="{{ data.grid_id }}-display[]" type="checkbox" data-setting-type="display" value="" <# if ( _.contains( data.settings.get( 'display' ), 'title' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-display-title" class="value"><?php _e( 'Title', 'wpmovielibrary' ); ?></label>
				</span>
				<span class="grid-setting-input">
					<input id="{{ data.grid_id }}-display-number" name="{{ data.grid_id }}-display[]" type="checkbox" data-setting-type="display" value="" <# if ( _.contains( data.settings.get( 'display' ), 'number' ) ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-display-number" class="value"><?php _e( 'Number of items', 'wpmovielibrary' ); ?></label>
				</span>
			</div>
<#
	}
#>
		</div>
<# } #>

		<button class="grid-customs-apply" type="button" data-action="apply"><?php _e( 'Apply', 'wpmovielibrary' ); ?></button>
