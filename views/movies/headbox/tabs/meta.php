<?php
/**
 * Movie Headbox Meta Tab Template view
 * 
 * Showing a movie's headbox meta tab.
 * 
 * @since    2.0
 * 
 * @uses    $meta
 */
?>

				<div class="wpmoly headbox movie meta fields">
<?php foreach ( $meta as $m ) : ?>
					<div class="wpmoly headbox movie meta field">
						<span class="wpmoly headbox movie meta field title"><span class="wpmolicon icon-<?php echo $m['slug'] ?>"></span> <?php echo $m['title'] ?></span>
						<span class="wpmoly headbox movie meta field value"><?php echo $m['value'] ?></span>
					</div>

<?php endforeach; ?>
				</div>
