
<# if ( data.custom_letter ) { #>
		<div class="grid-setting-block full-col letter-setting">
<?php
$letters = str_split( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
foreach ( $letters as $letter ) {
?>
			<span class="grid-setting-input">
				<input id="{{ data.grid_id }}-letter-<?php echo $letter; ?>" name="{{ data.grid_id }}-letter[]" type="radio" data-setting-type="letter" value="<?php echo $letter; ?>" <# if ( '<?php echo $letter; ?>' == data.query.letter ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-letter-<?php echo $letter; ?>" class="letter"><?php echo $letter; ?></label>
			</span>

<?php } ?>
			<span class="grid-setting-input">
				<input id="{{ data.grid_id }}-letter-all" type="radio" name="{{ data.grid_id }}-letter[]" data-setting-type="letter" value="" <# if ( '' == data.query.letter || ! data.query.letter ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-letter-all" class="letter"><?php _e( 'All', 'wpmovielibrary' ); ?></label>
			</span>
		</div>
<# } #>

<# if ( data.custom_order ) { #>
<# 	if ( 'movie' === data.type ) { #>
		<div class="grid-setting-block half-col orderby-setting">
			<span class="grid-setting-label"><?php _e( 'Order by:', 'wpmovielibrary' ); ?></span>
			<span class="grid-setting-input">
				<input id="{{ data.grid_id }}-orderby-title" name="{{ data.grid_id }}-orderby[]" type="radio" data-setting-type="orderby" value="title" <# if ( 'title' == data.query.orderby ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-orderby-title" class="value"><?php _e( 'Title', 'wpmovielibrary' ); ?></label>
			</span>
			<span class="grid-setting-input">
				<input id="{{ data.grid_id }}-orderby-date" name="{{ data.grid_id }}-orderby[]" type="radio" data-setting-type="orderby" value="date" <# if ( 'date' == data.query.orderby ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-orderby-date" class="value"><?php _e( 'Date', 'wpmovielibrary' ); ?></label>
			</span>
		</div>
<# 	} #>

		<div class="grid-setting-block half-col order-setting">
			<span class="grid-setting-label"><?php _e( 'Order:', 'wpmovielibrary' ); ?></span>
			<span class="grid-setting-input">
				<input id="{{ data.grid_id }}-order-asc" name="{{ data.grid_id }}-order[]" type="radio" data-setting-type="order" value="asc" <# if ( 'asc' == data.query.order ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-order-asc" class="value"><?php _e( 'Ascendingly', 'wpmovielibrary' ); ?></label>
			</span>
			<span class="grid-setting-input">
				<input id="{{ data.grid_id }}-order-desc" name="{{ data.grid_id }}-order[]" type="radio" data-setting-type="order" value="desc" <# if ( 'desc' == data.query.order ) { #>checked="checked" <# } #>/><label for="{{ data.grid_id }}-order-desc" class="value"><?php _e( 'Descendingly', 'wpmovielibrary' ); ?></label>
			</span>
		</div>
<# } #>

		<button class="grid-settings-apply" type="button" data-action="apply"><?php _e( 'Apply', 'wpmovielibrary' ); ?></button>
