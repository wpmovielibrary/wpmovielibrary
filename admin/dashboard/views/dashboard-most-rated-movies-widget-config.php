
							<div id="wpml-most-rated-movies-widget-config"<?php if ( ! $editing ) echo ' class="main-config"'; ?>>
								<form method="post" action="<?php echo admin_url( "admin.php?page=wpmovielibrary#{$this->widget_id}" ) ?>">
									<?php WPML_Utils::_nonce_field( "save-{$this->widget_id}" ) ?>
									<?php WPML_Utils::_nonce_field( 'load-more-widget-movies' ) ?>
									<table class="wp-list-table">
										<tbody>
											<tr>
												<td colspan="3">
													<em><?php _e( 'Use the following options to customize this Widget.', WPML_SLUG ) ?></em>
												</td>
											</tr>
											<tr>
												<td style="vertical-align:top;width:20%">
													<label><strong><?php _e( 'Number of movies:', WPML_SLUG ) ?></strong>
													<br /><input step="1" min="1" max="999" class="screen-per-page" name="<?php echo $this->widget_id ?>[movies_per_page]" id="most_rated_movies_movies_per_page" maxlength="3" value="<?php echo $settings['movies_per_page'] ?>" type="number" /> <?php _e( 'movies', WPML_SLUG ) ?></label>
												</td>
												<td style="vertical-align:top;width:40%">
													<label><input id="most_rated_movies_show_year" name="<?php echo $this->widget_id ?>[show_year]"<?php checked( $settings['show_year'], '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Show year', WPML_SLUG ) ?></strong></label><br />
													<em><?php _e( 'Show movies release years below the poster.', WPML_SLUG ) ?></em><br />
													<label><input id="most_rated_movies_show_rating" name="<?php echo $this->widget_id ?>[show_rating]"<?php checked( $settings['show_rating'], '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Show rating', WPML_SLUG ) ?></strong></label><br />
													<em><?php _e( 'Show movies ratings below the poster.', WPML_SLUG ) ?></em><br />
													<label><input id="most_rated_movies_show_more" name="<?php echo $this->widget_id ?>[show_more]"<?php checked( $settings['show_more'], '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Show "Load more" button', WPML_SLUG ) ?></strong></label><br />
													<em><?php _e( 'Show a button to load more movies to the Widget.', WPML_SLUG ) ?></em> <em class="hide-if-js"><?php _e( 'JavaScript required', WPML_SLUG ) ?></em><br />
													<label><input id="most_rated_movies_show_modal" name="<?php echo $this->widget_id ?>[show_modal]"<?php checked( $settings['show_modal'], '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Show a previewing modal window when clicking posters.', WPML_SLUG ) ?></strong></label><br />
													<em><?php _e( 'Open modal window on click', WPML_SLUG ) ?></em> <em class="hide-if-js"><?php _e( 'JavaScript required', WPML_SLUG ) ?></em>
												</td>
												<td style="vertical-align:top;width:40%">
													<label><input id="most_rated_movies_show_quickedit" name="<?php echo $this->widget_id ?>[show_quickedit]"<?php checked( $settings['show_quickedit'], '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Quick-Edit menu', WPML_SLUG ) ?></strong></label><br />
													<em><?php _e( 'Show a quick edit menu to perform basic actions on movies.', WPML_SLUG ) ?></em> <em class="hide-if-js"><?php _e( 'JavaScript required', WPML_SLUG ) ?></em><br />
													<label><input id="most_rated_movies_style_posters" name="<?php echo $this->widget_id ?>[style_posters]"<?php checked( $settings['style_posters'], '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Stylized posters', WPML_SLUG ) ?></strong></label><br />
													<em><?php _e( 'Show more stylish poster without rounded borders and shadows.', WPML_SLUG ) ?></em><br />
													<label><input id="most_rated_movies_style_metabox" name="<?php echo $this->widget_id ?>[style_metabox]"<?php checked( $settings['style_metabox'], '1' ) ?> type="checkbox" value="1" /> <strong><?php _e( 'Stylized Metabox', WPML_SLUG ) ?></strong></label><br />
													<em><?php _e( 'Use transparent Metabox instead of regular WordPress Metabox', WPML_SLUG ) ?></em>
												</td>
											</tr>
											<tr>
												<td colspan="3" style="text-align:right">
													<hr />
													<input type="submit" name="save" id="<?php echo "save_{$this->widget_id}" ?>" class="button button-primary hide-if-js" value="<?php _e( 'Save' ) ?>" />
												</td>
											</tr>
										</tbody>
									</table>
								</form>
							</div>