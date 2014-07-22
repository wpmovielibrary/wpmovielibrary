<div id="wpml-settings" class="wrap">

	<h2><?php esc_html_e( WPML_NAME ); ?> Settings</h2>

	<?php settings_errors(); ?>

	<div id="wpml-tabs">

		<form action="options.php" method="post">

			<ul class="wpml-tabs-nav">
			    <li class="wpml-tabs-nav<?php if ( 'api' == $_section || '' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpml_edit_settings&wpml_section=tmdb#wpml_section_tmdb' ) ?>" data-section="wpml_section=api"><h4><?php _e( 'TMDb API', WPML_SLUG ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'movies' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpml_edit_settings&wpml_section=wpml#wpml_section_wpml' ) ?>" data-section="wpml_section=movies"><h4><?php _e( 'Movies', WPML_SLUG ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'images' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpml_edit_settings&wpml_section=images#wpml_section_images' ) ?>" data-section="wpml_section=images"><h4><?php _e( 'Images', WPML_SLUG ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'taxonomies' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpml_edit_settings&wpml_section=taxonomies#wpml_section_taxonomies' ) ?>" data-section="wpml_section=taxonomies"><h4><?php _e( 'Taxonomies', WPML_SLUG ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'deactivate' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpml_edit_settings&wpml_section=deactivate#wpml_section_deactivate' ) ?>" data-section="wpml_section=deactivate"><h4><?php _e( 'Deactivate', WPML_SLUG ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'uninstall' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpml_edit_settings&wpml_section=uninstall#wpml_section_uninstall' ) ?>" data-section="wpml_section=uninstall"><h4><?php _e( 'Uninstall', WPML_SLUG ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'maintenance' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpml_edit_settings&wpml_section=maintenance#wpml_section_maintenance' ) ?>" data-section="wpml_section=maintenance"><h4><?php _e( 'Maintenance', WPML_SLUG ); ?></h4></a></li>
			</ul>

<?php settings_fields( 'wpml_edit_settings' ); ?>

			<div class="wpml-tabs-panels">

<?php do_settings_sections( 'wpml_settings' ); ?>

				<h3><?php _e( 'Maintenance', WPML_SLUG ); ?></h3>
				<span id="wpml_section_maintenance"></span>
				<table class="form-table" style="display: none;">
					<tbody>
						<tr>
							<th scope="row"><?php _e( 'Restore Default Settings', WPML_SLUG ); ?></th>
							<td>
								<p class="description"><?php _e( 'You may want to restore WPMovieLibrary default settings.', WPML_SLUG ); ?></p>
								<p class="description"><?php _e( '<strong>Caution!</strong> Doing this you will erase permanently all your custom settings. Don&rsquo;t do this unless you are positively sure of what you&rsquo;re doing!', WPML_SLUG ); ?></p>
								<p style="text-align:center">
									<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wpml_edit_settings&wpml_section=maintenance&wpml_restore_default=true' ), 'wpml-restore-default-settings', '_nonce') ?>" class="button button-secondary"><?php _e( 'Restore', WPML_SLUG ) ?></a>
								</p>
								
							</td>
						</tr>
						<!-- Cache deactivated until WPML 1.1.0 -->
						<!--<tr>
							<th scope="row"><?php _e( 'Empty Cache', WPML_SLUG ); ?></th>
							<td>
								<p class="description"><?php _e( 'Delete all temporarily stored data. This can solve issues like incomplete movie metadata repeatedly returned or incorrect API results.', WPML_SLUG ); ?></p>
								<p style="text-align:center">
									<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wpml_edit_settings&wpml_section=maintenance&wpml_empty_cache=true' ), 'wpml-empty-cache', '_nonce') ?>" class="button button-secondary"><?php _e( 'Empty cache', WPML_SLUG ) ?></a>
								</p>
							</td>
						</tr>-->
					</tbody>
				</table>

			</div>

			<p class="submit">
				<input type="hidden" name="<?php echo WPML_SETTINGS_SLUG . '[' . WPML_SETTINGS_REVISION_NAME . ']' ?>" id="<?php echo WPML_SETTINGS_REVISION_NAME ?>" value="<?php echo WPML_SETTINGS_REVISION ?>" />
				<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
			</p>

		</form>

	</div>

	<?php include_once( plugin_dir_path( __FILE__ ) . '../../common/views/help.php' ); ?>

</div> <!-- .wrap -->