<?php
/**
 * Movies Editor Search Results Template
 *
 * @since 3.0.0
 */
?>

			<# _.each( data.results, function( model ) { #>
			<div class="search-result">
				<div class="result-thumbnail"<# if ( ! _.isNull( model.poster_path ) ) { #> style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + model.poster_path }})"<# } #>>
					<div class="result-menu">
						<button type="button" class="button queue" data-action="enqueue" data-movie-id="{{ model.id }}"><span class="wpmolicon icon-edit"></span></button>
						<button type="button" class="button import" data-action="import" data-movie-id="{{ model.id }}"><span class="wpmolicon icon-download"></span></button>
					</div>
				</div>
				<div class="result-year">{{ new Date( model.release_date ).getFullYear() }}</div>
				<div class="result-title"><a href="https://themoviedb.org/movie/{{ model.id }}" target="_blank" title="\"{{ model.title }}\" on TheMovieDB.org">{{ model.title }}</a></div>
			</div>
			<# } ); #>

			<# if ( data.state.totalObjects ) { #>
			<div class="results-pagination">
				<button type="button" class="button first" data-action="first" title="<?php esc_html_e( 'First page' ); ?>"<# if ( 1 >= data.state.currentPage ) { #> disabled="disabled"<# } #>>&laquo;</button>
				<button type="button" class="button previous" data-action="previous" title="<?php esc_html_e( 'Previous page' ); ?>"<# if ( 1 >= data.state.currentPage ) { #> disabled="disabled"<# } #>>&lsaquo;</button>
				<# _.times( data.state.totalPages, function( page ) { page = page + 1; #>
				<button type="button" class="button number<# if ( data.state.currentPage === page ) { #> active<# } #>" data-action="jump-to" data-value="{{ page }}"<# if ( data.state.currentPage === page ) { #> disabled="disabled"<# } #>>{{ page }}</button>
				<# } ); #>
				<button type="button" class="button next" data-action="next" title="<?php esc_html_e( 'Next page' ); ?>"<# if ( data.state.currentPage >= data.state.totalPages - 1 ) { #> disabled="disabled"<# } #>>&rsaquo;</button>
				<button type="button" class="button last" data-action="last" title="<?php esc_html_e( 'Last page' ); ?>"<# if ( data.state.currentPage >= data.state.totalPages - 1 ) { #> disabled="disabled"<# } #>>&raquo;</button>
			</div>
			<# } #>
