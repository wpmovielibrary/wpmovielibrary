<?php
/**
 * Labeled Metadata Shortcode view Template
 *
 * Showing a specific movie metadata.
 *
 * @since 3.0.0
 *
 * @uses $meta
 * @uses $label
 * @uses $key
 */
?>

	<div class="wpmoly shortcode block<?php echo empty( $meta ) ? ' empty' : ''; ?>">
		<span class="wpmoly shortcode item meta <?php echo esc_attr( $key ) ?> title"><?php echo esc_attr( $label ) ?></span>
		<span class="wpmoly shortcode item meta <?php echo esc_attr( $key ) ?> value"><?php echo $meta; ?></span>
	</div>
