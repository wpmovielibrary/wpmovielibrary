<?php
/**
 * Movie Metadata view Template
 * 
 * Showing a movie's metadata.
 * 
 * @since    1.2
 * 
 * @uses    $items
 */
?>

	<div class="wpmoly_movie_metadata">
		<dl class="wpmoly_movie">
<?php foreach ( $items as $item ) : ?>
			<dt class="wpmoly_<?php echo $item['slug'] ?>_field_title"><?php echo $item['title'] ?></dt>
			<dd class="wpmoly_<?php echo $item['slug'] ?>_field_value"><?php echo $item['value'] ?></dd>

<?php endforeach; ?>
		</dl>
	</div>
