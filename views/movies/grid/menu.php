
				<ul id="wpmoly-movie-grid-menu" class="wpmoly movies grid menu list">

					<?php foreach ( $default as $letter ) : ?><li id="wpmoly-movie-grid-menu-item-<?php echo $letter ?>" class="wpmoly movies grid menu list item<?php if ( strtolower( $letter ) == strtolower( $current ) ) echo ' active'; ?>"><?php if ( in_array( $letter, $letters ) ) { ?><a href="<?php echo $url . $letter; ?>"><?php echo $letter; ?></a><?php } else { echo $letter; } ?></li><?php endforeach; ?>
				</ul>
