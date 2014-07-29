<?php
/**
 * Movies-by-Rating Default Template
 * 
 * Display a list of movies links with thumbnails
 * 
 * @since    1.2.0
 * 
 * @uses    $items array of movies
 * @uses    $style container classes
 * @uses    $description Widget's description
 */
?>

	<div class="<?php echo $style ?>">

		<div class="wpml-widget-description"><?php echo $description ?></div>

<?php foreach ( $items as $item ) : ?>
		<a href="<?php echo $item['link'] ?>" title="<?php echo __( 'Read more about', 'wpmovielibrary' ) . $item['title'] ?>">
			<figure id="movie-<?php the_ID(); ?>" class="most-rated-movie">
				<?php echo $item['thumbnail']; ?>
<?php if ( 'no' != $rating ) : ?>
				<div class="movie_rating_display <?php echo $item['rating_str'] . ' ' . $rating ?>"><?php if ( 'below' == $rating ) echo '<small>' . $item['rating'] . '/5</small>' ?></div>
<?php endif; ?>
			</figure>
		</a>

<?php endforeach; ?>
	</div>
