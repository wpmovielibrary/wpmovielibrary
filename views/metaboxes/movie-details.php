
		<div id="wpml-details" class="wpml-details">

			<div id="wpml-details-minor-publishing">

				<div id="wpml-details-publishing-actions">

					<div class="misc-pub-section">

						<span class="<?php echo ( WPML_Utils::is_modern_wp() ? 'dashicons dashicons-share-alt' : 'movie-status-icon' ) ?>"></span>
						<label for="movie-status"><?php _e( 'Status:', 'wpmovielibrary' ); ?></label>
						<span id="movie-status-display"><?php $_status = WPML_Settings::get_available_movie_status(); $_status = ( '' != $movie_status ? $_status[ $movie_status ] : 'None' ); _e( $_status, 'wpmovielibrary' ) ?></span>
						<a href="#movie-status" id="edit-movie-status" class="edit-movie-status hide-if-no-js" onclick="wpml_status.show(); return false;"><?php _e( 'Edit', 'wpmovielibrary' ); ?></a>

						<div id="movie-status-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_status" id="hidden-movie-status" value="movie-<?php echo $movie_status; ?>">
							<select name="wpml_details[movie_status]" id="movie-status">
								<option id="movie-default-status" value="" <?php selected( '', $movie_status ); ?>><?php _e( 'None', 'wpmovielibrary' ) ?></option>
<?php foreach ( WPML_Settings::get_available_movie_status() as $slug => $status ) : ?>
								<option id="movie-<?php echo $slug; ?>" value="<?php echo $slug; ?>" <?php selected( $slug, $movie_status ); ?>><?php _e( $status, 'wpmovielibrary' ) ?></option>
<?php endforeach; ?>
							</select>
							<a href="#movie-status" id="save-movie-status" class="save-movie-status hide-if-no-js button" onclick="wpml_status.update(); return false;"><?php _e( 'OK', 'wpmovielibrary' ); ?></a>
							<a href="#movie-status" id="cancel-movie-status" class="cancel-movie-status hide-if-no-js" onclick="wpml_status.revert(); return false;"><?php _e( 'Cancel', 'wpmovielibrary' ); ?></a>
						</div>

					</div><!-- .misc-pub-section -->

					<div class="misc-pub-section">
						<span class="<?php echo ( WPML_Utils::is_modern_wp() ? 'dashicons dashicons-editor-video' : 'movie-media-icon' ) ?>"></span>
						<label for="movie-media"><?php _e( 'Media:', 'wpmovielibrary' ); ?></label>
						<span id="movie-media-display"><?php $_media = WPML_Settings::get_available_movie_media(); $_media = ( '' != $movie_media ? $_media[ $movie_media ] : 'None' ); _e( $_media, 'wpmovielibrary' ) ?></span>
						<a href="#movie-media" id="edit-movie-media" class="edit-movie-media hide-if-no-js" onclick="wpml_media.show(); return false;"><?php _e( 'Edit', 'wpmovielibrary' ); ?></a>

						<div id="movie-media-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_media" id="hidden-movie-media" value="movie-<?php echo $movie_media; ?>">
							<select name="wpml_details[movie_media]" id="movie-media">
								<option id="movie-default-media" value="" <?php selected( '', $movie_media ); ?>><?php _e( 'None', 'wpmovielibrary' ) ?></option>
<?php foreach ( WPML_Settings::get_available_movie_media() as $slug => $media ) : ?>
								<option id="movie-<?php echo $slug; ?>" value="<?php echo $slug; ?>" <?php selected( $slug, $movie_media ); ?>><?php _e( $media, 'wpmovielibrary' ) ?></option>
<?php endforeach; ?>
							</select>
							<a href="#movie-media" id="save-movie-media" class="save-movie-media hide-if-no-js button" onclick="wpml_media.update(); return false;"><?php _e( 'OK', 'wpmovielibrary' ); ?></a>
							<a href="#movie-media" id="cancel-movie-media" class="cancel-movie-media hide-if-no-js" onclick="wpml_media.revert(); return false;"><?php _e( 'Cancel', 'wpmovielibrary' ); ?></a>
						</div>

					</div><!-- .misc-pub-section -->

					<div class="misc-pub-section">
						<span class="<?php echo ( WPML_Utils::is_modern_wp() ? 'dashicons dashicons-star-half' : 'movie-rating-icon' ) ?>"></span>
						<label for="movie-rating"><?php _e( 'Rating:', 'wpmovielibrary' ); ?></label>
						<div id="movie-rating-display" class="hide-if-no-js stars-<?php echo $movie_rating_str; ?>"></div>
						<a href="#movie-rating" id="edit-movie-rating" class="edit-movie-rating hide-if-no-js" onclick="wpml_rating.show(); return false;"><?php _e( 'Edit', 'wpmovielibrary' ); ?></a>

						<div id="movie-rating-select" class="hide-if-js">
							<input type="hidden" name="hidden_movie_rating" id="hidden-movie-rating" value="<?php echo $movie_rating; ?>">
							<input type="number" class="hide-if-js" id="movie-rating" name="wpml_details[movie_rating]" step="0.5" min="0.0" max="5.0" value="<?php echo $movie_rating; ?>"></input>
							<div class="movie-rating-block hide-if-no-js">
								<div id="stars" data-default-rating="<?php echo $movie_rating; ?>" data-rating="<?php echo $movie_rating; ?>" data-rated="false" class="stars stars-<?php echo $movie_rating_str; ?>">
									<div id="stars-labels" class="stars-labels">
										<span id="stars-label-0-5" class="stars-label"><?php _e( 'Junk', 'wpmovielibrary' ) ?></span>
										<span id="stars-label-1-0" class="stars-label"><?php _e( 'Very bad', 'wpmovielibrary' ) ?></span>
										<span id="stars-label-1-5" class="stars-label"><?php _e( 'Bad', 'wpmovielibrary' ) ?></span>
										<span id="stars-label-2-0" class="stars-label"><?php _e( 'Not that bad', 'wpmovielibrary' ) ?></span>
										<span id="stars-label-2-5" class="stars-label"><?php _e( 'Average', 'wpmovielibrary' ) ?></span>
										<span id="stars-label-3-0" class="stars-label"><?php _e( 'Not bad', 'wpmovielibrary' ) ?></span>
										<span id="stars-label-3-5" class="stars-label"><?php _e( 'Good', 'wpmovielibrary' ) ?></span>
										<span id="stars-label-4-0" class="stars-label"><?php _e( 'Very good', 'wpmovielibrary' ) ?></span>
										<span id="stars-label-4-5" class="stars-label"><?php _e( 'Excellent', 'wpmovielibrary' ) ?></span>
										<span id="stars-label-5-0" class="stars-label"><?php _e( 'Masterpiece', 'wpmovielibrary' ) ?></span>
									</div>
								</div>
							</div>
							<a href="#movie-media" id="save-movie-rating" class="save-movie-rating hide-if-no-js button" onclick="wpml_rating.update(); return false;"><?php _e( 'OK', 'wpmovielibrary' ); ?></a>
							<a href="#movie-media" id="cancel-movie-rating" class="cancel-movie-rating hide-if-no-js" onclick="wpml_rating.revert(); return false;"><?php _e( 'Cancel', 'wpmovielibrary' ); ?></a>
						</div>

					</div><!-- .misc-pub-section -->
				</div>
				<div class="clear"></div>
			</div>

		</div>

		<div id="wpml-details-major-publishing-actions" class="hide-if-no-js">
			<div id="wpml-details-status"></div>
			<div id="wpml-details-major-publishing-action">
				<span class="spinner"></span>
				<?php WPML_Utils::_nonce_field( 'save-movie-details', $referer = false ) ?>
				<input type="submit" name="wpml_save" id="wpml_save" class="button button-secondary button-large" value="<?php _e( 'Save', 'wpmovielibrary' ); ?>" accesskey="s" onclick="wpml_edit_details.save(); return false;" />
				<input name="wpml_details_save" type="hidden" id="wpml_details_save" value="<?php _e( 'Save', 'wpmovielibrary' ); ?>" />
			</div>
			<div class="clear"></div>
		</div>