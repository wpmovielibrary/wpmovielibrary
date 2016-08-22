<?php
/**
 * Movies Shortcode view Template
 * 
 * Showing a list of movies.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $movies
 */
?>

	<div class="wpmoly shortcode movies grid list theme-<?php echo $grid->theme; ?>">
		<div class="grid-menu clearfix">
			<button type="button" data-action="grid-menu" class="button left"><span class="wpmolicon icon-order"></span></button>
			<button type="button" data-action="grid-settings" class="button right"><span class="wpmolicon icon-settings"></span></button>
		</div>
		<div class="grid-content list clearfix">

<?php if ( $movies->has_items() ) : ?>
			<ul class="movies-list">
<?php
	while ( $movies->has_items() ) :
		$movie = $movies->the_item();
?>
				<li class="movie"><a href="<?php echo get_the_permalink( $movie->id ); ?>"><?php $movie->the( 'title' ); ?></a></li>
<?php endwhile; ?>
			</ul>
<?php endif; ?>
		</div>
		<div class="grid-menu pagination-menu clearfix">
			<button type="button" data-action="grid-paginate" data-value="prev" class="button left"><span class="wpmolicon icon-arrow-left"></span></button>
			<div class="pagination-menu">Page <span class="current-page"><input type="text" size="1" data-action="grid-paginate" value="1" /></span> of <span class="total-pages">123</span></div>
			<button type="button" data-action="grid-paginate" data-value="next" class="button right"><span class="wpmolicon icon-arrow-right"></span></button>
		</div>
	</div>
