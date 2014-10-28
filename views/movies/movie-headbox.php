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

	<div id="movie-headbox-<?php echo $id ?>" class="wpmoly block headbox contained <?php echo $theme ?>" style="background-image:url(<?php echo $movie['poster'] ?>);">
		<div class="wpmoly headbox movie poster">
			<div class="wpmoly headbox movie rating starlined"><?php echo $movie['rating'] ?></div>
			<h3 class="wpmoly headbox movie title"><?php echo $movie['title'] ?></h3>
			<h4 class="wpmoly headbox movie tagline"><?php echo $movie['tagline'] ?></h4>
			<div class="wpmoly headbox movie genres"><?php echo $movie['genres'] ?></div>
			<div class="wpmoly headbox movie details">
				<span class="wpmolicon status icon-circle-o <?php echo $movie['status'] ?>" title="<?php echo $movie['status'] ?>"></span>
<?php foreach ( $movie['media'] as $media ) : ?>
				<span class="wpmolicon media icon-<?php echo $media ?>" title="<?php echo $media ?>"></span>

<?php endforeach; ?>
			</div>
			<div class="wpmoly headbox movie meta">
				<span class="wpmoly headbox movie year"><?php echo $movie['year'] ?></span> / <span class="wpmoly headbox movie runtime"><?php echo $movie['runtime'] ?></span>
			</div>
<?php echo $menu ?>
		</div>
<?php echo $tabs ?>
	</div>
