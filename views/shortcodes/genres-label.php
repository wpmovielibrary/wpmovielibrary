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

	<div class="wpmoly shortcode block">
		<span class="wpmoly shortcode item genre title"><?php echo $title ?></span>
		<span class="wpmoly shortcode item genre value"><?php echo $genres ?></span>
	</div>
