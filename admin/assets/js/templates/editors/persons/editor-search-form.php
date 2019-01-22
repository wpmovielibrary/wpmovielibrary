<?php
/**
 * Movies Editor Search Form Template
 *
 * @since 3.0.0
 */

 use \wpmoly\core\L10n;
?>

			<div class="search-field">
				<input id="search-query" type="text" class="search-query" placeholder="<?php esc_html_e( 'Ex: Matthew McConaughey', 'wpmovielibrary' ); ?>" value="{{ data.query }}" data-value="search-query" required="true" />
				<button type="button" class="button search" data-action="search"><span class="wpmolicon icon-search"></span></button>
				<button type="button" class="button reset" data-action="reset"><span class="wpmolicon icon-no"></span></button>
			</div>
			<button type="button" class="button advanced" data-action="advanced-search"><span class="wpmolicon icon-filter"></span> <?php esc_html_e( 'Advanced Search', 'wpmovielibrary' ); ?></button>
			<div class="search-tip">
				<p><?php _e( '<strong>Pro Tip:</strong> You can search persons by name, IMDb ID or TMDb ID. <code>name:</code>, <code>imdb:</code> and <code>tmdb:</code> prefixes are also supported.', 'wpmovielibrary' ); ?></p>
			</div>
			<div class="search-filters">
				<div class="search-filter">
					<div class="field select-field">
						<div class="field-label"><?php esc_html_e( 'Language', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<select data-selectize="1" data-setting="language">
								<option value=""></option>
<?php foreach( L10n::$supported_languages as $code => $name ) : ?>
								<option value="<?php echo esc_attr( $code ); ?>"<# if ( '<?php echo esc_attr( $code ); ?>' === data.language ) { #> selected="selected"<# } #>><?php echo esc_html( $name ); ?></option>
<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
				<div class="search-filter">
					<div class="field checkbox-field">
						<div class="field-label"><?php esc_html_e( 'Restriction', 'wpmovielibrary' ); ?></div>
						<div class="field-description"><?php esc_html_e( 'Include adult persons', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input id="adult-persons" type="checkbox" data-setting="adult" value="1" <# if ( '' === data.adult ) { #> checked="checked"<# } #>/><label for="adult-persons"></label>
						</div>
					</div>
				</div>
			</div>
		</div>
