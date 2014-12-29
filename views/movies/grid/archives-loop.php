
				<div id="wpmoly-movie-grid" class="wpmoly movies archives<?php echo $theme; ?>">

<?php
global $post;

$vintage_mode = wpmoly_o( 'vintage-content' );
if ( ! empty( $movies ) ) :
	foreach ( $movies as $post ) :
		setup_postdata( $post );

		if ( ! $vintage_mode ) {
			echo WPMOLY_Headbox::get_content();
		} else {
?>
					<div id="post-<?php the_ID(); ?>" class="wpmoly movies archives movie">
						<h2><?php the_title(); ?></h2>
<?php
						echo WPMOLY_Movies::movie_vintage_content();
?>
					</div>
<?php
		}

	endforeach;
	wp_reset_postdata();
else :
?>
					<h5><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'wpmovielibrary' ); ?></h5>
					<p><?php _e( 'We could&rsquo;t find any movie matching your criteria.', 'wpmovielibrary' ); ?></p>
<?php endif; ?>

				</div>
