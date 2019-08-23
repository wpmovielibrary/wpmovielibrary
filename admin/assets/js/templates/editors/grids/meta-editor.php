<?php
/**
 * Grid Meta Editor Template
 *
 * @since 3.0.0
 */
?>

			<div class="metabox-menu">
				<ul>
					<li class="tab" data-tab="presets"><a class="navigate"><?php esc_html_e( 'Presets', 'wpmovielibrary' ); ?></a></li>
					<li class="tab" data-tab="appearance"><a class="navigate"><?php esc_html_e( 'Appearance', 'wpmovielibrary' ); ?></a></li>
					<li class="tab" data-tab="settings"><a class="navigate"><?php esc_html_e( 'Settings', 'wpmovielibrary' ); ?></a></li>
				</ul>
			</div>
			<div class="metabox-content">
				<div id="wpmoly-grid-presets" data-tab="presets" class="panel">
					<div id="wpmoly-grid-preset" class="field radio-image-field">
						<div class="field-label"><?php esc_html_e( 'Preset', 'wpmovielibrary' ); ?></div>
						<div class="field-description"><?php esc_html_e( 'Select a preset for the grid. Presets are predefined list of parameters used to query grid items dynamically, but can be overriden by visitor or URLs.', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<div class="field-values">
<# if ( 'movie' === data.type ) { #>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="alphabetical-movies-preset" value="alphabetical"<# if ( 'alphabetical' === data.preset ) { #> checked="checked"<# } #> />
									<label for="alphabetical-movies-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/alphabetical-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Alphabetical Movies', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="unalphabetical-movies-preset" value="unalphabetical"<# if ( 'unalphabetical' === data.preset ) { #> checked="checked"<# } #> />
									<label for="unalphabetical-movies-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/unalphabetical-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Unalphabetical Movies', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="current-year-movies-preset" value="current_year"<# if ( 'current_year' === data.preset ) { #> checked="checked"<# } #> />
									<label for="current-year-movies-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/current-year-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'This Year Movies', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="last-year-movies-preset" value="last_year"<# if ( 'last_year' === data.preset ) { #> checked="checked"<# } #> />
									<label for="last-year-movies-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/last-year-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Last Year Movies', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="last-added-movies-preset" value="last_added"<# if ( 'last_added' === data.preset ) { #> checked="checked"<# } #> />
									<label for="last-added-movies-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/last-added-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Latest Added Movies', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="first-added-movies-preset" value="first_added"<# if ( 'first_added' === data.preset ) { #> checked="checked"<# } #> />
									<label for="first-added-movies-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/first-added-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Earliest Added Movies', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="last-released-movies-preset" value="last_released"<# if ( 'last_released' === data.preset ) { #> checked="checked"<# } #> />
									<label for="last-released-movies-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/last-released-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Latest Released Movies', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="first-released-movies-preset" value="first_released"<# if ( 'first_released' === data.preset ) { #> checked="checked"<# } #> />
									<label for="first-released-movies-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/first-released-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Earliest Released Movies', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="incoming-movies-preset" value="incoming"<# if ( 'incoming' === data.preset ) { #> checked="checked"<# } #> />
									<label for="incoming-movies-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/incoming-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Incoming Movies', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="most-rated-movies-preset" value="most_rated"<# if ( 'most_rated' === data.preset ) { #> checked="checked"<# } #> />
									<label for="most-rated-movies-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/most-rated-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Most Rated Movies', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="least-rated-movies-preset" value="least_rated"<# if ( 'least_rated' === data.preset ) { #> checked="checked"<# } #> />
									<label for="least-rated-movies-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/least-rated-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Least Rated Movies', 'wpmovielibrary' ); ?></span></label>
								</div>
<# } else if ( 'person' === data.type ) { #>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="alphabetical-persons-preset" value="alphabetical"<# if ( 'alphabetical' === data.preset ) { #> checked="checked"<# } #> />
									<label for="alphabetical-persons-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/alphabetical-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Alphabetical Persons', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="unalphabetical-persons-preset" value="unalphabetical"<# if ( 'unalphabetical' === data.preset ) { #> checked="checked"<# } #> />
									<label for="unalphabetical-persons-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/unalphabetical-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Unalphabetical Persons', 'wpmovielibrary' ); ?></span></label>
								</div>
<# } else if ( 'actor' === data.type ) { #>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="alphabetical-actors-preset" value="alphabetical"<# if ( 'alphabetical' === data.preset ) { #> checked="checked"<# } #> />
									<label for="alphabetical-actors-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/alphabetical-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Alphabetical Actors', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="unalphabetical-actors-preset" value="unalphabetical"<# if ( 'unalphabetical' === data.preset ) { #> checked="checked"<# } #> />
									<label for="unalphabetical-actors-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/unalphabetical-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Unalphabetical Actors', 'wpmovielibrary' ); ?></span></label>
								</div>
<# } else if ( 'genre' === data.type ) { #>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="alphabetical-genres-preset" value="alphabetical"<# if ( 'alphabetical' === data.preset ) { #> checked="checked"<# } #> />
									<label for="alphabetical-genres-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/alphabetical-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Alphabetical Genres', 'wpmovielibrary' ); ?></span></label>
								</div>
								<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="unalphabetical-genres-preset" value="unalphabetical"<# if ( 'unalphabetical' === data.preset ) { #> checked="checked"<# } #> />
									<label for="unalphabetical-genres-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/unalphabetical-movies.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Unalphabetical Genres', 'wpmovielibrary' ); ?></span></label>
								</div>
<# } #>
								<!--<div class="field-value">
									<input type="radio" data-field="preset" name="grid-preset" id="custom-preset" value="custom"<# if ( 'custom' === data.preset ) { #> checked="checked"<# } #> />
									<label for="custom-preset"><img src="<?php echo esc_url( WPMOLY_URL . 'admin/assets/img/custom.png' ); ?>" alt="" ><span class="value-title"><?php esc_html_e( 'Custom', 'wpmovielibrary' ); ?></span></label>
								</div>-->
							</div>
						</div>
					</div>
				</div>
				<div id="wpmoly-grid-appearance" data-tab="appearance" class="panel">
<# if ( 'grid' === data.mode ) { #>
					<div id="wpmoly-grid-columns" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Columns', 'wpmovielibrary' ); ?></div>
						<div class="field-description"><?php esc_html_e( 'Number of grid columns.', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="number" size="2" min="1" max="24" data-field="columns" placeholder="5" value="{{ data.columns }}" />
						</div>
					</div>
					<div id="wpmoly-grid-rows" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'Rows', 'wpmovielibrary' ); ?></div>
						<div class="field-description"><?php esc_html_e( 'Number of grid rows.', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="number" size="2" min="1" data-field="rows" placeholder="4" value="{{ data.rows }}" />
						</div>
					</div>
<# } else if ( 'list' === data.mode ) { #>
					<div id="wpmoly-grid-list-columns" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'List columns', 'wpmovielibrary' ); ?></div>
						<div class="field-description"><?php esc_html_e( 'Number of list columns.', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="number" size="2" min="1" data-field="list_columns" placeholder="3" value="{{ data.list_columns }}" />
						</div>
					</div>
					<div id="wpmoly-grid-list-rows" class="field text-field half-field">
						<div class="field-label"><?php esc_html_e( 'List rows', 'wpmovielibrary' ); ?></div>
						<div class="field-description"><?php esc_html_e( 'Number of list rows.', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input type="number" size="2" min="1" data-field="list_rows" placeholder="8" value="{{ data.list_rows }}" />
						</div>
					</div>
<# } #>
				</div>
				<div id="wpmoly-grid-settings" data-tab="settings" class="panel">
					<div id="wpmoly-grid-enable-pagination" class="field checkbox-field">
						<div class="field-label"><?php esc_html_e( 'Enable Pagination', 'wpmovielibrary' ); ?></div>
						<div class="field-description"><?php esc_html_e( 'Allow visitors to browse through the grid. Is Ajax browsing is enabled, pagination will use Ajax to dynamically load new pages instead of reloading.', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input id="enable-pagination-toggle" type="checkbox" data-field="enable_pagination" value="1"<# if ( data.enable_pagination ) { #> checked="checked"<# } #> /><label for="enable-pagination-toggle"></label>
						</div>
					</div>
					<div id="wpmoly-grid-settings-control" class="field checkbox-field">
						<div class="field-label"><?php esc_html_e( 'Enable user settings', 'wpmovielibrary' ); ?></div>
						<div class="field-description"><?php esc_html_e( 'Visitors will be able to change some settings to browse the grid differently. The changes only impact the user’s view and are not kept between visits.', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input id="settings-control-toggle" type="checkbox" data-action="toggle" data-field="settings_control" value="1"<# if ( data.settings_control ) { #> checked="checked"<# } #> /><label for="settings-control-toggle"></label>
						</div>
					</div>
					<div id="wpmoly-grid-custom-letter" class="field checkbox-field">
						<div class="field-label"><?php esc_html_e( 'Enable letter filtering', 'wpmovielibrary' ); ?></div>
						<div class="field-description"><?php esc_html_e( 'Allow visitors to filter the grid by letters.', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input id="custom-letter-toggle" type="checkbox" data-field="custom_letter" value="1"<# if ( data.custom_letter ) { #> checked="checked"<# } #> /><label for="custom-letter-toggle"></label>
						</div>
					</div>
					<div id="wpmoly-grid-custom-order" class="field checkbox-field">
						<div class="field-label"><?php esc_html_e( 'Enable custom ordering', 'wpmovielibrary' ); ?></div>
						<div class="field-description"><?php esc_html_e( 'Allow visitors to change the grid ordering, ie. the sorting and ordering settings.', 'wpmovielibrary' ); ?></div>
						<div class="field-control">
							<input id="custom-order-toggle" type="checkbox" data-field="custom_order" value="1"<# if ( data.custom_order ) { #> checked="checked"<# } #> /><label for="custom-order-toggle"></label>
						</div>
					</div>
				</div>
			</div>
