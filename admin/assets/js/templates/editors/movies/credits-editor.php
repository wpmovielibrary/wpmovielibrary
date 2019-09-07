<?php
/**
 * Movies Credit Editor Template
 *
 * @since 3.0.0
 */
?>

				<div class="editor-menu">
					<button type="button" class="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
					<button type="button" class="button toggle" data-action="toggle"><span class="wpmolicon icon-up-open"></span></button>
				</div>
				<div class="editor-content active">
					<div class="panel panel-casting">
						<div class="panel-title"><?php esc_html_e( 'Casting', 'wpmovielibrary' ); ?></div>
						<div class="values">
							<div class="value">
								<select id="movie-cast" data-field="cast" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
								<# if ( ! _.isEmpty( data.actors ) ) { _.each( data.actors, function( actor ) { #>
									<option value="{{ actor.name }}" selected="selected">{{ actor.name }}</option>
								<# } ); } #>
								</select>
							</div>
						</div>
						<# if ( ! _.isEmpty( data.actors ) ) { #>
						<div class="persons-list">
							<# _.each( data.actors, function( actor ) { person = _.findWhere( data.persons, { name : actor.name } ); #>
							<div class="person cast-member<# if ( person ) { #> has-person<# } #>">
								<# if ( ! _.isNull( actor.profile_path ) ) { #>
								<div class="person-picture" style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + actor.profile_path || '' }})">
								<# } else if ( 1 === actor.gender ) { #>
								<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-female-thumbnail.png' ); ?>)">
								<# } else if ( 2 === actor.gender ) { #>
								<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-male-thumbnail.png' ); ?>)">
								<# } else { #>
								<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-neutral-thumbnail.png' ); ?>)">
								<# } #>
								<# if ( person ) { #>
									<a href="{{ person.edit_link }}" target="_blank"></a>
								<# } else { #>
									<div class="person-menu">
										<button type="button" class="button download" title="<?php esc_html_e( 'Import Person', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-download"></span></button>
									</div>
								<# } #>
								</div>
								<div class="person-name"><a href="#">{{ actor.name }}</a></div>
								<div class="person-job">{{ actor.character }}</div>
							</div>
						<# } ); #>
						</div>
						<# } #>
					</div>
					<div class="panel panel-crew">
						<div class="panel-title"><?php esc_html_e( 'Crew', 'wpmovielibrary' ); ?></div>
						<div class="panel-section crew-section half-section">
							<div class="section-title"><?php esc_html_e( 'Direction', 'wpmovielibrary' ); ?></div>
							<div class="values">
								<select id="movie-director" data-field="director" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
								<# if ( ! _.isEmpty( data.directors ) ) { _.each( data.directors, function( director ) { #>
									<option value="{{ director.name }}" selected="selected">{{ director.name }}</option>
								<# } ); } #>
								</select>
							</div>
							<# if ( ! _.isEmpty( data.directors ) ) { #>
							<div class="persons-list">
								<# _.each( data.directors, function( director ) { person = _.findWhere( data.persons, { name : director.name } ); #>
								<div class="person crew-member">
									<# if ( ! _.isNull( director.profile_path ) ) { #>
									<div class="person-picture" style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + director.profile_path || '' }})">
									<# } else if ( 1 === director.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-female-thumbnail.png' ); ?>)">
									<# } else if ( 2 === director.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-male-thumbnail.png' ); ?>)">
									<# } else { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-neutral-thumbnail.png' ); ?>)">
									<# } #>
									<# if ( person ) { #>
										<a href="{{ person.edit_link }}" target="_blank"></a>
									<# } else { #>
										<div class="person-menu">
											<button type="button" class="button download" title="<?php esc_html_e( 'Import Person', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-download"></span></button>
										</div>
									<# } #>
									</div>
									<div class="person-name"><a href="#">{{ director.name }}</a></div>
									<div class="person-job">{{ director.job }}</div>
								</div>
							<# } ); #>
							</div>
						<# } #>
						</div>
						<div class="panel-section crew-section half-section">
							<div class="section-title"><?php esc_html_e( 'Production', 'wpmovielibrary' ); ?></div>
							<div class="values">
								<select id="movie-producer" data-field="producer" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
								<# if ( ! _.isEmpty( data.producers ) ) { _.each( data.producers, function( producer ) { #>
									<option value="{{ producer.name }}" selected="selected">{{ producer.name }}</option>
								<# } ); } #>
								</select>
							</div>
							<# if ( ! _.isEmpty( data.producers ) ) { #>
							<div class="persons-list">
								<# _.each( data.producers, function( producer ) { person = _.findWhere( data.persons, { name : producer.name } ); #>
								<div class="person crew-member">
									<# if ( ! _.isNull( producer.profile_path ) ) { #>
									<div class="person-picture" style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + producer.profile_path || '' }})">
									<# } else if ( 1 === producer.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-female-thumbnail.png' ); ?>)">
									<# } else if ( 2 === producer.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-male-thumbnail.png' ); ?>)">
									<# } else { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-neutral-thumbnail.png' ); ?>)">
									<# } #>
									<# if ( person ) { #>
										<a href="{{ person.edit_link }}" target="_blank"></a>
									<# } else { #>
										<div class="person-menu">
											<button type="button" class="button download" title="<?php esc_html_e( 'Import Person', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-download"></span></button>
										</div>
									<# } #>
									</div>
									<div class="person-name"><a href="#">{{ producer.name }}</a></div>
									<div class="person-job">{{ producer.job }}</div>
								</div>
							<# } ); #>
							</div>
						<# } #>
						</div>
						<div class="panel-section crew-section half-section">
							<div class="section-title"><?php esc_html_e( 'Photography', 'wpmovielibrary' ); ?></div>
							<div class="values">
								<select id="movie-photography" data-field="photography" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
								<# if ( ! _.isEmpty( data.photography ) ) { _.each( data.photography, function( dop ) { #>
									<option value="{{ dop.name }}" selected="selected">{{ dop.name }}</option>
								<# } ); } #>
								</select>
							</div>
							<# if ( ! _.isEmpty( data.photography ) ) { #>
							<div class="persons-list">
								<# _.each( data.photography, function( dop ) { person = _.findWhere( data.persons, { name : dop.name } ); #>
								<div class="person crew-member">
									<# if ( ! _.isNull( dop.profile_path ) ) { #>
									<div class="person-picture" style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + dop.profile_path || '' }})">
									<# } else if ( 1 === dop.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-female-thumbnail.png' ); ?>)">
									<# } else if ( 2 === dop.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-male-thumbnail.png' ); ?>)">
									<# } else { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-neutral-thumbnail.png' ); ?>)">
									<# } #>
									<# if ( person ) { #>
										<a href="{{ person.edit_link }}" target="_blank"></a>
									<# } else { #>
										<div class="person-menu">
											<button type="button" class="button download" title="<?php esc_html_e( 'Import Person', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-download"></span></button>
										</div>
									<# } #>
									</div>
									<div class="person-name"><a href="#">{{ dop.name }}</a></div>
									<div class="person-job">{{ dop.job }}</div>
								</div>
							<# } ); #>
							</div>
						<# } #>
						</div>
						<div class="panel-section crew-section half-section">
							<div class="section-title"><?php esc_html_e( 'Composers', 'wpmovielibrary' ); ?></div>
							<div class="values">
								<select id="movie-composer" data-field="composer" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
								<# if ( ! _.isEmpty( data.composers ) ) { _.each( data.composers, function( composer ) { #>
									<option value="{{ composer.name }}" selected="selected">{{ composer.name }}</option>
								<# } ); } #>
								</select>
							</div>
							<# if ( ! _.isEmpty( data.composers ) ) { #>
							<div class="persons-list">
								<# _.each( data.composers, function( composer ) { person = _.findWhere( data.persons, { name : composer.name } ); #>
								<div class="person crew-member">
									<# if ( ! _.isNull( composer.profile_path ) ) { #>
									<div class="person-picture" style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + composer.profile_path || '' }})">
									<# } else if ( 1 === composer.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-female-thumbnail.png' ); ?>)">
									<# } else if ( 2 === composer.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-male-thumbnail.png' ); ?>)">
									<# } else { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-neutral-thumbnail.png' ); ?>)">
									<# } #>
									<# if ( person ) { #>
										<a href="{{ person.edit_link }}" target="_blank"></a>
									<# } else { #>
										<div class="person-menu">
											<button type="button" class="button download" title="<?php esc_html_e( 'Import Person', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-download"></span></button>
										</div>
									<# } #>
									</div>
									<div class="person-name"><a href="#">{{ composer.name }}</a></div>
									<div class="person-job">{{ composer.job }}</div>
								</div>
							<# } ); #>
							</div>
						<# } #>
						</div>
						<div class="panel-section crew-section half-section">
							<div class="section-title"><?php esc_html_e( 'Authors', 'wpmovielibrary' ); ?></div>
							<div class="values">
								<select id="movie-author" data-field="author" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
								<# if ( ! _.isEmpty( data.authors ) ) { _.each( data.authors, function( author ) { #>
									<option value="{{ author.name }}" selected="selected">{{ author.name }}</option>
								<# } ); } #>
								</select>
							</div>
							<# if ( ! _.isEmpty( data.authors ) ) { #>
							<div class="persons-list">
								<# _.each( data.authors, function( author ) { person = _.findWhere( data.persons, { name : author.name } ); #>
								<div class="person crew-member">
									<# if ( ! _.isNull( author.profile_path ) ) { #>
									<div class="person-picture" style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + author.profile_path || '' }})">
									<# } else if ( 1 === author.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-female-thumbnail.png' ); ?>)">
									<# } else if ( 2 === author.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-male-thumbnail.png' ); ?>)">
									<# } else { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-neutral-thumbnail.png' ); ?>)">
									<# } #>
									<# if ( person ) { #>
										<a href="{{ person.edit_link }}" target="_blank"></a>
									<# } else { #>
										<div class="person-menu">
											<button type="button" class="button download" title="<?php esc_html_e( 'Import Person', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-download"></span></button>
										</div>
									<# } #>
									</div>
									<div class="person-name"><a href="#">{{ author.name }}</a></div>
									<div class="person-job">{{ author.job }}</div>
								</div>
							<# } ); #>
							</div>
						<# } #>
						</div>
						<div class="panel-section crew-section half-section">
							<div class="section-title"><?php esc_html_e( 'Writers', 'wpmovielibrary' ); ?></div>
							<div class="values">
								<select id="movie-writer" data-field="writer" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
								<# if ( ! _.isEmpty( data.writers ) ) { _.each( data.writers, function( writer ) { #>
									<option value="{{ writer.name }}" selected="selected">{{ writer.name }}</option>
								<# } ); } #>
								</select>
							</div>
							<# if ( ! _.isEmpty( data.writers ) ) { #>
							<div class="persons-list">
								<# _.each( data.writers, function( writer ) { person = _.findWhere( data.persons, { name : writer.name } ); #>
								<div class="person crew-member">
									<# if ( ! _.isNull( writer.profile_path ) ) { #>
									<div class="person-picture" style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + writer.profile_path || '' }})">
									<# } else if ( 1 === writer.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-female-thumbnail.png' ); ?>)">
									<# } else if ( 2 === writer.gender ) { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-male-thumbnail.png' ); ?>)">
									<# } else { #>
									<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-neutral-thumbnail.png' ); ?>)">
									<# } #>
									<# if ( person ) { #>
										<a href="{{ person.edit_link }}" target="_blank"></a>
									<# } else { #>
										<div class="person-menu">
											<button type="button" class="button download" title="<?php esc_html_e( 'Import Person', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-download"></span></button>
										</div>
									<# } #>
									</div>
									<div class="person-name"><a href="#">{{ writer.name }}</a></div>
									<div class="person-job">{{ writer.job }}</div>
								</div>
							<# } ); #>
							</div>
						<# } #>
						</div>
					</div>
				</div>
