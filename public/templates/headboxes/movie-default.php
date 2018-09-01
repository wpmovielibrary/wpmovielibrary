<?php
/**
 * Movie Headbox view Template
 *
 * Showing a movie's default headbox.
 *
 * @since 3.0.0
 *
 * @uses $movie
 * @uses $headbox
 */

?>
	<div data-headbox="<?php echo $headbox->id; ?>" data-theme="default" class="wpmoly headbox post-headbox movie-headbox theme-default">
		<div class="headbox-header">
			<div class="headbox-backdrop-container">
				<div class="headbox-backdrop" style="background-image:url(<?php echo $movie->get_backdrop( 'random' )->render( 'medium', 'raw' ); ?>);"></div>
				<div class="headbox-poster-shadow"></div>
				<div class="headbox-angle"></div>
			</div>
			<div class="headbox-poster" style="background-image:url(<?php echo $movie->get_poster()->render( 'medium', 'raw' ); ?>);"></div>
			<div class="headbox-titles">
				<div class="movie-title"><a href="<?php the_permalink( $movie->id ); ?>"><?php $movie->the_title(); ?></a></div>
				<div class="movie-original-title"><?php $movie->the_original_title(); ?></div>
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-crew">
				<div class="movie-director"><?php _e( 'Directed by', 'wpmovielibrary' ); ?> <?php $movie->the_director(); ?></div>
				<div class="movie-actors"><?php _e( 'Staring', 'wpmovielibrary' ); ?> <?php
					$actors = $movie->get_the_actors();
					$actors = explode( ', ', $actors );
					$actors = array_slice( $actors, 0, 6 );
					$actors = implode( ', ', $actors );
					echo $actors . '&nbsp;&hellip;';
				?></div>
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
				<div class="movie-overview"><?php $movie->the_overview(); ?></div>
			</div>
		</div>
		<div class="headbox-more"><button data-action="expand"><span class="wpmolicon icon-arrow-down"></span></button></div>
		<div class="headbox-less"><button data-action="collapse"><span class="wpmolicon icon-arrow-up"></span></button></div>
	</div>
