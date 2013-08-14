<?php if ( false === $this->wpml_get_api_key() ) :
	_e( 'You need a valid <acronym title="TheMovieDB">TMDb</acronym> API key to start adding your movies. Go to the <a href="https://www.themoviedb.org/">WPMovieLibrary Settings page</a> to add your API key.', 'wpml' );
	return;
endif;
?>
		<select id="tmdb_search_type" name="tmdb_search_type">
			<option value="title" selected="selected"><?php _e( 'Movie Title', 'wp_movie_library' ); ?></option>
			<option value="id"><?php _e( 'TMDb ID', 'wp_movie_library' ); ?></option>
		</select>
		<input id="tmdb_query" type="text" name="tmdb_query" value="" />
		<input id="tmdb_search" name="tmdb_search" type="button" class="button button-secondary button-small" value="<?php _e( 'Fetch data', 'wp_movie_library' ); ?>" />

		<div id="tmdb_data">
		</div>

		<table class="list-table"<?php echo ( ! count( $value ) ? ' style="display:none"' : '' ); ?>>
			<thead>
				<tr>
					<th class="left"><?php _e( 'Type', 'wp_movie_library' ); ?></th>
					<th><?php _e( 'Value', 'wp_movie_library' ); ?></th>
				</tr>
			</thead>
			<tbody>
<?php foreach ( $this->wpml_options['meta_data'] as $slug => $meta ) : ?>
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