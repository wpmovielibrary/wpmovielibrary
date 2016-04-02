
			<label class="screen-reader-text" for="media-attachment-size-filter"><?php _e( 'Filter by size', 'wpmovielibrary' ) ?></label>
			<select id="media-attachment-size-filter" class="attachment-filters" data-action="filter-size">
				<option value="" data-min-value="0" data-max-value="0"><?php _e( 'All sizes', 'wpmovielibrary' ) ?></option>
				<option value="" data-min-value="1" data-max-value="640"><?php _e( 'Small', 'wpmovielibrary' ) ?></option>
				<option value="" data-min-value="640" data-max-value="1000"><?php _e( 'Medium', 'wpmovielibrary' ) ?></option>
				<option value="" data-min-value="1000" data-max-value="3000"><?php _e( 'Large', 'wpmovielibrary' ) ?></option>
				<option value="" data-min-value="3000" data-max-value="0"><?php _e( 'Giant', 'wpmovielibrary' ) ?></option>
			</select>
<# if ( 'poster' === data.mode ) { #>
			<label class="screen-reader-text" for="media-attachment-language-filter"><?php _e( 'Filter by language', 'wpmovielibrary' ) ?></label>
			<select id="media-attachment-language-filter" class="attachment-filters" data-action="filter-language">
				<option value="all"><?php _e( 'All languages', 'wpmovielibrary' ) ?></option>
<?php
// Load available languages
$languages = wpmoly_o( 'supported_languages' );
foreach ( $languages as $code => $lang ) :
?>
				<option value="<?php echo $code ?>"><?php _e( $lang, 'wpmovielibrary' ) ?></option>
<?php endforeach; ?>
			</select>
<# } #>
			<span class="spinner"></span>
