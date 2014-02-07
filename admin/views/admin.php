<style type="text/css">th>label{font-size:0.8em;}</style>

<div id="wpml-settings" class="wrap">
	<h2><?php _e( 'Plugin Options', 'wpml' ); ?></h2>
	
	<?php if ( isset( $this->msg_settings ) && '' != $this->msg_settings ) { ?>
	<div id="setting-error-settings_updated" class="updated settings-error"> 
		<p><strong><?php echo $this->msg_settings; ?></strong></p>
	</div>
	<?php } ?>

	<?php //if ( ! $this->wpml_get_api_key() ) $this->wpml_activate_notice( null ); ?>

	<div id="wpml-tabs">

		<form method="post">

			<ul>
			    <li><a href="#fragment-1"><h4><span class="ui-icon ui-icon-gear"></span> <?php _e( 'TMDb API', 'wpml' ); ?></h4></a></li>
			    <li><a href="#fragment-2"><h4><span class="ui-icon ui-icon-wrench"></span> <?php _e( 'WPMovieLibrary', 'wpml' ); ?></h4></a></li>
			    <li><a href="#fragment-3"><h4><span class="ui-icon ui-icon-power"></span> <?php _e( 'Deactivate/Uninstall', 'wpml' ); ?></h4></a></li>
			    <li style="float:right"><a href="#fragment-4"><h4><span class="ui-icon ui-icon-closethick"></span> <?php _e( 'Restore', 'wpml' ); ?></h4></a></li>
			</ul>

			<div id="fragment-1">
				<table class="form-table wpml-settings">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="APIKey"><?php _e( 'API Key', 'wpml' ); ?></label>
							</th>
							<td>
								<input id="APIKey" type="text" name="tmdb_data[tmdb][APIKey]" value="<?php echo ( $this->wpml_get_api_key() ? $this->wpml_get_api_key() : '' ); ?>" size="40" maxlength="32" />
								<input id="APIKey_check" type="button" name="APIKey_check" class="button button-secondary button-small" value="<?php _e( 'Check API Key', 'wpml' ); ?>" />
								<p class="description"><?php _e( 'You need a valid TMDb API key in order to fetch informations on the movies you add to WPMovieLibrary. You can get an individual API key by registering on <a href="https://www.themoviedb.org/">TheMovieDB</a>. If you don&rsquo;t want to get your own API Key, WPMovieLibrary will use a dummy, more restricted API. <a href="http://tmdb.caercam.org/">Learn more about the dummy API</a>.', 'wpml' ); ?></p>
								<label><input type="radio" name="tmdb_data[tmdb][dummy]" value="1" <?php checked( $this->wpml_o('tmdb-settings-dummy'), 1 ); ?>/> <?php _e( 'Use dummy API', 'wpml' ); ?></label>
								<label><input type="radio" name="tmdb_data[tmdb][dummy]" value="0" <?php checked( $this->wpml_o('tmdb-settings-dummy'), 0 ); ?>/> <?php _e( 'Don&rsquo;t', 'wpml' ); ?></label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="lang"><?php _e( 'API Language', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="lang" name="tmdb_data[tmdb][lang]">
									<option value="en" <?php selected( $this->wpml_o('tmdb-settings-lang'), 'en' ); ?>><?php _e( 'English', 'wpml' ); ?></option>
									<option value="fr" <?php selected( $this->wpml_o('tmdb-settings-lang'), 'fr' ); ?>><?php _e( 'French', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'Default language to use when fetching informations from TMDb. Default is english. You can always change this manually when add a new movie.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="scheme"><?php _e( 'API Scheme', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="scheme" name="tmdb_data[tmdb][scheme]">
									<option value="http" <?php selected( $this->wpml_o('tmdb-settings-scheme'), 'http' ); ?>><?php _e( 'HTTP', 'wpml' ); ?></option>
									<option value="https" <?php selected( $this->wpml_o('tmdb-settings-scheme'), 'https' ); ?>><?php _e( 'HTTPS', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'Default scheme used to contact TMDb API. Default is HTTPS.', 'wpml' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div id="fragment-2">

				<!-- WPML Poster Settings -->
				<h4><?php _e( 'Poster Settings', 'wpml' ); ?></h4>
				<table class="form-table wpml-settings">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="poster_size"><?php _e( 'Posters Default Size', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="poster_size" name="tmdb_data[tmdb][poster_size]">
									<option value="small" <?php selected( $this->wpml_o('tmdb-settings-poster_size'), 'small' ); ?>><?php _e( 'Small', 'wpml' ); ?></option>
									<option value="medium" <?php selected( $this->wpml_o('tmdb-settings-poster_size'), 'medium' ); ?>><?php _e( 'Medium', 'wpml' ); ?></option>
									<option value="full" <?php selected( $this->wpml_o('tmdb-settings-poster_size'), 'full' ); ?>><?php _e( 'Full', 'wpml' ); ?></option>
									<option value="original" <?php selected( $this->wpml_o('tmdb-settings-poster_size'), 'original' ); ?>><?php _e( 'Original', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'Movie Poster size. Default is TMDb&rsquo;s original size.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Add Posters As Thumbnails', 'wpml' ); ?></label>
							</th>
							<td>
								<label><input type="radio" name="tmdb_data[tmdb][poster_featured]" value="1" <?php checked( $this->wpml_o('tmdb-settings-poster_featured'), 1 ); ?>/> <?php _e( 'Use Posters as Movies Thumbnails', 'wpml' ); ?></label>
								<label><input type="radio" name="tmdb_data[tmdb][poster_featured]" value="0" <?php checked( $this->wpml_o('tmdb-settings-poster_featured'), 0 ); ?>/> <?php _e( 'Don&rsquo;t', 'wpml' ); ?></label>
								<p class="description"><?php _e( 'Using posters as movies thumbnails will automatically import new movies&rsquo; poster and set them as post featured image. This setting doesn’t affect movie import by list where posters are automatically saved and set as featured image.', 'wpml' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<!-- WPML Images Settings -->
				<h4><?php _e( 'Images Settings', 'wpml' ); ?></h4>
				<table class="form-table wpml-settings">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="images_size"><?php _e( 'Images Default Size', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="images_size" name="tmdb_data[tmdb][images_size]">
									<option value="small" <?php selected( $this->wpml_o('tmdb-settings-images_size'), 'small' ); ?>><?php _e( 'Small', 'wpml' ); ?></option>
									<option value="medium" <?php selected( $this->wpml_o('tmdb-settings-images_size'), 'medium' ); ?>><?php _e( 'Medium', 'wpml' ); ?></option>
									<option value="full" <?php selected( $this->wpml_o('tmdb-settings-images_size'), 'full' ); ?>><?php _e( 'Full', 'wpml' ); ?></option>
									<option value="original" <?php selected( $this->wpml_o('tmdb-settings-images_size'), 'original' ); ?>><?php _e( 'Original', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'Movie Poster size. Default is TMDb&rsquo;s original size.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="images_max"><?php _e( 'Maximum Images To Fetch', 'wpml' ); ?></label>
							</th>
							<td>
								<input id="images_max" type="text" name="tmdb_data[tmdb][images_max]" value="<?php echo $this->wpml_o('tmdb-settings-images_max'); ?>" size="4" maxlength="2" />
								<p class="description"><?php _e( 'Maximum amount of images to fetch. Especially useful if you activated automatic images import. Default is12, set at 0 to fetch all images.', 'wpml' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<!-- WPML Posts Settings -->
				<h4><?php _e( 'Posts Settings', 'wpml' ); ?></h4>
				<table class="form-table wpml-settings">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Show Movies in Home Page', 'wpml' ); ?></label>
							</th>
							<td>
								<label><input type="radio" name="tmdb_data[wpml][show_in_home]" value="1" <?php checked( $this->wpml_o('wpml-settings-show_in_home'), 1 ); ?>/> <?php _e( 'Show Movies', 'wpml' ); ?></label>
								<label><input type="radio" name="tmdb_data[wpml][show_in_home]" value="0" <?php checked( $this->wpml_o('wpml-settings-show_in_home'), 0 ); ?>/> <?php _e( 'Don&rsquo;t', 'wpml' ); ?></label>
								<p class="description"><?php _e( 'If enable, movies will appear among other Posts in the Home Page.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="tmdb_in_posts"><?php _e( 'Show basic movie details', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="tmdb_in_posts" name="tmdb_data[wpml][tmdb_in_posts]">
									<option value="everywhere" <?php selected( $this->wpml_o('wpml-settings-tmdb_in_posts'), 'everywhere' ); ?>><?php _e( 'Everywhere', 'wpml' ); ?></option>
									<option value="posts_only" <?php selected( $this->wpml_o('wpml-settings-tmdb_in_posts'), 'posts_only' ); ?>><?php _e( 'Only In Post Read', 'wpml' ); ?></option>
									<option value="nowhere" <?php selected( $this->wpml_o('wpml-settings-tmdb_in_posts'), 'nowhere' ); ?>><?php _e( 'Don&rsquo;t Show', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'Add details to posts&rsquo; content: director, genres, runtime…', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="default_post_tmdb"><?php _e( 'Movie details items', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="default_post_tmdb" name="tmdb_data[wpml][default_post_tmdb][]" multiple>
<?php
foreach ( $this->wpml_settings['wpml']['settings']['default_post_tmdb'] as $slug => $post_tmdb ) :
	$check = in_array( $slug, $this->wpml_o('wpml-settings-default_post_tmdb' ) ) || in_array( $post_tmdb, $this->wpml_o('wpml-settings-default_post_tmdb' ) );
?>
									<option value="<?php echo $slug; ?>" <?php selected( $check, true ); ?>><?php _e( 'Movie ' . $post_tmdb, 'wpml' ); ?></option>
<?php endforeach; ?>
								</select>
								<p class="description"><?php _e( 'Which movie details to display in posts: director, genres, runtime, rating…', 'wpml' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<!-- WPML Taxonomy Settings -->
				<h4><?php _e( 'Taxonomy Settings', 'wpml' ); ?></h4>
				<table class="form-table wpml-settings">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Enable Custom Taxonomies', 'wpml' ); ?></label>
							</th>
							<td>
								<label><input type="radio" name="tmdb_data[wpml][enable_collection]" value="1" <?php checked( $this->wpml_o('wpml-settings-enable_collection'), 1 ); ?>/> <?php _e( 'Enable Collections', 'wpml' ); ?></label>
								<label><input type="radio" name="tmdb_data[wpml][enable_collection]" value="0" <?php checked( $this->wpml_o('wpml-settings-enable_collection'), 0 ); ?>/> <?php _e( 'Don&rsquo;t', 'wpml' ); ?></label>
								<br />

								<label><input type="radio" name="tmdb_data[wpml][enable_genre]" value="1" <?php checked( $this->wpml_o('wpml-settings-enable_genre'), 1 ); ?>/> <?php _e( 'Enable Genres', 'wpml' ); ?></label>
								<label><input type="radio" name="tmdb_data[wpml][enable_genre]" value="0" <?php checked( $this->wpml_o('wpml-settings-enable_genre'), 0 ); ?>/> <?php _e( 'Don&rsquo;t', 'wpml' ); ?></label>
								<br />

								<label><input type="radio" name="tmdb_data[wpml][enable_actor]" value="1" <?php checked( $this->wpml_o('wpml-settings-enable_actor'), 1 ); ?>/> <?php _e( 'Enable Actors', 'wpml' ); ?></label>
								<label><input type="radio" name="tmdb_data[wpml][enable_actor]" value="0" <?php checked( $this->wpml_o('wpml-settings-enable_actor'), 0 ); ?>/> <?php _e( 'Don&rsquo;t', 'wpml' ); ?></label>

								<p class="description"><?php _e( 'Enable Custom Taxonomies to group movies. If enabled, three new Taxonomie will be active to help sort your movies: Collections, Actors and Genres. To learn more about WPML Taxonomies please refer to the documentation.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Automatic Taxonomy', 'wpml' ); ?></label>
							</th>
							<td>
								<label><input type="radio" name="tmdb_data[wpml][taxonomy_autocomplete]" value="1" <?php checked( $this->wpml_o('wpml-settings-taxonomy_autocomplete'), 1 ); ?>/> <?php _e( 'Add Taxonomy on import', 'wpml' ); ?></label>
								<label><input type="radio" name="tmdb_data[wpml][taxonomy_autocomplete]" value="0" <?php checked( $this->wpml_o('wpml-settings-taxonomy_autocomplete'), 0 ); ?>/> <?php _e( 'Don&rsquo;t', 'wpml' ); ?></label>
								<p class="description"><?php _e( 'Automatically add custom taxonomies when adding/importing movies. If enabled, each added/imported movie will be automatically added to the collection corresponding to the director. Actors and Genres tags will be filled automatically as well. Unexisting Taxonomies will be created. Ex: adding the movie <em>Fight Club</em> will add the movie to a "David Fincher" collection and the movie will be tagged with tags like "Edward Norton", "Brad Pitt", "Drama"...', 'wpml' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<!-- WPML Cache Settings -->
				<h4><?php _e( 'Cache Settings', 'wpml' ); ?></h4>
				<table class="form-table wpml-settings">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Enable Caching', 'wpml' ); ?></label>
							</th>
							<td>
								<label><input type="radio" name="tmdb_data[tmdb][caching]" value="1" <?php checked( $this->wpml_o('tmdb-settings-caching'), 1 ); ?>/> <?php _e( 'Enable caching', 'wpml' ); ?></label>
								<label><input type="radio" name="tmdb_data[tmdb][caching]" value="0" <?php checked( $this->wpml_o('tmdb-settings-caching'), 0 ); ?>/> <?php _e( 'Don&rsquo;t', 'wpml' ); ?></label>
								<p class="description"><?php _e( 'When enabled, WPML will store for a variable time the data fetched from TMDb. This prevents WPML from generating excessive, useless duplicate queries to the API. This is especially useful if you’re using the dummy API. <a href="http://www.caercam.org/wpmovielibrary/">Learn more about WPML Caching</a>', 'wpml' ); ?></p>
								<br />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Caching Time', 'wpml' ); ?></label>
							</th>
							<td>
								<input type="text" name="tmdb_data[tmdb][caching_time]" value="<?php echo $this->wpml_o('tmdb-settings-caching_time'); ?>" size="4" maxlength="2" />
								<p class="description"><?php _e( 'Time of validity for Cached data, in days.', 'wpml' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div id="fragment-3">

				<!-- WPML Deactivation -->
				<h4><?php _e( 'Deactivation Options', 'wpml' ); ?></h4>
				<p class="description"><?php _e( 'When deactivated or uninstalled, WPML can adopt specific behaviors to handle the contents created by its use: cached data, movies, images, collections… Default behavior is to conserve everything as it is when WPML is simply deactivated, and to convert contents to standard WordPress contents when uninstalled. Learn more on the deactive/uninstall options on <a href="http://www.caercam.org/wpmovielibrary/documentation.html#deactivate-uninstall">WPML Docs</a>, especially about content restoration after uninstallation.', 'wpml' ); ?></p>
				<table class="form-table wpml-settings">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for=""><?php _e( 'Movie Post Type', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="deactivate_movies" name="tmdb_data[wpml][deactivate][movies]">
									<option value="conserve" <?php selected( $this->wpml_o('wpml-settings-deactivate-movies'), 'conserve' ); ?>><?php _e( 'Conserve (recommended)', 'wpml' ); ?></option>
									<option value="convert" <?php selected( $this->wpml_o('wpml-settings-deactivate-movies'), 'convert' ); ?>><?php _e( 'Convert to Posts', 'wpml' ); ?></option>
									<option value="remove" <?php selected( $this->wpml_o('wpml-settings-deactivate-movies'), 'remove' ); ?>><?php _e( 'Delete (irreversible)', 'wpml' ); ?></option>
									<option value="delete" <?php selected( $this->wpml_o('wpml-settings-deactivate-movies'), 'delete' ); ?>><?php _e( 'Delete Completely (irreversible)', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'How to handle Movies when WPML is deactivated.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Collections Taxonomy', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="deactivate_collections" name="tmdb_data[wpml][deactivate][collections]">
									<option value="conserve" <?php selected( $this->wpml_o('wpml-settings-deactivate-collections'), 'conserve' ); ?>><?php _e( 'Conserve (recommended)', 'wpml' ); ?></option>
									<option value="convert" <?php selected( $this->wpml_o('wpml-settings-deactivate-collections'), 'convert' ); ?>><?php _e( 'Convert to Category', 'wpml' ); ?></option>
									<option value="remove" <?php selected( $this->wpml_o('wpml-settings-deactivate-collections'), 'remove' ); ?>><?php _e( 'Delete (irreversible)', 'wpml' ); ?></option>
									<option value="delete" <?php selected( $this->wpml_o('wpml-settings-deactivate-collections'), 'delete' ); ?>><?php _e( 'Delete Completely (irreversible)', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'How to handle Collections Taxonomy when WPML is deactivated.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Genres Taxonomy', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="deactivate_genres" name="tmdb_data[wpml][deactivate][genres]">
									<option value="conserve" <?php selected( $this->wpml_o('wpml-settings-deactivate-genres'), 'conserve' ); ?>><?php _e( 'Conserve (recommended)', 'wpml' ); ?></option>
									<option value="convert" <?php selected( $this->wpml_o('wpml-settings-deactivate-genres'), 'convert' ); ?>><?php _e( 'Convert to Tags', 'wpml' ); ?></option>
									<option value="remove" <?php selected( $this->wpml_o('wpml-settings-deactivate-genres'), 'remove' ); ?>><?php _e( 'Delete (irreversible)', 'wpml' ); ?></option>
									<option value="delete" <?php selected( $this->wpml_o('wpml-settings-deactivate-genres'), 'delete' ); ?>><?php _e( 'Delete Completely (irreversible)', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'How to handle Genres Taxonomy when WPML is deactivated.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Actors Taxonomy', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="deactivate_actors" name="tmdb_data[wpml][deactivate][actors]">
									<option value="conserve" <?php selected( $this->wpml_o('wpml-settings-deactivate-actors'), 'conserve' ); ?>><?php _e( 'Conserve (recommended)', 'wpml' ); ?></option>
									<option value="convert" <?php selected( $this->wpml_o('wpml-settings-deactivate-actors'), 'convert' ); ?>><?php _e( 'Convert to Tags', 'wpml' ); ?></option>
									<option value="remove" <?php selected( $this->wpml_o('wpml-settings-deactivate-actors'), 'remove' ); ?>><?php _e( 'Delete (irreversible)', 'wpml' ); ?></option>
									<option value="delete" <?php selected( $this->wpml_o('wpml-settings-deactivate-actors'), 'delete' ); ?>><?php _e( 'Delete Completely (irreversible)', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'How to handle Actors Taxonomy when WPML is deactivated.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Cache', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="deactivate_cache" name="tmdb_data[wpml][deactivate][cache]">
									<option value="conserve" <?php selected( $this->wpml_o('wpml-settings-deactivate-cache'), 'conserve' ); ?>><?php _e( 'Conserve', 'wpml' ); ?></option>
									<option value="empty" <?php selected( $this->wpml_o('wpml-settings-deactivate-cache'), 'empty' ); ?>><?php _e( 'Empty (recommended)', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'How to handle Cached data when WPML is deactivated.', 'wpml' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<!-- WPML Uninstall -->
				<h4><?php _e( 'Uninstall Options', 'wpml' ); ?></h4>
				<table class="form-table wpml-settings">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for=""><?php _e( 'Movie Post Type', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="uninstall_movies" name="tmdb_data[wpml][uninstall][movies]">
									<option value="conserve" <?php selected( $this->wpml_o('wpml-settings-uninstall-movies'), 'conserve' ); ?>><?php _e( 'Conserve', 'wpml' ); ?></option>
									<option value="convert" <?php selected( $this->wpml_o('wpml-settings-uninstall-movies'), 'convert' ); ?>><?php _e( 'Convert to Posts (recommended)', 'wpml' ); ?></option>
									<option value="remove" <?php selected( $this->wpml_o('wpml-settings-uninstall-actors'), 'remove' ); ?>><?php _e( 'Delete (irreversible)', 'wpml' ); ?></option>
									<option value="delete" <?php selected( $this->wpml_o('wpml-settings-uninstall-actors'), 'delete' ); ?>><?php _e( 'Delete Completely (irreversible)', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'How to handle Movies when WPML is uninstalled.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Collections Taxonomy', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="uninstall_collections" name="tmdb_data[wpml][uninstall][collections]">
									<option value="conserve" <?php selected( $this->wpml_o('wpml-settings-uninstall-collections'), 'conserve' ); ?>><?php _e( 'Conserve', 'wpml' ); ?></option>
									<option value="convert" <?php selected( $this->wpml_o('wpml-settings-uninstall-collections'), 'convert' ); ?>><?php _e( 'Convert to Category (recommended)', 'wpml' ); ?></option>
									<option value="remove" <?php selected( $this->wpml_o('wpml-settings-uninstall-actors'), 'remove' ); ?>><?php _e( 'Delete (irreversible)', 'wpml' ); ?></option>
									<option value="delete" <?php selected( $this->wpml_o('wpml-settings-uninstall-actors'), 'delete' ); ?>><?php _e( 'Delete Completely (irreversible)', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'How to handle Collections Taxonomy when WPML is uninstalled.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Genres Taxonomy', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="uninstall_genres" name="tmdb_data[wpml][uninstall][genres]">
									<option value="conserve" <?php selected( $this->wpml_o('wpml-settings-uninstall-genres'), 'conserve' ); ?>><?php _e( 'Conserve', 'wpml' ); ?></option>
									<option value="convert" <?php selected( $this->wpml_o('wpml-settings-uninstall-genres'), 'convert' ); ?>><?php _e( 'Convert to Tags (recommended)', 'wpml' ); ?></option>
									<option value="remove" <?php selected( $this->wpml_o('wpml-settings-uninstall-actors'), 'remove' ); ?>><?php _e( 'Delete (irreversible)', 'wpml' ); ?></option>
									<option value="delete" <?php selected( $this->wpml_o('wpml-settings-uninstall-actors'), 'delete' ); ?>><?php _e( 'Delete Completely (irreversible)', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'How to handle Genres Taxonomy when WPML is uninstalled.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Actors Taxonomy', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="uninstall_actors" name="tmdb_data[wpml][uninstall][actors]">
									<option value="conserve" <?php selected( $this->wpml_o('wpml-settings-uninstall-actors'), 'conserve' ); ?>><?php _e( 'Conserve', 'wpml' ); ?></option>
									<option value="convert" <?php selected( $this->wpml_o('wpml-settings-uninstall-actors'), 'convert' ); ?>><?php _e( 'Convert to Tags (recommended)', 'wpml' ); ?></option>
									<option value="remove" <?php selected( $this->wpml_o('wpml-settings-uninstall-actors'), 'remove' ); ?>><?php _e( 'Delete (irreversible)', 'wpml' ); ?></option>
									<option value="delete" <?php selected( $this->wpml_o('wpml-settings-uninstall-actors'), 'delete' ); ?>><?php _e( 'Delete Completely (irreversible)', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'How to handle Actors Taxonomy when WPML is uninstalled.', 'wpml' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e( 'Cache', 'wpml' ); ?></label>
							</th>
							<td>
								<select id="uninstall_cache" name="tmdb_data[wpml][uninstall][cache]">
									<option value="conserve" <?php selected( $this->wpml_o('wpml-settings-uninstall-cache'), 'conserve' ); ?>><?php _e( 'Conserve', 'wpml' ); ?></option>
									<option value="empty" <?php selected( $this->wpml_o('wpml-settings-uninstall-cache'), 'empty' ); ?>><?php _e( 'Empty (recommended)', 'wpml' ); ?></option>
								</select>
								<p class="description"><?php _e( 'How to handle Cached data when WPML is uninstalled.', 'wpml' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div id="fragment-4">

				<h4><?php _e( 'Restore Default Settings', 'wpml' ); ?></h4>
				<p class="update-nag">
					<span class="ui-icon ui-icon-alert"></span>
					<?php _e( 'You may want to restore WPMovieLibrary default settings.', 'wpml' ); ?>
					<?php _e( '<strong>Caution!</strong> Doing this you will erase permanently all your custom settings. Don&rsquo;t do this unless you are positively sure of what you&rsquo;re doing!', 'wpml' ); ?>
				</p>
				<p style="text-align:center">
					<input id="restore_default" type="submit" name="restore_default" class="button button-secondary button-large" value="Restore" />
				</p>
			</div>

			<p class="submit">
				<input type="submit" id="submit" name="submit" class="button-primary" value="Save Changes" />
			</p>

		</form>

	</div>

	<?php include_once 'help.php'; ?>

</div>