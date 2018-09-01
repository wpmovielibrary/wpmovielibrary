<?php
/**
 * Movies Editor Search Template
 *
 * @since 3.0.0
 */

 use \wpmoly\core\L10n;
?>

			<div class="search-field">
				<input id="search-query" type="text" class="search-query" placeholder="<?php esc_html_e( 'Ex: Interstellar', 'wpmovielibrary' ); ?>" value="{{ data.query }}" data-value="search-query" required="true" />
				<button type="button" class="button search" data-action="search"><span class="wpmolicon icon-search"></span></button>
				<button type="button" class="button reset" data-action="reset"><span class="wpmolicon icon-no"></span></button>
			</div>
			<button type="button" class="button advanced" data-action="advanced-search"><span class="wpmolicon icon-filter"></span> <?php esc_html_e( 'Advanced Search', 'wpmovielibrary' ); ?></button>
			<div class="search-filters">
				<div class="search-filter">
					<div class="field date-field">
						<div class="field-label"><?php esc_html_e( 'Year', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="number" min="1895" max="2042" step="1" data-setting="year" value="{{ data.year }}" placeholder="2018" />
						</div>
					</div>
				</div>
				<div class="search-filter">
					<div class="field date-field">
						<div class="field-label"><?php esc_html_e( 'Primary Year', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="number" min="1895" max="2042" step="1" data-setting="primary_year" value="{{ data.primary_year }}" placeholder="2018" />
						</div>
					</div>
				</div>
				<div class="search-filter">
					<div class="field select-field">
						<div class="field-label"><?php esc_html_e( 'Language', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<select data-field="subtitles" data-selectize="1" data-setting="language">
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
						<div class="field-description"><?php esc_html_e( 'Include adult movies', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input id="adult-movies" type="checkbox" data-setting="adult" value="1" <# if ( '' === data.adult ) { #> checked="checked"<# } #>/><label for="adult-movies"></label>
						</div>
					</div>
				</div>
			</div>
		</div>
