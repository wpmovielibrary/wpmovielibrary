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
<?php
$active = 'active';
foreach ( $links as $slug => $link ) : ?>
				<a id="movie-headbox-<?php echo $slug ?>-link-<?php echo $id ?>" class="<?php echo $active ?>" href="#movie-headbox-<?php echo $slug ?>-<?php echo $id ?>" onclick="wpmoly_headbox.toggle( '<?php echo $slug ?>', <?php echo $id ?> ); return false;"><span class="wpmolicon icon-<?php echo $link['icon'] ?>" title="<?php echo $link['title'] ?>"></span></a>

<?php
$active = '';
endforeach; ?>
				<!--<a href="#" onclick="wpmoly_headbox.resize( <?php echo $id ?> ); return false;"><span class="wpmolicon icon-resize-enlarge" title="<?php _e( 'Expend', 'wpmovielibrary' ) ?>"></span></a>-->
				<span class="wpmoly headbox movie menu hint"><?php _e( 'Click an icon to see more', 'wpmovielibrary' ) ?></span>
			</div>