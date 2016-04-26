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
				<a href="<?php echo get_the_permalink( $movie->id ); ?>"><?php $movie->get_poster()->render( 'medium', 'html' ); ?></a>
			</div>

			<a class="wpmoly shortcode link" href="<?php echo get_the_permalink( $movie->id ); ?>"><h4><?php $movie->meta->the_title(); ?></h4></a>

			<div class="wpmoly shortcode meta"></div>
		</div>
<?php endforeach; ?>

	</div>
