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

	<ul class="wpmoly archives taxonomy list <?php echo $taxonomy; ?>">
<?php foreach ( $links as $link ) : ?>

		<li class="wpmoly archives taxonomy list item"><a class="wpmoly archives taxonomy list item link" href="<?php echo $link['url']; ?>" title="<?php echo $link['attr_title']; ?>"><span class="wpmoly archives taxonomy list item link title"><?php echo $link['title']; ?></span> <span class="wpmoly archives taxonomy list item link count">(<?php echo $link['count']; ?>)</span></a></li>
<?php endforeach; ?>

	</ul>