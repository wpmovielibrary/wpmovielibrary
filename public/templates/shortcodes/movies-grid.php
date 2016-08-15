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

	<div class="wpmoly shortcode grid block movies theme-<?php echo $grid->theme; ?> <?php echo $grid->columns; ?>-columns" data-columns="<?php echo $grid->columns; ?>" data-rows="<?php echo $grid->rows; ?>" data-column-width="<?php echo $grid->column_width; ?>" data-row-height="<?php echo $grid->row_height; ?>">
		<div class="wpmoly grid menu clearfix">
			<button type="button" data-action="grid-menu" class="button left"><span class="wpmolicon icon-order"></span></button>
			<button type="button" data-action="grid-settings" class="button right"><span class="wpmolicon icon-settings"></span></button>
		</div>
		<div class="wpmoly shortcode grid content clearfix">

<?php
if ( $movies->has_items() ) :
	while ( $movies->has_items() ) :
		$movie = $movies->the_item();
?>
			<div class="wpmoly shortcode grid movie" data-width="<?php echo $grid->column_width; ?>" data-height="<?php echo $grid->row_height; ?>">
				<div class="wpmoly shortcode grid movie poster" style="background-image:url(<?php $movie->get_poster()->render( 'medium' ); ?>)">
					<a href="<?php echo get_the_permalink( $movie->id ); ?>"></a>
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
		<div class="wpmoly grid pagination-menu clearfix">
			<button type="button" data-action="grid-paginate" data-value="prev" class="button left"><span class="wpmolicon icon-arrow-left"></span></button>
			<div class="pagination-menu">Page <span class="current-page"><input type="text" size="1" data-action="grid-paginate" value="1" /></span> of <span class="total-pages">123</span></div>
			<button type="button" data-action="grid-paginate" data-value="next" class="button right"><span class="wpmolicon icon-arrow-right"></span></button>
		</div>
	</div>
