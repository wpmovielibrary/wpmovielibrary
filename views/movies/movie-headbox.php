<?php
/**
 * Movie Metadata view Template
 * 
 * Showing a movie's head box.
 * 
 * @since    2.0
 * 
 * @uses    $items
 */
?>

	<div id="movie-headbox-<?php echo $id ?>" class="wpmoly block headbox contained <?php echo $theme ?>" style="background-image:url(<?php echo $headbox['poster'] ?>);">
		<div class="wpmoly headbox movie poster">
			<div class="wpmoly headbox movie details-3"><?php echo $headbox['details_3'] ?></div>
			<h3 class="wpmoly headbox movie title"><a class="wpmoly headbox movie link" href="<?php the_permalink(); ?>"><?php echo $headbox['title'] ?></a></h3>
			<h4 class="wpmoly headbox movie subtitle"><?php echo $headbox['subtitle'] ?></h4>
			<div class="wpmoly headbox movie details-1"><?php echo $headbox['details_1'] ?></div>
			<div class="wpmoly headbox movie details-2"><?php echo $headbox['details_2'] ?></div>
<?php echo $menu ?>
		</div>
<?php echo $tabs ?>
	</div>
