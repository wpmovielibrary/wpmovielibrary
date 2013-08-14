<div class="wrap">
	<h2><?php _e("Plugin Options"); ?></h2>
	
	<?php if ( isset($this->msg_settings) ) { ?>
	<div id="setting-error-settings_updated" class="updated settings-error"> 
		<p><strong><?php echo $this->msg_settings; ?></strong></p>
	</div>
	<?php } ?>

	<?php //if ( ! $this->wpml_get_api_key() ) $this->wpml_activate_notice( null ); ?>

	<form method="post">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="APIKey"><?php _e("API Key"); ?></label>
					</th>
					<td>
						<input id="APIKey" type="text" name="APIKey" value="<?php //echo ( $this->wpml_get_api_key() ? $this->wpml_get_api_key() : '' ); ?>" size="40" maxlength="32" />
						<p class="description"><?php _e( 'You need a valid TMDb API key in order to fetch informations on the movies you add to WP-Movie-Library. You can get an individual API key by registering on <a href="https://www.themoviedb.org/">TheMovieDB</a>. If you don’t want to get your own API Key, WP-Movie-Library will use a built-in key, with restrictions of two movies added per day.' ); ?></p>
					</td>
				</tr>
				<!--<tr valign="top">
					<th scope="row">
						<label for="fields_to_display"><?php _e("Fields to display"); ?></label>
					</th>
					<td>
						<?php foreach ( $this->wpml_o('tmdb_fields') as $slug => $f ) { ?>
						<div class="tmdb_fields">
							<input type="checkbox" id="tmdb_fields_<?php echo $slug; ?>" name="tmdb_fields[]" value="<?php echo $slug; ?>" />
							<label for="tmdb_fields_<?php echo $slug; ?>"><?php echo $f['title']; ?></label>
							<?php echo ( isset( $f['description'] ) && '' != $f['description'] ? '<p class="description">'.$f['description'].'</p>' : '' ); ?>
						</div>
						<?php } ?>
						<select multiple="multiple" size="10" name="fields_to_display[]">
							<?php foreach ( $this->wpml_o('tmdb_fields') as $slug => $f ) { ?>
							<option value="<?php echo $slug; ?>"><?php echo $f['title']; ?></option>
							<?php } ?>
						</select>
						<p class="description"><?php _e( 'Press and hold CTRL button to select multiple.', 'wp_movie_library' ); ?></p>
					</td>
				</tr>-->
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" id="submit" class="button-primary" value="Save Changes" />
		</p>
	</form>
</div>