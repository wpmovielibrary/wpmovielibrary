<div class="wrap">
	<h2><?php _e("Plugin Options"); ?></h2>
	
	<?php if ( isset($this->msg_settings) ) { ?>
	<div id="setting-error-settings_updated" class="updated settings-error"> 
		<p><strong><?php echo $this->msg_settings; ?></strong></p>
	</div>
	<?php } ?>

	<form method="post">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="tmdb_api_key"><?php _e("API Key"); ?></label>
					</th>
					<td>
						<input id="tmdb_api_key" type="text" name="tmdb_api_key" value="<?php echo $current_options['tmdb_api_key']; ?>" size="40" maxlength="32" />
						<p class="description"><?php _e( 'You need a valid TMDb API key in order to fetch informations on the movies you add to WP-Movie-Library. You can get an individual API key by registering on <a href="https://www.themoviedb.org/">TheMovieDB</a>. If you don’t want to get your own API Key, WP-Movie-Library will use a built-in key, with restrictions of two movies added per day.' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="fields_to_display"><?php _e("Fields to display"); ?></label>
					</th>
					<td>
						<select multiple="multiple" size="10" name="fields_to_display[]">
							<?php foreach ( $this->tmdb_fields as $field ) { ?>
							<option<?php if ( in_array( $field, $current_options['fields_to_display'] ) ) { ?> selected="selected"<?php } ?>><?php echo $field; ?></option>
							<?php } ?>
						</select>
						<p class="description"><?php _e( 'Press and hold CTRL button to select multiple.', 'wp_movie_library' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php _e("Comments"); ?>
					</th>
					<td>
						<fieldset>
							<label for="allow_comments">
								<input id="allow_comments" type="checkbox" name="allow_comments" <?php checked( $current_options['allow_comments'], 1 ); ?>/>
								Enable comments for movies
							</label>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" id="submit" class="button-primary" value="Save Changes" />
		</p>
	</form>
</div>