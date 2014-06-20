
<?php if ( ! $offset ) : ?>
							<div class="main">
<?php
endif;

if ( ! empty( $movies ) ) :
	foreach ( $movies as $movie ) :
?>
								<div id="movie-<?php echo $movie->ID ?>" class="<?php echo $class ?>">
									<a href="<?php echo get_edit_post_link( $movie->ID ) ?>" data-movie-edit-link="<?php echo get_edit_post_link( $movie->ID ) ?>" data-movie-meta="<?php echo htmlspecialchars( $movie->meta ) ?>" data-movie-rating="<?php echo $movie->rating ?>" data-movie-poster="<?php echo $movie->poster ?>" data-movie-backdrop="<?php echo $movie->backdrop ?>">
										<?php echo get_the_post_thumbnail( $movie->ID, 'medium' ) ?>
									</a>
									<span class="movie-year<?php if ( '0' == $settings['show_year'] ) echo ' hide-if-js hide-if-no-js'; ?>"><?php echo $movie->year ?></span>
									<span class="movie-rating<?php if ( '0' == $settings['show_rating'] ) echo ' hide-if-js hide-if-no-js'; ?>"><div id="movie-rating-display" class="movie_rating_title stars stars-<?php echo str_replace( '.', '-', $movie->rating ) ?>"></div></span>
									<div  class="movie-quickedit<?php if ( '0' == $settings['show_quickedit'] ) echo ' hide-if-js hide-if-no-js'; ?>">
										<a href="<?php echo get_edit_post_link( $movie->ID ) ?>" title="<?php _e( 'Edit' ) ?>"><span class="dashicons dashicons-welcome-write-blog"></span></a>
										<a href="<?php echo get_delete_post_link( $movie->ID ) ?>" title="<?php _e( 'Delete' ) ?>"><span class="dashicons dashicons-trash"></span></a>
										<a href="<?php echo get_permalink( $movie->ID ) ?>" title="<?php _e( 'View' ) ?>"><span class="dashicons dashicons-format-video"></span></a>
									</div>
								</div>
<?php
	endforeach;
else :
?>
							<em><?php _e( 'No movies found', WPML_SLUG ); ?></em>
<?php
endif;

if ( ! $offset ) : ?>
							</div>
<?php
endif;

if ( true !== DOING_AJAX ) : ?>
							<div style="text-align:center">
								<a href="#" id="latest_movies_load_more" class="button button-default hide-if-no-js<?php if ( '0' == $settings['show_more'] ) echo ' hide-if-js'; ?>">
									<span class="dashicons dashicons-plus"></span> <span><?php _e( 'Load more', WPML_SLUG ) ?></span>
								</a>
							</div>
<?php endif; ?>