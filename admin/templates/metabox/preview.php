
		<div id="wpmoly-movie-preview" class="wpmoly-movie-preview<?php echo $empty ? ' hidden' : ''; ?>">

			<div class="wpmoly-movie-preview-bg-container">
				<div class="wpmoly-movie-preview-background" style="background-image:url(<?php echo $background; ?>)"></div>
			</div>

			<div class="wpmoly-movie-preview-content clearfix">
				<div class="wpmoly-movie-preview-poster">
					<img src="<?php echo $poster; ?>" alt="" />
					<button type="button" data-action="open-editor" class="button button-primary hide-if-no-js"><?php _e( 'Open Editor', 'wpmovielibrary' ); ?></button>
					<button type="button" data-action="close-editor" class="button button-secondary hide-if-no-js hidden"><?php _e( 'Close Editor', 'wpmovielibrary' ); ?></button>
				</div>

				<div class="wpmoly-movie-preview-meta">
					<div class="wpmoly-movie-preview-hgroup">
						<h2 class="wpmoly-movie-preview-title"><?php $movie->meta->the( 'title' ); ?><span class="wpmoly-movie-preview-original-title">(<?php $movie->meta->the( 'original_title' ); ?>)</span></h2>
						<h5 class="wpmoly-movie-preview-tagline"><?php $movie->meta->the( 'tagline' ); ?></h5>
					</div>
					<div class="wpmoly-movie-preview-intro">
						<span><?php echo substr( $movie->meta->get( 'release_date' ), 0, 4 ); ?></span>&nbsp;|&nbsp;
						<span><?php $movie->meta->the( 'runtime' ); ?> min</span>&nbsp;|&nbsp;
						<span><?php $movie->meta->the( 'genres' ); ?></span>&nbsp;|&nbsp;
						<span><?php $movie->meta->the( 'certification' ); ?></span>
					</div>
					<div class="wpmoly-movie-preview-overview"><?php $movie->meta->the( 'overview' ); ?></div>
				</div>
			</div>

		</div>
