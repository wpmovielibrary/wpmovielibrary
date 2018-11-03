<?php
/**
 * Post Editor Categories Block Template
 *
 * @since 3.0.0
 */

?>

							<p class="description"><?php esc_html_e( 'A list of categories.', 'wpmovielibrary' ); ?></p>
							<div class="block-menu">
								<button class="button" type="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
							</div>
							<div class="term-list">
								<# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( category ) { #>
								<div class="term-item category-item">
									{{ 'svg:icon:category' }}
									<# if ( ! _.isEmpty( category.edit_link ) ) { #>
									<a href="{{ category.edit_link }}" target="_blank" class="term-name category-name">{{ category.name }}</a>
									<# } else { #>
									<a class="term-name category-name" title="{{ s.sprintf( wpmolyEditorL10n.category_does_not_exist, category.name ) }}">{{ category.name }}</a>
									<# } #>
								</div>
								<# } ); } else { #>
								<p class="description">{{ wpmolyEditorL10n.no_category_found }}</p>
								<# } #>
							</div>
							<div class="term-field">
								<div id="post-categories-field" class="field select-field">
									<div class="field-label"><?php esc_html_e( 'Categories', 'wpmovielibrary' ); ?></div>
									<div class="field-value">
										<div class="field-control">
											<select id="post-categories" data-field="categories" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
												<option value=""></option>
											<# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( category ) { #>
												<option value="{{ category.name }}" selected="selected">{{ category.name }}</option>
											<# } ); } #>
											</select>
										</div>
									</div>
								</div>
							</div>
