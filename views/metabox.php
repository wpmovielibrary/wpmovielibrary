<?php if ( false === $this->wpml_get_api_key() ) :
	_e( 'You need a valid <acronym title="TheMovieDB">TMDb</acronym> API key to start adding your movies. Go to the <a href="https://www.themoviedb.org/">WPMovieLibrary Settings page</a> to add your API key.', 'wpml' );
	return;
endif;
?>
		<p><strong><?php _e( 'Find movie on TMDb:', 'wpml' ); ?></strong></p>

		<select id="scheme" name="scheme">
			<option value="http" <?php selected( $this->wpml_o('tmdb-settings-scheme'), 'http' ); ?>><?php _e( 'HTTP', 'wpml' ); ?></option>
			<option value="https" <?php selected( $this->wpml_o('tmdb-settings-scheme'), 'https' ); ?>><?php _e( 'HTTPS', 'wpml' ); ?></option>
		</select>
		<select id="lang" name="lang">
			<option value="en" <?php selected( $this->wpml_o('tmdb-settings-lang'), 'en' ); ?>><?php _e( 'English', 'wpml' ); ?></option>
			<option value="fr" <?php selected( $this->wpml_o('tmdb-settings-lang'), 'fr' ); ?>><?php _e( 'French', 'wpml' ); ?></option>
		</select>
		<select id="tmdb_search_type" name="tmdb_search_type">
			<option value="title" selected="selected"><?php _e( 'Movie Title', 'wp_movie_library' ); ?></option>
			<option value="id"><?php _e( 'TMDb ID', 'wp_movie_library' ); ?></option>
		</select>
		<input id="tmdb_query" type="text" name="tmdb_query" value="" size="40" maxlength="32" />
		<input id="tmdb_search" name="tmdb_search" type="button" class="button button-secondary button-small" value="<?php _e( 'Fetch data', 'wp_movie_library' ); ?>" />
		<input id="tmdb_empty" name="tmdb_empty" type="button" class="button button-secondary button-small button-empty" value="<?php _e( 'Empty Results', 'wp_movie_library' ); ?>" />

		<div id="tmdb_data"></div>

		<table class="list-table"<?php echo ( ! count( $value ) ? ' style="display:none"' : '' ); ?>>
			<thead>
				<tr>
					<th class="left"><?php _e( 'Type', 'wp_movie_library' ); ?></th>
					<th><?php _e( 'Value', 'wp_movie_library' ); ?></th>
				</tr>
			</thead>
			<tbody>
<?php foreach ( $this->wpml_o('meta_data') as $slug => $meta ) : ?>
				<tr>
					<td class="left"><?php echo $meta['title']; ?></td>
<?php if ( 'images' == $slug ) : ?>
					<td>
						<input id="tmdb_save_images" name="tmdb_save_images" type="button" class="button button-secondary button-small" value="<?php _e( 'Import Images', 'wp_movie_library' ); ?>" />
						<input id="tmdb_data_<?php echo $slug; ?>" type="hidden" name="tmdb_data[<?php echo $slug; ?>]" value='<?php echo ( isset( $value[$slug] ) && '' != $value[$slug] ? $value[$slug] : '' ); ?>' size="64" />
					</td>
<?php else : ?>
					<td><input id="tmdb_data_<?php echo $slug; ?>" type="text" name="tmdb_data[<?php echo $slug; ?>]" value="<?php echo ( isset( $value[$slug] ) && '' != $value[$slug] ? $value[$slug] : '' ); ?>" size="64" /></td>
<?php endif; ?>
				</tr>
<?php endforeach; ?>
			</tbody>
		</table>