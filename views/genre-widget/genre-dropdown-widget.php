<?php
/**
 * Genres Default Template
 * 
 * Display a dropdown list of items
 * 
 * @since    1.2.0
 * 
 * @uses    $items array of movies
 * @uses    $style container classes
 * @uses    $description Widget's description
 */
?>
	<div class="<?php echo $style ?>">

		<select class="wpml-list">
			<option value=""><?php _e( 'Select a genre', WPML_SLUG ) ?></option>
<?php foreach ( $items as $item ) : ?>
			<option value="<?php echo $item['link'] ?>"><?php echo $item['title'] ?></option>

<?php endforeach; ?>
		</select>

	</div>