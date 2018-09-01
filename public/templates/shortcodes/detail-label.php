<?php
/**
 * Labeled Detail Shortcode view Template
 *
 * Showing a specific movie detail.
 *
 * @since 3.0.0
 *
 * @uses $detail
 * @uses $label
 * @uses $key
 */

?>

	<div class="wpmoly shortcode block<?php echo empty( $detail ) ? ' empty' : ''; ?>">
		<span class="wpmoly shortcode item detail <?php echo esc_attr( $key ) ?> title"><?php echo esc_attr( $label ) ?></span>
		<span class="wpmoly shortcode item detail <?php echo esc_attr( $key ) ?> value"><?php echo $detail; ?></span>
	</div>
