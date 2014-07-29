
		<fieldset class="inline-edit-col-wpml inline-edit-col-left">
		<h4><?php _e( 'Movie Dtails', 'wpmovielibrary' ) ?></h4>
		<div class="inline-edit-col">
			<div class="inline-edit-group">
				<label>
					<span class="title"><?php _e( 'Media', 'wpmovielibrary' ) ?></span>
					<select class="movie-media" id="movie-media" name="wpml_details[movie_media]">
<?php foreach ( $default_movie_media as $slug => $title ) : ?>
						<option value="<?php echo $slug ?>"><?php _e( $title, 'wpmovielibrary' ) ?></option>
<?php endforeach; ?>
					</select>
				</label>
			</div>
			<div class="inline-edit-group">
				<label>
					<span class="title"><?php _e( 'Status', 'wpmovielibrary' ) ?></span>
					<select class="movie-status" id="movie-status" name="wpml_details[movie_status]">
<?php foreach ( $default_movie_status as $slug => $title ) : ?>
						<option value="<?php echo $slug ?>"><?php _e( $title, 'wpmovielibrary' ) ?></option>
<?php endforeach; ?>
					</select>
				</label>
			</div>
			<div class="inline-edit-group">
				<label>
					<span class="title"><?php _e( 'Rating', 'wpmovielibrary' ) ?></span>
					<input type="hidden" id="hidden-movie-rating" name="hidden_movie_rating" value="0.0">
					<input type="hidden" id="movie-rating" name="wpml_details[movie_rating]" value="0.0">
					<div id="stars" data-default-rating="0.0" data-rating="0.0" data-rated="false" class="stars">
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
				</label>
			</div>
			<?php WPML_Utils::_nonce_field( 'quickedit-movie-details', $referer = false ) ?>
			<input type="hidden" name="<?php echo $check ?>" value="true" />
		</div></fieldset>
