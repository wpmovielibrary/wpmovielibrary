<?php
/**
 * Movie Headbox Menu Template view
 * 
 * Showing a movie's headbox menu, Allociné style.
 * 
 * @since    2.1.4
 * 
 * @uses    $links
 */
?>

			<div class="wpmoly headbox allocine movie menu">
				<ul>
<?php
$active = 'active';
foreach ( $links as $slug => $link ) :

	$title = '';
	if ( ! empty( $link['title'] ) )
		$title = $link['title'];
	if ( ! empty( $link['icon'] ) )
		$title = sprintf( '<span class="wpmolicon icon-%s"></span>&nbsp;%s', $link['icon'], $title );
?>
					<li><a id="movie-headbox-<?php echo $slug ?>-link-<?php echo $id ?>" class="<?php echo $active ?>" href="#movie-headbox-<?php echo $id ?>" onclick="wpmoly_headbox.toggle( '<?php echo $slug ?>', <?php echo $id ?> ); return false;"><?php echo $title ?></a></li>
<?php
$active = '';
endforeach; ?>
				</ul>
			</div>
