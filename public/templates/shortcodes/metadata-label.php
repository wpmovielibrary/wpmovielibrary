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

	<div class="wpmoly shortcode block<?php if ( empty( $meta ) ) echo ' empty'; ?>">
		<span class="wpmoly shortcode item meta <?php echo esc_attr( $key ) ?> title"><?php echo esc_attr( $label ) ?></span>
		<span class="wpmoly shortcode item meta <?php echo esc_attr( $key ) ?> value"><?php echo ! empty( $meta ) ? esc_attr( $meta ) : '−'; ?></span>
	</div>
