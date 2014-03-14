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

			<div>
				<select id="tmdb_search_lang" name="lang">
					<option value="en" <?php selected( $this->wpml_o('tmdb-settings-lang'), 'en' ); ?>><?php _e( 'English', 'wpml' ); ?></option>
					<option value="fr" <?php selected( $this->wpml_o('tmdb-settings-lang'), 'fr' ); ?>><?php _e( 'French', 'wpml' ); ?></option>
				</select>
				<select id="tmdb_search_type" name="tmdb_search_type">
					<option value="title" selected="selected"><?php _e( 'Movie Title', 'wpml' ); ?></option>
					<option value="id"><?php _e( 'TMDb ID', 'wpml' ); ?></option>
				</select>
				<input id="tmdb_query" type="text" name="tmdb_query" value="" size="40" maxlength="32" />
				<input id="tmdb_search" name="tmdb_search" type="button" class="button button-secondary" value="<?php _e( 'Fetch data', 'wpml' ); ?>" />
				<input id="tmdb_empty" name="tmdb_empty" type="button" class="button button-secondary button-empty hide-if-no-js" value="<?php _e( 'Empty Results', 'wpml' ); ?>" />
			</div>

			<div id="tmdb_status"></div>
			<div style="clear:both"></div>

			<div id="tmdb_data"></div>


<?php foreach ( $this->wpml->wpml_tmdb_box as $id => $box ) : ?>
			<table class="list-table tmdb_<?php echo $id ?>">
				<thead>
					<tr>
						<th class="left"><?php echo $box['type'] ?></th>
						<th><?php echo $box['value'] ?></th>
					</tr>
				</thead>
				<tbody>
<?php foreach ( $box['data'] as $slug => $meta ) :
	$_value = '';
	if ( isset( $value[ $id ][ $slug ] ) )
		$_value = apply_filters( 'wpml_stringify_array', $value[ $id ][ $slug ] );
?>
<tr>
						<td class="left"><?php _e( $meta['title'], 'wpml' ) ?></td>
<?php if ( isset( $meta['type'] ) && 'textarea' == $meta['type'] ) : ?>
						<td>
							<textarea id="tmdb_data_<?php echo $slug; ?>" name="tmdb_data[<?php echo $slug; ?>]" class="tmdb_data_field" rows="6"><?php echo $_value ?></textarea>
						</td>
<?php elseif ( isset( $meta['type'] ) && in_array( $meta['type'], array( 'text', 'hidden' ) ) ) : ?>
						<td>
							<input type="<?php echo $meta['type']; ?>" id="tmdb_data_<?php echo $slug; ?>" name="tmdb_data[<?php echo $slug; ?>]" class="tmdb_data_field" value='<?php echo $_value ?>' size="64" />
						</td>
<?php endif; ?>
					</tr>
<?php endforeach; ?>
				</tbody>
			</table>
<?php endforeach; ?>
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