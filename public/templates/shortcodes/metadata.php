<?php
/**
 * Metadata Shortcode view Template
 * 
 * Showing a specific movie metadata.
 * 
 * @since    1.2
 * 
 * @uses    $movies
 */
?>

	<span class="wpmoly shortcode item meta <?php echo esc_attr( $key ) ?> value<?php if ( empty( $meta ) ) echo ' empty'; ?>"><?php echo $meta ?></span>
