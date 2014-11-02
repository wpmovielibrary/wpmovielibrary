
		<div id="wpmoly_dashboard_welcome_panel_widget" class="<?php if ( in_array( 'wpmoly_dashboard_welcome_panel_widget', $hidden ) ) echo ' hidden hide-if-js'; ?>">
			<div id="wpmoly-welcome-panel" class="welcome-panel">
				<a id="wpmoly-welcome-panel-close" href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wpmovielibrary&amp;show_wpmoly_welcome_panel=1' ), 'show-wpmoly-welcome-panel', 'show_wpmoly_welcome_panel_nonce' ) ?>" class="welcome-panel-close" onclick="wpmoly_dashboard.update_screen_option( 'welcome_panel', false ); return false;"><span class="wpmolicon icon-no-alt"></span><?php _e( 'Dismiss', 'wpmovielibrary' ); ?></a>
				<div class="welcome-panel-content">
					<h3><?php _e( 'Welcome to WordPress Movie Library!', 'wpmovielibrary' ); ?></h3>
					<p class="about-description">
						<?php _e( 'Thank you for using WPMovieLibrary. We made this plugin for movie lovers! Here are a few links to get you started.', 'wpmovielibrary' ); ?>
					</p>
					<div class="welcome-panel-column-container">
						<div class="welcome-panel-column">
							<h4><?php _e( 'Get Started', 'wpmovielibrary' ); ?></h4>
							<a class="button button-primary button-hero button-wpmoly" href="http://wpmovielibrary.com/documentation/"><?php _e( 'View the Documentation', 'wpmovielibrary' ); ?></a>
							<p><?php printf( __( 'and you may want to look at the <a href="%s">plugin settings</a>.', 'wpmovielibrary' ), admin_url( 'admin.php?page=wpmovielibrary-settings' ) ) ?></p>
						</div>

						<div class="welcome-panel-column">
							<h4><?php _e( 'Start building your library', 'wpmovielibrary' ); ?></h4>
							<ul>
								<li><span class="wpmolicon icon-add-page"></span><a href="<?php echo admin_url( 'post-new.php?post_type=movie' ) ?>"><?php _e( 'Create your first movie', 'wpmovielibrary' ); ?></a></li>
								<li><span class="wpmolicon icon-import"></span><a href="<?php echo admin_url( 'admin.php?page=wpmovielibrary-import' ) ?>"><?php _e( 'Import lists of movies', 'wpmovielibrary' ); ?></a></li>
								<li><span class="wpmolicon icon-movie"></span><a href="<?php echo admin_url( 'edit.php?post_type=movie' ) ?>"><?php _e( 'Manage your movies', 'wpmovielibrary' ); ?></a></li>
							</ul>
						</div>

						<div class="welcome-panel-column">
							<h4><?php _e( 'Furthermore', 'wpmovielibrary' ); ?></h4>
							<ul>
								<li><span class="wpmolicon icon-collection"></span><a href="<?php echo admin_url( 'edit-tags.php?taxonomy=collection&amp;post_type=movie' ) ?>"><?php _e( 'Create and manage Collections', 'wpmovielibrary' ); ?></a></li>
								<li><span class="wpmolicon icon-tag"></span><?php printf( __( 'Create and manage <a href="%s">Genres</a> and <a href="%s">Actors</a>', 'wpmovielibrary' ), admin_url( 'edit-tags.php?taxonomy=genre&amp;post_type=movie' ), admin_url( 'edit-tags.php?taxonomy=actor&amp;post_type=movie' ) ) ?></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div> 
