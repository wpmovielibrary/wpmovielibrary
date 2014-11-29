
		<div id="wpmoly-home" class="wrap">

			<h2><?php _e( 'WPMovieLibrary Custom Page creation tool', 'wpmovielibrary' ); ?></h2>

			<p><?php _e( 'This page will allow you create custom pages to display your library’s taxonomies and movies archives.', 'wpmovielibrary' ); ?></p>

			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder">
					<div id="postbox-container-1" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">

<?php if ( ! empty( $existing ) ) : ?>
							<div id="wpmoly_dashboard_missing_pages" class="postbox">
								<h3 class="hndle"><span><?php _e( 'Missing pages', 'wpmovielibrary' ); ?></span></h3>
								<div class="inside">

									<div class="main">

										<div class="missing-pages">
<?php if ( ! empty( $missing ) ) : ?>
											<p><?php echo _n( 'The following archive page has no page set:', 'The following archives pages have no pages set:', count( $missing ), 'wpmovielibrary' ); ?></p>

											<table id="missing-pages" style="width:100%">
												<tbody>
<?php
foreach ( $missing as $slug => $page ) :
	$title = ucwords( $slug ) . 's';
?>
													<tr>
														<td><?php _e( $title, 'wpmovielibrary' ); ?></td>
														<td><a href="<?php echo wpmoly_nonce_url( admin_url( '/admin.php?page=wpmovielibrary-add-custom-pages&create_pages=' . $slug ), 'create-custom-pages' ); ?>" class="button button-default"><?php _e( 'Create page', 'wpmovielibrary' ); ?></a></td>
													</tr>

<?php endforeach; ?>
												</tbody>
											</table>

<?php else : ?>
											<p><em><?php _e( 'No missing page!', 'wpmovielibrary' ) ?></em></p>

<?php endif; ?>
										</div>
									</div>
								</div>
							</div>

							<div id="wpmoly_dashboard_existing_pages" class="postbox">
								<h3 class="hndle"><span><?php _e( 'Existing pages', 'wpmovielibrary' ); ?></span></h3>
								<div class="inside">

									<div class="main">

										<div class="existing-pages">
<?php if ( ! empty( $existing ) ) : ?>
											<p><?php echo _n( 'The following page as been set as archive page:', 'The following pages as been set as archive pages:', count( $existing ), 'wpmovielibrary' ); ?></p>

											<table id="existing-pages" style="width:100%">
												<thead>
													<tr>
														<th><?php _e( 'Page ID', 'wpmovielibrary' ); ?></th>
														<th><?php _e( 'Page Title', 'wpmovielibrary' ); ?></th>
														<th><?php _e( 'Archive Type', 'wpmovielibrary' ); ?></th>
													</tr>
												</thead>
												<tbody>
<?php
foreach ( $existing as $slug => $page ) :
	$title = ucwords( $slug ) . 's';
?>
													<tr>
														<td>#<?php echo $page->ID; ?></td>
														<td><a href="<?php echo get_permalink( $page->ID ); ?>"><?php echo get_the_title( $page->ID ); ?></a></td>
														<td><?php _e( $title, 'wpmovielibrary' ); ?></td>
													</tr>

<?php endforeach; ?>
												</tbody>
											</table>

<?php else : ?>
											<p><em><?php _e( 'No page set yet!', 'wpmovielibrary' ) ?></em></p>

<?php endif; ?>
										</div>
									</div>
								</div>
							</div>

<?php else : ?>
							<div id="wpmoly_dashboard_existing_pages" class="postbox">
								<h3 class="hndle"><span><?php _e( 'Create Archives Pages', 'wpmovielibrary' ); ?></span></h3>
								<div class="inside">
									<div class="main">
										<p></p>
										<p style="text-align:center"><a href="<?php echo wpmoly_nonce_url( admin_url( '/admin.php?page=wpmovielibrary-add-custom-pages&create_pages=all' ), 'create-custom-pages' ); ?>" class="button button-primary button-hero button-wpmoly"><?php _e( 'Create Archives Pages', 'wpmovielibrary' ); ?></a></p>
										<p><?php _e( 'This will add four new pages to your site and set them as archives pages: "Movies", "Collections", "Genres" and "Actors".', 'wpmovielibrary' ); ?></p>
									</div>
								</div>
							</div>

<?php endif; ?>
						</div>
					</div>

					<div id="postbox-container-2" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
							<div id="wpmoly_dashboard_create_pages_info" class="postbox">
								<h3 class="hndle"><span><?php _e( 'Custom Archives Pages', 'wpmovielibrary' ); ?></span></h3>
								<div class="inside">
									<div class="main">
										<p><?php _e( 'Custom Archives Pages are used to replace WordPress’ default archives pages with more customized views. Taxonomies will appear as list, movies will appear in a poster grid. Both movie and taxonomy archives can be manipulated with an alphabetical menu and sorting options.', 'wpmovielibrary' ); ?></p>
										<p><?php printf( __( 'WPMovieLibrary can automatically create pages and set them as archive pages, but you can add your own pages and set them manually as archive pages in the "Archives" section of your <a href="%s">Settings panel</a>.', 'wpmovielibrary' ), admin_url( '/admin.php?page=wpmovielibrary-settings' ) ); ?></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
