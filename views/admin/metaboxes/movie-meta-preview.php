
<?php if ( $empty ) : ?>
					<div id="wpmoly-movie-preview-message" class="wpmoly-movie-preview-message">
						<p><em><?php _e( 'Nothing to preview yet!', 'wpmovielibrary' ) ?></em></p>
					</div>
<?php endif; ?>

					<div id="wpmoly-movie-preview" class="wpmoly-movie-preview<?php if ( $empty ) echo ' empty' ?>">
						<div id="wpmoly-movie-preview-poster" class="wpmoly-movie-preview-poster">
							<?php echo $thumbnail ?>
						</div>
						<h3 id="wpmoly-movie-preview-title"><?php echo $preview['title'] ?></h3>
						<h5 id="wpmoly-movie-preview-original_title"><?php echo $preview['original_title'] ?></h5>
						<p>
							<span id="wpmoly-movie-preview-genres"><?php echo $preview['genres'] ?></span> − 
							<span id="wpmoly-movie-preview-release_date"><?php echo $preview['release_date'] ?></span>
							<span id="wpmoly-movie-preview-rating"><span id="movie-rating-display" class="stars-<?php echo str_replace( '.', '-', $rating ) ?>"></span></span>
						</p>
						<p id="wpmoly-movie-preview-overview">
							<?php echo apply_filters( 'wpmoly_format_movie_overview', $preview['overview'] ) ?>
						</p>
						<p>
							<?php _e ( 'Directed by:', 'wpmovielibrary' ) ?>&nbsp; <span id="wpmoly-movie-preview-director"><?php echo $preview['director'] ?></span><br />
							<?php _e ( 'Starring:', 'wpmovielibrary' ) ?>&nbsp; <span id="wpmoly-movie-preview-cast"><?php echo $preview['cast'] ?></span>
						</p>
						<div style="clear:both"></div>
					</div>

