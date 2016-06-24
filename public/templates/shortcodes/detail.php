<?php
/**
 * Detail Shortcode view Template
 * 
 * Showing a specific movie detail.
 * 
 * @since    3.0
 * 
 * @uses    $detail
 * @uses    $key
 */
?>

	<span class="wpmoly shortcode item detail <?php echo esc_attr( $key ) ?> value<?php if ( empty( $detail ) ) echo ' empty'; ?>"><?php echo $detail ?></span>
