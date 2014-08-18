<?php
/**
 * Movie Poster Shortcode view Template
 * 
 * Showing a movie's poster.
 * 
 * @since    1.2
 * 
 * @uses    $size
 * @uses    $movie_id
 * @uses    $poster
 */
?>

	<div class="wpml_shortcode_div wpml_movie_poster wpml_movie_poster_<?php echo $size ?>">
		<a href="<?php echo $poster['full'][0] ?>">
			<img src="<?php echo $poster['thumbnail'][0] ?>" width="<?php echo $poster['thumbnail'][1] ?>" height="<?php echo $poster['thumbnail'][2] ?>" alt="" />
		</a>
	</div>
