<?php
/**
 * Labeled Detail Shortcode view Template
 * 
 * Showing a specific movie detail.
 * 
 * @since    3.0
 * 
 * @uses    $detail
 * @uses    $label
 * @uses    $key
 */

print_r( $detail );
?>

	<div class="wpmoly shortcode block<?php if ( empty( $detail ) ) echo ' empty'; ?>">
		<span class="wpmoly shortcode item detail <?php echo esc_attr( $key ) ?> title"><?php echo esc_attr( $label ) ?></span>
		<span class="wpmoly shortcode item detail <?php echo esc_attr( $key ) ?> value"><?php echo $detail; ?></span>
	</div>
