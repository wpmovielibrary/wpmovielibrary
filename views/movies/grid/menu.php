
				<ul id="wpmoly-movie-grid-menu" class="wpmoly movies grid menu list">

<?php foreach ( $default as $letter ) : ?>
					<li id="wpmoly-movie-grid-menu-item-<?php echo $letter ?>" class="wpmoly movies grid menu list item<?php if ( strtolower( $letter ) == strtolower( $current ) ) echo ' active'; ?>"><?php if ( in_array( $letter, $letters ) ) { ?><a href="<?php echo get_permalink() . '?letter=' . $letter; ?>"><?php echo $letter; ?></a><?php } else { echo $letter; } ?></li>

<?php endforeach; ?>
					<li id="wpmoly-movie-grid-menu-item-all" class="wpmoly movies grid menu list item<?php if ( 'all' == $current ) echo ' active'; ?>"><a href="<?php the_permalink() ?>"><?php _e( 'All', 'wpmovielibrary' ) ?></a></li>
				</ul>
