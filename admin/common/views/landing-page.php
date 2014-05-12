	<div id="wpml-home" class="wrap">

		<h2>WPMovieLibrary</h2>

		<div id="wpml-welcome-panel" class="welcome-panel">
			<a href="<?php echo admin_url( 'admin.php?page=wpmovielibrary&amp;wpml_welcome_panel=dismiss' ) ?>" class="welcome-panel-close"><span class="dashicons dashicons-dismiss"></span><?php _e( 'Dismiss', WPML_SLUG ); ?></a>
			<div class="welcome-panel-content">
				<h3><?php _e( 'Welcome to WordPress Movie Library!', WPML ); ?></h3>
				<p class="about-description">
					<?php _e( 'Thank you for using WPMovieLibrary. We made this plugin for movie lovers! Here are a few links to get you started.', WPML_SLUG ); ?>
				</p>
				<div class="welcome-panel-column-container">
					<div class="welcome-panel-column">
						<h4><?php _e( 'Get Started', WPML_SLUG ); ?></h4>
						<a class="button button-primary button-hero" href="http://caercam.org/wpmovielibrary/documentation.html"><?php _e( 'View the Documentation', WPML_SLUG ); ?></a>
						<p><?php printf( __( 'and you may want to look at the <a href="%s">plugin settings</a>.', WPML_SLUG ), admin_url( 'admin.php?page=wpml_edit_settings' ) ) ?></p>
					</div>

					<div class="welcome-panel-column">
						<h4><?php _e( 'Start building your library', WPML_SLUG ); ?></h4>
						<ul>
							<li><span class="dashicons dashicons-welcome-write-blog"></span><a href="<?php echo admin_url( 'post-new.php?post_type=movie' ) ?>"><?php _e( 'Create your first movie', WPML_SLUG ); ?></a></li>
							<li><span class="dashicons dashicons-playlist-video"></span><a href="<?php echo admin_url( 'admin.php?page=wpml_import' ) ?>"><?php _e( 'Import lists of movies', WPML_SLUG ); ?></a></li>
							<li><span class="dashicons dashicons-format-video"></span><a href="<?php echo admin_url( 'edit.php?post_type=movie' ) ?>"><?php _e( 'Manage your movies', WPML_SLUG ); ?></a></li>
						</ul>
					</div>

					<div class="welcome-panel-column">
						<h4><?php _e( 'Furthermore', WPML_SLUG ); ?></h4>
						<ul>
							<li><span class="dashicons dashicons-category"></span><a href="<?php echo admin_url( 'edit-tags.php?taxonomy=collection&post_type=movie' ) ?>"><?php _e( 'Create and manage Collections', WPML_SLUG ); ?></a></li>
							<li><span class="dashicons dashicons-tag"></span><?php printf( __( 'Create and manage <a href="%s">Genres</a> and <a href="%s">Actors</a>', WPML_SLUG ), admin_url( 'edit-tags.php?taxonomy=genre&post_type=movie' ), admin_url( 'edit-tags.php?taxonomy=actor&post_type=movie' ) ) ?></a></li>
							<li><a href="<?php echo admin_url( '' ) ?>"><?php _e( '', WPML_SLUG ); ?></a></li>
							<li><a href=""><?php _e( '', WPML_SLUG ); ?></a></li>
						</ul>
					</div>
				</div>
			</div>

		</div>

		<div id="dashboard-widgets-wrap">
			<div id="dashboard-movies" class="metabox-holder">
<?php if ( $movies->have_posts() ) :
	foreach ( $movies->posts as $movie ) :
?>
				<div id="movie-<?php echo $movie->ID ?>" class="wpml-movie">
					<a href="<?php echo get_permalink( $movie->ID ) ?>">
						<?php echo get_the_post_thumbnail( $movie->ID, 'medium' ) ?>
					</a>
				</div>
<?php
	endforeach;
endif;
?>
			</div>
			<div id="dashboard-widgets" class="metabox-holder">
				<div id="postbox-container-1" class="postbox-container">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						<div id="dashboard_right_now" class="postbox ">
							<div class="handlediv" title="Cliquer pour inverser."><br></div>
							<h3 class="hndle"><span>D’un coup d’œil</span></h3>
							<div class="inside">
							<div class="main">
								<ul>
									<li class="post-count"><a href="edit.php?post_type=post">1 articles</a></li>
									<li class="page-count"><a href="edit.php?post_type=page">1 page</a></li>
									<li class="comment-count"><a href="edit-comments.php">1 commentaire</a></li>
								</ul>
							    <p id="wp-version-message">WordPress 3.9 avec le thème <a href="themes.php">Twenty Fourteen</a>. <a href="http://wp39/wp-admin/update-core.php" class="button">Mettre à jour vers la version 3.9.1</a></p>
							    <p><a href="options-reading.php" title="Votre site indique aux moteurs de recherche de ne pas indexer son contenu">Moteurs de recherche refusés</a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

<?php include_once( 'movie-showcase.php' ); ?>