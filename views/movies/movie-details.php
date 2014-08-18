<?php
/**
 * Movie Details view Template
 * 
 * Showing a movie's details.
 * 
 * @since    1.2
 * 
 * @uses    $items
 */
?>

	<div class="wpml_movie_detail">
<?php foreach ( $items as $item ) : ?>
		<?php echo $item ?>

<?php endforeach; ?>
	</div>
