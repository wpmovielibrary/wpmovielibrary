<?php
/**
 * Movies Shortcode view Template
 * 
 * Showing a list of movies.
 * 
 * @since    1.2
 * 
 * @uses    $grid
 * @uses    $movies
 */
?>

	<div class="wpmoly shortcode block movies grid <?php echo $grid->columns; ?>-columns">

<?php
if ( $movies->has_items() ) :
	while ( $movies->has_items() ) :
		$movie = $movies->the_item();
?>
		<div class="wpmoly shortcode inline-block movie">
			<div class="wpmoly shortcode poster">
				<a href="<?php echo get_the_permalink( $movie->id ); ?>"><?php $movie->get_poster()->render( 'medium', 'html' ); ?></a>
			</div>
			<div class="wpmoly shortcode meta title"><a class="wpmoly shortcode link" href="<?php echo get_the_permalink( $movie->id ); ?>"><?php $movie->the( 'title' ); ?></a></div>
			<div class="wpmoly shortcode meta genres"><?php echo apply_filters( 'wpmoly/shortcode/format/genres/value', $movie->genres ); ?></div>
			<div class="wpmoly shortcode meta runtime"><?php echo apply_filters( 'wpmoly/shortcode/format/runtime/value', $movie->runtime ); ?></div>
		</div>
<?php
	endwhile;
endif;
?>
	</div>
