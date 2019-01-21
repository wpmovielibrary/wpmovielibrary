<?php
/**
 * Movies Editor Search Loading Template
 *
 * @since 3.0.0
 */

?>

		<div class="search-loading" style="background-image:url({{ 'https://image.tmdb.org/t/p/original' + data.backdrop }})">
			<div class="dust"></div>
			<p><?php esc_html_e( 'Downloading data, please wait...', 'wpmovielibrary' ); ?></p>
		</div>
