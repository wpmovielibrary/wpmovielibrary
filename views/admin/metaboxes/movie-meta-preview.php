

					<div id="wpmoly-movie-preview" class="wpmoly-movie-preview">
						<div id="wpmoly-movie-preview-poster" class="wpmoly-movie-preview-poster">
							<?php echo $thumbnail ?>
						</div>
						<h3 id="wpmoly-movie-preview-title"><?php echo $metadata['title'] ?></h3>
						<h5 id="wpmoly-movie-preview-original_title"><?php echo $metadata['original_title'] ?></h5>
						<p>
							<span id="wpmoly-movie-preview-genres"><?php echo apply_filters( 'wpmoly_format_movie_genres', $metadata['genres'] ) ?></span> − 
							<span id="wpmoly-movie-preview-release_date"><?php echo apply_filters( 'wpmoly_format_movie_runtime', $metadata['release_date'], 'Y' ) ?></span>
							<span id="wpmoly-movie-preview-rating"><span id="movie-rating-display" class="stars-<?php echo str_replace( '.', '-', $rating ) ?>"></span></span>
						</p>
						<p id="wpmoly-movie-preview-overview">
							<?php echo apply_filters( 'wpmoly_format_movie_overview', $metadata['overview'] ) ?>
						</p>
						<p>
							<?php _e ( 'Directed by:', 'wpmovielibrary' ) ?>&nbsp; <span id="wpmoly-movie-preview-director"><?php echo apply_filters( 'wpmoly_format_movie_director', $metadata['director'] ) ?></span><br />
							<?php _e ( 'Starring:', 'wpmovielibrary' ) ?>&nbsp; <span id="wpmoly-movie-preview-cast"><?php echo apply_filters( 'wpmoly_format_movie_actors', $metadata['cast'] ) ?></span>
						</p>
						<div style="clear:both"></div>
					</div>

