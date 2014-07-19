<?php
/**
 * Movie Images Shortcode view Template
 * 
 * Showing a movie's images.
 * 
 * @since    1.2.0
 * 
 * @uses    $size
 * @uses    $images
 */
?>

	<ul class="wpml_shortcode_ul wpml_movie_images">

<?php foreach ( $images as $image ) : ?>
		<li class="wpml_movie_image wpml_movie_image_<?php echo $size ?> wpml_movie_imported_image"><?php echo $image ?></li>

<?php endforeach; ?>
	</ul>
