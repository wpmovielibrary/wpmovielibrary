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

	<div class="wpmoly shortcode block">
		<span class="wpmoly shortcode item meta <?php echo $key ?> title"><?php echo $title ?></span>
		<span class="wpmoly shortcode item meta <?php echo $key ?> value"><?php echo $meta ?></span>
	</div>
