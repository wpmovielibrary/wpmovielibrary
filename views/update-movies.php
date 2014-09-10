
		<div id="wpml-home" class="wrap">

			<h2><?php printf( '%s <small>v%s</small>', __( 'Welcome to WPMovieLibrary ', 'wpmovielibrary' ), WPML_VERSION ); ?></h2>

			<p><?php _e( 'This page will allow you update your library to the new metadata format introduced in WPMovieLibrary 1.3.', 'wpmovielibrary' ); ?></p>

			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder">
					<div id="postbox-container-1" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
							<div id="wpml_dashboard_deprecated_movies" class="postbox">
								<div class="handlediv" title="Click to toggle"><br></div>
								<h3 class="hndle"><span>Update deprecated movie meta</span></h3>
								<div class="inside">

									<div class="main">
										<div class="deprecated-movies">
											<table id="deprecated-movies">
												<thead>
													<tr>
														<th colspan="2"><?php printf( _n( 'The following movie needs an update:', 'The following movies needs an update:', $deprecated, 'wpmovielibrary' ) ); ?></th>
													</tr>
													<tr>
														<th colspan="2" height="8"></th>
													</tr>
												</thead>
												<tbody>
<?php
global $post;
foreach ( $deprecated as $post ) :
	setup_postdata( $post );
?>
													<tr id="post-<?php the_ID(); ?>">
														<td class="label"><span class="dashicons dashicons-arrow-right-alt2"></span></td>
														<td class="movie-title"><span><?php the_title(); ?></span></td>
														<td class=""><a href="#"><span class="dashicons dashicons-yes"></span></a></td>
														<td class=""><a href="#"><span class="dashicons dashicons-update"></span></a></td>
													</tr>

<?php
endforeach;
wp_reset_postdata();
?>
												</tbody>
											</table>
										</div>

										<div class="updated-movies">
											<table id="updated-movies">
												<thead>
													<tr>
														<th colspan="2"><?php printf( _n( 'The following movie is up to date:', 'The following movies are up to date:', $updated, 'wpmovielibrary' ) ); ?></th>
													</tr>
													<tr>
														<th colspan="2" height="8"></th>
													</tr>
												</thead>
												<tbody>
<?php
global $post;
foreach ( $updated as $post ) :
	setup_postdata( $post );
?>
													<tr id="post-<?php the_ID(); ?>">
														<td class="label"><span class="dashicons dashicons-yes"></span></td>
														<td class="movie-title"><span><?php the_title(); ?></span></td>
													</tr>

<?php
endforeach;
wp_reset_postdata();
?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div id="postbox-container-2" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
							<div id="wpml_dashboard_deprecated_movies_update" class="postbox">
								<div class="handlediv" title="Click to toggle"><br></div>
								<h3 class="hndle"><span>Update movies</span></h3>
								<div class="inside">

									<div class="main">
										<p><?php printf( __( 'You have a total of <strong>%s</strong> using a deprecated metadata format; you can use the present page to update your library to new format and access new features.', 'wpmovielibrary' ), sprintf( _n( 'one movie', '%d movies', count( $deprecated ), 'wpmovielibrary' ), count( $deprecated ) ) ); ?></p>
										<p><?php _e( 'You update all your movies at once, select a few movies manually (<span class="dashicons dashicons-yes"></span> link) or update directly a specific movies (<span class="dashicons dashicons-update"></span> link).', 'wpmovielibrary' ) ?></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
