<div id="wpml-settings" class="wrap">

	<h2><?php esc_html_e( WPML_NAME ); ?> Settings</h2>

	<?php if ( '' == WPML_Settings::tmdb__apikey() ) WPML_Utils::admin_notice( sprintf( __( 'Congratulation, you successfully installed WPMovieLibrary. You need a valid <acronym title="TheMovieDB">TMDb</acronym> API key to start adding your movies. Go to the <a href="%s">WPMovieLibrary Settings page</a> to add your API key.', 'wpml' ), admin_url( 'edit.php?post_type=movie&page=settings' ) ), 'wpml' ); ?>

	<?php WPML_Utils::admin_notice( null/*$_notice*/ ); settings_errors(); ?>

	<div id="wpml-tabs">

		<form action="options.php" method="post">

			<ul class="wpml-tabs-nav">
			    <li class="wpml-tabs-nav<?php if ( 'tmdb' == $_section || '' == $_section ) echo ' active'; ?>"><a href="#" data-section="wpml_section=tmdb"><h4><?php _e( 'TMDb API', 'wpml' ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'wpml' == $_section ) echo ' active'; ?>"><a href="#" data-section="wpml_section=wpml"><h4><?php _e( 'WPMovieLibrary', 'wpml' ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'uninstall' == $_section ) echo ' active'; ?>"><a href="#" data-section="wpml_section=uninstall"><h4><?php _e( 'Deactivate/Uninstall', 'wpml' ); ?></h4></a></li>
			    <li class="wpml-tabs-nav<?php if ( 'restore' == $_section ) echo ' active'; ?>"><a href="#" data-section="wpml_section=restore"><h4><?php _e( 'Restore', 'wpml' ); ?></h4></a></li>
			</ul>

<?php settings_fields( 'wpml_edit_settings' ); ?>

			<div class="wpml-tabs-panels <?php /*if ( 'tmdb' == $_section || '' == $_section ) echo ' active';*/ ?>">

<?php do_settings_sections( 'wpml_settings' ); ?>

			</div>

			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
			</p>

		</form>

	</div>

	<?php include_once( plugin_dir_path( __FILE__ ) . '../../common/views/help.php' ); ?>

</div> <!-- .wrap -->