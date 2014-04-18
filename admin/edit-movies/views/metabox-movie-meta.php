<?php if ( false === WPML_Settings::wpml__apikey() && false === WPML_Settings::wpml__dummy() ) :
	_e( 'You need a valid <acronym title="TheMovieDB">TMDb</acronym> API key to start adding your movies. Go to the <a href="https://www.themoviedb.org/">WPMovieLibrary Settings page</a> to add your API key.', 'wpml' );
	return;
endif;
?>
		<div class="hide-if-js"><?php WPML_Utils::admin_notice( __( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', WPML_SLUG ), 'error' ); ?></div>

		<div id="wpml-tmdb" class="wpml-tmdb hide-if-no-js">
<?php if ( true === WPML_Settings::tmdb__dummy() ) : ?>
			<div class="updated"><p><em><?php printf( __( 'WPMovieLibrary is using the dummy TMDb API; add your valid API key to the <a href="%s">Settings Page</a> or <a href="http://tmdb.caercam.org/">Learn more</a> about the dummy API.', 'wpml' ), admin_url( 'edit.php?post_type=movie&page=settings' ) ); ?></em></p></div>
<?php endif; ?>
			<p><strong><?php _e( 'Find movie on TMDb:', 'wpml' ); ?></strong></p>

			<div>
				<select id="tmdb_search_lang" name="wpml[lang]">
					<option value="en" <?php selected( WPML_Settings::tmdb__lang(), 'en' ); ?>><?php _e( 'English', 'wpml' ); ?></option>
					<option value="fr" <?php selected( WPML_Settings::tmdb__lang(), 'fr' ); ?>><?php _e( 'French', 'wpml' ); ?></option>
				</select>
				<select id="tmdb_search_type" name="wpml[tmdb_search_type]">
					<option value="title" selected="selected"><?php _e( 'Movie Title', 'wpml' ); ?></option>
					<option value="id"><?php _e( 'TMDb ID', 'wpml' ); ?></option>
				</select>
				<input id="tmdb_query" type="text" name="wpml[tmdb_query]" value="" size="40" maxlength="32" />
				<a id="tmdb_search" name="wpml[tmdb_search]" href="<?php echo get_edit_post_link() ?>&amp;wpml_auto_fetch=1" class="button button-secondary"><?php _e( 'Fetch data', 'wpml' ); ?></a>
				<span class="spinner"></span>
				<input id="tmdb_empty" name="wpml[tmdb_empty]" type="submit" class="button button-secondary button-empty hide-if-no-js" value="<?php _e( 'Empty Results', 'wpml' ); ?>" />
			</div>

			<div id="tmdb_status"></div>
			<div style="clear:both"></div>

			<div id="tmdb_data"></div>
			<input type="text" id="tmdb_data_tmdb_id" name="tmdb_data[tmdb_id]" class="hide-if-js hide-if-no-js" value="<?php echo $value['tmdb_id'] ?>" />

<?php foreach ( WPML_Settings::get_supported_movie_meta( $type = null, false ) as $id => $box ) : ?>
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
							<textarea id="tmdb_data_<?php echo $slug; ?>" name="tmdb_data[<?php echo $id; ?>][<?php echo $slug; ?>]" class="tmdb_data_field" rows="6"><?php echo $_value ?></textarea>
						</td>
<?php elseif ( isset( $meta['type'] ) && in_array( $meta['type'], array( 'text', 'hidden' ) ) ) : ?>
						<td>
							<input type="<?php echo $meta['type']; ?>" id="tmdb_data_<?php echo $slug; ?>" name="tmdb_data[<?php echo $id; ?>][<?php echo $slug; ?>]" class="tmdb_data_field" value='<?php echo $_value ?>' />
						</td>
<?php endif; ?>
					</tr>
<?php endforeach; ?>
				</tbody>
			</table>
<?php endforeach; ?>

			<div style="clear:both"></div>

		</div>