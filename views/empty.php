<?php
/**
 * Empty Template
 * 
 * Showing when there's nothing else to show.
 * 
 * @since    1.2
 * 
 * @uses    $message
 */

if ( ! isset( $message ) )
	$message = __( 'Nothing to display.', 'wpmovielibrary' );
?>
	<div class="wpmoly-empty">
		<em><?php echo $message ?></em>
	</div>