<?php if ( false === $this->wpml_get_api_key() && false === $this->wpml_is_dummy() ) :
	_e( 'You need a valid <acronym title="TheMovieDB">TMDb</acronym> API key to start adding your movies. Go to the <a href="https://www.themoviedb.org/">WPMovieLibrary Settings page</a> to add your API key.', 'wpml' );
	return;
endif;
?>
		<div id="wpml-tmdb" class="wpml-tmdb">
<?php if ( true === $this->wpml_is_dummy() ) : ?>
			<div class="updated"><p><em><?php printf( __( 'WPMovieLibrary is using the dummy TMDb API; add your valid API key to the <a href="%s">Settings Page</a> or <a href="http://tmdb.caercam.org/">Learn more</a> about the dummy API.', 'wpml' ), admin_url( 'edit.php?post_type=movie&page=settings' ) ); ?></em></p></div>
<?php endif; ?>
			<p><strong><?php _e( 'Find movie on TMDb:', 'wpml' ); ?></strong></p>

			<select id="tmdb_search_lang" name="lang">
				<option value="en" <?php selected( $this->wpml_o('tmdb-settings-lang'), 'en' ); ?>><?php _e( 'English', 'wpml' ); ?></option>
				<option value="fr" <?php selected( $this->wpml_o('tmdb-settings-lang'), 'fr' ); ?>><?php _e( 'French', 'wpml' ); ?></option>
			</select>
			<select id="tmdb_search_type" name="tmdb_search_type">
				<option value="title" selected="selected"><?php _e( 'Movie Title', 'wpml' ); ?></option>
				<option value="id"><?php _e( 'TMDb ID', 'wpml' ); ?></option>
			</select>
			<input id="tmdb_query" type="text" name="tmdb_query" value="" size="40" maxlength="32" />
			<input id="tmdb_search" name="tmdb_search" type="button" class="button button-secondary button-small" value="<?php _e( 'Fetch data', 'wpml' ); ?>" />
			<span id="tmdb_status"></span>
			<input id="tmdb_empty" name="tmdb_empty" type="button" class="button button-secondary button-small button-empty" value="<?php _e( 'Empty Results', 'wpml' ); ?>" />

			<div id="tmdb_data"></div>

			<table class="list-table tmdb_meta"<?php echo ( ! count( $value ) ? ' style="display:none"' : '' ); ?>>
				<thead>
					<tr>
						<th class="left"><?php _e( 'Type', 'wpml' ); ?></th>
						<th><?php _e( 'Value', 'wpml' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php foreach ( $this->wpml_meta as $slug => $meta ) : ?>
					<tr>
						<td class="left"><?php echo $meta['title']; ?></td>
<?php if ( isset( $meta['type'] ) && 'textarea' == $meta['type'] ) : ?>
						<td>
							<textarea id="tmdb_data_<?php echo $slug; ?>" name="tmdb_data[<?php echo $slug; ?>]" class="tmdb_data_field" rows="6"><?php echo ( isset( $value[$slug] ) && '' != $value[$slug] ? $value[$slug] : '' ); ?></textarea>
						</td>
<?php elseif ( isset( $meta['type'] ) && in_array( $meta['type'], array( 'text', 'hidden' ) ) ) : ?>
						<td>
							<input type="<?php echo $meta['type']; ?>" id="tmdb_data_<?php echo $slug; ?>" name="tmdb_data[<?php echo $slug; ?>]" class="tmdb_data_field" value='<?php echo ( isset( $value[$slug] ) && '' != $value[$slug] ? $value[$slug] : '' ); ?>' size="64" />
						</td>
<?php endif; ?>
					</tr>
<?php endforeach; ?>
				</tbody>
			</table>

			<table class="list-table tmdb_crew"<?php echo ( ! count( $value ) ? ' style="display:none"' : '' ); ?>>
				<thead>
					<tr>
						<th class="left"><?php _e( 'Job', 'wpml' ); ?></th>
						<th><?php _e( 'Names', 'wpml' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php foreach ( $this->wpml_o('tmdb-default_fields') as $slug => $meta ) : ?>
					<tr>
						<td class="left"><?php _e( $meta, 'wpml' ); ?></td>
						<td><textarea id="tmdb_data_<?php echo $slug; ?>" name="tmdb_data[<?php echo $slug; ?>]" class="tmdb_data_field" rows="2"><?php echo ( isset( $value[$slug] ) && '' != $value[$slug] ? $value[$slug] : '' ); ?></textarea></td>
					</tr>
<?php endforeach; ?>
				</tbody>
			</table>

			<table class="list-table tmdb_images_preview"<?php echo ( ! count( $value ) ? ' style="display:none"' : '' ); ?>>
				<thead>
					<tr>
						<th><?php _e( 'Images', 'wpml' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<input id="tmdb_data_images" type="hidden" name="tmdb_data[images]" class="tmdb_data_field" value='<?php echo ( isset( $value[$slug] ) && '' != $value[$slug] ? $value[$slug] : '' ); ?>' size="64" />
							<div id="progressbar"><div class="progress-label">0</div></div>
						</td>
					</tr>
					<tr>
						<td id="tmdb_images_preview"></td>
					</tr>
						<td>
							<input id="tmdb_save_images" name="tmdb_save_images" type="button" class="button button-secondary button-large" value="<?php _e( 'Import Images', 'wpml' ); ?>" />
						</td>
				</tbody>
			</table>

			<div style="clear:both"></div>

		</div>