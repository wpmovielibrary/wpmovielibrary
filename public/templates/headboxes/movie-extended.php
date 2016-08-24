<?php
/**
 * Movie Headbox view Template
 * 
 * Showing a movie's default headbox.
 * 
 * @since    3.0
 * 
 * @uses    $movie
 */

$year = apply_filters( 'wpmoly/shortcode/format/release_date/value', $movie->release_date, 'Y' );
$genres = apply_filters( 'wpmoly/shortcode/format/genres/value', $movie->genres );
$runtime = sprintf( '%s %s', $movie->get( 'runtime' ), _x( 'min', 'movie runtime in minutes', 'wpmovielibrary' ) );
$certification = apply_filters( 'wpmoly/shortcode/format/certification/value', $movie->certification );
$director = apply_filters( 'wpmoly/shortcode/format/director/value', $movie->director );

$actors = explode( ', ', $movie->cast );
$actors = array_splice( $actors, 0, 4 );
$actors = implode( ', ', $actors );
$actors = apply_filters( 'wpmoly/shortcode/format/cast/value', $actors );

?>
	<div id="movie-headbox-<?php echo $movie->id; ?>" class="wpmoly movie-headbox theme-extended">
		<div class="headbox-header">
			<div class="headbox-backdrop-container">
				<div class="headbox-backdrop" style="background-image:url(<?php echo $movie->get_backdrop( 'random' )->render( 'medium', 'raw' ); ?>);"></div>
				<div class="headbox-angle"></div>
			</div>
			<div class="headbox-poster" style="background-image:url(<?php echo $movie->get_poster()->render( 'medium', 'raw' ); ?>);"></div>
			<div class="headbox-titles">
				<div class="movie-title"><a href="<?php echo get_permalink( $movie->id ); ?>"><?php $movie->the( 'title' ); ?></a></div>
				<div class="movie-original-title"><?php $movie->the( 'original_title' ); ?></div>
				<div class="movie-tagline"><?php $movie->the( 'tagline' ); ?></div>
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-menu">
				
			</div>
			<div class="headbox-metadata">
				<div class="movie-overview"><?php $movie->the( 'overview' ); ?></div>
			</div>
		</div>
	</div>
