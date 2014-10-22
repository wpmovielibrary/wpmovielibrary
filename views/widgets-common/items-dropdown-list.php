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

<?php if ( '' != $description ) : ?>
		<div class="<?php echo $style ?> description"><?php echo $description ?></div>
<?php endif; ?>

		<select class="<?php echo $style ?> list">
			<option value=""><?php echo $default_option ?></option>
<?php foreach ( $items as $item ) : ?>
			<option value="<?php echo $item['link'] ?>"><?php echo $item['title'] ?></option>

<?php endforeach; ?>
		</select>

	</div>