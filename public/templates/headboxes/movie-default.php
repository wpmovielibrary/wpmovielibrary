<?php
/**
 * Movie Headbox view Template
 * 
 * Showing a movie's default headbox.
 * 
 * @since    3.0
 * 
 * @uses    $movie
 */

?>
	<div id="movie-headbox-<?php echo $movie->id; ?>" class="wpmoly movie-headbox theme-default">
		<div class="headbox-header">
			<div class="headbox-backdrop-container">
				<div class="headbox-backdrop" style="background-image:url(<?php echo $movie->get_backdrop( 'random' )->render( 'medium', 'raw' ); ?>);"></div>
				<div class="headbox-angle"></div>
			</div>
			<div class="headbox-poster" style="background-image:url(<?php echo $movie->get_poster()->render( 'medium', 'raw' ); ?>);"></div>
			<div class="headbox-titles">
				<div class="movie-title"><a href="<?php the_permalink( $movie->id ); ?>"><?php $movie->the_title(); ?></a></div>
				<div class="movie-original-title"><?php $movie->the_original_title(); ?></div>
				<div class="movie-tagline"><?php $movie->the_tagline(); ?></div>
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-cast">
				<div class="movie-director"><?php _e( 'Directed by', 'wpmovielibrary' ); ?> <?php $movie->the_director(); ?></div>
				<div class="movie-actors"><?php _e( 'Staring', 'wpmovielibrary' ); ?> <?php $movie->the_actors(); ?></div>
			</div>
			<div class="headbox-metadata">
				<div class="movie headbox-release-info">
					<span class="movie-year"><?php $movie->the_year(); ?></span>&nbsp;|&nbsp;<span class="movie-runtime"><?php printf( '%s %s', $movie->get_runtime(), _x( 'min', 'movie runtime in minutes', 'wpmovielibrary' ) ); ?></span>&nbsp;|&nbsp;<span class="movie-genres"><?php $movie->the_genres(); ?></span>&nbsp;|&nbsp;<span class="movie-certification"><?php $movie->the_certification(); ?></span>
				</div>
				<div class="movie-overview"><?php $movie->the_overview(); ?></div>
			</div>
		</div>
	</div>
