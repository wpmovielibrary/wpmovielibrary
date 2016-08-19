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
	<div id="movie-headbox-<?php echo $movie->id; ?>" class="wpmoly movie headbox default">
		<div class="wpmoly movie headbox header">
			<div class="wpmoly movie headbox backdrop-container">
				<div class="wpmoly movie headbox backdrop" style="background-image:url(<?php echo $movie->get_backdrop( 'random' )->render( 'medium', 'raw' ); ?>);"></div>
				<div class="wpmoly movie headbox angle"></div>
			</div>
			<div class="wpmoly movie headbox poster" style="background-image:url(<?php echo $movie->get_poster()->render( 'medium', 'raw' ); ?>);"></div>
			<div class="wpmoly movie headbox titles">
				<h3 class="wpmoly movie meta title"><a href="<?php the_permalink(); ?>"><?php $movie->the( 'title' ); ?></a></h3>
				<h4 class="wpmoly movie meta original-title"><?php $movie->the( 'original_title' ); ?></h4>
				<h5 class="wpmoly movie meta tagline"><?php $movie->the( 'tagline' ); ?></h5>
			</div>
		</div>
		<div class="wpmoly movie headbox content clearfix">
			<div class="wpmoly movie headbox cast">
				<div class="wpmoly movie meta director"><?php _e( 'Directed by', 'wpmovielibrary' ); ?> <?php echo $director; ?></div>
				<div class="wpmoly movie meta actors"><?php _e( 'Staring', 'wpmovielibrary' ); ?> <?php echo $actors; ?></div>
			</div>
			<div class="wpmoly movie headbox metadata">
				<div class="wpmoly movie headbox meta release-info">
					<span class="wpmoly movie headbox meta year"><?php echo $year; ?></span>&nbsp;|&nbsp;<span class="wpmoly movie headbox meta runtime"><?php echo $runtime; ?></span>&nbsp;|&nbsp;<span class="wpmoly movie headbox meta genres"><?php echo $genres; ?></span>&nbsp;|&nbsp;<span class="wpmoly movie headbox meta certification"><?php echo $certification; ?></span>
				</div>
				<div class="wpmoly movie headbox meta overview"><?php $movie->the( 'overview' ); ?></div>
			</div>
		</div>
	</div>
