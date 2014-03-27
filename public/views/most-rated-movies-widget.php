<?php
$title = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
$description = $instance['description'];
$number = $instance['number'];
$display_rating = ( 1 == $instance['display_rating'] ? true : false );
$rating_only = ( 1 == $instance['rating_only'] ? true : false );

?>
		<?php echo $title; ?>
		<p class="widget-description"><?php echo $description; ?></p>
<?php
if ( $rating_only ) :
	$ratings = apply_filters( 'wpml_get_available_movie_rating', null );
	$items = array();
	foreach ( $ratings as $slug => $rating_title ) :
		$items[] = array(
				'ID'          => $slug,
				'attr_title'  => sprintf( __( 'Permalink for &laquo; %s Rated Movies &raquo;', 'wpml' ), esc_attr( $rating_title ) ),
				'link'        => home_url( "/movies/{$slug}/" ),
				'title'       => '<div class="movie_rating_display stars_' . str_replace( '.', '_', $slug ) . '"><div class="stars_labels"><span class="stars_label stars_label_' . str_replace( '.', '_', $slug ) . '">' . esc_attr( $rating_title ) . '</span></div></div>'
			);
	endforeach;

	$html = apply_filters( 'wpml_format_widget_lists', $items, false, null, __( 'Select a Movie', 'wpml' ) );

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
			$thumbnail  = ( '' != $thumbnail ? $thumbnail : sprintf( '<img src="%s" alt="%s" width="%d" height="%d" />', plugins_url( '../assets/no_poster.png', __FILE__ ), get_the_title(), 150, 150 ) );
			$rating     = get_post_meta( get_the_ID(), '_wpml_movie_rating', true );
			$rating_str = ( '' == $rating ? "stars_0_0" : 'stars_' . str_replace( '.', '_', $rating ) );
?>
			<a href="<?php the_permalink(); ?>" title="<?php printf( '%s %s', __( 'Read more about', 'wpml' ), get_the_title() ); ?>">
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








/*$title          = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
$description    = $instance['description'];
$number         = $instance['number'];
$display_rating = $instance['display_rating'];

$movies = new WP_Query(
	array(
		'posts_per_page' => $number,
		'post_type'      => 'movie',
		'order'          => 'DESC',
		'orderby'        => 'meta_value_num',
		'meta_key'       => '_wpml_movie_rating',
	)
);
?>

		<?php echo $title; ?>
		<p class="widget-description"><?php echo $description; ?></p>

<?php if ( $movies->have_posts() ) : ?>
		<div class="most-rated-movies">
<?php while ( $movies->have_posts() ) :
		 $movies->the_post();
		 $thumbnail  = get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
		 $thumbnail  = ( '' != $thumbnail ? $thumbnail : sprintf( '<img src="%s" alt="%s" width="%d" height="%d" />', plugins_url( '../assets/no_poster.png', __FILE__ ), get_the_title(), 150, 150 ) );
		 $rating     = get_post_meta( get_the_ID(), '_wpml_movie_rating', true );
		 $rating_str = ( '' == $rating ? "stars_0_0" : 'stars_' . str_replace( '.', '_', $rating ) );
?>
			<a href="<?php the_permalink(); ?>" title="<?php printf( '%s %s', __( 'Read more about', 'wpml' ), get_the_title() ); ?>">
				<figure id="movie-<?php the_ID(); ?>" class="most-rated-movie">
					<?php echo $thumbnail; ?>
<?php if ( 'no' != $display_rating ) : ?>
					<div class="movie_rating_display <?php echo $rating_str . ' ' . $display_rating ?>"><?php if ( 'below' == $display_rating ) echo '<small>' . $rating . '/5</small>' ?></div>
<?php endif; ?>
				</figure>
			</a>
<?php endwhile; ?>
		</div>
<?php endif;*/ ?>