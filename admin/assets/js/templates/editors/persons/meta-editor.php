<?php
/**
 * Movies Meta Editor Template
 *
 * @since 3.0.0
 */
?>

			<# console.log( data ); #>
			<div class="headbox-header">
				<div class="headbox-backdrop" style="background-image:url(https://image.tmdb.org/t/p/original/xu9zaAevzQ5nnrsXN6JcahLnG4i.jpg)"></div>
				<div class="headbox-hbar"></div>
				<div class="headbox-picture" style="background-image:url(https://image.tmdb.org/t/p/original/jdRmHrG0TWXGhs4tO6TJNSoL25T.jpg)"></div>
				<div class="headbox-hgroup">
					<div class="movie-name">Matthew McConaughey</div>
					<div class="movie-departement"><span class="label"><?php esc_html_e( 'Known for', 'wpmovielibrary' ); ?></span> Acting</div>
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
						<div id="person-name-field" class="field half-field text-field {{ data.name.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Name', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.name.node || data.name.snapshot || data.name.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="person-name" data-field="name" value="{{ data.name.meta || data.name.snapshot || data.name.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="person-also-known-as-field" class="field half-field text-field {{ data.also_known_as.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Also known as', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.also_known_as.node || data.also_known_as.snapshot || data.also_known_as.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="person-also-known-as" data-field="also_known_as" value="{{ data.also_known_as.meta || data.also_known_as.snapshot || data.also_known_as.default || '' }}" />
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
						<div id="person-gender-field" class="field half-field text-field {{ data.gender.status + '-field' || 'empty-field' }}">
							<div class="field-label"><?php esc_html_e( 'Gender', 'wpmovielibrary' ); ?></div>
							<div class="field-value">
								<span class="value">{{{ data.gender.node || data.gender.snapshot || data.gender.meta || '−' }}}</span>
								<div class="field-control">
									<input type="text" id="person-gender" data-field="gender" value="{{ data.gender.meta || data.gender.snapshot || data.gender.default || '' }}" />
								</div>
							</div>
						</div>
						<div id="person-place-of-birth-field" class="field half-field text-field {{ data.place_of_birth.status + '-field' || 'empty-field' }}">
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
