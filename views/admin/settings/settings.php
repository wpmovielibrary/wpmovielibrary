<div id="wpmoly-settings" class="wrap">

	<h2><?php esc_html_e( WPMOLY_NAME ); ?> Settings</h2>

	<?php settings_errors(); ?>

	<div id="wpmoly-tabs">

		<form action="options.php" method="post">

			<ul class="wpmoly-tabs-nav">
			    <li class="wpmoly-tabs-nav<?php if ( 'api' == $_section || '' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpmoly_edit_settings&amp;wpmoly_section=api#wpmoly_section_api' ) ?>" data-section="wpmoly_section=api"><h4><?php _e( 'API', 'wpmovielibrary' ); ?></h4></a></li>
			    <li class="wpmoly-tabs-nav<?php if ( 'movies' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpmoly_edit_settings&amp;wpmoly_section=wpmoly#wpmoly_section_wpmoly' ) ?>" data-section="wpmoly_section=wpmoly"><h4><?php _e( 'Movies', 'wpmovielibrary' ); ?></h4></a></li>
			    <li class="wpmoly-tabs-nav<?php if ( 'images' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpmoly_edit_settings&amp;wpmoly_section=images#wpmoly_section_images' ) ?>" data-section="wpmoly_section=images"><h4><?php _e( 'Images', 'wpmovielibrary' ); ?></h4></a></li>
			    <li class="wpmoly-tabs-nav<?php if ( 'taxonomies' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpmoly_edit_settings&amp;wpmoly_section=taxonomies#wpmoly_section_taxonomies' ) ?>" data-section="wpmoly_section=taxonomies"><h4><?php _e( 'Taxonomies', 'wpmovielibrary' ); ?></h4></a></li>
			    <li class="wpmoly-tabs-nav<?php if ( 'deactivate' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpmoly_edit_settings&amp;wpmoly_section=deactivate#wpmoly_section_deactivate' ) ?>" data-section="wpmoly_section=deactivate"><h4><?php _e( 'Deactivate', 'wpmovielibrary' ); ?></h4></a></li>
			    <li class="wpmoly-tabs-nav<?php if ( 'uninstall' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpmoly_edit_settings&amp;wpmoly_section=uninstall#wpmoly_section_uninstall' ) ?>" data-section="wpmoly_section=uninstall"><h4><?php _e( 'Uninstall', 'wpmovielibrary' ); ?></h4></a></li>
			    <li class="wpmoly-tabs-nav<?php if ( 'cache' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpmoly_edit_settings&amp;wpmoly_section=cache#wpmoly_section_cache' ) ?>" data-section="wpmoly_section=cache"><h4><?php _e( 'Cache', 'wpmovielibrary' ); ?></h4></a></li>
			    <li class="wpmoly-tabs-nav<?php if ( 'legacy' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpmoly_edit_settings&amp;wpmoly_section=legacy#wpmoly_section_legacy' ) ?>" data-section="wpmoly_section=legacy"><h4><?php _e( 'Legacy', 'wpmovielibrary' ); ?></h4></a></li>
			    <li class="wpmoly-tabs-nav<?php if ( 'maintenance' == $_section ) echo ' active'; ?>"><a href="<?php echo admin_url( 'admin.php?page=wpmoly_edit_settings&amp;wpmoly_section=maintenance#wpmoly_section_maintenance' ) ?>" data-section="wpmoly_section=maintenance"><h4><?php _e( 'Maintenance', 'wpmovielibrary' ); ?></h4></a></li>
			</ul>

<?php settings_fields( 'wpmoly_edit_settings' ); ?>

			<div class="wpmoly-tabs-panels">

<?php do_settings_sections( 'wpmoly_settings' ); ?>

				<h3><?php _e( 'Maintenance', 'wpmovielibrary' ); ?></h3>
				<span id="wpmoly_section_maintenance"></span>
				<table class="form-table" style="display: none;">
					<tbody>
						<tr>
							<th scope="row"><?php _e( 'Restore Default Settings', 'wpmovielibrary' ); ?></th>
							<td>
								<p class="description"><?php _e( 'You may want to restore WPMovieLibrary default settings.', 'wpmovielibrary' ); ?></p>
								<p class="description"><?php _e( '<strong>Caution!</strong> Doing this you will erase permanently all your custom settings. Don&rsquo;t do this unless you are positively sure of what you&rsquo;re doing!', 'wpmovielibrary' ); ?></p>
								<p style="text-align:center">
									<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wpmoly_edit_settings&wpmoly_section=maintenance&wpmoly_restore_default=true' ), 'wpmoly-restore-default-settings', '_nonce') ?>" class="button button-secondary"><?php _e( 'Restore', 'wpmovielibrary' ) ?></a>
								</p>
								
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Empty Cache', 'wpmovielibrary' ); ?></th>
							<td>
								<p class="description"><?php _e( 'Delete all temporarily stored data. This can solve issues like incomplete movie metadata repeatedly returned, outdated Widgets or Shortcodes display...', 'wpmovielibrary' ); ?></p>
								<p style="text-align:center">
									<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wpmoly_edit_settings&wpmoly_section=maintenance&wpmoly_empty_cache=true' ), 'wpmoly-empty-cache', '_nonce') ?>" class="button button-secondary"><?php _e( 'Empty cache', 'wpmovielibrary' ) ?></a>
								</p>
							</td>
						</tr>
					</tbody>
				</table>

			</div>

			<p class="submit">
				<input type="hidden" name="<?php echo WPMOLY_SETTINGS_SLUG . '[' . WPMOLY_SETTINGS_REVISION_NAME . ']' ?>" id="<?php echo WPMOLY_SETTINGS_REVISION_NAME ?>" value="<?php echo WPMOLY_SETTINGS_REVISION ?>" />
				<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
			</p>

		</form>

	</div>

	<?php echo self::render_admin_template( 'help.php' ); ?>

</div> <!-- .wrap -->