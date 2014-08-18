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

	<div class="wpml_shortcode_spans">
		<span class="wpml_shortcode_span wpml_shortcode_span_title wpml_movie_<?php echo $key ?>_title"><?php echo $title ?></span>
		<span class="wpml_shortcode_span wpml_shortcode_span_value wpml_movie_<?php echo $key ?>_value"><?php echo $meta ?></span>
	</div>
