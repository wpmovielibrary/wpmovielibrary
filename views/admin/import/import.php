<div id="wpmoly-import" class="wrap">
	<h2><?php _e( 'Movies Import', 'wpmovielibrary' ); ?></h2>

	<div class="no-js-alert hide-if-js"><?php _e( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', 'wpmovielibrary' ); ?></div>

	<div id="wpmoly-tabs">

		<div id="wpmoly_status"></div>

		<ul class="wpmoly-tabs-nav">
		    <li class="wpmoly-tabs-nav<?php if ( '' == $_section || 'wpmoly_imported' == $_section ) echo ' active'; ?>"><a id="_wpmoly_imported" href="" data-section="wpmoly_section=wpmoly_imported"><h4><?php _e( 'Imported Movies', 'wpmovielibrary' ); ?><span><?php echo $_imported; ?></span></h4></a></li>
		    <li class="wpmoly-tabs-nav<?php if ( 'wpmoly_import_queue' == $_section ) echo ' active'; ?> hide-if-no-js"><a id="_wpmoly_import_queue" href="" data-section="wpmoly_section=wpmoly_import_queue"><h4><?php _e( 'Import Queue', 'wpmovielibrary' ); ?><span><?php echo $_queued; ?></span></h4></a></li>
		    <li class="wpmoly-tabs-nav<?php if ( 'wpmoly_import' == $_section ) echo ' active'; ?>"><a id="_wpmoly_import" href="" data-section="wpmoly_section=wpmoly_import"><h4><?php _e( 'Import New Movies', 'wpmovielibrary' ); ?></h4></a></li>
		</ul>

		<div class="wpmoly-tabs-panels">

			<div id="wpmoly_imported" class="form-table hide-if-js<?php if ( '' == $_section || 'wpmoly_imported' == $_section ) echo ' active'; ?>">

				<div id="import-intro">
					<p><?php _e( 'Here are the movies you previously updated but didn’t save. You can save them, edit them individually or apply bulk actions. Posters are automatically saved and set as featured images, but images are not. Use the bulk action to import, but be aware that it can take some time if you select a lot of movies. Don’t forget to save your imports when you’re done!', 'wpmovielibrary' ); ?></p>
				</div>

				<?php WPMOLY_Import::display_import_movie_list(); ?>

				<form method="post" id="meta_data_form">

					<div id="meta_data" style="display:none"></div>

					<p style="text-align:right">
						<?php wpmoly_nonce_field( 'save-imported-movies', $referer = false ) ?>
						<input type="hidden" id="wpmoly_imported_ids" name="wpmoly_imported_ids" value="" />
						<input type="button" id="wpmoly_empty" name="wpmoly_empty" class="button button-secondary button-large" value="<?php _e( 'Empty All', 'wpmovielibrary' ); ?>" />
						<input type="submit" id="wpmoly_save_imported" name="wpmoly_save_imported" class="button button-primary button-large" value="<?php _e( 'Save Movies', 'wpmovielibrary' ); ?>" />
					</p>

				</form>

			</div>

			<div id="wpmoly_import_queue" class="form-table hide-if-no-js<?php if ( 'wpmoly_import_queue' == $_section ) echo ' active'; ?>">

				<form method="post">
					<input type="hidden" name="page" value="import" />
					<div class="tablenav top hide-if-no-js">
						<div class="alignleft actions bulkactions">
							<select name="queue-action">
								<option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'wpmovielibrary' ); ?></option>
									<option value="delete"><?php _e( 'Delete Movie', 'wpmovielibrary' ); ?></option>
									<option value="dequeue"><?php _e( 'Dequeue Movie', 'wpmovielibrary' ); ?></option>
							</select>
							<input type="submit" name="" id="do-queue-action" class="button action" value="<?php _e( 'Apply', 'wpmovielibrary' ); ?>" onclick="wpmoly_movies_queue.do(); return false;">
							<span class="spinner"></span>
						</div>
						<div class="tablenav-pages"><span class="displaying-num"><?php if ( $_queued ) printf( _n( '1 item', '%s items', $_queued ), number_format_i18n( $_queued ) ); else _e( 'No item', 'wpmovielibrary' ); ?></span></div>
					</div>
					<div id="wpmoly-queued-list-header" class="hide-if-no-js">
						<div class="check-column"><input type="checkbox" id="post_all" value="" onclick="wpmoly_queue_utils.toggle_inputs();" /></div>
							<div class="movietitle column-movietitle"><?php _e( 'Title', 'wpmovielibrary' ) ?></div>
							<div class="director column-director"><?php _e( 'Director', 'wpmovielibrary' ) ?></div>
							<div class="actions column-actions"><?php _e( 'Actions', 'wpmovielibrary' ) ?></div>
							<div class="status column-status"><?php _e( 'Status', 'wpmovielibrary' ) ?></div>
					</div>

					<?php WPMOLY_Queue::display_queued_movie_list(); ?>
				</form>

				<form method="post" action="">

					<p style="text-align:right">
						<input type="submit" id="wpmoly_import_queued" name="wpmoly_import_queued" class="button button-primary button-large" value="<?php _e( 'Import Queued Movies', 'wpmovielibrary' ); ?>" onclick="wpmoly_movies_queue.import(); return false;" />
						<input type="hidden" id="queue_progress_value" value="0" />
						<div id="queue_progress_block">
							<div id="queue_progressbar"><div id="queue_progress"></div></div>
							<div id="queue_status"><?php printf( '<span id="_queued_imported">%d</span> %s <span id="_queued_left">%d</span> %s', 0, __( 'of', 'wpmovielibrary' ), 0, __( 'imported', 'wpmovielibrary' ) ); ?></div>
							<div id="queue_status_message"></div>
						</div>
					</p>

				</form>

			</div>

			<div id="wpmoly_import" class="form-table hide-if-js<?php if ( 'wpmoly_import' == $_section ) echo ' active'; ?>">

				<form method="post" action="">

					<table class="form-table wpmoly-settings">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="wpmoly_import_list"><?php _e( 'Input a list of movies to search and import separated by commas:', 'wpmovielibrary' ); ?></label>
									<p><em><?php _e( 'Titles don’t have to be exact, but try to be specific to get better results.<br /> Ex: interview with the vampire, Se7en, Twelve Monkeys, joe black, fight club, snatch, babel, inglourious basterds', 'wpmovielibrary' ); ?></em></p>
								</th>
								<td>
									<textarea id="wpmoly_import_list" name="wpmoly_import_list" placeholder="<?php _e( 'List of movie titles separated by commas', 'wpmovielibrary' ) ?>"></textarea>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"></th>
								<td style="text-align:right">
									<?php wpmoly_nonce_field( 'import-movies-list', $referer = false ) ?>
									<span class="spinner"></span>
									<input type="submit" id="wpmoly_importer" name="wpmoly_importer" class="button button-secondary button-large" value="<?php _e( 'Import Movies', 'wpmovielibrary' ); ?>" onclick="wpmoly_import_movies.import(); return false;" />
								</td>
							</tr>
						</tbody>
					</table>

				</form>

				<?php wpmoly_nonce_field( 'imported-movies', $referer = false ) ?>
				<?php wpmoly_nonce_field( 'search-movies', $referer = false ) ?>
				<?php wpmoly_nonce_field( 'enqueue-movies', $referer = false ) ?>
				<?php wpmoly_nonce_field( 'delete-movies', $referer = false ) ?>
				<?php wpmoly_nonce_field( 'queued-movies', $referer = false ) ?>
				<?php wpmoly_nonce_field( 'dequeue-movies', $referer = false ) ?>
				<?php wpmoly_nonce_field( 'import-queued-movies', $referer = false ) ?>

			</div>

		</div>

	</div>

</div>