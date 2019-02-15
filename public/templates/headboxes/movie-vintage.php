<?php
/**
 * Movie Headbox view Template
 *
 * Showing a movie's vintage headbox.
 *
 * @since 3.0.0
 *
 * @uses $movie
 * @uses $headbox
 */

?>
	<div data-headbox="<?php echo $headbox->id; ?>" data-theme="vintage" class="wpmoly headbox post-headbox movie-headbox theme-vintage" style="background-image:url(<?php $movie->get_poster(); ?>)">
		<div class="headbox-header">
			<div class="headbox-rating"><span class="wpmolicon icon-star"></span><?php echo $movie->get( 'rating' ); ?></div>
			<div class="headbox-titles">
				<div class="movie-title"><a href="<?php the_permalink( $movie->id ); ?>"><?php $movie->the_title(); ?></a></div>
				<div class="movie-tagline"><?php $movie->the_tagline(); ?></div>
			</div>
			<div class="headbox-release-info">
<?php if ( $movie->get( 'year' ) ) { ?>
				<span class="movie-year"><?php $movie->the_year(); ?></span>
<?php } if ( $movie->get( 'runtime' ) ) { ?>
				<span class="movie-runtime"><?php $movie->the_runtime(); ?></span>
<?php } if ( $movie->get( 'certification' ) ) { ?>
				<span class="movie-certification"><?php $movie->the_certification(); ?></span>
<?php } ?>
			</div>
			<ul class="headbox-menu">
				<li class="headbox-tab active"><a data-tab="overview" href="#"><span class="wpmolicon icon-overview"></span></a></li>
				<li class="headbox-tab"><a data-tab="meta" href="#"><span class="wpmolicon icon-meta"></span></a></li>
				<li class="headbox-tab"><a data-tab="details" href="#"><span class="wpmolicon icon-details"></span></a></li>
				<li class="headbox-tab"><a data-tab="images" href="#"><span class="wpmolicon icon-images"></span></a></li>
				<li class="headbox-tab"><a data-tab="actors" href="#"><span class="wpmolicon icon-actor"></span></a></li>
			</ul>
		</div>
		<div class="headbox-content">
			<div data-panel="overview" class="headbox-panel overview active">
				<span class="wpmolicon icon-overview"></span><?php $movie->the_overview(); ?>
			</div>
			<div data-panel="meta" class="headbox-panel meta">
				<div class="meta field director">
					<span class="meta field title"><?php _e( 'Director', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_director(); ?></span>
				</div>
				<div class="meta field producer">
					<span class="meta field title"><?php _e( 'Producer', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_producer(); ?></span>
				</div>
				<div class="meta field photography">
					<span class="meta field title"><?php _e( 'Director of photography', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_photography(); ?></span>
				</div>
				<div class="meta field composer">
					<span class="meta field title"><?php _e( 'Original music composer', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_composer(); ?></span>
				</div>
				<div class="meta field writer">
					<span class="meta field title"><?php _e( 'Writer', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_writer(); ?></span>
				</div>
				<div class="meta field author">
					<span class="meta field title"><?php _e( 'Author', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_author(); ?></span>
				</div>
				<div class="meta field companies">
					<span class="meta field title"><?php _e( 'Production companies', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_production_companies(); ?></span>
				</div>
				<div class="meta field countries">
					<span class="meta field title"><?php _e( 'Production countries', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_production_countries(); ?></span>
				</div>
				<div class="meta field languages">
					<span class="meta field title"><?php _e( 'Spoken languages', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_spoken_languages(); ?></span>
				</div>
				<div class="meta field adult">
					<span class="meta field title"><?php _e( 'Adult movie', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_adult(); ?></span>
				</div>
			</div>
			<div data-panel="details" class="headbox-panel details">
				<div class="meta detail field format">
					<span class="meta field title"><?php _e( 'Format', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_format(); ?></span>
				</div>
				<div class="meta field media">
					<span class="meta field title"><?php _e( 'Media', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_media(); ?></span>
				</div>
				<div class="meta field status">
					<span class="meta field title"><?php _e( 'Status', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_status(); ?></span>
				</div>
				<div class="meta field rating">
					<span class="meta field title"><?php _e( 'Rating', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_rating(); ?></span>
				</div>
				<div class="meta field language">
					<span class="meta field title"><?php _e( 'Language', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_language(); ?></span>
				</div>
				<div class="meta field subtitles">
					<span class="meta field title"><?php _e( 'Subtitles', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_subtitles(); ?></span>
				</div>
			</div>
			<div data-panel="images" class="headbox-panel images">
				<div class="movie-backdrops clearfix">
					<?php
					$backdrops = $movie->get_backdrops();
					while ( $backdrops->has_items() ) :
						$backdrop = $backdrops->the_item();
					?><div class="movie-backdrop"><a href="<?php $backdrop->render( 'original', 'raw' ); ?>"><?php $backdrop->render( 'thumbnail', 'html' ); ?></a></div><?php
					endwhile;
					?>
				</div>
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
			<div data-panel="actors" class="headbox-panel actors"><?php _e( 'Starring', 'wpmovielibrary' ); ?> <?php $movie->the_actors(); ?></div>
		</div>
	</div>
