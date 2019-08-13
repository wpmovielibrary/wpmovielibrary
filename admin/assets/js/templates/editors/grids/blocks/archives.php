<?php
/**
 * Grid Editor Archives Block Template
 *
 * @since 3.0.0
 */
?>

							<p class="description"><?php esc_html_e( 'Use this grid for archives.', 'wpmovielibrary' ); ?></p>
							<div class="field">
								<div class="field-label"><label for="archive-type"><?php esc_html_e( 'Archives Type', 'wpmovielibrary' ); ?></label></div>
								<div class="field-value">
									<div class="field-control">
										<select id="archive-type" data-value="archive-type" data-selectize="1" placeholder="<?php esc_html_e( 'Select a Type', 'wpmovielibrary' ); ?>">
											<option value=""></option>
											<option value="movie"<# if ( 'movie' === data.current_type ) { #> selected="selected"<# } #>><?php esc_html_e( 'Movies', 'wpmovielibrary' ); ?></option>
											<option value="person"<# if ( 'person' === data.current_type ) { #> selected="selected"<# } #>><?php esc_html_e( 'Persons', 'wpmovielibrary' ); ?></option>
											<option value="genre"<# if ( 'genre' === data.current_type ) { #> selected="selected"<# } #>><?php esc_html_e( 'Genres', 'wpmovielibrary' ); ?></option>
											<option value="actor"<# if ( 'actor' === data.current_type ) { #> selected="selected"<# } #>><?php esc_html_e( 'Actors', 'wpmovielibrary' ); ?></option>
										</select>
									</div>
								</div>
							</div>
							<div class="field">
								<div class="field-label"><label for="archive-page"><?php esc_html_e( 'Archives Page', 'wpmovielibrary' ); ?></label></div>
								<div class="field-value">
									<div class="field-control">
										<select id="archive-page" data-value="archive-page" data-selectize="1" placeholder="<?php esc_html_e( 'Select a Page', 'wpmovielibrary' ); ?>">
											<option value=""></option>
<# _.each( data.pages, function( page ) { #>
											<option value="{{ page.id }}"<# if ( page.id === data.current_page ) { #> selected="selected"<# } #>>{{ page.title.rendered }}</option>
<# } ); #>
										</select>
									</div>
								</div>
							</div>
							<button id="update-setting" type="button" class="button submit" data-action="update-setting" disabled="disabled"><?php esc_html_e( 'Update Archives Page', 'wpmovielibrary' ); ?></button>
