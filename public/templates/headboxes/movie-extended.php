<?php
/**
 * Movie Headbox view Template
 *
 * Showing a movie's extended headbox.
 *
 * @since 3.0.0
 *
 * @uses $movie
 */

?>
	<div data-headbox="<?php echo $headbox->id; ?>" data-theme="extended" class="wpmoly headbox post-headbox movie-headbox theme-extended">
		<div class="headbox-header">
			<div class="headbox-backdrop-container">
				<div class="headbox-backdrop" style="background-image:url(<?php echo $movie->get_backdrop( 'random' ); ?>);"></div>
				<div class="headbox-poster-shadow"></div>
				<div class="headbox-angle"></div>
			</div>
			<div class="headbox-poster" style="background-image:url(<?php echo $movie->get_poster(); ?>);"></div>
			<div class="headbox-rating"><span class="wpmolicon icon-star"></span><?php echo $movie->get_rating(); ?></div>
			<div class="headbox-titles">
				<div class="movie-title"><a href="<?php the_permalink( $movie->id ); ?>"><?php $movie->the_title(); ?></a></div>
				<div class="movie-original-title"><?php $movie->the_original_title(); ?></div>
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-crew">
				<div class="movie-director"><?php _e( 'Directed by', 'wpmovielibrary' ); ?> <?php $movie->the_director(); ?></div>
				<div class="movie-producer"><?php _e( 'Produced by', 'wpmovielibrary' ); ?> <?php $movie->the_producer(); ?></div>
				<div class="movie-photography"><?php _e( 'Photography by', 'wpmovielibrary' ); ?> <?php $movie->the_photography(); ?></div>
				<div class="movie-composer"><?php _e( 'Music by', 'wpmovielibrary' ); ?> <?php $movie->the_composer(); ?></div>
				<div class="movie-writer"><?php _e( 'Written by', 'wpmovielibrary' ); ?> <?php $movie->the_writer(); ?></div>
			</div>
			<div class="headbox-metadata">
				<div class="movie headbox-release-info">
<?php if ( $movie->get( 'year' ) ) { ?>
					<span class="movie-year"><?php $movie->the_year(); ?></span>
<?php } if ( $movie->get( 'runtime' ) ) { ?>
					<span class="movie-runtime"><?php $movie->the_runtime(); ?></span>
<?php } if ( $movie->get( 'certification' ) ) { ?>
					<span class="movie-certification"><?php $movie->the_certification(); ?></span>
<?php } ?>
				</div>
<?php if ( $movie->get( 'genres' ) ) { ?>
				<div class="movie-genres"><?php $movie->the_genres(); ?></div>
<?php } ?>
				<div class="movie-tagline"><?php $movie->the_tagline(); ?></div>
				<div class="movie-overview"><?php $movie->the_overview(); ?></div>
				<div class="movie-actors"><?php _e( 'Staring', 'wpmovielibrary' ); ?> <?php $movie->the_actors(); ?></div>
				<div class="movie-production"><?php _e( 'Produced in', 'wpmovielibrary' ); ?> <?php $movie->the_production_countries(); ?> <?php _e( 'by', 'wpmovielibrary' ); ?> <?php $movie->the_production_companies(); ?></div>
			</div>
		</div>
	</div>
