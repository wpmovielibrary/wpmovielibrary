<?php
/**
 * Movie Images Shortcode view Template
 *
 * Showing a list of movie images.
 *
 * @since 3.0.0
 *
 * @uses $images
 * @uses $type
 * @uses $size
 */
?>

	<div class="wpmoly shortcode block <?php echo $type; ?>">

<?php
if ( $images->has_items() ) :
	while ( $images->has_items() ) :
		$image = $images->the_item();
		if ( ! is_null( $image ) ) :
?>

		<div class="wpmoly shortcode inline-block image <?php echo $type . ' ' . $size . ' ' . 'attachment-' . $image->id; ?>">

			<?php $image->render( $size, 'html' ); ?>
		</div>
<?php
		endif;
	endwhile;
endif;
?>

	</div>
