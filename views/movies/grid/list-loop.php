<?php if ( ! is_null( $debug ) ) : ?>
				<div>
					<strong>$main_args:</strong><br />
					<pre><?php print_r( $debug['main_args'] ); ?></pre>
					<strong>$permalinks_args:</strong><br />
					<pre><?php print_r( $debug['permalinks_args'] ); ?></pre>
				</div>
<?php endif; ?>

				<div id="wpmoly-movie-grid" class="wpmoly movies list<?php echo $theme; ?>">

<?php
global $post;
if ( ! empty( $movies ) ) :
	foreach ( $movies as $letter => $block ) :
?>
					<h5><?php echo $letter ?></h5>
					<ul id="wpmoly-movie-list-<?php echo $letter ?>">
<?php
		foreach ( $block as $movie ) :
?>
						<li id="wpmoly-movie-<?php echo $movie['id']; ?>" class="wpmoly movie">
							<a class="wpmoly list movie link" href="<?php echo $movie['url']; ?>"><?php echo $movie['title']; ?></a>
						</li>

<?php
		endforeach;
?>
					</ul>
<?php
	endforeach;
else :
?>
					<h5><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'wpmovielibrary' ); ?></h5>
					<p><?php _e( 'We could&rsquo;t find any movie matching your criteria.', 'wpmovielibrary' ); ?></p>
<?php endif; ?>

				</div>
