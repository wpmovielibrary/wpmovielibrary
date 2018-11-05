<?php
/**
 * Movie Modal Editor Template.
 *
 * @since 3.0.0
 */

?>

		<div class="movie-posters clearfix">
		<# if ( data.posters ) { #>
			<# if ( 1 <= data.posters.length ) { #><div class="movie-poster first-poster" style="background-image:url({{ data.posters[0].sizes.medium.url }})"></div><# } #>
			<# if ( 2 <= data.posters.length ) { #><div class="movie-poster second-poster" style="background-image:url({{ data.posters[1].sizes.thumbnail.url }})"></div><# } #>
			<# if ( 3 <= data.posters.length ) { #><div class="movie-poster third-poster" style="background-image:url({{ data.posters[2].sizes.thumbnail.url }})"></div><# } #>
			<# if ( 4 <= data.posters.length ) { #><div class="movie-poster fourth-poster" style="background-image:url({{ data.posters[3].sizes.thumbnail.url }})"></div><# } #>
			<# if ( 5 <= data.posters.length ) { #><div class="movie-poster fifth-poster" style="background-image:url({{ data.posters[4].sizes.thumbnail.url }})"></div><# } #>
		<# } else if ( data.poster ) { #>
			<div class="movie-poster first-poster" style="background-image:url({{ data.poster.sizes.medium.url }})"></div>
		<# } #>
		</div>
		<div class="movie-backdrops clearfix">
		<# if ( data.backdrops ) { #>
			<# if ( 1 <= data.backdrops.length ) { #><div class="movie-backdrop first-backdrop" style="background-image:url({{ data.backdrops[0].sizes.thumbnail.url }})"></div><# } #>
			<# if ( 2 <= data.backdrops.length ) { #><div class="movie-backdrop second-backdrop" style="background-image:url({{ data.backdrops[1].sizes.medium.url }})"></div><# } #>
			<# if ( 3 <= data.backdrops.length ) { #><div class="movie-backdrop third-backdrop" style="background-image:url({{ data.backdrops[2].sizes.thumbnail.url }})"></div><# } #>
			<# if ( 4 <= data.backdrops.length ) { #><div class="movie-backdrop fourth-backdrop" style="background-image:url({{ data.backdrops[3].sizes.thumbnail.url }})"></div><# } #>
			<# if ( 5 <= data.backdrops.length ) { #><div class="movie-backdrop fifth-backdrop" style="background-image:url({{ data.backdrops[4].sizes.thumbnail.url }})"></div><# } #>
		<# } else if ( data.backdrop ) { #>
			<div class="movie-backdrop first-backdrop" style="background-image:url({{ data.backdrop.sizes.medium.url }})"></div>
		<# } #>
		</div>
