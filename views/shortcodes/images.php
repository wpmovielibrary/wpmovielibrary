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

	<ul class="wpml_shortcode_ul wpml_movie_images <?php echo $size ?>">

<?php foreach ( $images as $image ) : ?>
		<li class="wpml_movie_image wpml_movie_image_<?php echo $size ?> wpml_movie_imported_image">
			<a href="<?php echo $image['full'][0]; ?>">
				<img src="<?php echo $image['thumbnail'][0]; ?>" width="<?php echo $image['thumbnail'][1]; ?>" height="<?php echo $image['thumbnail'][2]; ?>" alt="" />
			</a>
		</li>

<?php endforeach; ?>
	</ul>
