
<?php include_once( 'dashboard-latest-movies-widget-config.php' ); ?>

							<div class="main">
<?php
if ( ! empty( $movies ) ) :
	foreach ( $movies as $movie ) :
?>
								<div id="movie-<?php echo $movie->ID ?>" class="wpml-movie">
									<a href="<?php echo get_permalink( $movie->ID ) ?>" data-movie-edit-link="<?php echo get_edit_post_link( $movie->ID ) ?>" data-movie-meta="<?php echo htmlspecialchars( $movie->meta ) ?>" data-movie-rating="<?php echo $movie->rating ?>" data-movie-poster="<?php echo $movie->poster ?>" data-movie-backdrop="<?php echo $movie->backdrop ?>">
										<?php echo get_the_post_thumbnail( $movie->ID, 'medium' ) ?>
									</a>
								</div>
<?php
	endforeach;
endif;
?>
							</div>