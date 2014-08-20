<?php
/**
 * General Template for items dropdown lists
 * 
 * @since    1.2
 * 
 * @uses    $items array of movies
 * @uses    $style container classes
 * @uses    $description Widget's description
 * @uses    $default_option Select default option
 */
?>
	<div class="<?php echo $style ?>">

		<div class="wpml-widget-description"><?php echo $description ?></div>

		<select class="wpml-list">
			<option value=""><?php echo $default_option ?></option>
<?php foreach ( $items as $item ) : ?>
			<option value="<?php echo $item['link'] ?>"><?php echo $item['title'] ?></option>

<?php endforeach; ?>
		</select>

	</div>