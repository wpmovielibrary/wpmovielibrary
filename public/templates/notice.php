<?php
/**
 * Movie Headbox view Template
 *
 * Showing a movie's default headbox.
 *
 * @since 3.0.0
 *
 * @uses $icon
 * @uses $type
 * @uses $message
 * @uses $note
 */
?>
	<div class="wpmoly notice <?php echo $type; ?>">
		<div class="notice-content">
			<p><span class="<?php echo $icon; ?>"></span><?php echo $message; ?></p>
		</div>
		<div class="notice-footnote"><?php echo $note; ?></div>
	</div>
