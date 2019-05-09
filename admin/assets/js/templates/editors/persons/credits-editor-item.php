<?php
/**
 * Movies Meta Editor Template
 *
 * @since 3.0.0
 */
?>

							<div class="credit-poster" style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + data.poster_path || '' }})">
								<# if ( data.exists ) { #>#>
								<a href="#" class="button export" title="<?php esc_html_e( 'View movie', 'wpmovielibrary' ); ?>">{{ 'svg:icon:export' }}</a>
								<# } else { #>
								<button type="button" class="button download" data-action="import" data-tmdb-id="{{ data.tmdb_id }}" title="<?php esc_html_e( 'Import movie', 'wpmovielibrary' ); ?>">{{ 'svg:icon:download' }}</button>
								<# } #>
							</div>
							<div class="credit-year">{{ data.year || '' }}</div>
							<div class="credit-title"><a href="https://www.themoviedb.org/movie/{{ data.tmdb_id }}" target="_blank" title="{{ data.title }}">{{ data.title }}</a></div>
							<# if ( data.character ) { #>
							<div class="credit-character">{{ data.character }}</div>
							<# } else if ( data.job ) { #>
							<div class="credit-job">{{ data.job }}</div>
							<# } #>
