<?php
/**
 * Movies Meta Editor Template
 *
 * @since 3.0.0
 */
?>

			<div class="headbox-header">
				<div class="headbox-backdrop" style="background-image:url({{ data.backdrop }})"></div>
				<div class="headbox-hbar"></div>
				<div class="headbox-poster" style="background-image:url({{ data.poster }})"></div>
				<div class="headbox-hgroup">
					<div class="movie-title">{{{ data.title.node || data.title.snapshot || data.title.meta || '<?php esc_html_e( 'Untitled', 'wpmovielibrary' ); ?>' }}}</div>
					<div class="movie-year">{{{ data.year || '−' }}}</div>
					<div class="movie-director"><span class="label"><?php esc_html_e( 'Directed by', 'wpmovielibrary' ); ?></span> {{{ data.director.node || data.director.snapshot || data.director.meta || '−' }}}</div>
				</div>
			</div>
			<div class="headbox-content">
				<div class="editor-menu">
					<button class="button" type="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
					<button class="button" type="button refresh" data-action="reload"><span class="wpmolicon icon-refresh"></span></button>
					<button class="button" type="button help" data-action="help"><span class="wpmolicon icon-help-circled-o"></span></button>
				</div>
				<div class="editor-content clearfix">
					<div class="panel left">
						<div id="movie-runtime-field" class="field text-field {{ data.runtime.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Runtime', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.runtime.node || data.runtime.snapshot || data.runtime.meta || '−' }}}</span>
								<div class="field-control">
									<input type="number" min="0" id="movie-runtime" data-field="runtime" value="{{ data.runtime.meta || data.runtime.snapshot || data.runtime.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="movie-budget-field" class="field text-field {{ data.budget.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Budget', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.budget.node || data.budget.snapshot || data.budget.meta || '−' }}}</span>
								<div class="field-control">
									<input type="number" min="0" id="movie-budget" data-field="budget" value="{{ data.budget.meta || data.budget.snapshot || data.budget.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="movie-revenue-field" class="field text-field {{ data.revenue.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Revenue', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.revenue.node || data.revenue.snapshot || data.revenue.meta || '−' }}}</span>
								<div class="field-control">
									<input type="number" min="0" id="movie-revenue" data-field="revenue" value="{{ data.revenue.meta || data.revenue.snapshot || data.revenue.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="movie-tmdb-id-field" class="field text-field hidden">
							<div class="field-label"><?php esc_html_e( 'TMDb ID', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.tmdb_id.node || data.tmdb_id.snapshot || data.tmdb_id.meta || '−' }}}</span>
								<div class="field-control">
									<input type="number" min="0" id="movie-tmdb-id" data-field="tmdb_id" value="{{ data.tmdb_id.meta || data.tmdb_id.snapshot || data.tmdb_id.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="movie-imdb-id-field" class="field text-field hidden">
							<div class="field-label"><?php esc_html_e( 'IMDb Id', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.imdb_id.node || data.imdb_id.snapshot || data.imdb_id.meta || '−' }}}</span>
								<div class="field-control">
									<input type="number" min="0" id="movie-imdb-id" data-field="imdb_id" value="{{ data.imdb_id.meta || data.imdb_id.snapshot || data.imdb_id.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="movie-adult-field" class="field text-field {{ data.adult.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Adult', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.adult.node || data.adult.snapshot || data.adult.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="movie-adult" data-field="adult" value="{{ data.adult.meta || data.adult.snapshot || data.adult.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="movie-homepage-field" class="field text-field {{ data.homepage.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Homepage', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.homepage.node || data.homepage.snapshot || data.homepage.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="movie-homepage" data-field="homepage" value="{{ data.homepage.meta || data.homepage.snapshot || data.homepage.default || '' }}" />
								</div>
							</div>
						</div>
					</div>
					<div class="panel right">
						<div id="movie-title-field" class="field half-field text-field {{ data.title.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Title', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.title.node || data.title.snapshot || data.title.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="movie-title" data-field="title" value="{{ data.title.meta || data.title.snapshot || data.title.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="movie-original-title-field" class="field half-field text-field {{ data.original_title.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Original Title', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.original_title.node || data.original_title.snapshot || data.original_title.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="movie-original-title" data-field="original_title" value="{{ data.original_title.meta || data.original_title.snapshot || data.original_title.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="movie-release-date-field" class="field half-field text-field {{ data.release_date.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Release Date', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.release_date.node || data.release_date.snapshot || data.release_date.meta || '−' }}}</span>
								<div class="field-control">
									<input type="date" id="release-date" data-field="release_date" value="{{ data.release_date.meta || data.release_date.snapshot || data.release_date.default || '' }}" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" />
								</div>
							</div>
						</div>
						<div id="movie-genres-field" class="field half-field select-field {{ data.genres.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Genres', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.genres.node || _.pluck( data.genres.snapshot, 'name' ) || data.genres.meta || '−' }}}</span>
								<div class="field-control">
									<select id="movie-genres" data-field="genres" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
									<# _.each( ( _.isString( data.genres.meta ) ? data.genres.meta.split( ',' ) : data.genres.meta ) || _.pluck( data.genres.snapshot, 'name' ), function( genre ) { #>
										<option value="{{ s.trim( genre ) }}" selected="selected">{{ s.trim( genre ) }}</option>
									<# } ); #>
									</select>
								</div>
							</div>
						</div>
						<div id="movie-tagline-field" class="field text-field {{ data.tagline.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Tagline', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.tagline.node || data.tagline.snapshot || data.tagline.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="movie-tagline" data-field="tagline" value="{{ data.tagline.meta || data.tagline.snapshot || data.tagline.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="movie-overview-field" class="field text-field {{ data.overview.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Overview', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<p class="value">{{{ data.overview.node || data.overview.snapshot || data.overview.meta || '−' }}}</p>
								<div class="field-control">
									<textarea id="movie-overview" data-field="overview">{{ data.overview.meta || data.overview.snapshot || data.overview.default || '' }}</textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
