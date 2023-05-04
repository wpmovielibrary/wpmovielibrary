<?php
/**
 * Movie Headbox Actors Tab Template view
 * 
 * Showing a movie's headbox actors tab, Allociné style.
 * 
 * @since    2.1.4
 * 
 * @uses    $id
 * @uses    $actors
 * @uses    $crew
 */
?>

					<div id="movie-headbox-<?php echo $id ?>-" class="wpmoly headbox allocine movie section casting">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Directors', 'wpmovielibrary' ); ?></h3>
						<div class="wpmoly headbox allocine movie meta casting">
<?php
foreach ( $meta['director'] as $actor ) :
	if ( ! empty( $actor ) ) :
?>
							<div class="wpmoly headbox allocine movie meta actor">
								<div class="wpmoly headbox allocine movie meta photo"><span class="wpmolicon icon-camera"></span></div>
								<div class="wpmoly headbox allocine movie meta name"><?php echo $actor ?></div>
							</div>

<?php
	endif;
endforeach;
?>
						</div>
					</div>
					<hr />
					<div id="movie-headbox-<?php echo $id ?>-" class="wpmoly headbox allocine movie section casting">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Actors and Actresses', 'wpmovielibrary' ); ?></h3>
						<div class="wpmoly headbox allocine movie meta casting">
<?php
$i = 0; $class = '';
foreach ( $meta['cast'] as $actor ) :
	if ( ! empty( $actor ) ) :

		if ( 8 == $i )
			$class = ' small';
?>
							<div class="wpmoly headbox allocine movie meta actor<?php echo $class ?>">
								<div class="wpmoly headbox allocine movie meta photo"><span class="wpmolicon icon-camera"></span></div>
								<div class="wpmoly headbox allocine movie meta name"><?php echo $actor ?></div>
							</div>

<?php
			$i++;
	endif;
endforeach;
?>
						</div>
					</div>
					<hr />
					<div id="movie-headbox-<?php echo $id ?>-" class="wpmoly headbox allocine movie section casting">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Scénario', 'wpmovielibrary' ); ?></h3>
						<div class="wpmoly headbox allocine movie meta casting">
<?php
foreach ( $meta['writer'] as $actor ) :
	if ( ! empty( $actor ) ) :
?>
							<div class="wpmoly headbox allocine movie meta actor small">
								<div class="wpmoly headbox allocine movie meta photo"><span class="wpmolicon icon-camera"></span></div>
								<div class="wpmoly headbox allocine movie meta name"><?php echo $actor ?></div>
							</div>

<?php
	endif;
endforeach;
?>
						</div>
					</div>
					<hr />
					<div id="movie-headbox-<?php echo $id ?>-" class="wpmoly headbox allocine movie section casting">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Soundtrack', 'wpmovielibrary' ); ?></h3>
						<div class="wpmoly headbox allocine movie meta casting">
<?php
foreach ( $meta['composer'] as $actor ) :
	if ( ! empty( $actor ) ) :
?>
							<div class="wpmoly headbox allocine movie meta actor small">
								<div class="wpmoly headbox allocine movie meta photo"><span class="wpmolicon icon-camera"></span></div>
								<div class="wpmoly headbox allocine movie meta name"><?php echo $actor ?></div>
							</div>

<?php
	endif;
endforeach;
?>
						</div>
					</div>
					<hr />
					<div id="movie-headbox-<?php echo $id ?>-" class="wpmoly headbox allocine movie section casting">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Production', 'wpmovielibrary' ); ?></h3>
						<div class="wpmoly headbox allocine movie meta casting">
<?php
foreach ( $meta['producer'] as $actor ) :
	if ( ! empty( $actor ) ) :
?>
							<div class="wpmoly headbox allocine movie meta actor small">
								<div class="wpmoly headbox allocine movie meta photo"><span class="wpmolicon icon-camera"></span></div>
								<div class="wpmoly headbox allocine movie meta name"><?php echo $actor ?></div>
							</div>

<?php
	endif;
endforeach;
?>
						</div>
					</div>
					<hr />
					<div id="movie-headbox-<?php echo $id ?>-" class="wpmoly headbox allocine movie section casting">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Companies', 'wpmovielibrary' ); ?></h3>
						<div class="wpmoly headbox allocine movie meta casting">
<?php
foreach ( $meta['production_companies'] as $actor ) :
	if ( ! empty( $actor ) ) :
?>
							<div class="wpmoly headbox allocine movie meta actor small">
								<div class="wpmoly headbox allocine movie meta photo"><span class="wpmolicon icon-camera"></span></div>
								<div class="wpmoly headbox allocine movie meta name"><?php echo $actor ?></div>
							</div>

<?php
	endif;
endforeach;
?>
						</div>
					</div> 
