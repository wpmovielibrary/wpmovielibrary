 <?php
/**
 * Taxonomies Archive Template
 * 
 * @since    1.2
 * 
 * @uses    $taxonomy
 * @uses    $links
 */

?>

	<ul class="wpml_archives wpml_<?php echo $taxonomy; ?>_archives">
<?php foreach ( $links as $link ) : ?>

		<li><a href="<?php echo $link['url']; ?>" title="<?php echo $link['attr_title']; ?>"><?php echo $link['title']; ?> (<?php echo $link['count']; ?>)</a></li>
<?php endforeach; ?>

	</ul>