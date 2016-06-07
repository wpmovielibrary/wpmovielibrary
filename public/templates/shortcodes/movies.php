<?php
/**
 * Movies Shortcode view Template
 * 
 * Showing a list of movies.
 * 
 * @since    1.2
 * 
 * @uses    $movies
 */
?>

	<div class="wpmoly shortcode block movies">

<?php foreach ( $movies as $movie ) : ?>

		<div class="wpmoly shortcode inline-block movie">

			<div class="wpmoly shortcode poster">
				<a href="<?php echo get_the_permalink( $movie->id ); ?>"><?php $movie->get_poster()->render( $poster, 'html' ); ?></a>
			</div>

			<a class="wpmoly shortcode link" href="<?php echo get_the_permalink( $movie->id ); ?>"><h4><?php $movie->meta->the_title(); ?></h4></a>

			<div class="wpmoly shortcode meta">
<?php
	if ( ! empty( $meta ) ) :
		foreach ( $meta as $slug ) :
?>
				<dt class="wpmoly shortcode meta <?php echo $slug ?> title"><?php echo $slug ?></dt>
				<dd class="wpmoly shortcode meta <?php echo $slug ?> value"><?php $movie->meta->the( $slug ); ?></dd>
<?php
		endforeach;
	endif;
?>
			</div>
		</div>
<?php endforeach; ?>

	</div>
