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

	<div class="wpml_movie_metadata">
		<dl class="wpml_movie">
<?php foreach ( $items as $item ) : ?>
			<dt class="wpml_<?php echo $item['slug'] ?>_field_title"><?php echo $item['title'] ?></dt>
			<dd class="wpml_<?php echo $item['slug'] ?>_field_value"><?php echo $item['value'] ?></dd>

<?php endforeach; ?>
		</dl>
	</div>
