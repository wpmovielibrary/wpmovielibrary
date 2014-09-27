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

	<div class="wpmoly_shortcode_spans">
		<span class="wpmoly_shortcode_span wpmoly_shortcode_span_title wpmoly_movie_genre_title"><?php echo $title ?></span>
		<span class="wpmoly_shortcode_span wpmoly_shortcode_span_value wpmoly_movie_genre_value"><?php echo $genres ?></span>
	</div>
