
		<div id="wpml-tmdb" class="wpml-tmdb">

			<p><strong><?php _e( 'Find movie on TMDb:', WPML_SLUG ); ?></strong></p>

			<div>
				<?php WPML_Utils::_nonce_field( 'search-movies' ) ?>
				<select id="tmdb_search_lang" name="wpml[lang]" onchange="wpml_edit_meta.lang=this.value;">
<?php foreach ( WPML_Settings::get_available_languages() as $code => $lang ) : ?>
					<option value="<?php echo $code ?>" <?php selected( WPML_Settings::tmdb__lang(), $code ); ?>><?php echo $lang ?></option>
<?php endforeach; ?>
				</select>
				<select id="tmdb_search_type" name="wpml[tmdb_search_type]">
					<option value="title" selected="selected"><?php _e( 'Movie Title', WPML_SLUG ); ?></option>
					<option value="id"><?php _e( 'TMDb ID', WPML_SLUG ); ?></option>
				</select>
				<input id="tmdb_query" type="text" name="wpml[tmdb_query]" value="" size="40" maxlength="32" />
				<a id="tmdb_search" name="wpml[tmdb_search]" href="<?php echo get_edit_post_link() ?>&amp;wpml_auto_fetch=1" class="button button-secondary"><?php _e( 'Search', WPML_SLUG ); ?></a>
				<span class="spinner"></span>
				<a id="tmdb_empty" name="wpml[tmdb_empty]" type="submit" class="button button-secondary button-empty hide-if-no-js"><?php _e( 'Empty Results', WPML_SLUG ); ?></a>
			</div>

			<div id="wpml_status"><?php echo $status; ?></div>
			<div style="clear:both"></div>

<?php if ( ! is_null( $select ) ) : ?>
			<div id="tmdb_data" style="display:block">
<?php foreach ( $select as $movie ) : ?>

				<div class="tmdb_select_movie">
					<a id="tmdb_<?php echo $movie['id'] ?>" href="<?php echo wp_nonce_url( get_edit_post_link( get_the_ID() ) . "&amp;wpml_search_movie=1&amp;search_by=id&amp;search_query={$movie['id']}", 'search-movies' ) ?>" onclick="wpml_edit_meta.get( <?php echo $movie['id'] ?> ); return false;">
						<img src="<?php echo $movie['poster'] ?>" alt="<?php echo $movie['title'] ?>" />
						<em><?php echo $movie['title'] ?></em>
					</a>
					<input type="hidden" value='<?php echo $movie['json'] ?>' />
				</div>
<?php endforeach; ?>
			</div>
<?php else: ?>
			<div id="tmdb_data"></div>
<?php endif; ?>

			<div id="tmdb_data"></div>
			<input type="hidden" id="tmdb_data_tmdb_id" name="tmdb_data[tmdb_id]" class="hide-if-js hide-if-no-js" value="<?php echo $value['tmdb_id'] ?>" />
			<input type="hidden" id="wpml_actor_limit" class="hide-if-js hide-if-no-js" value="<?php echo WPML_Settings::taxonomies__actor_limit() ?>" />

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
						<td class="left"><?php _e( $meta['title'], WPML_SLUG ) ?></td>
<?php if ( isset( $meta['type'] ) && 'textarea' == $meta['type'] ) : ?>
						<td>
							<textarea id="tmdb_data_<?php echo $slug; ?>" name="tmdb_data[<?php echo $id; ?>][<?php echo $slug; ?>]" class="tmdb_data_field" rows="6"><?php echo $_value ?></textarea>
						</td>
<?php elseif ( isset( $meta['type'] ) && in_array( $meta['type'], array( 'text', 'hidden' ) ) ) : ?>
						<td>
							<input type="<?php echo $meta['type']; ?>" id="tmdb_data_<?php echo $slug; ?>" name="tmdb_data[<?php echo $id; ?>][<?php echo $slug; ?>]" class="tmdb_data_field" value="<?php echo $_value ?>" />
						</td>
<?php endif; ?>
					</tr>
<?php endforeach; ?>
				</tbody>
			</table>
<?php endforeach; ?>

			<div style="clear:both"></div>

		</div>