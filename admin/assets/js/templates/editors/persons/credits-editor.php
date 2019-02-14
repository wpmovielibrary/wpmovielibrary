<?php
/**
 * Movies Meta Editor Template
 *
 * @since 3.0.0
 */
?>


				<div class="editor-menu">
					<!--<button class="button" type="button movies" data-action="movies"><?php esc_html_e( 'Movies', 'wpmovielibrary' ); ?></button>
					<button class="button" type="button tv-shows" data-action="tv-shows"><?php esc_html_e( 'TV Shows', 'wpmovielibrary' ); ?></button>-->
				</div>
				<div class="editor-content">
					<div class="panel">
						<div class="panel-title"><?php esc_html_e( 'Credits', 'wpmovielibrary' ); ?></div>
						<div class="panel-subtitle"><?php esc_html_e( 'Cast', 'wpmovielibrary' ); ?></div>
						<# if ( ! _.isEmpty( data.cast ) ) { _.each( data.cast, function( credit ) { #>
						<div class="credit-item">
							<div class="credit-poster" style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + credit.poster_path || '' }})">
								<# if ( credit.exists ) { #>#>
								<a href="#" class="button export" title="<?php esc_html_e( 'View movie', 'wpmovielibrary' ); ?>">{{ 'svg:icon:export' }}</a>
								<# } else { #>
								<button type="button" class="button download" data-action="download" data-tmdb-id="{{ credit.tmdb_id }}" title="<?php esc_html_e( 'Import movie', 'wpmovielibrary' ); ?>">{{ 'svg:icon:download' }}</button>
								<# } #>
							</div>
							<div class="credit-year">{{ credit.year || '' }}</div>
							<div class="credit-title"><a href="https://www.themoviedb.org/movie/{{ credit.tmdb_id }}" target="_blank" title="{{ credit.title }}">{{ credit.title }}</a></div>
							<div class="credit-character">{{ credit.character }}</div>
						</div>
						<# } ); } #>
						<div class="panel-subtitle"><?php esc_html_e( 'Crew', 'wpmovielibrary' ); ?></div>
						<# if ( ! _.isEmpty( data.crew ) ) { _.each( data.crew, function( credit ) { #>
						<div class="credit-item">
							<div class="credit-poster" style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + credit.poster_path || '' }})">
								<# if ( credit.exists ) { #>#>
								<a href="#" class="button export" title="<?php esc_html_e( 'View movie', 'wpmovielibrary' ); ?>">{{ 'svg:icon:export' }}</a>
								<# } else { #>
								<button type="button" class="button download" data-action="download" data-tmdb-id="{{ credit.tmdb_id }}" title="<?php esc_html_e( 'Import movie', 'wpmovielibrary' ); ?>">{{ 'svg:icon:download' }}</button>
								<# } #>
							</div>
							<div class="credit-year">{{ credit.year || '' }}</div>
							<div class="credit-title"><a href="https://www.themoviedb.org/movie/{{ credit.tmdb_id }}" target="_blank" title="{{ credit.title }}">{{ credit.title }}</a></div>
							<div class="credit-job">{{ credit.job }}</div>
						</div>
						<# } ); } #>
					</div>
				</div>
