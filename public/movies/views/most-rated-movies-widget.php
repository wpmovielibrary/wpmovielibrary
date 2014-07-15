
		<?php echo $title; ?>
		<p class="widget-description"><?php echo $description; ?></p>
<?php
if ( $rating_only ) :
	$ratings = array_reverse( WPML_Settings::get_available_movie_rating() );
	$movies = WPML_Settings::wpml__movie_rewrite();

	$items = array();

	foreach ( $ratings as $slug => $rating_title ) :
		$items[] = array(
				'ID'          => $slug,
				'attr_title'  => sprintf( __( 'Permalink for &laquo; %s Rated Movies &raquo;', WPML_SLUG ), esc_attr__( $rating_title, WPML_SLUG ) ),
				'link'        => home_url( "/{$movies}/{$slug}/" ),
				'title'       => '<div class="movie_rating_display stars_' . str_replace( '.', '_', $slug ) . '"><div class="stars_labels"><span class="stars_label stars_label_' . str_replace( '.', '_', $slug ) . '">' . esc_attr__( $rating_title, WPML_SLUG ) . '</span></div></div>'
			);
	endforeach;

	$html = apply_filters( 'wpml_format_widget_lists', $items, array( 'dropdown' => false, 'styling' => false, 'title' => __( 'Select a Movie', WPML_SLUG ), 'title_filter' => null ) );

	echo $html;

else :
	$movies = new WP_Query(
		array(
			'posts_per_page' => $number,
			'post_type'      => 'movie',
			'order'          => 'DESC',
			'orderby'        => 'meta_value_num',
			'meta_key'       => '_wpml_movie_rating',
		)
	);

	if ( $movies->have_posts() ) : ?>
		<div class="most-rated-movies">
<?php 
		while ( $movies->have_posts() ) :
			$movies->the_post();
			$thumbnail  = get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
			$thumbnail  = ( '' != $thumbnail ? $thumbnail : sprintf( '<img src="%s" alt="%s" width="%d" height="%d" />', WPML_DEFAULT_POSTER_URL, get_the_title(), 150, 150 ) );
			$rating     = get_post_meta( get_the_ID(), '_wpml_movie_rating', true );
			$rating_str = ( '' == $rating ? "stars_0_0" : 'stars_' . str_replace( '.', '_', $rating ) );
?>
			<a href="<?php the_permalink(); ?>" title="<?php printf( '%s %s', __( 'Read more about', WPML_SLUG ), get_the_title() ); ?>">
				<figure id="movie-<?php the_ID(); ?>" class="most-rated-movie">
					<?php echo $thumbnail; ?>
<?php if ( 'no' != $display_rating ) : ?>
					<div class="movie_rating_display <?php echo $rating_str . ' ' . $display_rating ?>"><?php if ( 'below' == $display_rating ) echo '<small>' . $rating . '/5</small>' ?></div>
<?php endif; ?>
				</figure>
			</a>
<?php endwhile; ?>
		</div>
<?php endif;

endif;

?>