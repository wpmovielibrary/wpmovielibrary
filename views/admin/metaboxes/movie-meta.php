
		<div id="wpml-tmdb" class="wpml-tmdb">

			<p><strong><?php _e( 'Find movie on TMDb:', 'wpmovielibrary' ); ?></strong></p>

			<div>
				<?php wpml_nonce_field( 'search-movies' ) ?>
				<select id="tmdb_search_lang" name="wpml[lang]" onchange="wpml_edit_meta.lang=this.value;">
<?php foreach ( $languages as $code => $lang ) : ?>
					<option value="<?php echo $code ?>" <?php selected( WPML_Settings::tmdb__lang(), $code ); ?>><?php echo $lang ?></option>
<?php endforeach; ?>
				</select>
				<select id="tmdb_search_type" name="wpml[tmdb_search_type]">
					<option value="title" selected="selected"><?php _e( 'Movie Title', 'wpmovielibrary' ); ?></option>
					<option value="id"><?php _e( 'TMDb ID', 'wpmovielibrary' ); ?></option>
				</select>
				<input id="tmdb_query" type="text" name="wpml[tmdb_query]" value="" size="40" maxlength="32" />
				<a id="tmdb_search" name="wpml[tmdb_search]" href="<?php echo get_edit_post_link() ?>&amp;wpml_auto_fetch=1" class="button button-secondary"><?php _e( 'Search', 'wpmovielibrary' ); ?></a>
				<span class="spinner"></span>
				<?php wpml_nonce_field( 'empty-movie-meta' ) ?>
				<a id="tmdb_empty" name="wpml[tmdb_empty]" type="submit" class="button button-secondary button-empty hide-if-no-js"><?php _e( 'Empty Results', 'wpmovielibrary' ); ?></a>
			</div>

			<div id="wpml_status"><?php echo $status; ?></div>
			<div style="clear:both"></div>

<?php if ( ! is_null( $select ) ) : ?>
			<div id="meta_data" style="display:block">
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
			<div id="meta_data"></div>
<?php endif; ?>

			<input type="hidden" id="meta_data_tmdb_id" name="meta_data[tmdb_id]" class="hide-if-js hide-if-no-js" value="<?php echo $metadata['tmdb_id'] ?>" />
			<input type="hidden" id="wpml_actor_limit" class="hide-if-js hide-if-no-js" value="<?php echo WPML_Settings::taxonomies__actor_limit() ?>" />
			<input type="hidden" id="wpml_poster_featured" class="hide-if-js hide-if-no-js" value="<?php echo ( 1 == WPML_Settings::images__poster_featured() ? '1' : '0' ) ?>" />

			<table class="list-table meta">
				<thead>
					<tr>
						<th class="left"><?php _e( 'Type', 'wpmovielibrary' ) ?></th>
						<th><?php _e( 'Value', 'wpmovielibrary' ) ?></th>
					</tr>
				</thead>
				<tbody>
<?php 
$cur_meta = 'meta';
foreach ( $metas as $slug => $meta ) :

	if ( $meta['group'] != $cur_meta ) :
		$cur_meta = $meta['group'];
?>
				</tbody>
			</table>

			<table class="list-table crew">
				<thead>
					<tr>
						<th class="left"><?php _e( 'Job', 'wpmovielibrary' ) ?></th>
						<th><?php _e( 'Name(s)', 'wpmovielibrary' ) ?></th>
					</tr>
				</thead>
				<tbody>
<?php
	endif;

	$_value = '';
	if ( isset( $metadata[ $slug ] ) )
		$_value = apply_filters( 'wpml_stringify_array', $metadata[ $slug ] );
?>
					<tr>
						<td class="left"><?php _e( $meta['title'], 'wpmovielibrary' ) ?></td>
<?php if ( isset( $meta['type'] ) && 'textarea' == $meta['type'] ) : ?>
						<td>
							<textarea id="meta_data_<?php echo $slug; ?>" name="meta_data[<?php echo $slug; ?>]" class="meta_data_field" rows="6"><?php echo $_value ?></textarea>
						</td>
<?php elseif ( isset( $meta['type'] ) && in_array( $meta['type'], array( 'text', 'hidden' ) ) ) : ?>
						<td>
							<input type="<?php echo $meta['type']; ?>" id="meta_data_<?php echo $slug; ?>" name="meta_data[<?php echo $slug; ?>]" class="meta_data_field" value="<?php echo $_value ?>" />
						</td>
<?php endif; ?>
					</tr>
<?php endforeach; ?>
				</tbody>
			</table>

			<div style="clear:both"></div>

		</div>