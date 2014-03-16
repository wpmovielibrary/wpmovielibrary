
		<fieldset class="inline-edit-col-right">
		<h4><?php _e( 'Movie Details', 'wpml' ) ?></h4>
		<div class="inline-edit-col">
			<div class="inline-edit-group">
				<label>
					<span class="title"><?php _e( 'Media', 'wpml' ) ?></span>
					<select id="bulk_movie_media" name="wpml_details[movie_media]">
<?php foreach ( $default_movie_media as $slug => $title ) : ?>
						<option value="<?php echo $slug ?>"><?php echo $title ?></option>
<?php endforeach; ?>
					</select>
				</label>
			</div>
			<div class="inline-edit-group">
				<label>
					<span class="title"><?php _e( 'Status', 'wpml' ) ?></span>
					<select id="bulk_movie_status" name="wpml_details[movie_status]">
<?php foreach ( $default_movie_status as $slug => $title ) : ?>
						<option value="<?php echo $slug ?>"><?php echo $title ?></option>
<?php endforeach; ?>
					</select>
				</label>
			</div>
			<div class="inline-edit-group">
				<label>
					<span class="title"><?php _e( 'Rating', 'wpml' ) ?></span>
					<input type="hidden" id="bulk_hidden_movie_rating" name="hidden_movie_rating" value="">
					<input type="hidden" id="bulk_movie_rating" name="wpml_details[movie_rating]" value="">
					<div id="bulk_stars" data-default-rating="" data-rating="" data-rated="false" class="stars">
						<div id="bulk_stars_labels" class="stars_labels">
							<span id="bulk_stars_label_0_5" class="stars_label"><?php _e( 'Junk', 'wpml' ) ?></span>
							<span id="bulk_stars_label_1_0" class="stars_label"><?php _e( 'Very bad', 'wpml' ) ?></span>
							<span id="bulk_stars_label_1_5" class="stars_label"><?php _e( 'Bad', 'wpml' ) ?></span>
							<span id="bulk_stars_label_2_0" class="stars_label"><?php _e( 'Not that bad', 'wpml' ) ?></span>
							<span id="bulk_stars_label_2_5" class="stars_label"><?php _e( 'Average', 'wpml' ) ?></span>
							<span id="bulk_stars_label_3_0" class="stars_label"><?php _e( 'Not bad', 'wpml' ) ?></span>
							<span id="bulk_stars_label_3_5" class="stars_label"><?php _e( 'Good', 'wpml' ) ?></span>
							<span id="bulk_stars_label_4_0" class="stars_label"><?php _e( 'Very good', 'wpml' ) ?></span>
							<span id="bulk_stars_label_4_5" class="stars_label"><?php _e( 'Excellent', 'wpml' ) ?></span>
							<span id="bulk_stars_label_5_0" class="stars_label"><?php _e( 'Masterpiece', 'wpml' ) ?></span>
						</div>
					</div>
				</label>
			</div>
			<?php wp_nonce_field( '_wpml_bulk_movie_details', 'wpml_bulk_movie_details_nonce' ); ?>
			<input type="hidden" name="is_bulkedit" value="true" />
		</div></fieldset>
