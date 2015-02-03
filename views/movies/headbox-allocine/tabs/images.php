<?php
/**
 * Movie Headbox Images Tab Template view
 * 
 * Showing a movie's headbox images tab, Allociné style.
 * 
 * @since    2.1.4
 * 
 * @uses    $posters
 * @uses    $images
 */
?>

					<div id="movie-headbox-<?php echo $id ?>-posters" class="wpmoly headbox allocine movie section images">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Posters', 'wpmovielibrary' ); ?></h3>
<?php echo $posters; ?>

					</div>
					<hr />
					<div id="movie-headbox-<?php echo $id ?>-images" class="wpmoly headbox allocine movie section images">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Movie Pictures', 'wpmovielibrary' ); ?></h3>
<?php echo $images; ?>

					</div>
