<?php
/**
 * Movie Headbox Details Tab Template view
 * 
 * Showing a movie's headbox details tab, Allociné style.
 * 
 * @since    2.1.4
 * 
 * @uses    $id
 * @uses    $tagline
 * @uses    $overview
 * @uses    $details
 * @uses    $metas
 */
?>

					<div id="movie-headbox-<?php echo $id ?>-overview" class="wpmoly headbox allocine movie section">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Overview', 'wpmovielibrary' ); ?></h3>
						<h6><?php echo $tagline ?></h6>
						<p><?php echo $overview ?></p>
					</div>
					<hr />
					<div id="movie-headbox-<?php echo $id ?>-details" class="wpmoly headbox allocine movie section">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Details', 'wpmovielibrary' ); ?></h3>
<?php
$i = 1; $t = count( $details ); $m = ceil( $t / 2 ) + 1;
foreach ( $details as $detail ) :
	if ( 1 == $i ) :
?>
						<div class="wpmoly headbox allocine movie subsection">
							<ul>
<?php
	elseif ( $i == $m ) :
?>
							</ul>
						</div>
						<div class="wpmoly headbox allocine movie subsection">
							<ul>
<?php
	endif;
?>
								<li>
									<span class="wpmoly headbox allocine movie meta label"><?php echo $detail['title']; ?>&nbsp;</span>
									<span class="wpmoly headbox allocine movie meta value"><?php echo $detail['value']; ?></span>
								</li>
<?php
	if ( $i == $t ) :
?>
							</ul>
						</div>

<?php
	endif;

	$i++;
endforeach;
?>
					</div>
					<hr />
					<div id="movie-headbox-<?php echo $id ?>-metadata" class="wpmoly headbox allocine movie section">
						<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Metadata', 'wpmovielibrary' ); ?></h3>
						<div class="wpmoly headbox allocine movie subsection full">
							<ul>
<?php foreach ( $metas as $meta ) : ?>
								<li>
									<span class="wpmoly headbox allocine movie meta label"><?php echo $meta['title']; ?>&nbsp;</span>
									<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['value']; ?></span>
								</li>

<?php endforeach; ?>
							</ul>
						</div>
					</div>
 
