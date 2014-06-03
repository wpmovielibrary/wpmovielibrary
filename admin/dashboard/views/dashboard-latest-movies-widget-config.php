
							<div id="wpml-latest-movies-widget-config" class="main-config">
								<form method="post" action="">
									<table class="wp-list-table">
										<tbody>
											<tr>
												<td colspan="3">
													<em><?php _e( 'Use the following options to customize this Widget.', WPML_SLUG ) ?></em>
												</td>
											</tr>
											<tr>
												<td style="vertical-align:top;width:20%">
													<label><strong><?php _e( 'Number of movies:', WPML_SLUG ) ?></strong> <input step="1" min="1" max="999" class="screen-per-page" name="wp_screen_options[wpml_dashboard_latest_movies_widget][movies_per_page]" id="latest_movies_movies_per_page" maxlength="3" value="8" type="number" /> <?php _e( 'movies', WPML_SLUG ) ?></label>
												</td>
												<td style="vertical-align:top;width:40%">
													<label><input id="latest_movies_show_ratings" name="wp_screen_options[wpml_dashboard_latest_movies_widget][show_ratings]"<?php checked( $duh, '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Show ratings', WPML_SLUG ) ?></strong></label><br />
													<label><input id="latest_movies_show_more" name="wp_screen_options[wpml_dashboard_latest_movies_widget][show_more]"<?php checked( $duh, '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Show "Load more" button', WPML_SLUG ) ?></strong> <em class="hide-if-js">JavaScript required</em></label><br />
													<label><input id="latest_movies_show_modal" name="wp_screen_options[wpml_dashboard_latest_movies_widget][show_modal]"<?php checked( $duh, '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Open modal window on click', WPML_SLUG ) ?></strong> <em class="hide-if-js">JavaScript required</em></label>
												</td>
												<td style="vertical-align:top;width:40%">
													<label><input id="latest_movies_show_quickedit" name="wp_screen_options[wpml_dashboard_latest_movies_widget][show_quickedit]"<?php checked( $duh, '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Quick-Edit menu', WPML_SLUG ) ?></strong> <em class="hide-if-js">JavaScript required</em></label><br />
													<label><input id="latest_movies_style_posters" name="wp_screen_options[wpml_dashboard_latest_movies_widget][style_posters]"<?php checked( $duh, '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Stylized posters', WPML_SLUG ) ?></strong></label><br />
													<label><input id="latest_movies_style_metabox" name="wp_screen_options[wpml_dashboard_latest_movies_widget][style_metabox]"<?php checked( $duh, '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Stylized Metabox', WPML_SLUG ) ?></strong></label>
												</td>
											</tr>
											<tr>
												<td colspan="3" style="text-align:right">
													<hr />
													<input type="submit" name="save" id="save-wpml_dashboard_latest_movies_widget" class="button button-primary" value="<?php _e( 'Save' ) ?>" />
												</td>
											</tr>
										</tbody>
									</table>
								</form>
							</div>