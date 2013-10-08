<?php
$title       = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
$description = $instance['description'];
$number      = $instance['number'];

$movies = new WP_Query(
	array(
		'posts_per_page' => $number,
		'post_type'      => 'movie',
		'order'          => 'DESC',
		'orderby'        => 'date'
	)
);
?>

		<h3 class="widget-title"><?php echo $title; ?></h3>
		<p class="widget-description"><?php echo $description; ?></p>

<?php if ( $movies->have_posts() ) : ?>
		<div class="recent-movies">
<?php while ( $movies->have_posts() ) :
		 $movies->the_post();
		 $thumbnail = get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
		 $thumbnail = ( '' != $thumbnail ? $thumbnail : sprintf( '<img src="%s" alt="%s" width="%d" height="%d" />', plugins_url( '../assets/no_poster.png', __FILE__ ), get_the_title(), 150, 150 ) );
?>
			<a href="<?php the_permalink(); ?>" title="<?php printf( '%s %s', __( 'Read more about', 'wpml' ), get_the_title() ); ?>">
				<figure id="movie-<?php the_ID(); ?>" class="recent-movie">
					<?php echo $thumbnail; ?>
				</figure>
			</a>
<?php endwhile; ?>
		</div>
<?php endif; ?>