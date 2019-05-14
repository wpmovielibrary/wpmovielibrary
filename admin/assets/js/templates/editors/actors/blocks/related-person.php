<?php
/**
 * Actors Editor Relate Persons Block Template
 *
 * @since 3.0.0
 */

$persons = get_posts( array(
	'post_type' => 'person',
	'posts_per_page' => -1,
) );
?>

							<input type="hidden" data-value="person" value="{{ data.person }}" />
							<p class="description">{{ wpmolyEditorL10n.about_related_persons }}</p>
							<div class="field">
								<div class="field-label"><?php esc_html_e( 'Person', 'wpmovielibrary' ); ?></div>
								<div class="field-control">
									<select data-field="person" data-selectize="1" data-selectize-plugins="remove_button">
										<option value=""<# if ( _.isEmpty( data.person ) ) { #> selected="selected"<# } #>><?php esc_html_e( 'Select a person', 'wpmovielibrary' ); ?></option>
<?php foreach ( $persons as $person ) : ?>
										<option value="<?php echo esc_attr( $person->ID ); ?>"<# if ( '<?php echo esc_attr( $person->ID ); ?>' == data.person ) { #> selected="selected"<# } #>><?php echo esc_html( $person->post_title ); ?></option>
<?php endforeach; ?>
									</select>
								</div>
							</div>
							<button id="save-person" type="button" class="button submit" data-action="save-person"<# if ( ! _.isNumber( data.person ) ) { #> disabled="disabled"<# } #>>{{ wpmolyEditorL10n.save_person }}</button>
