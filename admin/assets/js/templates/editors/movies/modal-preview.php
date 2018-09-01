<?php
/**
 * Movie Modal Preview Template.
 *
 * @since 3.0.0
 */
?>

		<div class="movie-preview-poster" style="background-image:url({{{ data.poster }}})">
			<div class="movie-preview-navigation">
				<# if ( data.has_previous ) { #><button type="button" class="button previous" data-action="browse-previous"><span class="wpmolicon icon-left-arrow"><span></button><# } #>
				<# if ( data.has_next ) { #><button type="button" class="button next" data-action="browse-next"><span class="wpmolicon icon-right-arrow"><span></button><# } #>
			</div>
		</div>
		<div class="movie-preview-info">
			<div class="movie-preview-header with-background" style="background-image:url({{{ data.backdrop }}})">
				<h2 class="movie-title">{{{ data.title || '' }}} <span class="year">{{{ data.year || '' }}}</span></h2>
				<p class="movie-genres">{{{ data.genres || '' }}}</p>
				<div class="movie-preview-header-navigation">
					<button type="button" class="button close" data-action="close-modal"><span class="wpmolicon icon-no"><span></button>
					<button type="button" class="button edit" data-action="edit-movie"><span class="wpmolicon icon-edit"><span></button>
				</div>
			</div>
			<div class="movie-preview-content">
				<div class="movie-preview-details">
					<div class="movie-detail movie-status">
						<span class="movie-detail-label"><?php _e( 'Status', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{{ data.status || '−' }}}</span>
					</div>
					<div class="movie-detail movie-media">
						<span class="movie-detail-label"><?php _e( 'Media', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{{ data.media || '−' }}}</span>
					</div>
					<div class="movie-detail movie-rating">
						<span class="movie-detail-label"><?php _e( 'Rating', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{{ data.rating || '−' }}}</span>
					</div>
					<div class="movie-detail movie-format">
						<span class="movie-detail-label"><?php _e( 'Format', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{{ data.format || '−' }}}</span>
					</div>
					<div class="movie-detail movie-language">
						<span class="movie-detail-label"><?php _e( 'Language', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{{ data.language || '−' }}}</span>
					</div>
					<div class="movie-detail movie-subtitles">
						<span class="movie-detail-label"><?php _e( 'Subtitles', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{{ data.subtitles || '−' }}}</span>
					</div>
				</div>
				<div class="movie-preview-meta">
					<div class="movie-meta movie-director">
						<span class="movie-meta-label"><?php _e( 'Directed by', 'wpmovielibrary' ); ?></span>
						<span class="movie-meta-value">{{{ data.director || '−' }}}</span>
					</div>
					<div class="movie-meta movie-cast">
						<span class="movie-meta-label"><?php _e( 'Starring', 'wpmovielibrary' ); ?></span>
						<span class="movie-meta-value">{{{ data.cast || '−' }}}</span>
					</div>
					<div class="movie-meta movie-overview">
						<h4>{{{ data.tagline || '' }}}</h4>
						<p>{{{ data.overview || '' }}}</p>
					</div>
				</div>
			</div>
		</div>
