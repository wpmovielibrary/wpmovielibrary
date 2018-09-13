<?php
/**
* Movies Editor Collections Block Template
 *
 * @since 3.0.0
 */

?>

							<p class="description"><?php esc_html_e( 'A list of terms for the Collections Taxonomy. Usually a list of directors, but can be used independantly from the \'director\' metadata.', 'wpmovielibrary' ); ?></p>
							<div class="block-menu">
								<button class="button" type="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
							</div>
							<div class="term-list">
								<# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( collection ) { #>
								<div class="term-item collection-item">
									<# if ( ! _.isEmpty( collection.edit_link ) ) { #>
									<div class="term-thumbnail collection-thumbnail" style="background-image:url({{ collection.thumbnail }})"></div>
									<a href="{{ collection.edit_link }}" target="_blank" class="term-name collection-name">{{ collection.name }}</a>
									<# } else { #>
									<div class="term-thumbnail collection-thumbnail" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/new-term-thumbnail.png' ); ?>)"></div>
									<a class="term-name collection-name" title="{{ s.sprintf( wpmolyEditorL10n.collection_does_not_exist, collection.name ) }}">{{ collection.name }}</a>
									<# } #>
								</div>
								<# } ); } else { #>
								<p class="description">{{ wpmolyEditorL10n.no_collection_found }}</p>
								<# } #>
							</div>
							<div class="term-field">
								<div id="movie-collections-field" class="field select-field">
									<div class="field-label"><?php esc_html_e( 'Collections', 'wpmovielibrary' ); ?></div>
									<div class="field-value">
										<div class="field-control">
											<select id="movie-collections" data-field="collections" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
												<option value=""></option>
											<# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( collection ) { #>
												<option value="{{ collection.name }}" selected="selected">{{ collection.name }}</option>
											<# } ); } #>
											</select>
										</div>
										<button class="button empty" type="button" data-action="clear-terms"><?php esc_html_e( 'Clear all', 'wpmovielibrary' ); ?></button>
									</div>
								</div>
							</div>
							<button id="synchronize-collections" type="button" class="button submit" data-action="synchronize">{{ wpmolyEditorL10n.synchronize_collections }}</button>
