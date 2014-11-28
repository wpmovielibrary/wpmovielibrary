
				<ul id="wpmoly-movie-grid-menu" class="wpmoly movies grid menu list">

<?php foreach ( $default as $letter ) : ?>
					<li id="wpmoly-movie-grid-menu-item-<?php echo $letter ?>" class="wpmoly movies grid menu list item<?php if ( strtolower( $letter ) == strtolower( $current ) ) echo ' active'; ?>"><?php if ( in_array( $letter, $letters ) ) { ?><a href="<?php echo add_query_arg( array( 'letter' => $letter, 'order' => 'DESC', 'orderby' => 'title', 'number' => $count ), get_permalink() ); ?>"><?php echo $letter; ?></a><?php } else { echo $letter; } ?></li>

<?php endforeach; ?>
					<li id="wpmoly-movie-grid-menu-item-all" class="wpmoly movies grid menu list item<?php if ( 'all' == $current ) echo ' active'; ?>"><a href="<?php the_permalink() ?>"><?php _e( 'All', 'wpmovielibrary' ) ?></a></li>
				</ul>

				<ul id="wpmoly-movie-grid-menu-2" class="wpmoly movies grid menu list">

					<li id="wpmoly-movie-grid-menu-item-alpha-asc" class="wpmoly movies grid menu list item<?php if ( 'ASC' == $order && 'title' == $orderby ) echo ' active'; ?>"><a href="<?php echo add_query_arg( array( 'letter' => $current, 'order' => 'ASC', 'orderby' => 'title', 'number' => $count ), get_permalink() ) ?>" title="<?php _e( 'List alphabetically, ascendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-alpha-asc"></span></a></li>
					<li id="wpmoly-movie-grid-menu-item-alpha-desc" class="wpmoly movies grid menu list item<?php if ( 'DESC' == $order && 'title' == $orderby ) echo ' active'; ?>"><a href="<?php echo add_query_arg( array( 'letter' => $current, 'order' => 'DESC', 'orderby' => 'title', 'number' => $count ), get_permalink() ) ?>" title="<?php _e( 'List alphabetically, descendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-alpha-desc"></span></a></li>
					<li id="wpmoly-movie-grid-menu-item-numeric-asc" class="wpmoly movies grid menu list item<?php if ( 'ASC' == $order && 'count' == $orderby ) echo ' active'; ?>"><a href="<?php echo add_query_arg( array( 'letter' => $current, 'order' => 'ASC', 'orderby' => 'count', 'number' => $count ), get_permalink() ) ?>" title="<?php _e( 'List by movies count, ascendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-numeric-asc"></span></a></li>
					<li id="wpmoly-movie-grid-menu-item-numeric-desc" class="wpmoly movies grid menu list item<?php if ( 'DESC' == $order && 'count' == $orderby ) echo ' active'; ?>"><a href="<?php echo add_query_arg( array( 'letter' => $current, 'order' => 'DESC', 'orderby' => 'count', 'number' => $count ), get_permalink() ) ?>" title="<?php _e( 'List by movies count, descendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-numeric-desc"></span></a></li>
					<li id="wpmoly-movie-grid-menu-item-count" class="wpmoly movies grid menu list item"><form action="<?php echo add_query_arg( array( 'order' => $order, 'orderby' => $orderby ), get_permalink() ) ?>"><?php _e( 'Items per page:', 'wpmovielibrary' ) ?> <input type="hidden" name="order" value="<?php echo $order; ?>" /><input type="hidden" name="orderby" value="<?php echo $orderby; ?>" /><input name="number" type="text" size="3" maxlength="3" placeholder="50" value="<?php echo $count; ?>" /></form></li>
				</ul>
