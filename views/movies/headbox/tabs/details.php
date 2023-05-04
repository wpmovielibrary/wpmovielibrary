<?php
/**
 * Movie Headbox Details Tab Template view
 * 
 * Showing a movie's headbox details tab.
 * 
 * @since    2.0
 * 
 * @uses    $details
 */
?>

				<div class="wpmoly headbox movie details fields">
<?php foreach ( $details as $detail ) : ?>
					<div class="wpmoly headbox movie details field">
						<span class="wpmoly headbox movie details field title"><span class="wpmolicon icon-<?php echo $detail['slug'] ?>"></span> <?php echo $detail['title'] ?></span>
						<span class="wpmoly headbox movie details field value">
<?php foreach ( $detail['value'] as $value ) : ?>
							<span><?php echo $value ?></span><br />

<?php endforeach; ?>
						</span>
					</div>

<?php endforeach; ?>
				</div>
