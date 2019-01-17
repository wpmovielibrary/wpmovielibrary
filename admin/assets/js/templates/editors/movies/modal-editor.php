<?php
/**
 * Movie Modal Editor Template.
 *
 * @since 3.0.0
 */

use wpmoly\utils;

$meta = utils\get_registered_movie_meta();
?>

		<div class="movie-editor-menu">
			<div class="movie-editor-navigation">
				<span class="spinner"></span>
				<button type="button" class="button preview" data-action="preview-movie" title="<?php esc_html_e( 'Preview' ); ?>"><span class="wpmolicon icon-screen"></span></button>
				<# if ( data.has_previous ) { #><button type="button" class="button previous" data-action="browse-previous" title="<?php esc_html_e( 'Previous' ); ?>"><span class="wpmolicon icon-left-arrow"></span></button><# } #>
				<# if ( data.has_next ) { #><button type="button" class="button next" data-action="browse-next" title="<?php esc_html_e( 'Next' ); ?>"><span class="wpmolicon icon-right-arrow"></span></button><# } #>
				<button type="button" class="button close" data-action="close-modal" title="<?php esc_html_e( 'Close' ); ?>"><span class="wpmolicon icon-no"></span></button>
			</div>
		</div>

		<div class="movie-editor-images"></div>

		<div class="movie-editor-meta">
			<div class="movie-editor-meta-inner">
				<div class="movie-metadata">
					<div id="title" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Title', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="title" value="{{ data.title }}" />
						</div>
					</div>
					<div id="original_title" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Original Title', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="original_title" value="{{ data.original_title }}" />
						</div>
					</div>
					<div id="tagline" class="field text-field">
						<div class="field-label"><?php esc_html_e( 'Tagline', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="tagline" value="{{ data.tagline }}" />
						</div>
					</div>
					<div id="overview" class="field text-field">
						<div class="field-label"><?php esc_html_e( 'Overview', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<textarea data-field="overview">{{ data.overview }}</textarea>
						</div>
					</div>
					<div id="release_date" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Release Date', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="release_date" value="{{ data.release_date }}" />
						</div>
					</div>
					<div id="local_release_date" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Local Release Date', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="local_release_date" value="{{ data.local_release_date }}" />
						</div>
					</div>
					<div id="runtime" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Runtime', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="runtime" value="{{ data.runtime }}" />
						</div>
					</div>
					<div id="production_companies" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Production', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="production_companies" value="{{ data.production_companies }}" />
						</div>
					</div>
					<div id="production_countries" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Country', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="production_countries" value="{{ data.production_countries }}" />
						</div>
					</div>
					<div id="spoken_languages" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Languages', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="spoken_languages" value="{{ data.spoken_languages }}" />
						</div>
					</div>
					<div id="genres" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Genres', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="genres" value="{{ data.genres }}" />
						</div>
					</div>
					<div id="director" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Director', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="director" value="{{ data.director }}" />
						</div>
					</div>
					<div id="producer" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Producer', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="producer" value="{{ data.producer }}" />
						</div>
					</div>
					<div id="photography" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Director of Photography', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="photography" value="{{ data.photography }}" />
						</div>
					</div>
					<div id="composer" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Original Music Composer', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="composer" value="{{ data.composer }}" />
						</div>
					</div>
					<div id="author" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Author', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="author" value="{{ data.author }}" />
						</div>
					</div>
					<div id="writer" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Writer', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="writer" value="{{ data.writer }}" />
						</div>
					</div>
					<div id="certification" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Certification', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="certification" value="{{ data.certification }}" />
						</div>
					</div>
					<div id="budget" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Budget', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="budget" value="{{ data.budget }}" />
						</div>
					</div>
					<div id="revenue" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Revenue', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="revenue" value="{{ data.revenue }}" />
						</div>
					</div>
					<div id="imdb_id" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'IMDb Id', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="imdb_id" value="{{ data.imdb_id }}" />
						</div>
					</div>
					<div id="tmdb_id" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'TMDb ID', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="tmdb_id" value="{{ data.tmdb_id }}" />
						</div>
					</div>
					<div id="adult" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Adult', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="adult" value="{{ data.adult }}" />
						</div>
					</div>
					<div id="homepage" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Homepage', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="text" data-field="homepage" value="{{ data.homepage }}" />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="movie-editor-details">
			<div class="movie-editor-details-inner">
				<div class="movie-details">
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
				</div>
			</div>
		</div>
