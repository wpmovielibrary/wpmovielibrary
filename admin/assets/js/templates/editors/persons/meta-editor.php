<?php
/**
 * Movies Meta Editor Template
 *
 * @since 3.0.0
 */
?>

			<# //console.log( data ); #>
			<div class="headbox-header">
				<div class="headbox-backdrop" style="background-image:url({{ data.backdrop }})"></div>
				<div class="headbox-hbar"></div>
				<div class="headbox-picture" style="background-image:url({{ data.picture }})"></div>
				<div class="headbox-hgroup">
					<div class="person-name">{{{ data.name.node || data.name.snapshot || data.name.meta || '<?php esc_html_e( 'Unamed', 'wpmovielibrary' ); ?>' }}}</div>
					<div class="person-department"><span class="label"><?php esc_html_e( 'Known for', 'wpmovielibrary' ); ?></span> {{{ data.department.node || data.department.snapshot || data.department.meta || '−' }}}</div>
				</div>
			</div>
			<div class="headbox-content">
				<div class="editor-menu">
					<button class="button" type="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
				</div>
				<div class="editor-content clearfix">
					<div class="panel left">
						<div id="person-tmdb-id-field" class="field text-field hidden">
							<div class="field-label"><?php esc_html_e( 'TMDb ID', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.tmdb_id.node || data.tmdb_id.snapshot || data.tmdb_id.meta || '−' }}}</span>
								<div class="field-control">
									<input type="number" min="0" id="person-tmdb-id" data-field="tmdb_id" value="{{ data.tmdb_id.meta || data.tmdb_id.snapshot || data.tmdb_id.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="person-imdb-id-field" class="field text-field {{ data.imdb_id.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'IMDb Id', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.imdb_id.node || data.imdb_id.snapshot || data.imdb_id.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="person-imdb-id" data-field="imdb_id" value="{{ data.imdb_id.meta || data.imdb_id.snapshot || data.imdb_id.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="person-adult-field" class="field text-field {{ data.adult.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Adult', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.adult.node || data.adult.snapshot || data.adult.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="person-adult" data-field="adult" value="{{ data.adult.meta || data.adult.snapshot || data.adult.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="person-homepage-field" class="field text-field {{ data.homepage.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Homepage', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.homepage.node || data.homepage.snapshot || data.homepage.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="person-homepage" data-field="homepage" value="{{ data.homepage.meta || data.homepage.snapshot || data.homepage.default || '' }}" />
								</div>
							</div>
						</div>
					</div>
					<div class="panel right">
						<div id="person-name-field" class="field text-field {{ data.name.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Name', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.name.node || data.name.snapshot || data.name.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="person-name" data-field="name" value="{{ data.name.meta || data.name.snapshot || data.name.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="person-also-known-as-field" class="field text-field {{ data.also_known_as.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Also known as', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.also_known_as.node || data.also_known_as.snapshot || data.also_known_as.meta || '−' }}}</span>
								<div class="field-control">
									<select id="person-also-known-as" data-field="also_known_as" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
									<# _.each( ( _.isString( data.also_known_as.meta ) ? data.also_known_as.meta.split( ',' ) : data.also_known_as.meta ) || _.isString( data.also_known_as.snapshot ? data.also_known_as.snapshot.split( ',' ) : data.also_known_as.snapshot ), function( alias ) { #>
										<option value="{{ s.trim( alias ) }}" selected="selected">{{ s.trim( alias ) }}</option>
									<# } ); #>
									</select>
								</div>
							</div>
						</div>
						<div id="person-birthday-field" class="field half-field text-field {{ data.birthday.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Date of birth', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.birthday.node || data.birthday.snapshot || data.birthday.meta || '−' }}}</span>
								<div class="field-control">
									<input type="date" id="person-birthday" data-field="birthday" value="{{ data.birthday.meta || data.birthday.snapshot || data.birthday.default || '' }}" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" />
								</div>
							</div>
						</div>
						<div id="person-deathday-field" class="field half-field text-field {{ data.deathday.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Date of death', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.deathday.node || data.deathday.snapshot || data.deathday.meta || '−' }}}</span>
								<div class="field-control">
									<input type="date" id="person-deathday" data-field="deathday" value="{{ data.deathday.meta || data.deathday.snapshot || data.deathday.default || '' }}" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" />
								</div>
							</div>
						</div>
						<div id="person-place-of-birth-field" class="field text-field {{ data.place_of_birth.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Place of birth', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.place_of_birth.node || data.place_of_birth.snapshot || data.place_of_birth.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="person-place-of-birth" data-field="place_of_birth" value="{{ data.place_of_birth.meta || data.place_of_birth.snapshot || data.place_of_birth.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="person-biography-field" class="field text-field {{ data.biography.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Biography', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.biography.node || data.biography.snapshot || data.biography.meta || '−' }}}</span>
								<div class="field-control">
									<textarea id="person-biography" data-field="biography">{{ data.biography.meta || data.biography.snapshot || data.biography.default || '' }}</textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
