
				<div id="wpmoly-movie-grid" class="wpmoly movies archives">

<?php
global $post;
if ( ! empty( $movies ) ) :
	foreach ( $movies as $post ) :
		setup_postdata( $post );

		echo WPMOLY_Headbox::get_content();
?>
					

<?php
	endforeach;
	wp_reset_postdata();
else :
?>
					<h5><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'wpmovielibrary' ); ?></h5>
					<p><?php _e( 'We could&rsquo;t find any movie matching your criteria.', 'wpmovielibrary' ); ?></p>
<?php endif; ?>

				</div>
