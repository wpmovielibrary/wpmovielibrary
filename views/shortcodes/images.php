<?php
/**
 * Movie Images Shortcode view Template
 * 
 * Showing a movie's images.
 * 
 * @since    1.2
 * 
 * @uses    $size
 * @uses    $movie_id
 * @uses    $images
 */
?>

	<ul class="wpmoly shortcode <?php echo $size ?> images list">

<?php foreach ( $images as $image ) : ?>
		<li class="wpmoly shortcode imported <?php echo $size ?> image">
			<a class="wpmoly shortcode imported <?php echo $size ?> image link" href="<?php echo $image['full'][0]; ?>">
				<img src="<?php echo $image['thumbnail'][0]; ?>" width="<?php echo $image['thumbnail'][1]; ?>" height="<?php echo $image['thumbnail'][2]; ?>" alt="" />
			</a>
		</li>

<?php endforeach; ?>
	</ul>
