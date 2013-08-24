<div id="wpml-import" class="wrap">
	<h2><?php _e( 'Movies Import', 'wpml' ); ?></h2>

	<div id="wpml-tabs">

		<ul>
			<li><a href="#fragment-1"><h4><span class="ui-icon ui-icon-folder-open"></span> <?php _e( 'Imported Movies', 'wpml' ); ?></h4></a></li>
			<li><a href="#fragment-2"><h4><span class="ui-icon ui-icon-plus"></span> <?php _e( 'Import New Movies', 'wpml' ); ?></h4></a></li>
		</ul>

		<div id="fragment-1">

			<p><?php _e( 'Here are the movies you previously updated but didn’t save. You can save them, edit them individually or apply bulk actions. Posters are automatically saved and set as featured images, but images are not. Use the bulk action to import, but be aware that it can take some time if you select a lot of movies. Don’t forget to save your imports when you’re done!', 'wpml' ); ?></p>

			<form method="post">

<?php $this->wpml_import_movie_list(); ?>

				<p style="text-align:right">
					<input type="button" id="wpml_empty" name="wpml_empty" class="button button-secondary button-large" value="<?php _e( 'Empty All', 'wpml' ); ?>" />
					<input type="submit" id="wpml_save_imported" name="wpml_save_imported" class="button button-primary button-large" value="<?php _e( 'Save Movies', 'wpml' ); ?>" />
				</p>

			</form>

		</div>

		<div id="fragment-2">
			<form method="post">
				<table class="form-table wpml-settings">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="wpml_import_list"><?php _e( 'Input a list of movies to search and import separated by commas:', 'wpml' ); ?></label>
								<p><em><?php _e( 'Titles don’t have to be exact, but try to be specific to get better results.<br /> Ex: interview with the vampire, Se7en, Twelve Monkeys, joe black, fight club, snatch, babel, inglourious basterds', 'wpml' ); ?></em></p>
							</th>
							<td>
								<textarea id="wpml_import_list" name="wpml_import_list" placeholder="List of movie titles separated by commas"></textarea>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"></th>
							<td style="text-align:right">
								<input type="submit" id="wpml_importer" name="wpml_importer" class="button button-secondary button-large" value="<?php _e( 'Import Movies', 'wpml' ); ?>" />
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>

	</div>

	<?php include_once $this->plugin_path . 'views/help.php'; ?>

</div>