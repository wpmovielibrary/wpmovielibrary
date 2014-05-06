<div id="wpml-settings" class="wrap">

	<h2><?php esc_html_e( WPML_NAME ); ?> Settings</h2>

	<?php if ( '' == WPML_Settings::tmdb__apikey() ) WPML_Utils::admin_notice( sprintf( __( 'Congratulation, you successfully installed WPMovieLibrary. You need a valid <acronym title="TheMovieDB">TMDb</acronym> API key to start adding your movies. Go to the <a href="%s">WPMovieLibrary Settings page</a> to add your API key.', WPML_SLUG ), admin_url( 'edit.php?post_type=movie&page=settings' ) ), WPML_SLUG ); ?>

	<?php settings_errors(); ?>

	<div id="wpml-tabs">

		<form action="options.php" method="post">

			<ul class="wpml-tabs-nav">
			    <li class="wpml-tabs-nav<?php if ( 'api' == $_section || '' == $_section ) echo ' active'; ?>"><a href="#" data-section="wpml_section=api"><h4><?php _e( 'TMDb API', WPML_SLUG ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'movies' == $_section ) echo ' active'; ?>"><a href="#" data-section="wpml_section=movies"><h4><?php _e( 'Movies', WPML_SLUG ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'taxonomies' == $_section ) echo ' active'; ?>"><a href="#" data-section="wpml_section=taxonomies"><h4><?php _e( 'Taxonomies', WPML_SLUG ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'deactivate' == $_section ) echo ' active'; ?>"><a href="#" data-section="wpml_section=deactivate"><h4><?php _e( 'Deactivate', WPML_SLUG ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'uninstall' == $_section ) echo ' active'; ?>"><a href="#" data-section="wpml_section=uninstall"><h4><?php _e( 'Uninstall', WPML_SLUG ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'restore' == $_section ) echo ' active'; ?>"><a href="#" data-section="wpml_section=restore"><h4><?php _e( 'Restore', WPML_SLUG ); ?></h4></a></li>
			</ul>

<?php settings_fields( 'wpml_edit_settings' ); ?>

			<div class="wpml-tabs-panels">

<?php do_settings_sections( 'wpml_settings' ); ?>

			</div>

			<p class="submit">
				<input type="hidden" name="<?php echo WPML_SETTINGS_SLUG . '[' . WPML_SETTINGS_REVISION_NAME . ']' ?>" id="<?php echo WPML_SETTINGS_REVISION_NAME ?>" value="<?php echo WPML_SETTINGS_REVISION ?>" />
				<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
			</p>

		</form>

	</div>

	<?php include_once( plugin_dir_path( __FILE__ ) . '../../common/views/help.php' ); ?>

</div> <!-- .wrap -->