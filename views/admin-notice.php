
<?php if ( 'api-key-error' == $notice ) : ?>
	<div class="update-nag wpml">
		<?php _e( 'You haven\'t specified a valid <acronym title="TheMovieDB">TMDb</acronym> API key in your Settings; this is required for the plugin to search a get movies metadata. WPMovieLibrary will use an internal API key, but you may consider getting your own personnal one at <a href="https://www.themoviedb.org/">TMDb</a> to get better results.', 'wpmovielibrary' ) ?><br />
		<span style="float:right">
			<a class="button-secondary" href="http://tmdb.caercam.org/"><?php _e( 'Learn more about the internal API key', 'wpmovielibrary' ) ?></a>
			<a class="button-primary" href="<?php echo wp_nonce_url( admin_url( '/admin.php?page=wpmovielibrary&amp;hide_wpml_api_key_notice=1' ), 'hide-wpml-api-key-notice', '_nonce' ) ?>"><?php _e( 'Do not notify me again', 'wpmovielibrary' ) ?></a>
		</span>
	</div>
<?php elseif ( 'missing-archive' == $notice ) : ?>

	<div class="update-nag">
		<?php _e( 'WPMovieLibrary couldn\'t find an archive page; this page is required to provide archives of your collections, genres and actors.', 'wpmovielibrary' ) ?><br /><br />
		<span style="float:right">
			<a class="button-primary" href="<?php echo wp_nonce_url( admin_url( '/admin.php?page=wpmovielibrary&amp;wpml_set_archive_page=1' ), 'wpml-set-archive-page', '_nonce' ) ?>"><?php _e( 'Create an archive page', 'wpmovielibrary' ) ?></a>
		</span>
	</div>
<?php elseif ( 'deprecated-meta' == $notice ) : ?>

	<div class="update-nag wpml">
		<div class="label"><span class="dashicons dashicons-no"></span></div>
		<div class="content"><?php _e( 'WPMovieLibrary found deprecated movie metadata; since version 1.3 movies metadata are stored and managed differently to provide extended search and filtering features. Therefore, you will need to update your movies to the new standard format. <strong>Not doing so will produce bugs and random behaviours when using the plugin</strong>. Please proceed to <a href="">update your movies</a> using the dedicated tool. <a href="http://wpmovielibrary/development/release-notes/#version-1.3">Learn more about this change</a>.', 'wpmovielibrary' ) ?></div>
	</div>
<?php endif; ?>
