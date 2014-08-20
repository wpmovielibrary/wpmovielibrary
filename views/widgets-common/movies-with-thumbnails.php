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

		<div class="wpml-widget-description"><?php echo $description ?></div>

<?php foreach ( $items as $item ) : ?>
		<a href="<?php echo $item['link'] ?>" title="<?php echo $item['attr_title'] ?>">
			<figure class="widget-movie">
				<?php echo get_the_post_thumbnail( $item['ID'], 'thumbnail' ) ?>
			</figure>
		</a>

<?php endforeach; ?>
	</div>