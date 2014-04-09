
		<fieldset class="inline-edit-col-wpml inline-edit-col-left">
		<h4><?php _e( 'Movie Details', 'wpml' ) ?></h4>
		<div class="inline-edit-col">
			<div class="inline-edit-group">
				<label>
					<span class="title"><?php _e( 'Media', 'wpml' ) ?></span>
					<select class="movie_media" id="movie_media" name="wpml_details[movie_media]">
<?php foreach ( $default_movie_media as $slug => $title ) : ?>
						<option value="<?php echo $slug ?>"><?php echo $title ?></option>
<?php endforeach; ?>
					</select>
				</label>
			</div>
			<div class="inline-edit-group">
				<label>
					<span class="title"><?php _e( 'Status', 'wpml' ) ?></span>
					<select class="movie_status" id="movie_status" name="wpml_details[movie_status]">
<?php foreach ( $default_movie_status as $slug => $title ) : ?>
						<option value="<?php echo $slug ?>"><?php echo $title ?></option>
<?php endforeach; ?>
					</select>
				</label>
			</div>
			<div class="inline-edit-group">
				<label>
					<span class="title"><?php _e( 'Rating', 'wpml' ) ?></span>
					<input type="hidden" id="hidden_movie_rating" name="hidden_movie_rating" value="0.0">
					<input type="hidden" id="movie_rating" name="wpml_details[movie_rating]" value="0.0">
					<div id="stars" data-default-rating="0.0" data-rating="0.0" data-rated="false" class="stars">
						<div id="stars_labels" class="stars_labels">
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
				</label>
			</div>
			<input type="hidden" name="<?php echo $nonce_name ?>" id="<?php echo $nonce_name ?>" value="<?php echo $nonce ?>" />
			<input type="hidden" name="<?php echo $check ?>" value="true" />
		</div></fieldset>