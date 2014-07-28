<?php
/**
 * Empty Template
 * 
 * Showing when there's nothing else to show.
 * 
 * @since    1.2.0
 * 
 * @uses    $message
 */

if ( ! isset( $message ) )
	$message = __( 'Nothing to display.', WPML_SLUG );
?>
	<div class="wpml-empty">
		<em><?php echo $message ?></em>
	</div>