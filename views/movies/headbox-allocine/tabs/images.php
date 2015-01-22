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
<?php foreach ( $posters as $poster ) : ?>
						<img src="<?php echo esc_url( $poster['sizes']['thumbnail']['url'] ); ?>" width="<?php echo $poster['sizes']['thumbnail']['width']; ?>" height="<?php echo $poster['sizes']['thumbnail']['height']; ?>" title="<?php echo esc_attr( $poster['title'] ); ?>" alt="<?php echo esc_attr( $poster['alt'] ); ?>" />

<?php endforeach; ?>
					</div>
					<hr />
					<div id="movie-headbox-<?php echo $id ?>-images" class="wpmoly headbox allocine movie section images">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Movie Pictures', 'wpmovielibrary' ); ?></h3>
<?php foreach ( $images as $image ) : ?>
						<img src="<?php echo esc_url( $image['sizes']['thumbnail']['url'] ); ?>" width="<?php echo $image['sizes']['thumbnail']['width']; ?>" height="<?php echo $image['sizes']['thumbnail']['height']; ?>" title="<?php echo esc_attr( $image['title'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />

<?php endforeach; ?>
					</div>
