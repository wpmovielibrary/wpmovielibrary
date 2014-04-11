
		<div id="wpml-details" class="wpml-details">

			<div id="minor-publishing">

				<div id="misc-publishing-actions">

					<div class="misc-pub-section">
						<span id="movie-status-icon"> <label for="movie-status"><?php _e( 'Status:', 'wpml' ); ?></label></span>
						<span id="movie-status-display"><?php $_status = WPML_Settings::get_available_movie_status(); _e( $_status[$movie_status], 'wpml' ) ?></span>
						<a href="#movie-status" id="edit-movie-status" class="edit-movie-status hide-if-no-js"><?php _e( 'Edit', 'wpml' ); ?></a>

						<div id="movie-status-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_status" id="hidden-movie-status" value="<?php echo $movie_status; ?>">
							<select name="wpml_details[movie_status]" id="movie-status">
<?php foreach ( WPML_Settings::get_available_movie_status() as $slug => $status ) : ?>
								<option value="<?php echo $slug; ?>" <?php selected( $status, $movie_status ); ?>><?php _e( $status, 'wpml' ) ?></option>
<?php endforeach; ?>
							</select>
							<a href="#movie-status" id="save-movie-status" class="save-movie-status hide-if-no-js button"><?php _e( 'OK', 'wpml' ); ?></a>
							<a href="#movie-status" id="cancel-movie-status" class="cancel-movie-status hide-if-no-js"><?php _e( 'Cancel', 'wpml' ); ?></a>
						</div>

					</div><!-- .misc-pub-section -->

					<div class="misc-pub-section">
						<span id="movie-media-icon"> <label for="movie-media"><?php _e( 'Media:', 'wpml' ); ?></label></span>
						<span id="movie-media-display"><?php $_media = WPML_Settings::get_available_movie_media(); _e( $_media[$movie_media], 'wpml' ) ?></span>
						<a href="#movie-media" id="edit-movie-media" class="edit-movie-media hide-if-no-js"><?php _e( 'Edit', 'wpml' ); ?></a>

						<div id="movie-media-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_media" id="hidden-movie-edia" value="<?php echo $movie_media; ?>">
							<select name="wpml_details[movie_media]" id="movie-media">
<?php foreach ( WPML_Settings::get_available_movie_media() as $slug => $media ) : ?>
								<option value="<?php echo $slug; ?>" <?php selected( $media, $movie_media ); ?>><?php _e( $media, 'wpml' ) ?></option>
<?php endforeach; ?>
							</select>
							<a href="#movie-media" id="save-movie-media" class="save-movie-media hide-if-no-js button"><?php _e( 'OK', 'wpml' ); ?></a>
							<a href="#movie-media" id="cancel-movie-media" class="cancel-movie-media hide-if-no-js"><?php _e( 'Cancel', 'wpml' ); ?></a>
						</div>

					</div><!-- .misc-pub-section -->

					<div class="misc-pub-section">
						<span id="movie-rating-icon"> <label for="movie-rating"><?php _e( 'Rating:', 'wpml' ); ?></label></span>
						<div id="movie-rating-display" class="stars_<?php echo $movie_rating_str; ?>"></div>
						<a href="#movie-rating" id="edit-movie-rating" class="edit-movie-rating hide-if-no-js"><?php _e( 'Edit', 'wpml' ); ?></a>

						<div id="movie-rating-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_rating" id="hidden-movie-rating" value="<?php echo $movie_rating; ?>">
							<input type="number" class="hide-if-js" name="wpml_details[movie_rating]" id="movie-rating" step="0.5" min="0.0" max="5.0" value="<?php echo $movie_rating; ?>"></input>
							<div class="movie-rating-block hide-if-no-js">
								<div id="stars" data-default-rating="<?php echo $movie_rating; ?>" data-rating="<?php echo $movie_rating; ?>" data-rated="false" class="stars stars-<?php echo $movie_rating_str; ?>">
									<div id="stars-labels" class="stars-labels">
										<span id="stars-label-0-5" class="stars-label"><?php _e( 'Junk', 'wpml' ) ?></span>
										<span id="stars-label-1-0" class="stars-label"><?php _e( 'Very bad', 'wpml' ) ?></span>
										<span id="stars-label-1-5" class="stars-label"><?php _e( 'Bad', 'wpml' ) ?></span>
										<span id="stars-label-2-0" class="stars-label"><?php _e( 'Not that bad', 'wpml' ) ?></span>
										<span id="stars-label-2-5" class="stars-label"><?php _e( 'Average', 'wpml' ) ?></span>
										<span id="stars-label-3-0" class="stars-label"><?php _e( 'Not bad', 'wpml' ) ?></span>
										<span id="stars-label-3-5" class="stars-label"><?php _e( 'Good', 'wpml' ) ?></span>
										<span id="stars-label-4-0" class="stars-label"><?php _e( 'Very good', 'wpml' ) ?></span>
										<span id="stars-label-4-5" class="stars-label"><?php _e( 'Excellent', 'wpml' ) ?></span>
										<span id="stars-label-5-0" class="stars-label"><?php _e( 'Masterpiece', 'wpml' ) ?></span>
									</div>
								</div>
							</div>
							<a href="#movie-media" id="save-movie-rating" class="save-movie-rating hide-if-no-js button"><?php _e( 'OK', 'wpml' ); ?></a>
							<a href="#movie-media" id="cancel-movie-rating" class="cancel-movie-rating hide-if-no-js"><?php _e( 'Cancel', 'wpml' ); ?></a>
						</div>

					</div><!-- .misc-pub-section -->
				</div>
				<div class="clear"></div>
			</div>

			<div id="major-publishing-actions">
				<div id="publishing-action">
					<span class="spinner"></span>
					<input name="wpml_details_save" type="hidden" id="wpml_details_save" value="<?php _e( 'Save', 'wpml' ); ?>">
					<input type="button" name="wpml_save" id="wpml_save" class="button button-secondary button-large" value="<?php _e( 'Save', 'wpml' ); ?>" accesskey="s">
				</div>
				<div class="clear"></div>
			</div>
		</div>