
	<div class="wpml-widget-description"><?php echo $description ?></div>

	<select class="<?php echo $style ?>">
		<?php if ( ! is_null( $title ) ) echo '<option value="">' . __( 'Select a media', WPML_SLUG ) . '</option>' ?>
<?php foreach ( $items as $item ) : ?>
		<option value="<?php echo $item['link'] ?>"><?php echo $item['title'] ?></option>

<?php endforeach; ?>
	</select>