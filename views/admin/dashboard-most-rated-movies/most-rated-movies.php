
<?php if ( ! $offset ) : ?>
							<div class="main">
<?php
endif;

if ( ! empty( $movies ) ) :
	foreach ( $movies as $movie ) :
?>
								<div id="movie-<?php echo $movie->ID ?>" class="<?php echo $class ?>">
									<a href="<?php echo get_edit_post_link( $movie->ID ) ?>" data-movie-permalink="<?php echo get_permalink( $movie->ID ) ?>" data-movie-meta="<?php echo htmlspecialchars( $movie->meta ) ?>" data-movie-rating="<?php echo $movie->rating ?>" data-movie-poster="<?php echo $movie->poster ?>" data-movie-backdrop="<?php echo $movie->backdrop ?>">
										<?php echo get_the_post_thumbnail( $movie->ID, 'medium' ) ?>
									</a>
									<span class="movie-year<?php if ( '0' == $settings['show_year'] ) echo ' hide-if-js hide-if-no-js'; ?>"><?php echo $movie->year ?></span>
									<span class="movie-rating<?php if ( '0' == $settings['show_rating'] ) echo ' hide-if-js hide-if-no-js'; ?>"><?php echo $movie->_rating ?></span>
									<div  class="movie-quickedit<?php if ( '0' == $settings['show_quickedit'] ) echo ' hide-if-js hide-if-no-js'; ?>">
										<a href="<?php echo get_edit_post_link( $movie->ID ) ?>" title="<?php _e( 'Edit' ) ?>"><span class="wpmolicon icon-edit-page"></span></a>
										<a href="<?php echo get_delete_post_link( $movie->ID ) ?>" title="<?php _e( 'Delete' ) ?>"><span class="wpmolicon icon-trash"></span></a>
										<a href="<?php echo get_permalink( $movie->ID ) ?>" title="<?php _e( 'View' ) ?>"><span class="wpmolicon icon-movie"></span></a>
									</div>
								</div>
<?php
	endforeach;
elseif ( ! defined( 'DOING_AJAX' ) || true !== DOING_AJAX ) :
?>
								<em><?php _e( 'No movies found', 'wpmovielibrary' ); ?></em>
								<p style="text-align:center"><a href="<?php echo admin_url( 'post-new.php?post_status=publish&post_type=movie' ) ?>"><span class="wpmolicon icon-plus"></span> <?php _e( 'Add a movie!', 'wpmovielibrary' ) ?></a></p>
<?php
endif;

if ( ! $offset ) : ?>
							</div>
<?php
endif;

if ( ! defined( 'DOING_AJAX' ) || true !== DOING_AJAX ) : ?>
							<div style="text-align:center">
								<a href="#" id="most_rated_movies_load_more" class="button button-default hide-if-no-js<?php if ( '0' == $settings['show_more'] ) echo ' hide-if-js'; ?>">
									<span class="wpmolicon icon-plus"></span> <span><?php _e( 'Load more', 'wpmovielibrary' ) ?></span>
								</a>
							</div>
<?php
endif;
