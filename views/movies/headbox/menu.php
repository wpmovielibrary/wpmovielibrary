<?php
/**
 * Movie Headbox Menu Template view
 * 
 * Showing a movie's headbox menu.
 * 
 * @since    2.0
 * 
 * @uses    $links
 */
?>

			<div class="wpmoly headbox movie menu">
<?php foreach ( $links as $slug => $link ) : ?>
				<a href="#movie-headbox-<?php echo $slug ?>-<?php echo $id ?>" onclick="wpmoly_headbox.toggle( 'movie-headbox-<?php echo $slug ?>-<?php echo $id ?>' ); return false;"><span class="wpmolicon icon-<?php echo $link['icon'] ?>" title="<?php echo $link['title'] ?>"></span></a>

<?php endforeach; ?>
			</div>