<?php
/**
 * Movie Headbox view Template
 *
 * Showing a movie's allocine headbox.
 *
 * @since 3.0.0
 *
 * @uses $movie
 * @uses $headbox
 */

?>
	<div data-headbox="<?php echo $headbox->id; ?>" data-theme="allocine" class="wpmoly headbox post-headbox movie-headbox theme-allocine">
		<div class="headbox-header">
			<ul class="headbox-menu">
				<li class="headbox-tab active"><a data-tab="overview" href="#"><span class="wpmolicon icon-home"></span></a></li>
				<li class="headbox-tab"><a data-tab="details" href="#"><span class="wpmolicon icon-meta"></span><?php _e( 'Details', 'wpmovielibrary' ); ?></a></li>
				<li class="headbox-tab"><a data-tab="actors" href="#"><span class="wpmolicon icon-actor"></span><?php _e( 'Casting', 'wpmovielibrary' ); ?></a></li>
				<li class="headbox-tab"><a data-tab="images" href="#"><span class="wpmolicon icon-images"></span><?php _e( 'Images', 'wpmovielibrary' ); ?></a></li>
			</ul>
		</div>
		<div class="headbox-content">
			<div data-panel="overview" class="headbox-panel overview active">
				<div class="headbox-poster"><?php $movie->get_poster()->render( 'medium', 'html' ); ?></div>
				<div class="headbox-meta">
					<div class="meta field release">
						<span class="meta field title"><?php _e( 'Release date', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_release_date(); ?> (<?php $movie->the_runtime(); ?>)</span>
					</div>
					<div class="meta field director">
						<span class="meta field title"><?php _e( 'By', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_director(); ?></span>
					</div>
					<div class="meta field casting">
						<span class="meta field title"><?php _e( 'Starring', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php
							$actors = $movie->get_the_actors();
							$actors = explode( ', ', $actors );
							$actors = array_slice( $actors, 0, 4 );
							$actors = implode( ', ', $actors );
							echo $actors;
						?></span>
					</div>
					<div class="meta field genres">
						<span class="meta field title"><?php _e( 'Genres', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_genres(); ?></span>
					</div>
					<div class="meta field countries">
						<span class="meta field title"><?php _e( 'Countries', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_production_countries(); ?></span>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="headbox-overview">
					<h4><?php _e( 'Synopsis and details', 'wpmovielibrary' ); ?></h4>
					<div class="movie-overview"><?php $movie->the_overview(); ?></div>
					<div class="headbox-more"><button data-action="expand"><?php _e( 'Show details', 'wpmovielibrary' ); ?><span class="wpmolicon icon-arrow-down"></span></button></div>
					<div class="movie-meta">
						<div class="meta field companies">
							<span class="meta field title"><?php _e( 'Production', 'wpmovielibrary' ); ?></span>
							<span class="meta field value"><?php $movie->the_production_companies(); ?></span>
						</div>
						<div class="meta field year">
							<span class="meta field title"><?php _e( 'Release year', 'wpmovielibrary' ); ?></span>
							<span class="meta field value"><?php $movie->the_year(); ?></span>
						</div>
						<div class="meta field budget">
							<span class="meta field title"><?php _e( 'Budget', 'wpmovielibrary' ); ?></span>
							<span class="meta field value"><?php $movie->the_budget(); ?></span>
						</div>
						<div class="meta field revenue">
							<span class="meta field title"><?php _e( 'Revenue', 'wpmovielibrary' ); ?></span>
							<span class="meta field value"><?php $movie->the_revenue(); ?></span>
						</div>
						<div class="meta field languages">
							<span class="meta field title"><?php _e( 'Languages', 'wpmovielibrary' ); ?></span>
							<span class="meta field value"><?php $movie->the_spoken_languages(); ?></span>
						</div>
						<div class="meta field adult">
							<span class="meta field title"><?php _e( 'Adult movie', 'wpmovielibrary' ); ?></span>
							<span class="meta field value"><?php $movie->the_adult(); ?></span>
						</div>
						<div class="meta field homepage">
							<span class="meta field title"><?php _e( 'Official website', 'wpmovielibrary' ); ?></span>
							<span class="meta field value"><?php $movie->the_homepage(); ?></span>
						</div>
					</div>
				</div>
			</div>
			<div data-panel="details" class="headbox-panel details">
				<div class="headbox-overview">
					<h4><?php _e( 'Overview', 'wpmovielibrary' ); ?></h4>
					<div class="movie-tagline"><?php $movie->the_tagline(); ?></div>
					<div class="movie-overview"><?php $movie->the_overview(); ?></div>
				</div>
				<div class="headbox-details">
					<h4><?php _e( 'Viewing details', 'wpmovielibrary' ); ?></h4>
					<div class="meta field detail status">
						<span class="meta field title"><?php _e( 'Status', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_status(); ?></span>
					</div>
					<div class="meta field detail media">
						<span class="meta field title"><?php _e( 'Media', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_media(); ?></span>
					</div>
					<div class="meta field detail format">
						<span class="meta field title"><?php _e( 'Format', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_format(); ?></span>
					</div>
					<div class="meta field detail language">
						<span class="meta field title"><?php _e( 'Language', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_language(); ?></span>
					</div>
					<div class="meta field detail rating">
						<span class="meta field title"><?php _e( 'Rating', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_rating(); ?></span>
					</div>
					<div class="meta field detail subtitles">
						<span class="meta field title"><?php _e( 'Subtitles', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_subtitles(); ?></span>
					</div>
				</div>
				<div class="headbox-release-info">
					<h4><?php _e( 'Release info', 'wpmovielibrary' ); ?></h4>
					<div class="meta field original-title">
						<span class="meta field title"><?php _e( 'Original Title', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_original_title(); ?></span>
					</div>
					<div class="meta field release">
						<span class="meta field title"><?php _e( 'Release Date', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_release_date(); ?></span>
					</div>
					<div class="meta field local-release">
						<span class="meta field title"><?php _e( 'Local Release Date', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_local_release_date(); ?></span>
					</div>
					<div class="meta field runtime">
						<span class="meta field title"><?php _e( 'Runtime', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_runtime(); ?></span>
					</div>
					<div class="meta field companies">
						<span class="meta field title"><?php _e( 'Production', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_production_companies(); ?></span>
					</div>
					<div class="meta field countries">
						<span class="meta field title"><?php _e( 'Country', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_production_countries(); ?></span>
					</div>
					<div class="meta field languages">
						<span class="meta field title"><?php _e( 'Languages', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_spoken_languages(); ?></span>
					</div>
					<div class="meta field genres">
						<span class="meta field title"><?php _e( 'Genres', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_genres(); ?></span>
					</div>
					<div class="meta field director">
						<span class="meta field title"><?php _e( 'Director', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_director(); ?></span>
					</div>
					<div class="meta field producer">
						<span class="meta field title"><?php _e( 'Producer', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_producer(); ?></span>
					</div>
					<div class="meta field photography">
						<span class="meta field title"><?php _e( 'Director of Photography', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_photography(); ?></span>
					</div>
					<div class="meta field composer">
						<span class="meta field title"><?php _e( 'Original Music Composer', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_composer(); ?></span>
					</div>
					<div class="meta field author">
						<span class="meta field title"><?php _e( 'Author', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_author(); ?></span>
					</div>
					<div class="meta field writer">
						<span class="meta field title"><?php _e( 'Writer', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_writer(); ?></span>
					</div>
					<div class="meta field certification">
						<span class="meta field title"><?php _e( 'Certification', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_certification(); ?></span>
					</div>
					<div class="meta field budget">
						<span class="meta field title"><?php _e( 'Budget', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_budget(); ?></span>
					</div>
					<div class="meta field revenue">
						<span class="meta field title"><?php _e( 'Revenue', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_revenue(); ?></span>
					</div>
					<div class="meta field adult">
						<span class="meta field title"><?php _e( 'Adult', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_adult(); ?></span>
					</div>
					<div class="meta field homepage">
						<span class="meta field title"><?php _e( 'Homepage ', 'wpmovielibrary' ); ?></span>
						<span class="meta field value"><?php $movie->the_homepage(); ?></span>
					</div>
				</div>
			</div>
			<div data-panel="actors" class="headbox-panel actors">
				<div class="headbox-director">
					<h4><?php _e( 'Director', 'wpmovielibrary' ); ?></h4>
					<div class="movie-directors">
						<?php
						$directors = $movie->get_director();
						$directors = explode( ',', $directors );
						foreach ( $directors as $director ) {
							$term = get_term_by( 'slug', trim( $director ), 'collection' );
							if ( $term ) {
								$director = get_collection( $term );
						?><div class="movie-director">
							<a href="<?php echo get_term_link( $term->term_id ); ?>">
								<div class="movie-director-thumbnail"><img src="<?php echo $director->get_thumbnail( '', 'thumbnail' ) ?>" alt="" /></div>
								<div class="movie-director-permalink"><?php echo $term->name; ?></div>
							</a>
						</div><?php
							}
						}
						?>
					</div>
				</div>
				<div class="headbox-cast">
					<h4><?php _e( 'Actors and actresses', 'wpmovielibrary' ); ?></h4>
					<div class="movie-cast">
						<?php
						$actors = $movie->get_actors();
						$actors = explode( ',', $actors );
						foreach ( $actors as $i => $actor ) {
							$term = get_term_by( 'slug', trim( $actor ), 'actor' );
							if ( $term ) {
								$actor = get_actor( $term );
						?><div class="movie-actor<?php echo ( 6 <= $i ) ? ' small' : ''; ?>">
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
			</div>
			<div data-panel="images" class="headbox-panel images">
				<h4><?php _e( 'Backdrops', 'wpmovielibrary' ); ?></h4>
				<div class="movie-backdrops clearfix">
					<?php
					$backdrops = $movie->get_backdrops();
					while ( $backdrops->has_items() ) :
						$backdrop = $backdrops->the_item();
					?><div class="movie-backdrop"><a href="<?php $backdrop->render( 'original', 'raw' ); ?>"><?php $backdrop->render( 'thumbnail', 'html' ); ?></a></div><?php
					endwhile;
					?>
				</div>
				<h4><?php _e( 'Posters', 'wpmovielibrary' ); ?></h4>
				<div class="movie-posters clearfix">
					<?php
					$posters = $movie->get_posters();
					while ( $posters->has_items() ) :
						$poster = $posters->the_item();
					?><div class="movie-poster"><a href="<?php $poster->render( 'original', 'raw' ); ?>"><?php $poster->render( 'thumbnail', 'html' ); ?></a></div><?php
					endwhile;
					?>
				</div>
			</div>
		</div>
	</div>
