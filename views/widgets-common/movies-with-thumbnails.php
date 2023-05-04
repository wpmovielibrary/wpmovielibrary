<?php
/**
 * General Template for Movies lists width thumbnails
 * 
 * @since    1.2
 * 
 * @uses    $items array of movies
 * @uses    $style container classes
 * @uses    $description Widget's description
 */
?>

	<div class="<?php echo $style ?>">

<?php if ( '' != $description ) : ?>
		<div class="<?php echo $style ?> description"><?php echo $description ?></div>
<?php endif; ?>

<?php foreach ( $items as $item ) : ?>
		<a href="<?php echo $item['link'] ?>" title="<?php echo $item['attr_title'] ?>">
			<figure class="<?php echo $style ?> movie">
				<?php echo get_the_post_thumbnail( $item['ID'], 'thumbnail', array( 'class' => $style . ' movie poster' ) ) ?>
			</figure>
		</a>

<?php endforeach; ?>
	</div>