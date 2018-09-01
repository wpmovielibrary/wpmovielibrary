<?php
/**
 * Movies Editor Details Block Template
 *
 * @since 3.0.0
 */

 use \wpmoly\helpers;

 $meta = helpers\get_registered_movie_meta();
?>

              <p class="description"><?php esc_html_e( '.', 'wpmovielibrary' ); ?></p>
							<div id="format" class="field select-field">
								<div class="field-label"><?php esc_html_e( 'Format', 'wpmovielibrary' ); ?></div>
								<div class="field-control">
									<select multiple="true" data-field="format" data-selectize="1" data-selectize-plugins="remove_button">
										<option value=""<# if ( _.isEmpty( data.format ) ) { #> selected="selected"<# } #>></option>
<?php foreach ( $meta['format']['show_in_rest']['enum'] as $key => $value ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>"<# if ( _.contains( data.format, '<?php echo esc_attr( $key ); ?>' ) ) { #> selected="selected"<# } #>><?php echo esc_html( $value ); ?></option>
<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div id="language" class="field -field">
								<div class="field-label"><?php esc_html_e( 'Language', 'wpmovielibrary' ); ?></div>
								<div class="field-control">
									<select multiple="true" data-field="language" data-selectize="1" data-selectize-plugins="remove_button">
										<option value=""<# if ( _.isEmpty( data.language ) ) { #> selected="selected"<# } #>></option>
<?php foreach ( $meta['language']['show_in_rest']['enum'] as $key => $value ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>"<# if ( _.contains( data.language, '<?php echo esc_attr( $key ); ?>' ) ) { #> selected="selected"<# } #>><?php echo esc_html( $value ); ?></option>
<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div id="media" class="field select-field">
								<div class="field-label"><?php esc_html_e( 'Media', 'wpmovielibrary' ); ?></div>
								<div class="field-control">
									<select multiple="true" data-field="media" data-selectize="1" data-selectize-plugins="remove_button">
										<option value=""<# if ( _.isEmpty( data.media ) ) { #> selected="selected"<# } #>></option>
<?php foreach ( $meta['media']['show_in_rest']['enum'] as $key => $value ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>"<# if ( _.contains( data.media, '<?php echo esc_attr( $key ); ?>' ) ) { #> selected="selected"<# } #>><?php echo esc_html( $value ); ?></option>
<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div id="rating" class="field select-field">
								<div class="field-label"><?php esc_html_e( 'Rating', 'wpmovielibrary' ); ?></div>
								<div class="field-control">
									<select data-field="rating" data-selectize="1" data-selectize-plugins="remove_button">
										<option value=""<# if ( '' === data.rating ) { #> selected="selected"<# } #>></option>
<?php foreach ( $meta['rating']['show_in_rest']['enum'] as $key => $value ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>"<# if ( '<?php echo esc_attr( $key ); ?>' === data.rating ) { #> selected="selected"<# } #>><?php echo esc_html( $value ); ?></option>
<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div id="status" class="field select-field">
								<div class="field-label"><?php esc_html_e( 'Status', 'wpmovielibrary' ); ?></div>
								<div class="field-control">
									<select data-field="status" data-selectize="1" data-selectize-plugins="remove_button">
										<option value=""<# if ( '' === data.status ) { #> selected="selected"<# } #>></option>
<?php foreach ( $meta['status']['show_in_rest']['enum'] as $key => $value ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>"<# if ( '<?php echo esc_attr( $key ); ?>' === data.status ) { #> selected="selected"<# } #>><?php echo esc_html( $value ); ?></option>
<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div id="subtitles" class="field select-field">
								<div class="field-label"><?php esc_html_e( 'Subtitles', 'wpmovielibrary' ); ?></div>
								<div class="field-control">
									<select multiple="true" data-field="subtitles" data-selectize="1" data-selectize-plugins="remove_button">
										<option value=""<# if ( _.isEmpty( data.subtitles ) ) { #> selected="selected"<# } #>></option>
<?php foreach ( $meta['subtitles']['show_in_rest']['enum'] as $key => $value ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>"<# if ( _.contains( data.subtitles, '<?php echo esc_attr( $key ); ?>' ) ) { #> selected="selected"<# } #>><?php echo esc_html( $value ); ?></option>
<?php endforeach; ?>
									</select>
								</div>
							</div>
							<button id="update-details" type="button" class="button submit" data-action="update-details" disabled="disabled"><?php esc_html_e( 'Update Details', 'wpmovielibrary' ); ?></button>
