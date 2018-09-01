<?php
/**
 * Movie Headbox view Template
 *
 * Showing a movie's imdb v2 headbox.
 *
 * @since 3.0.0
 *
 * @uses $movie
 * @uses $headbox
 */

?>
	<div data-headbox="<?php echo $headbox->id; ?>" data-theme="imdb-2" class="wpmoly headbox post-headbox movie-headbox theme-imdb-2">
		<div class="headbox-header clearfix">
			<div class="headbox-release-info">
				<div class="movie-rating"><span class="wpmolicon icon-star"></span><?php echo $movie->get_rating(); ?></div>
				<div class="headbox-titles">
					<span class="movie-title"><?php $movie->the_title(); ?></span>
					<span class="movie-year">(<?php $movie->the_year(); ?>)</span>
				</div>
				<div class="movie-meta">
					<span class="movie-certification"><?php $movie->the_certification(); ?></span>
					<span class="movie-runtime"><?php $movie->the_runtime(); ?></span>
					<span class="movie-genres"><?php $movie->the_genres(); ?></span>
					<span class="movie-release-date"><?php $movie->the_release_date(); ?></span>
				</div>
			</div>
			<div class="headbox-poster"><?php
				$poster = $movie->get_poster();
				$poster->render( 'medium', 'html' );
			?></div>
			<div class="headbox-backdrop"><?php
				$backdrop = $movie->get_backdrop( 'first' );
				$backdrop->render( 'large', 'html' );
			?></div>
		</div>
		<div class="headbox-content">
			<div class="headbox-intro">
				<div class="movie-tagline"><?php $movie->the_tagline(); ?></div>
				<div class="movie-director"><?php _e( 'Director', 'wpmovielibrary' ); ?>: <?php $movie->the_director(); ?></div>
				<div class="movie-writer"><?php _e( 'Writer', 'wpmovielibrary' ); ?>: <?php $movie->the_writer(); ?></div>
				<div class="movie-actors"><?php _e( 'Stars', 'wpmovielibrary' ); ?>: <?php
					$actors = $movie->get_the_actors();
					$actors = explode( ', ', $actors );
					$actors = array_slice( $actors, 0, 4 );
					$actors = implode( ', ', $actors );
					echo $actors . '&nbsp;&hellip;';
				?></div>
			</div>
			<div class="headbox-images">
				<div class="movie-backdrops clearfix">
					<?php
					$backdrops = $movie->get_backdrops();
					while ( $backdrops->has_items() ) :
						$backdrop = $backdrops->the_item();
					?><div class="movie-backdrop"><?php $backdrop->render( 'thumbnail', 'html' ); ?></div><?php
						endwhile;
					?>
				</div>
			</div>
			<div class="headbox-cast">
				<h4><?php _e( 'Cast', 'wpmovielibrary' ); ?></h4>
				<div class="movie-cast">
					<?php
					$actors = $movie->get_actors();
					$actors = explode( ',', $actors );
					foreach ( $actors as $i => $actor ) {
						$term = get_term_by( 'slug', trim( $actor ), 'actor' );
						if ( $term ) {
							$actor = get_actor( $term );
					?><div class="movie-actor">
							<a href="<?php echo get_term_link( $term->term_id ); ?>">
								<div class="movie-actor-thumbnail"><img src="<?php echo $actor->get_thumbnail( '', 'thumbnail' ) ?>" alt="" /></div>
								<div class="movie-actor-permalink"><?php echo $term->name; ?></div>
							</a>
					</div><?php
						}
					}
					?>
				</div>
			</div>
			<div class="headbox-meta">
				<h4><?php _e( 'Storyline', 'wpmovielibrary' ); ?></h4>
				<div class="movie-overview"><?php $movie->the_overview(); ?></div>
			</div>
			<div class="headbox-details">
				<h4><?php _e( 'Details', 'wpmovielibrary' ); ?></h4>
				<div class="meta field homepage">
					<span class="meta field title"><?php _e( 'Homepage', 'wpmovielibrary' ); ?>:</span>
					<span class="meta field value"><?php $movie->the_homepage(); ?></span>
				</div>
				<div class="meta field country">
					<span class="meta field title"><?php _e( 'Countries', 'wpmovielibrary' ); ?>:</span>
					<span class="meta field value"><?php $movie->the_production_countries(); ?></span>
				</div>
				<div class="meta field languages">
					<span class="meta field title"><?php _e( 'Languages', 'wpmovielibrary' ); ?>:</span>
					<span class="meta field value"><?php $movie->the_spoken_languages(); ?></span>
				</div>
				<div class="meta field release-date">
					<span class="meta field title"><?php _e( 'Release Date', 'wpmovielibrary' ); ?>:</span>
					<span class="meta field value"><?php $movie->the_release_date(); ?></span>
				</div>
			</div>
			<div class="headbox-box-office">
				<h4><?php _e( 'Box Office', 'wpmovielibrary' ); ?></h4>
				<div class="meta field budget">
					<span class="meta field title"><?php _e( 'Budget', 'wpmovielibrary' ); ?>:</span>
					<span class="meta field value"><?php $movie->the_budget(); ?></span>
				</div>
				<div class="meta field revenue">
					<span class="meta field title"><?php _e( 'Revenue', 'wpmovielibrary' ); ?>:</span>
					<span class="meta field value"><?php $movie->the_revenue(); ?></span>
				</div>
			</div>
			<div class="headbox-credits">
				<h4><?php _e( 'Credits', 'wpmovielibrary' ); ?></h4>
				<div class="meta field companies">
					<span class="meta field title"><?php _e( 'Companies', 'wpmovielibrary' ); ?>:</span>
					<span class="meta field value"><?php $movie->the_production_companies(); ?>:</span>
				</div>
			</div>
			<div class="headbox-technical">
				<h4><?php _e( 'Technical details', 'wpmovielibrary' ); ?></h4>
				<div class="meta field runtime">
					<span class="meta field title"><?php _e( 'Runtime', 'wpmovielibrary' ); ?>:</span>
					<span class="meta field value"><?php $movie->the_runtime(); ?></span>
				</div>
			</div>
		</div>
	</div>
