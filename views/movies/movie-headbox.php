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

	<div id="movie-headbox-<?php echo $id ?>" class="wpmoly block headbox contained <?php echo $theme ?>" style="background-image:url('//wp40/wp-content/uploads/2014/10/dX2RmqOi3xzMkLfra7WVfzOXexj.jpg');">
		<div class="wpmoly headbox movie poster">
			<span class="wpmoly headbox movie rating"><?php echo apply_filters( 'wpmoly_movie_rating_stars', 3.5 ) ?></span>
			<h3 class="wpmoly headbox movie title">The Secret Life of Walter Mitty</h3>
			<h4 class="wpmoly headbox movie tagline">Stop Dreaming, Start Living</h4>
			<span class="wpmoly headbox movie genres">Adventure, Comedy, Drama, Fantasy</span>
			<span class="wpmoly headbox movie year">2013</span> / <span class="wpmoly headbox movie runtime">1 h 54 min</span>
<?php echo $menu ?>
		</div>
<?php echo $tabs ?>
	</div>
