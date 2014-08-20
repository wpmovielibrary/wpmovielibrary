<?php
/**
 * Labeled Genres Shortcode view Template
 * 
 * Showing a specific movie genres.
 * 
 * @since    1.2
 * 
 * @uses    $title
 * @uses    $genres
 */
?>

	<div class="wpml_shortcode_spans">
		<span class="wpml_shortcode_span wpml_shortcode_span_title wpml_movie_genre_title"><?php echo $title ?></span>
		<span class="wpml_shortcode_span wpml_shortcode_span_value wpml_movie_genre_value"><?php echo $genres ?></span>
	</div>
