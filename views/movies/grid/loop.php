
				<div id="wpmoly-movie-grid" class="wpmoly movies grid grid-col-<?php echo $columns; ?>">

<?php
global $post;
foreach ( $movies as $post ) :
	setup_postdata( $post );
?>
					<div id="wpmoly-movie-<?php the_ID(); ?>" <?php post_class( 'wpmoly movie' ) ?>>
						<a class="wpmoly grid movie link" href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium', array( 'class' => 'wpmoly grid movie poster' ) ); ?>
<?php if ( $title ) : ?>
							<h4 class="wpmoly grid movie title"><?php the_title(); ?></h4>
<?php endif; if ( $genre ) : ?>
							<span class="wpmoly grid movie genres"><?php echo wpmoly_get_movie_meta( get_the_ID(), 'genres' ); ?></span>
<?php endif; if ( $rating ) : ?>
							<span class="wpmoly grid movie rating"><?php echo apply_filters( 'wpmoly_movie_rating_stars', wpmoly_get_movie_rating( get_the_ID() ) ); ?></span>
<?php endif; ?>
						</a>
					</div>

<?php
endforeach;
wp_reset_postdata();
?>

				</div>
