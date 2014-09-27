<?php
/**
 * Labeled Metadata Shortcode view Template
 * 
 * Showing a specific movie metadata.
 * 
 * @since    1.2
 * 
 * @uses    $movies
 */
?>

	<div class="wpmoly_shortcode_spans">
		<span class="wpmoly_shortcode_span wpmoly_shortcode_span_title wpmoly_movie_<?php echo $key ?>_title"><?php echo $title ?></span>
		<span class="wpmoly_shortcode_span wpmoly_shortcode_span_value wpmoly_movie_<?php echo $key ?>_value"><?php echo $meta ?></span>
	</div>
