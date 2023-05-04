<?php
/**
 * Statistics Default Template
 * 
 * @since    1.2
 * 
 * @uses    $content array of movies
 * @uses    $style container classes
 * @uses    $description Widget's description
 */
?>
	<div class="<?php echo $style ?>">

<?php if ( '' != $description ) : ?>
		<div class="<?php echo $style ?> description"><?php echo $description ?></div>
<?php endif; ?>

		<?php echo $content ?>

	</div>
