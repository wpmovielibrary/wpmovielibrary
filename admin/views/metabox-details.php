
		<div id="wpml-details" class="wpml-details">

			<div id="minor-publishing">

				<div id="misc-publishing-actions">

					<div class="misc-pub-section">
						<span id="movie-status-icon"> <label for="movie_status"><?php _e( 'Status:', 'wpml' ); ?></label></span>
						<span id="movie-status-display"><?php _e( $this->wpml->default_post_details['movie_status']['options'][$movie_status], 'wpml' ) ?></span>
						<a href="#movie_status" class="edit-movie-status hide-if-no-js"><?php _e( 'Edit', 'wpml' ); ?></a>

						<div id="movie-status-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_status" id="hidden_movie_status" value="<?php echo $movie_status; ?>">
							<select name="wpml_details[movie_status]" id="movie_status">
<?php foreach ( $this->wpml->default_post_details['movie_status']['options'] as $slug => $status ) : ?>
								<option value="<?php echo $slug; ?>" <?php selected( $status, $movie_status ); ?>><?php _e( $status, 'wpml' ) ?></option>
<?php endforeach; ?>
							</select>
							<a href="#movie_status" class="save-movie-status hide-if-no-js button"><?php _e( 'OK', 'wpml' ); ?></a>
							<a href="#movie_status" class="cancel-movie-status hide-if-no-js"><?php _e( 'Cancel', 'wpml' ); ?></a>
						</div>

					</div><!-- .misc-pub-section -->

					<div class="misc-pub-section">
						<span id="movie-media-icon"> <label for="movie_media"><?php _e( 'Media:', 'wpml' ); ?></label></span>
						<span id="movie-media-display"><?php _e( $this->wpml->default_post_details['movie_media']['options'][$movie_media], 'wpml' ) ?></span>
						<a href="#movie_media" class="edit-movie-media hide-if-no-js"><?php _e( 'Edit', 'wpml' ); ?></a>

						<div id="movie-media-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_media" id="hidden_movie_media" value="<?php echo $movie_media; ?>">
							<select name="wpml_details[movie_media]" id="movie_media">
<?php foreach ( $this->wpml->default_post_details['movie_media']['options'] as $slug => $media ) : ?>
								<option value="<?php echo $slug; ?>" <?php selected( $media, $movie_media ); ?>><?php _e( $media, 'wpml' ) ?></option>
<?php endforeach; ?>
							</select>
							<a href="#movie_media" class="save-movie-media hide-if-no-js button"><?php _e( 'OK', 'wpml' ); ?></a>
							<a href="#movie_media" class="cancel-movie-media hide-if-no-js"><?php _e( 'Cancel', 'wpml' ); ?></a>
						</div>

					</div><!-- .misc-pub-section -->

					<div class="misc-pub-section">
						<label for="movie_rating"><?php _e( 'Rating:', 'wpml' ); ?></label>
						<div id="movie-rating-display" class="stars_<?php echo $movie_rating_str; ?>"></div>
						<a href="#movie-rating" class="edit-movie-rating hide-if-no-js"><?php _e( 'Edit', 'wpml' ); ?></a>

						<div id="movie-rating-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_rating" id="hidden_movie_rating" value="<?php echo $movie_rating; ?>">
							<input type="hidden" name="wpml_details[movie_rating]" id="movie_rating" value="<?php echo $movie_rating; ?>">
							<div class="movie-rating-block">
								<div id="stars" data-default-rating="<?php echo $movie_rating; ?>" data-rating="<?php echo $movie_rating; ?>" data-rated="false" class="stars stars_<?php echo $movie_rating_str; ?>">
									<div id="stars_label">
										<span id="stars_label_0_5" class="stars_label"><?php _e( 'Junk', 'wpml' ) ?></span>
										<span id="stars_label_1_0" class="stars_label"><?php _e( 'Very bad', 'wpml' ) ?></span>
										<span id="stars_label_1_5" class="stars_label"><?php _e( 'Bad', 'wpml' ) ?></span>
										<span id="stars_label_2_0" class="stars_label"><?php _e( 'Not that bad', 'wpml' ) ?></span>
										<span id="stars_label_2_5" class="stars_label"><?php _e( 'Average', 'wpml' ) ?></span>
										<span id="stars_label_3_0" class="stars_label"><?php _e( 'Not bad', 'wpml' ) ?></span>
										<span id="stars_label_3_5" class="stars_label"><?php _e( 'Good', 'wpml' ) ?></span>
										<span id="stars_label_4_0" class="stars_label"><?php _e( 'Very good', 'wpml' ) ?></span>
										<span id="stars_label_4_5" class="stars_label"><?php _e( 'Excellent', 'wpml' ) ?></span>
										<span id="stars_label_5_0" class="stars_label"><?php _e( 'Masterpiece', 'wpml' ) ?></span>
									</div>
								</div>
							</div>
							<a href="#movie_media" class="save-movie-rating hide-if-no-js button"><?php _e( 'OK', 'wpml' ); ?></a>
							<a href="#movie_media" class="cancel-movie-rating hide-if-no-js"><?php _e( 'Cancel', 'wpml' ); ?></a>
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