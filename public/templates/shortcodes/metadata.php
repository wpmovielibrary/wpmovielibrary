<?php
/**
 * Metadata Shortcode view Template
 *
 * Showing a specific movie metadata.
 *
 * @since 3.0.0
 *
 * @uses $meta
 * @uses $key
 */
?>

	<span class="wpmoly shortcode item meta <?php echo esc_attr( $key ) ?> value<?php echo empty( $meta ) ? ' empty' : ''; ?>"><?php echo $meta ?></span>
