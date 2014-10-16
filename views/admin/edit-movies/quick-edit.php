
		<fieldset class="inline-edit-col-wpmoly">
			<h4><?php _e( 'Movie Dtails', 'wpmovielibrary' ) ?></h4>
			<div class="inline-edit-col">
<?php foreach ( $fields as $id => $field ) : ?>
				<div class="inline-edit-group">
					<label>
						<span class="title"><span class="<?php echo $field['icon'] ?>"></span><?php echo $field['title']; ?></span>
						<select class="movie-<?php echo $id ?>" id="movie-<?php echo $id ?>" name="wpmoly_details[<?php echo $id ?>]">
							<option value=""><?php _e( 'None', 'wpmovielibrary' ) ?></option>
<?php foreach ( $field['options'] as $slug => $title ) : ?>
							<option value="<?php echo $slug ?>"><?php echo $title; ?></option>
<?php endforeach; ?>

						</select>
					</label>
				</div>
<?php endforeach; ?>
				<?php wpmoly_nonce_field( 'quickedit-movie-details', $referer = false ) ?>
				<input type="hidden" name="<?php echo $check ?>" value="true" />
			</div>
		</fieldset>
