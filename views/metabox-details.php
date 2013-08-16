
		<div class="wpml_details" id="wpml_details">

			<div id="minor-publishing">

				<div id="misc-publishing-actions">

					<div class="misc-pub-section">
						<span id="movie-status-icon"> <label for="movie_status"><?php _e( 'Status:', 'wpml' ); ?></label></span>
						<span id="movie-status-display"><?php _e( 'Available', 'wpml' ); ?></span>
						<a href="#movie_status" class="edit-movie-status hide-if-no-js"><?php _e( 'Edit', 'wpml' ); ?></a>

						<div id="movie-status-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_status" id="hidden_movie_status" value="<?php _e( 'Available', 'wpml' ); ?>">
							<select name="movie_status" id="movie_status">
								<option selected="selected" value="available"><?php _e( 'Available', 'wpml' ); ?></option>
								<option value="loaned"><?php _e( 'Loaned', 'wpml' ); ?></option>
								<option value="scheduled"><?php _e( 'Scheduled', 'wpml' ); ?></option>
							</select>
							<a href="#movie_status" class="save-movie-status hide-if-no-js button"><?php _e( 'OK', 'wpml' ); ?></a>
							<a href="#movie_status" class="cancel-movie-status hide-if-no-js"><?php _e( 'Cancel', 'wpml' ); ?></a>
						</div>

					</div><!-- .misc-pub-section -->

					<div class="misc-pub-section">
						<span id="movie-media-icon"> <label for="movie_media"><?php _e( 'Media:', 'wpml' ); ?></label></span>
						<span id="movie-media-display"><?php _e( 'DVD', 'wpml' ); ?></span>
						<a href="#movie_media" class="edit-movie-media hide-if-no-js"><?php _e( 'Edit', 'wpml' ); ?></a>

						<div id="movie-media-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_media" id="hidden_movie_media" value="<?php _e( 'DVD', 'wpml' ); ?>">
							<select name="movie_media" id="movie_media">
								<option selected="selected" value="dvd"><?php _e( 'DVD', 'wpml' ); ?></option>
								<option value="vod"><?php _e( 'VOD', 'wpml' ); ?></option>
								<option value="vhs"><?php _e( 'VHS', 'wpml' ); ?></option>
								<option value="other"><?php _e( 'Other', 'wpml' ); ?></option>
							</select>
							<a href="#movie_media" class="save-movie-media hide-if-no-js button"><?php _e( 'OK', 'wpml' ); ?></a>
							<a href="#movie_media" class="cancel-movie-media hide-if-no-js"><?php _e( 'Cancel', 'wpml' ); ?></a>
						</div>

					</div><!-- .misc-pub-section -->

					<div class="misc-pub-section">
						<label for="movie_rating"><?php _e( 'Rating:', 'wpml' ); ?></label>
						<div id="movie_rating_display" class="stars-3"></div>
						<a href="#movie_rating" class="edit-movie-rating hide-if-no-js"><?php _e( 'Edit', 'wpml' ); ?></a>

						<div id="movie-rating-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_rating" id="hidden_movie_rating" value="3">
							<div id="stars">
								<div id="star-1" class="star"></div>
								<div id="star-2" class="star"></div>
								<div id="star-3" class="star"></div>
								<div id="star-4" class="star"></div>
								<div id="star-5" class="star"></div>
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
					<input name="original_publish" type="hidden" id="original_publish" value="<?php _e( 'Save', 'wpml' ); ?>">
					<input type="submit" name="wpml_save" id="wpml_save" class="button button-secondary button-large" value="<?php _e( 'Save', 'wpml' ); ?>" accesskey="s">
				</div>
				<div class="clear"></div>
			</div>
		</div>